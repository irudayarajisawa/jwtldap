<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
include_once '../database/Database.php';
$database = new Database();
$obj = new Database();

if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    $data = json_decode(file_get_contents("php://input", true));
    $id = htmlentities($data->id);

    if (!empty($id)) {
	    $obj->delete('tblstudinfo', "id={$id}");
	    $data = $obj->getResult();
	    if ($data[0] == 1) {
		    #"email='{$email}'"
		    http_response_code(200);
		    echo json_encode([
			    'status' => 1,
			    'message' => 'Alias deleted successfully',
		    ]);
	    } else {
		    http_response_code(404);
		    echo json_encode([
			    'status' => 0,
			    'message' => 'Alias not found',
		    ]);
	    }
    } else {
	    http_response_code(400);
	    echo json_encode([
		    'status' => 0,
		    'message' => 'Invalid request. Missing ID',
	    ]);
    }
} else {
	http_response_code(405);
	echo json_encode([
		'status' => 0,
		'message' => 'Method Not Allowed',
	]);
}
?>

