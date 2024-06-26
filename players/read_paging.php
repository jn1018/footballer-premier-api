<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/core.php';
include_once '../shared/utilities.php';
include_once '../config/database.php';
include_once '../objects/player.php';

// utilities
$utilities = new Utilities();

// instantiate database and player object
$database = new Database();
$db = $database->getConnection();

// initialize object
$player = new Player($db);

// query players
$stmt = $player->readPaging($from_record_num, $records_per_page);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

	// players array
	$players_arr=array();
	$players_arr["records"]=array();
	$players_arr["paging"]=array();

	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		$player_item=array(
			"id" => $id,
			"first_name" => $first_name,
			"last_name" => $last_name,
			"position" => html_entity_decode($position),
			"nation" => $nation,
			"squad_id" => $squad_id,
			"squad_name" => $squad_name
		);

		array_push($players_arr["records"], $player_item);
	}


	// include paging
	$total_rows=$player->count();
	$page_url="{$home_url}player/read_paging.php?";
	$paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
	$players_arr["paging"]=$paging;

	// set response code - 200 OK
	http_response_code(200);

	// make it json format
	echo json_encode($players_arr);
}

else{

	// set response code - 404 Not found
	http_response_code(404);

	// tell the user players does not exist
    echo json_encode(
		array("message" => "No players found.")
	);
}
?>
