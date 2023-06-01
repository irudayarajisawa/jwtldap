<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once '../database/Database.php';
$database = new Database();
$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$data = json_decode(file_get_contents("php://input", true));
	$rollno = htmlentities($data->rollno);
	$smail = htmlentities($data->smail);
	$alias = htmlentities($data->alias);
	if(!empty($data->rollno) && !empty($data->smail) && !empty($data->alias)){
		$obj->insert('tblstudinfo', ['rollno' => $rollno, 'smail' => $smail, 'alias' => $alias, 'updated' => date('Y-m-d H:i:s')]);
		$data = $obj->getResult();
		if ($data[0] == 1) {
			http_response_code(201);
			echo json_encode([
				'status' => 1,
				'message' => 'Alias Saved Successfully. #A9722',
			]);
		} else {
			http_response_code(503);
			echo json_encode([
				'status' => 0,
				'message' => 'Server Problem! #A3980',
			]);
		}
	}else{
		http_response_code(400);
		echo json_encode(array("message" => "Unable to create item. Data is invalid. #A2134"));
	}

}
?>

