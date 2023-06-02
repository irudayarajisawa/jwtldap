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
    $filter = '(uid=' . $username . ')';
    $attributes = array('cn', 'givenname', 'mail', 'uidnumber', 'gidnumber', 'mobile', 'title', 'departmentnumber', 'roomnumber', 'manager');    
    $result = ldap_search($this->ldap_connection, $this->ldap_search_base, "(uid=$username)", $attributes);
    $ldapEntries = ldap_get_entries($this->ldap_connection, $result);
    if ($ldapEntries['count'] >= 1) {

                // Process and return the data
                $users = array();
                for ($i = 0; $i < $ldapEntries['count']; $i++) {
                    $givenname = isset($ldapEntries[$i]['givenname'][0]) ? $ldapEntries[$i]['givenname'][0] : null;
                    $mail = isset($ldapEntries[$i]['mail'][0]) ? $ldapEntries[$i]['mail'][0] : null;
                    $cn = isset($ldapEntries[$i]['cn'][0]) ? $ldapEntries[$i]['cn'][0] : null;
                    $uidnumber = isset($ldapEntries[$i]['uidnumber'][0]) ? $ldapEntries[$i]['uidnumber'][0] : null;
                    $gidnumber = isset($ldapEntries[$i]['gidnumber'][0]) ? $ldapEntries[$i]['gidnumber'][0] : null;
                    $mobile = isset($ldapEntries[$i]['mobile'][0]) ? $ldapEntries[$i]['mobile'][0] : null;
                    $title = isset($ldapEntries[$i]['title'][0]) ? $ldapEntries[$i]['title'][0] : null;
                    $departmentnumber = isset($ldapEntries[$i]['departmentnumber'][0]) ? $ldapEntries[$i]['departmentnumber'][0] : null;
                    $roomnumber = isset($ldapEntries[$i]['roomnumber'][0]) ? $ldapEntries[$i]['roomnumber'][0] : null;
                    $manager = isset($ldapEntries[$i]['manager'][0]) ? $ldapEntries[$i]['manager'][0] : null;



                    $user = array(
                        'cn' => $cn,
                        'Display_Name' => $givenname,
                        'Email' => $mail,
                        'uidNumber' => $uidnumber,
                        'gidNumber' => $gidnumber,
                        'Mobile' => $mobile,
                        'title' => $title,
                        'departmentnumber' => $departmentnumber,
                        'roomnumber' => $roomnumber,
                        'manager' => $manager
                    );
                    $users[] = $user;
                }

                // Return the fetched data as the API response
                //header('Content-Type: application/json');
                //echo json_encode($users);	    

	return $users;
      #return $entries[0];
    }
    return false;
  }

  public function __destruct() {
    ldap_unbind($this->ldap_connection);
  }
}

?>
