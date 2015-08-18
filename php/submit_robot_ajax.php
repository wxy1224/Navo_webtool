<?php
$building_id = filter_input(INPUT_POST, "buildingid", FILTER_SANITIZE_STRING);
$floornumber = filter_input(INPUT_POST, "floor_number", FILTER_SANITIZE_STRING);
$arraydata = $_POST["pts"];
	//echo "building:".$building_id."floor".$floornumber;
//print $arraydata;
//$array = json_decode($arraydata, true);


require_once "config.php";
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

if ($mysqli->errno) {
	print($mysqli->error);
	die();
}
/*
$get_str = "";

$get_query = "SELECT * FROM cornell_map_server_robotpath where building_id = $building_id AND floor_number = $floornumber";
$get_result = $mysqli->query($get_query);
if($get_result->num_rows!=0){
	$get_row = $get_result->fetch_assoc();
	$get_str = $get_row["path_structure"];
}

$all_array = array();
echo $get_str;
if($get_str!==null && $get_str!==""){
	$get_json = json_decode($get_str, true);
	$all_array = array_merge($array, $get_json);	
}else{
	$all_array = $array;
}
*/

/*
$query = "";
if($all_str!==null && $all_str!==""){
	if($get_result->num_rows!=0){
		$query = "UPDATE cornell_map_server_robotpath SET path_structure='$all_str' WHERE building_id = $building_id AND floor_number = $floornumber;";
	}else{
		$query = "INSERT INTO cornell_map_server_robotpath(building_id, floor_number, path_structure) VALUES($building_id, $floornumber, '$all_str') ";
	}
}
*/

		$query = "INSERT INTO cornell_map_server_robotpath(building_id, floor_number, path_structure) VALUES($building_id, $floornumber, '$arraydata') ";

$result = $mysqli->query($query);
if($result){
	echo "successfully added\n".$arraydata;
	$get_query = "SELECT * FROM cornell_map_server_robotpath WHERE path_structure = '$arraydata'";
	$get_result = $mysqli->query($get_query);
	$get_row = $get_result->fetch_assoc();
	$id = $get_row["id"];
	echo "\n path id: ".$id;
}else{
	echo "unsuccessful ".$query;
}
die();

?>