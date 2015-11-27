<?php
$building_id = filter_input(INPUT_POST, "buildingid", FILTER_SANITIZE_STRING);
$floor_number = filter_input(INPUT_POST, "floornumber", FILTER_SANITIZE_STRING);
$vertStr = $_POST["vertices"];

$vertJSON = json_decode($vertStr);

require_once "config.php";
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

if ($mysqli->errno) {
	print($mysqli->error);
	die();
}

for($i=0; $i<count($vertJSON); $i++){
	$x = $vertJSON[$i]->x;
	$y = $vertJSON[$i]->y;
	$query = "INSERT INTO cornell_map_server_geometryvertex (building_id, floor_number, x, y) VALUES ($building_id, $floor_number, $x, $y)";
	$result = $mysqli->query($query);
	if(!$result){
		echo "insert unsuccessful \n".$query;
		die();
	}
}
echo "insert successful \n".$query;

$mysqli->close();
?>