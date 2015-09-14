<?php 

require_once 'php/config.php';
$mysqli= new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

if ($mysqli->connect_error) {
	echo "Connection failed: " . $mysqli->connect_error;
} 

$geo_query = "SELECT * FROM cornell_map_server_geometry where building_id = 4 and floor_number = 1";
$geo_result = $mysqli->query($geo_query);
$geo_row = $geo_result->fetch_assoc();

$floor_query = "SELECT * FROM cornell_map_server_floor where building_id = 4 and floor_number = 1";
$floor_result = $mysqli->query($floor_query);
$floor_row = $floor_result->fetch_assoc();
$width_meter = floatval($floor_row["width"]);
$height_meter = floatval($floor_row["height"]);

while($geo_row){
	$id = $geo_row["id"];
	$geometry_str = $geo_row["geometry"];
	$geometry_json = json_decode($geometry_str);

	for($i = 0; $i<count($geometry_json); $i++){
		$point = $geometry_json[$i];
		$x = floatval($point->x);
		$y = floatval($point->y);
		$geometry_json[$i]->x = $x;
		$geometry_json[$i]->y = $y;
	}
	$new_geo_str = json_encode($geometry_json);
	$update_query = "UPDATE cornell_map_server_geometry SET geometry = '$new_geo_str' WHERE id= $id ";
	$update_result = $mysqli->query($update_query);
	if(!$update_result){
		echo $update_query."<br>";
	}
	else{
		echo "success";
	}
	$geo_row = $geo_result->fetch_assoc();
}


?>