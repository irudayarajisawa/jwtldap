<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header('Content-Type:application/json');
include '../database/Database.php';
include '../vendor/autoload.php';
include('../class/auth.class.php');

use \Firebase\JWT\JWT;
$conf = parse_ini_file('/etc/projectsconf/jwtldap.ini');
$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input", true));
    #$email = htmlentities($data->email);
    $username = htmlentities($data->username);
    $password = htmlentities($data->password);
#    $email = 'test@example.com';
#    $password = '123456';

    #$obj->select('users', '*', null, "email='{$email}'", null, null);
    $obj->select('users', '*', null, "username='{$username}'", null, null);
    $datas = $obj->getResult();
    foreach ($datas as $data) {
        $id = $data['id'];
        $email = $data['email'];
        $username = $data['username'];
        // $password=$data['password'];


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
#var_dump($auth);
$logindetails = $auth->login($username,$password);

	#if (!password_verify($password, $data['password'])) {
	if (!$logindetails) {
            echo json_encode([
                'status' => 0,
                'message' => $conf['jwt_failuremsg'],
            ]);
        } else {
            $payload = [
                'iss' => $conf['jwt_iss'],
                'aud' => $conf['jwt_aud'],
                'exp' => time() + 1000, //10 mint
                'data' => [
                    'id' => $id,
                    'username' => $username,
                    'email' => $email,
                ],
            ];
            $secret_key = utf8_decode(base64_decode($conf['jwt_secretkey']));
            $jwt = JWT::encode($payload, $secret_key, $conf['jwt_encode']);
            echo json_encode([
                'status' => 1,
                'jwt' => $jwt,
                'message' => $conf['jwt_successmsg'],
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => $conf['jwt_errormsg1001'],
    ]);
}
