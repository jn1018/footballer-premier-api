<?php
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

// get keywords
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";

// query squads
$stmt = $squad->searchAll_WithoutPagination($keywords);
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){

	$data="";
	$x=1;

	// retrieve our table contents
	// fetch() is faster than fetchAll()
	// http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		// extract row
		// this will make $row['name'] to
		// just $name only
		extract($row);

		$data .= '{';
			$data .= '"id":'  . $id . ',';
			$data .= '"name":"'   . $name . '",';
			$data .= '"min_played":"'   . $min_played . '",';
			$data .= '"wins":"'   . $wins . '",';
			$data .= '"draws":"'   . $draws . '",';
			$data .= '"losses":"'   . $losses . '",';
			$data .= '"points":"'   . $points . '"';
		$data .= '}';

		$data .= $x<$num ? ',' : '';

		$x++;
	}

	// json format output
	echo "[{$data}]";
}

else{
    echo '{';
		echo '"message": "No squads found."';
	echo '}';
}
?>
