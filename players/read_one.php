<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/player.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare player object
$player= new Player($db);

// set ID property of record to read
$player->id = isset($_GET['id']) ? $_GET['id'] : die();

// read the details of player to be edited
$player->readOne();

if($player->name!=null){
	// create array
	$player_arr = array(
		"id" =>  $player->id,
		"first_name" => $player->first_name,
		"last_name" => $player->last_name,
		"position" => $player->position,
		"nation" => $player->nation,
		"squad_id" => $player->squad_id,
		"squad_name" => $player->squad_name

	);

	// set response code - 200 OK
	http_response_code(200);

	// make it json format
	echo json_encode($player_arr);
}

else{
	// set response code - 404 Not found
    http_response_code(404);

	// tell the user player does not exist
	echo json_encode(array("message" => "player does not exist."));
}
?>
