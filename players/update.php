<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../config/database.php';
include_once '../objects/player.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare player object
$player = new Player($db);

// get id of player to be edited
$data = json_decode(file_get_contents("php://input"));

// set ID property of player to be edited
$player->id = $data->id;

// set player property values
$player->first_name = $data->first_name;
$player->last_name = $data->last_name;
$player->position = $data->position;
$player->nation = $data->nation;
$player->squad_id = $data->squad_id;

// update the player
if($player->update()){

	// set response code - 200 ok
	http_response_code(200);

	// tell the user
	echo json_encode(array("message" => "player was updated."));
}

// if unable to update the player, tell the user
else{

	// set response code - 503 service unavailable
	http_response_code(503);

	// tell the user
	echo json_encode(array("message" => "Unable to update player."));
}
?>
