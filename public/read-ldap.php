<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header('Content-Type:application/json');
include '../database/Database.php';
include '../vendor/autoload.php';
include('../class/auth.class.php');


use \Firebase\JWT\JWT;
$conf = parse_ini_file('/etc/projectsconf/jwtldap.ini');
$obj = new Database($conf['mysql_host'], $conf['mysql_username'], utf8_decode(base64_decode($conf['mysql_password'])), $conf['mysql_database']);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    
   try{
    $allheaders=getallheaders();
    $jwt=$allheaders['Authorization'];

    $secret_key = utf8_decode(base64_decode($conf['jwt_secretkey']));
    $user_data=JWT::decode($jwt,$secret_key,array('HS256'));
    $data=$user_data->data;
    $username = $data->username;

    if(isset($data->username)){

$ldap_server = $conf['ldap_host'];
$ldap_port = $conf['ldap_port'];
$ldap_user = $conf['ldap_binduser'];
$ldap_password = utf8_decode(base64_decode($conf['ldap_bindpassword']));
$ldap_search_base = $conf['ldap_base'];
$dbhost = $conf['mysql_host'];
$dbname = $conf['mysql_database'];
$dbuser = $conf['mysql_username'];
$dbpass = utf8_decode(base64_decode($conf['mysql_password']));

$auth = new Auth($dbhost,$dbname,$dbuser, $dbpass, $ldap_server, $ldap_port, $ldap_user, $ldap_password, $ldap_search_base);
$userdetails = $auth->fetchuserdata($username);

                // Return the fetched data as the API response
                header('Content-Type: application/json');
                echo json_encode($userdetails);
                 } else {
    echo json_encode([
        'status' => 0,
        'message' => "No matching results found for 'uid' value: " . $username,
    ]);
			 
                }


   }catch(Exception $e){
    echo json_encode([
        'status' => 0,
        'message' => $e->getMessage(),
    ]);
   }
}else {
    echo json_encode([
        'status' => 0,
        'message' => $conf['jwt_errormsg1001'],
    ]);
}
