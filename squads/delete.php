<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object file
include_once '../config/database.php';
include_once '../objects/squad.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare squad object
$squad = new Squad($db);

// get squad id
$data = json_decode(file_get_contents("php://input"));

// set squad id to be deleted
$squad->id = $data->id;

// delete the squad
if($squad->delete()){
	echo '{';
		echo '"message": "Squad was deleted."';
	echo '}';
}

// if unable to delete the squad
else{
	echo '{';
		echo '"message": "Unable to delete object."';
	echo '}';
}
?>
