<?php
session_start();

require("php/function.php");

$x = "";
$y = "";
$width = "";
$height = "";
$roomid = "";
$roomname = "";
$roomdes = "";

$rid1 = "";
$rid2 = "";
$x1 = "";
$x2 = "";
$y1 = "";
$y2 = "";

$sel_buildingid="";
$sel_floorid = "";

$floor_number = "";


$image_url = "";
$image_url2 = "";

$dbl_con_error = "";

$all_connectors_str = "";

require_once 'php/config.php';
$mysqli= new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

if ($mysqli->connect_error) {
	echo "Connection failed: " . $mysqli->connect_error;
} 

$buildingquery = "SELECT * FROM cornell_map_server_building";
$buildingresult = $mysqli->query($buildingquery);
//echo $buildingresult->num_rows;
if(isset($_POST["choose"]) || isset($_POST["chs_connector"]) || isset($_POST["dbl_connector"]) 
	|| isset($_POST["robot_path"]) || isset($_POST["robot_sample"]) || isset($_POST["add_vertices"]) 
	|| isset($_POST["nav_region"]) || isset($_POST["fingerprint"]) || isset($_POST["fingerprint_sparse"])){
	$sel_buildingid = $_POST["building"];
	//echo $sel_buildingid;
	$sel_floorid = $_POST["floor"];
	//echo ":".$sel_floorid;

	$getfloorquery = "SELECT * FROM cornell_map_server_floor WHERE building_id = $sel_buildingid and id = $sel_floorid";
	//echo $getfloorquery;
	$getfloorresult = $mysqli->query($getfloorquery);
	$getfloorrow = $getfloorresult->fetch_assoc();
	$image_url = $getfloorrow["img_url"];
	$floor_number = $getfloorrow["floor_number"];
	$floor_width_meter = $getfloorrow["width"];
	$floor_height_meter = $getfloorrow["height"];


	// get all the recorded rooms on this floor
	//$getroomquery = "SELECT * FROM cornell_map_server_room WHERE building_id = $sel_buildingid and floor_number = $floor_number";
	//$getroomresult = $mysqli->query($getroomquery);
	$getgeoquery = "SELECT * FROM cornell_map_server_geometry WHERE building_id = $sel_buildingid and floor_number = $floor_number";
	$getgeoresult = $mysqli->query($getgeoquery);
	// get all the recorded connectors in this building

	$getconnectorquery = "SELECT * FROM cornell_map_server_roomconnector WHERE building_id = $sel_buildingid";
	$getcon_result = $mysqli->query($getconnectorquery);

	$connectors_json = "";
	if($getcon_result->num_rows!=0){
		$building_connector = $getcon_result->fetch_assoc();
		$all_connectors_str = $building_connector["connector_structure"];
		$connectors_json = json_decode($all_connectors_str, true);
		//echo count($connectors_json);

	}

	if(isset($_POST["dbl_connector"])){
		$floor2_number = $floor_number+1;
		$getfloor2query = "SELECT * FROM cornell_map_server_floor WHERE building_id = $sel_buildingid and floor_number = $floor2_number";
		$floor2result = $mysqli->query($getfloor2query);
		if($floor2result->num_rows!=0){
			$floor2row = $floor2result->fetch_assoc();
			$image_url2 = $floor2row["img_url"];
			$floor2_width_meter = $floor2row["width"];
			$floor2_height_meter = $floor2row["height"];


			$getgeoquery2 = "SELECT * FROM cornell_map_server_geometry WHERE building_id = $sel_buildingid and floor_number = $floor2_number";
			$getgeoresult2 = $mysqli->query($getgeoquery2);
		}else{
			$dbl_con_error = "error";
		}
	}

	$nav = 0;
	if(isset($_POST["nav_region"])){
		$nav = 1;
	}
	
}


?>


<!-- ///////////////////////////////////////////// html ///////////////////////////////////////////////////////// -->

<!DOCTYPE html>
<html>
<head>
	<title>index</title>
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="lib/fabric.min.js"></script>
	<script src="lib/fabric.js"></script>
	<script src="lib/fabric.require.js"></script>
	<script src="js/menu_ajax.js"></script>
	<script src="js/submit_ajax.js"></script>
	<script src ="js/submit_con_ajax.js"></script>
	<script src="js/submit_dbl_ajax.js"></script>
	<script src="js/submit_robot_ajax.js"></script>
	<script src="js/function.js"></script>
</head>
<body>
	<div class="indexcontent">

		<script>

		var building_id = "<?php  echo $sel_buildingid; ?>";
		var floor_number = <?php  echo $floor_number; ?>;

		</script>
		<!-- menu to choose floor -->
		<div id="menu">
			<form id="menuform" action = "index.php" method = "post">
				<!-- drop down list -->
				<div class="menuitem" id="building">
					<label for = "building_list">Building:</label>
					<select id="building_list" name="building">
						<option class='select_building' value = '-1'>&nbsp;</option>
						<?php
						$buildingrow = $buildingresult->fetch_assoc();
						while($buildingrow!=false){
							$buildingid = $buildingrow["id"];
							echo "<option class='select_building' value = '$buildingid'";
							if($sel_buildingid==$buildingid){
								echo " selected";
							}

							echo ">$buildingid</option>";
							$buildingrow = $buildingresult->fetch_assoc();
						}

						?>
						
					</select>
				</div>
				<input name ="hidden_sel_floorid" id="hidden_sel_floorid" type = "hidden" value = "<?php echo $sel_floorid; ?>">
				<div class="menuitem" id="floor">
					<label for = "floor_list">Floor: </label>
					<select id="floor_list" name="floor">
						<?php
						if($sel_floorid!=""){
							echo "<option value = '$sel_floorid' selected>$sel_floorid</option>";
						}
						?>
					</select>
				</div>
				<div class="menuitem" id="choose">
					<input name="choose" type="submit" value="add geometry">
				</div>
				<div class="menuitem" id="chs_connector">
					<input name="chs_connector" type="submit" value="add connector">
				</div>
				<div class="menuitem" id="dbl_connector">
					<input name="dbl_connector" type="submit" value="add connector for ladders">
				</div>
				<div class="menuitem" id="robot_path">
					<input name="robot_path" type="submit" value="add robot path">
				</div>
				<div class="menuitem" id="robot_sample">
					<input name="robot_sample" type="submit" value="see robot sample">
				</div>
				<!--<div class="menuitem" id="add_vertices">
					<input name="add_vertices" type="submit" value="add vertices">
				</div>-->
				<!--<div class="menuitem" id="nav_region">
					<input name="nav_region" type="submit" value="navigation region">
				</div>-->
				<div class="menuitem" id="fingerprint">
					<input name="fingerprint" type="submit" value="fingerprint">
				</div> 
				<div class="menuitem" id="fingerprint_sparse">
					<input name="fingerprint_sparse" type="submit" value="fingerprint sparse">
				</div> 
			</form>

			<div class="information">
				<?php 
				if(isset($_POST['building'])){
					echo "building id: ".$sel_buildingid;
					echo "  &nbsp; &nbsp; &nbsp;  floor id: ".$sel_floorid;
					
					if($dbl_con_error!=""){
						echo " &nbsp; &nbsp; &nbsp; connector error: This is the top floor of the building.";
					}
				}
				?>
			</div>

		</div>


		<?php 

		if($image_url!=""){

			
			?>

			<!-- //////////////////////////////////////// main content ///////////////////////////////////////////////// -->

			<div id="display">

				<?php
				

				list($img_width, $img_height) = @getimgsize($image_url, 'https://s3-us-west-2.amazonaws.com');
				if($img_width == NULL || $img_width !="" || strlen($img_width)==0){
					//echo "null";
					list($img_width, $img_height, $type, $attr) = getimagesize($image_url);

				}

				$img2_width = 0;
				$img2_height = 0;

				if($image_url2!=""){
					list($img2_width, $img2_height) = @getimgsize($image_url2, 'https://s3-us-west-2.amazonaws.com');
				}

				$map_width = 0;
				$map_height = 0;

				if($img_width>$img2_width){
					$map_width = $img_width+50;
				}else{
					$map_width = $img2_width+50;
				}
				$map_height = $img_height+$img2_height+100;

				?>

				<div id="map" style = "width:<?php echo $map_width; ?>px ; height: <?php echo $map_height; ?>px; " >
					<?php



					echo '<canvas id="mapcanvas1" width = "'.$img_width.'" height = "'.$img_height.'" style = "position: absolute; left: 50; top: 0px; z-index = 0; background-image: url('.$image_url.');" ></canvas>';
					echo '<canvas id="mapcanvas5" width = "'.$img_width.'" height = "'.$img_height.'" style = "position: absolute; left: 50; top: 0px; z-index = 1;" ></canvas>';
					
					echo '<canvas id="mapcanvas2" width = "'.$img_width.'" height = "'.$img_height.'" style = "position: absolute; left: 50; top: 0px; z-index = 2;" ></canvas>';
					
					if($image_url2!=""){

						$pos_top = $img_height + 0;

						echo '<canvas id="mapcanvas3" width = "'.$img2_width.'" height = "'.$img2_height.'" style = "position: absolute; left: 50; top: '.$pos_top.'px; z-index = 0; background-image: url('.$image_url2.');" ></canvas>';
						echo '<canvas id="mapcanvas6" width = "'.$img2_width.'" height = "'.$img2_height.'" style = "position: absolute; left: 50; top: '.$pos_top.'px; z-index = 1;"></canvas>';						
						echo '<canvas id="mapcanvas4" width = "'.$img2_width.'" height = "'.$img2_height.'" style = "position: absolute; left: 50; top: '.$pos_top.'px; z-index = 2;"></canvas>';

						?>

						<script>
						var canvas2 = document.getElementById("mapcanvas6");
						var context2 = canvas2.getContext("2d");

						var img2_width = <?php echo $img2_width; ?>;
						var img2_height = <?php echo $img2_height; ?>;

						var floor2_width_meter = <?php echo $floor2_width_meter; ?>;
						var floor2_height_meter = <?php echo $floor2_height_meter; ?>;

						</script>


						<?php
					}

					?>

					<script>

					var canvas_reg = document.getElementById("mapcanvas5");
					var context = canvas_reg.getContext("2d");
					//var canvas = new fabric.Canvas('mapcanvas3');
					var img_width = <?php echo $img_width; ?>;
					var img_height = <?php echo $img_height; ?>;

					var floor_width_meter = <?php echo $floor_width_meter; ?>;
					var floor_height_meter = <?php echo $floor_height_meter; ?>;

					var nav = <?php echo $nav; ?>;

					colors = [	
					"rgba(255, 0, 0, 0.5)", 
					"rgba(0, 255, 0, 0.5)",
					"rgba(0, 0, 255, 0.5)",
					"rgba(255, 128, 0, 0.5)", 
					"rgba(0, 255, 128, 0.5)", 
					"rgba(128, 0, 255, 0.5)",
					"rgba(255, 255, 0, 0.5)", 
					"rgba(255, 0, 255, 0.5)", 
					"rgba(0, 255, 255, 0.5)"
					];
					default_color = "rgba(128, 128, 128, 0.5)";
					</script>

					<?php
					if (!isset($_POST["fingerprint"]) && !isset($_POST["fingerprint_sparse"])){
						include "draw_geo.php";
						include "draw_con.php";
						include "second_img.php";
					}
					
					?>
				</div>
				<?php
					include "geometry.php";
					include "connector.php";	
					include "dbl_connector.php";
					include "add_robot_path.php";
					include "robotsample.php";
					include "nav_region.php";
					include 'fingerprint.php';
					include 'fingerprint_sparse.php'
				?>

</div>

<?php
}
?>

</div>
</body>
</html>
