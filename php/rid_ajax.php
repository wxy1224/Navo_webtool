<?php
	$building_id = intval(filter_input(INPUT_POST, "building_id", FILTER_SANITIZE_STRING));
	$floor_number = intval(filter_input(INPUT_POST, "floor_number", FILTER_SANITIZE_STRING));
	$x = floatval(filter_input(INPUT_POST, "x", FILTER_SANITIZE_STRING));
	$y = floatval(filter_input(INPUT_POST, "y", FILTER_SANITIZE_STRING));

	require_once "config.php";
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

	if ($mysqli->errno) {
		print($mysqli->error);
		die();
	}



	$query = "SELECT * FROM 

	(SELECT *, (x+width) as x_right, (y+height) as y_top FROM cornell_map_server_room) AS view

	 WHERE building_id = $building_id and floor_number = $floor_number and $x>x and $x<x_right and $y>y and $y<y_top";
	$result = $mysqli->query($query);

	if($result){
		if($result->num_rows!=0){
			$row = $result->fetch_assoc();
			echo $row["id"];
			die();

		}else{
			echo "";
			die();
		}
		
	}else{
		echo "unsuccessful".$query;
		die();
	}


?>






