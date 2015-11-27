<?php
$building_id = intval(filter_input(INPUT_POST, "building_id", FILTER_SANITIZE_STRING));
$floor_number = intval(filter_input(INPUT_POST, "floor_number", FILTER_SANITIZE_STRING));
$x = floatval(filter_input(INPUT_POST, "x", FILTER_SANITIZE_STRING));
$y = floatval(filter_input(INPUT_POST, "y", FILTER_SANITIZE_STRING));
require_once "function.php";
require_once "config.php";
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

if ($mysqli->errno) {
	print($mysqli->error);
	die();
}



$geo_query = "SELECT * FROM cornell_map_server_geometry WHERE building_id = $building_id AND floor_number = $floor_number";
$geo_result = $mysqli->query($geo_query);

$geo_row = $geo_result->fetch_assoc();
$inside = false;
$uid = "";
while($geo_row && !$inside){
	$geometry = $geo_row["geometry"];
	$uid = $geo_row["uid"];
	$geo_pts = json_decode($geometry);
	$inside = insidePoly($x, $y, $geo_pts);
	$geo_row = $geo_result->fetch_assoc();
}



if($inside){
	echo $uid;
	die();


}else{
	echo "";
	die();
}


?>






