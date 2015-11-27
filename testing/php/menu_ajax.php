<?php

$buildingid = filter_input(INPUT_POST, "building_id", FILTER_SANITIZE_STRING);
$result = "";
$query = "";

if($buildingid == "-1"){
	echo "nothing";
	die();
}else{
	require_once "config.php";
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

	if ($mysqli->errno) {
		print($mysqli->error);
		die();
	}

	$query = "SELECT * FROM cornell_map_server_floor WHERE building_id = $buildingid";
	$result = $mysqli->query($query);

	if( !$result ) {
	echo "unsuccessful";
	die();
	}

	$data = array();
	$row = $result->fetch_assoc();
	while($row!=false){
		array_push($data, $row["id"], $row["floor_name"]);
		$row = $result->fetch_assoc();
	}
	echo json_encode($data);
	die();


}






?>