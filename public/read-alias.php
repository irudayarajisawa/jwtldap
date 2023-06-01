<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Method:POST');
header("Content-Type: application/json; charset=UTF-8");
include '../database/Database.php';
include '../vendor/autoload.php';
use \Firebase\JWT\JWT;
$obj = new Database();

if($_SERVER["REQUEST_METHOD"] == "GET"){
   try{
    $allheaders=getallheaders();
    $jwt=$allheaders['Authorization'];
    $secret_key = utf8_decode(base64_decode($conf['jwt_secretkey']));
    $user_data=JWT::decode($jwt,$secret_key,array('HS256'));
    $data=$user_data->data;
    $rollno = $data->username;
	    if (!$rollno){
		    echo json_encode([
			    'status' => 0,
			    'message' => "Invalid Rollno. #A4398",
		    ]);
	    }
    #$obj->select("tblstudinfo", "smail,alias", null, "rollno='{$rollno}'", null, null);
    $obj->select("tblstudinfo", "*", null, "rollno='{$rollno}'", null, null);
    $userdata = $obj->getResult();
    if (isset($userdata[0]['alias'])) {
	    http_response_code(200);     
	    echo json_encode([
		    'status' => 1,
		    'userdata' => $userdata,
	    ]);
    }else{
	    echo json_encode([
		    'status' => 0,
		    'userdata' =>  "Record Not Found",
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
        'message' => 'Access Denied',
    ]);
}
?>
