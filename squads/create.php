<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get database connection
include_once '../config/database.php';

// instantiate squad object
include_once '../objects/squad.php';

$database = new Database();
$db = $database->getConnection();

$squad = new Squad($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// set squad property values
$squad->name = $data->name;
$squad->min_played = $data->min_played;
$squad->wins = $data->wins;
$squad->draws = $data->draws;
$squad->losses = $data->losses;
$squad->points = $data->points;
$squad->created = date('Y-m-d H:i:s');

// create the squad
if ($squad->create()) {
	echo '{';
		echo '"message": "squad was created."';
	echo '}';
}

// if unable to create the squad, tell the user
else{
	echo '{';
		echo '"message": "Unable to create squad."';
	echo '}';
}
?>
