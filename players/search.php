<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/player.php';

// instantiate database and player object
$database = new Database();
$db = $database->getConnection();

// initialize object
$player = new Player($db);

// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";

// query players
$stmt = $player->search($keywords);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num > 0) {

	// players array
	$players_arr=array();
	$players_arr["records"]=array();

	// retrieve our table contents
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		$player_item=array(
			"id" => $id,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"position" => $position,
			"nation" => $nation,
			"squad_id" => $squad_id,
			"squad_name" => $squad_name
		);

		array_push($players_arr["records"], $player_item);
	}

	// set response code - 200 OK
	http_response_code(200);

	// show players data
	echo json_encode($players_arr);
}

else{
	// set response code - 404 Not found
	http_response_code(404);

	// tell the user no players found
    echo json_encode(
		array("message" => "No players found.")
	);
}
?>
