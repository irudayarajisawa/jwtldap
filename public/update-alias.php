<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../database/Database.php';
$conf = parse_ini_file('/etc/projectsconf/jwtldap.ini');
$obj = new Database($conf['mysql_host'], $conf['mysql_username'], utf8_decode(base64_decode($conf['mysql_password'])), $conf['mysql_database']);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input", true));
    $rollno = htmlentities($data->rollno);
    $smail = htmlentities($data->smail);
    $alias = htmlentities($data->alias);

    if (!empty($data->rollno) && !empty($data->smail) && !empty($data->alias)) {
	    $obj->update('tblstudinfo', ['smail' => $smail, 'alias' => $alias, 'updated' => date('Y-m-d H:i:s')], "rollno = '{$rollno}'");
	    $data = $obj->getResult();
	    if ($data[0] == 1) {
		    http_response_code(200);
		    echo json_encode([
			    'status' => 1,
			    'message' => 'Alias Updated Successfully. #A9722',
		    ]);
	    } else {
		    http_response_code(503);
		    echo json_encode([
			    'status' => 0,
			    'message' => 'Server Problem! #A3980',
		    ]);
	    }
    } else {
        http_response_code(400);
        echo json_encode([
            'status' => 0,
            'message' => 'Unable to update alias. Data is invalid. #A2134',
        ]);
    }
}
?>

