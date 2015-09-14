<?php


$rid1 = filter_input(INPUT_POST, "rid1", FILTER_SANITIZE_STRING);
$rid2 = filter_input(INPUT_POST, "rid2", FILTER_SANITIZE_STRING);
$x1 = filter_input(INPUT_POST, "x1", FILTER_SANITIZE_STRING);
$y1 = filter_input(INPUT_POST, "y1", FILTER_SANITIZE_STRING);
$x2 = filter_input(INPUT_POST, "x2", FILTER_SANITIZE_STRING);
$y2 = filter_input(INPUT_POST, "y2", FILTER_SANITIZE_STRING);
//$connectors = filter_input(INPUT_POST, "connectors", FILTER_SANITIZE_STRING);
$building_id = filter_input(INPUT_POST, "building_id", FILTER_SANITIZE_STRING);
//$connectors = $_POST["connectors"];
$con_dir = $_POST["direction"];

require_once "config.php";
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

if ($mysqli->errno) {
	print($mysqli->error);
	die();
}

$all_connectors_str="";
$getconnectorquery = "SELECT * FROM cornell_map_server_roomconnector WHERE building_id = $building_id";
$getcon_result = $mysqli->query($getconnectorquery);
$connectors_json = "";
if($getcon_result->num_rows!=0){
	$building_connector = $getcon_result->fetch_assoc();
	$all_connectors_str = $building_connector["connector_structure"];
	//$connectors_json = json_decode($all_connectors_str, true);
}
	$con_json = json_decode($all_connectors_str, true);

//$con_json = json_decode(stripslashes($connectors));
$num_con = count($con_json);

$con_json[$num_con]["y2"] = floatval($y2);
$con_json[$num_con]["x2"] = floatval($x2);
$con_json[$num_con]["y1"] = floatval($y1);
$con_json[$num_con]["x1"] = floatval($x1);
$con_json[$num_con]["rid2"] = intval($rid2);
$con_json[$num_con]["rid1"] = intval($rid1);
$con_json[$num_con]["dir"] = intval($con_dir);

$con_str = json_encode($con_json);

$query = "";
$select_query = "SELECT * FROM cornell_map_server_roomconnector WHERE building_id = $building_id";
$select_result = $mysqli->query($select_query);
if($select_result->num_rows!=0){
	$query = "UPDATE cornell_map_server_roomconnector SET connector_structure='$con_str' WHERE building_id = $building_id;";
}else{
	$query = "INSERT INTO cornell_map_server_roomconnector(building_id, connector_structure)VALUES($building_id, '$con_str')";
}

$result = $mysqli->query($query);

if($result){
	echo "succeed";
}else{
	echo $query;
}


die();


?>