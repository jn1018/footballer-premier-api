<?php
// required header
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// include database and object files
include_once '../config/database.php';
include_once '../objects/squad.php';

// instantiate database and squad object
$database = new Database();
$db = $database->getConnection();

// initialize object
$squad = new Squad($db);

// query squads
$stmt = $squad->read();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

	// squad array
	$squad_arr=array();
	$squad_arr["records"]=array();

	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		$squad_item=array(
			"id" => $id,
			"name" => $name,
			"min_played" => $min_played,
			"wins" => $wins,
			"draws" => $draws,
			"losses" => $losses,
			"points" => $points
		);

		array_push($squad_arr["records"], $squad_item);
	}

	// set response code - 200 OK
    http_response_code(200);

	// show squads data in json format
	echo json_encode($squad_arr);
}

else{

	// set response code - 404 Not found
	http_response_code(404);

	// tell the user no squads found
    echo json_encode(
		array("message" => "No squads found.")
	);
}
?>
