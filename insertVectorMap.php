<?php

require_once "php/config.php";
$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

if ($mysqli->errno) {
	print($mysqli->error);
	die();
}

$file = "map/level1map.svg";
$xml = simplexml_load_file($file);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

$rects = $array["rect"];
for($i = 0; $i < count($rects); $i++){
	$rect = $rects[$i];
	$attr = $rect["@attributes"];
	$x1 = floatval($attr["x"]);
	$y1 = floatval($attr["y"]);
	$width = floatval($attr["width"]);
	$height = floatval($attr["height"]);
	$x2 = floatval($x1+$width);
	$y2 = floatval($y1+$height);
	$geo_pts = array();
	$geo_pts[0] = array("x"=>$x1, "y"=>$y1);
	$geo_pts[1] = array("x"=>$x2, "y"=>$y1);
	$geo_pts[2] = array("x"=>$x2, "y"=>$y2);
	$geo_pts[3] = array("x"=>$x1, "y"=>$y2);
	$string = json_encode($geo_pts);
	$query = "INSERT INTO cornell_map_server_geometry(uid, building_id, floor_number, parent_uid, introduction, detail, geometry, geometry_type,navigation_region) 
	VALUES($i, 6, 1, NULL, 'georect', 'geodetail', '$string', 0, 0)";
	$result = $mysqli->query($query);
	if(!$result){
		echo $query;
	}
}


$polys = $array["polygon"];
for($i=0; $i<count($polys); $i++){
	$poly = $polys[$i];
	$attr = $poly["@attributes"];
	$points = $attr["points"];
	$ptsarray = explode(" ", trim($points));
	$geo_pts = array();
	for($j = 0; $j<count($ptsarray); $j++){
		$xyarray = explode(",", $ptsarray[$j]);
		$x = floatval($xyarray[0]);
		$y = floatval($xyarray[1]);
		$geo_pts[$j] = array("x"=>$x, "y"=>$y);
	}
	$string = json_encode($geo_pts);
	$uid = $i+count($rects);
	$query = "INSERT INTO cornell_map_server_geometry(uid, building_id, floor_number, parent_uid, introduction, detail, geometry, geometry_type,navigation_region) 
	VALUES($uid, 6, 1, NULL, 'geopoly', 'geodetail', '$string', 0, 0)";
	$result = $mysqli->query($query);
	if(!$result){
		echo $query;
	}
}

?>