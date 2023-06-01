<?php

//    add headers

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header('Content-Type:application/json');
include '../database/Database.php';
$conf = parse_ini_file('/etc/projectsconf/jwtldap.ini');
$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input", true));

    $name = htmlentities($data->name);
    $email = htmlentities($data->email);
    $password = htmlentities($data->password);
    $new_password = password_hash($password, PASSWORD_DEFAULT);
    // check user by email
    $obj->select("users", "email", null, "email='{$email}'", null, null);
    $is_email = $obj->getResult();
    if (isset($is_email[0]['email']) == $email) {
        echo json_encode([
            'status' => 2,
            'message' => 'Email already Exists',
        ]);
    }else{
        $obj->insert('users', ['name' => $name, 'email' => $email, 'password' => $new_password, 'create_at' => date('Y-m-d')]);
        $data = $obj->getResult();
        if ($data[0] == 1) {
            echo json_encode([
                'status' => 1,
                'message' => 'User updated successfully',
            ]);
        } else {
            echo json_encode([
                'status' => 0,
                'message' => 'Server Problem',
            ]);
        }
    }
   
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Access Denied',
    ]);
}
<?php

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Method:POST');
header('Content-Type:application/json');
include './database/Database.php';
include './vendor/autoload.php';

use \Firebase\JWT\JWT;

$obj = new Database();

if($_SERVER["REQUEST_METHOD"] == "GET"){
    
   try{
    $allheaders=getallheaders();
    $jwt=$allheaders['Authorization'];

    $secret_key =  utf8_decode(base64_decode($conf['jwt_secretkey']));
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
        'message' => $conf['jwt_errormsg1001'],
    ]);
}
