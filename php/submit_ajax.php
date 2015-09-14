<?php
$geoid = filter_input(INPUT_POST, "geo_id", FILTER_SANITIZE_STRING);
$parentid = filter_input(INPUT_POST, "parent_id", FILTER_SANITIZE_STRING);
$buildingid = filter_input(INPUT_POST, "building_id", FILTER_SANITIZE_STRING);
$floornumber = filter_input(INPUT_POST, "floor_number", FILTER_SANITIZE_STRING);
$geointro = json_encode($_POST["geo_intro"]);
$geodetail = json_encode($_POST["geo_detail"]);
$geotype = filter_input(INPUT_POST, "geo_type", FILTER_SANITIZE_STRING);
$pts_str = $_POST["geo_pts"];
//$pts_str = json_encode($pts_str);


require_once "config.php";
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

	if ($mysqli->errno) {
		print($mysqli->error);
		die();
	}

	$query = "INSERT INTO cornell_map_server_geometry(uid, building_id, floor_number, parent_uid, introduction, detail, geometry, geometry_type) VALUES($geoid, $buildingid, $floornumber, $parentid, $geointro, $geodetail, '$pts_str', $geotype)";
	if(intval($geotype)>4){
		$query = "INSERT INTO cornell_map_server_geometry(uid, building_id, floor_number, parent_uid, introduction, detail, geometry, geometry_type) VALUES($geoid, NULL, NULL, $parentid, $geointro, $geodetail, '$pts_str', $geotype)";	
	}
	
	
	$result = $mysqli->query($query);

	if($result){
		echo "successfully added ".$query;
	}else{
		echo "unsuccessful ".$query;
	}
	

	//echo $query;

	die();
?>