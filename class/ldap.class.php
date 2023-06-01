<?php
class LDAP {

  private $ldap_server;
  private $ldap_port;
  private $ldap_user;
  private $ldap_password;
  private $ldap_search_base;
  private $ldap_connection;
  private $ldap_bind;
  public function __construct($ldap_server, $ldap_port, $ldap_user, $ldap_password, $ldap_search_base) {
    $this->ldap_server = $ldap_server;
    $this->ldap_port = $ldap_port;
    $this->ldap_user = $ldap_user;
    $this->ldap_password = $ldap_password;
    $this->ldap_search_base = $ldap_search_base;
    $this->ldap_connection = ldap_connect($this->ldap_server, $this->ldap_port);
    ldap_set_option($this->ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($this->ldap_connection, LDAP_OPT_REFERRALS, 0);
    $this->ldap_bind = ldap_bind($this->ldap_connection, $this->ldap_user, $this->ldap_password);
  }

  public function authenticate($username, $password) {
    $result = ldap_search($this->ldap_connection, $this->ldap_search_base, "(uid=$username)");
    $entries = ldap_get_entries($this->ldap_connection, $result);
    if ($entries['count'] == 1) {
      $ldap_user_dn = $entries[0]['dn'];
      if (ldap_bind($this->ldap_connection, $ldap_user_dn, $password)) {
        return true;
      }
    }
    return false;
  }

  public function get_user_details($username) {
    $result = ldap_search($this->ldap_connection, $this->ldap_search_base, "(uid=$username)");
    $entries = ldap_get_entries($this->ldap_connection, $result);
    if ($entries['count'] == 1) {
      return $entries[0];
    }
    return false;
  }

  public function __destruct() {
    ldap_unbind($this->ldap_connection);
  }
}

?>
