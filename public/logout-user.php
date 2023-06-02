<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header('Content-Type:application/json');
include '../database/Database.php';
include '../vendor/autoload.php';

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
    echo json_encode([
        'status' => 1,
        'message' => $data,
    ]);
   }catch(Exception $e){
    echo json_encode([
        'status' => 0,
        'message' => $e->getMessage(),
    ]);
   }
}else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
