<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

// include database and object files
include_once '../config/database.php';
include_once '../objects/squad.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare squad object
$squad = new Squad($db);

// set ID property of record to read
$squad->id = isset($_GET['id']) ? $_GET['id'] : die();

// read the details of squad to be edited
$squad->readOne();

// create array
$squad_arr = array(
	"id" =>  $squad->id,
	"name" => $squad->name,
	"min_played" => $squad->min_played,
	"wins" => $squad->wins,
	"draws" => $squad->draws,
	"losses" => $squad->losses,
	"points" => $squad->points
);

// make it json format
print_r(json_encode($squad_arr));
?>
