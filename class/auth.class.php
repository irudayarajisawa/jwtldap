<?php
require_once 'ldap.class.php';

class Auth {
  private $db_connection;
  private $ldap_connection;
  
  public function __construct($db_host, $db_name, $db_user, $db_password, $ldap_server, $ldap_port, $ldap_user, $ldap_password, $ldap_search_base) {
    session_start();
    $this->db_connection = new mysqli($db_host, $db_user, $db_password, $db_name);
    $this->ldap_connection = new LDAP($ldap_server, $ldap_port, $ldap_user, $ldap_password, $ldap_search_base);
  }

  public function login($username, $password) {
    if ($this->ldap_connection->authenticate($username, $password)) {
      $user_details = $this->ldap_connection->get_user_details($username);
	$this->log('Ldap Login Successful for '. $username);
      $query = "SELECT * FROM users WHERE username='$username'";
      $result = $this->db_connection->query($query);
      if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
	$this->log('DB Check Successful for '. $username);
        return true;
      }
    }
    return false;
  }

  public function logout() {
    session_unset();
    session_destroy();
    $this->log("Logout successful");
  }

  private function log($message) {
    $log_file = "../log/auth.log";
    $log_entry = "[" . date("Y-m-d H:i:s") . "] " . $message . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
  }

  public function fetchuserdata($username) {
	  $user_details = $this->ldap_connection->get_user_details($username);
	  if(!empty($user_details)){
		  return $user_details;
	  }
    return false;
  }

}
