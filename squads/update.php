<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/database.php';
include_once '../objects/squad.php';

// instantiate database and squad object
$database = new Database();
$db = $database->getConnection();

$squad = new Squad($db);

// get id of squad to be edited
$data = json_decode(file_get_contents("php://input"));

// set ID property of squad to be edited
$squad->id = $data->id;

// set squad property values
$squad->name = $data->name;
$squad->description = $data->description;

// execute the query
if($squad->update()){
	echo '{';
		echo '"message": "squad was updated."';
	echo '}';
}

// if unable to update the squad, tell the user
else{
	echo '{';
		echo '"message": "Unable to update squad."';
	echo '}';
}
