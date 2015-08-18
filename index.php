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
if(isset($_POST["choose"]) || isset($_POST["chs_connector"]) || isset($_POST["dbl_connector"]) || isset($_POST["robot_path"]) || isset($_POST["robot_sample"])){
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


			$getgeoquery2 = "SELECT * FROM cornell_map_server_room WHERE building_id = $sel_buildingid and floor_number = $floor2_number";
			$getgeoresult2 = $mysqli->query($getgeoquery2);
		}else{
			$dbl_con_error = "error";
		}
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
	<script src = "js/submit_con_ajax.js"></script>
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
						echo '<canvas id="mapcanvas4" width = "'.$img2_width.'" height = "'.$img2_height.'" style = "position: absolute; left: 50; top: '.$pos_top.'px; z-index = 1;"></canvas>';

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

					//var canvas = new fabric.Canvas('mapcanvas5');
					var canvas_reg = document.getElementById("mapcanvas5");
					var context = canvas_reg.getContext("2d");
					var canvas = new fabric.Canvas('mapcanvas3');
					var img_width = <?php echo $img_width; ?>;
					var img_height = <?php echo $img_height; ?>;

					var floor_width_meter = <?php echo $floor_width_meter; ?>;
					var floor_height_meter = <?php echo $floor_height_meter; ?>;

					colors = [	"rgba(255, 0, 0, 0.5)", 
					"rgba(255, 128, 0, 0.5)", 
					"rgba(255, 0, 128, 0.5)",
					"rgba(255, 255, 0, 0.5)", 
					"rgba(255, 0, 255, 0.5)", 
					"rgba(0, 255, 255, 0.5)", 
					"rgba(128, 0, 255, 0.5)", 
					"rgba(128, 128, 128, 0.5)"
					];

					</script>

					<?php
					$geometry_row = $getgeoresult->fetch_assoc();
					while($geometry_row){
						$get_geo_intro = $geometry_row["introduction"];
						$get_geo_pts_str = $geometry_row["geometry"];
						$get_geo_name = $geometry_row["introduction"];
						$get_geo_uid = $geometry_row["uid"];
						$get_geo_pts_json = json_encode($get_geo_pts_str);
						$get_geo_type = $geometry_row["geometry_type"];
						$get_geo_pts = json_decode($get_geo_pts_str);
						?>
						<script>
						var get_poly_pts = <?php echo $get_geo_pts_json; ?>;
						var get_geo_type = <?php echo $get_geo_type; ?>;
						var get_poly_pts = JSON.parse(get_poly_pts);
						var get_geo_name = "<?php echo $get_geo_name; ?>";
						var get_geo_uid = <?php echo $get_geo_uid; ?>;

						context.fillStyle = colors[get_geo_type];
						context.strokeStyle = colors[get_geo_type];

						context.beginPath();
						console.log(get_poly_pts);
						pt0 = get_poly_pts[0];
						pt0_x = pt0.x/floor_width_meter*img_width;
						pt0_y = (floor_height_meter - pt0.y)/floor_height_meter*img_height;
						context.moveTo(pt0_x, pt0_y);
						for(var i = 1; i<get_poly_pts.length; i++){
							pt = get_poly_pts[i];
							pt_x = pt.x/floor_width_meter*img_width;
							pt_y = (floor_height_meter - pt.y)/floor_height_meter*img_height;
							context.lineTo(pt_x, pt_y);
						}
						context.closePath();
						context.stroke();
						context.fill();

						var poly_center = getCentroid(get_poly_pts);
						context.font = "20px Comic Sans MS";
						context.fillStyle = "black";
						context.textAlign = "center";
						context.fillText(get_geo_name+"     "+get_geo_uid, poly_center.x/floor_width_meter*img_width, (floor_height_meter - poly_center.y)/floor_height_meter*img_height); 
						
						</script>
						<?php
						$geometry_row = $getgeoresult->fetch_assoc();
					}

					//////////////////////////////////////  add connectors ///////////////////////////////////////////

					if($connectors_json!=""){
						$num_con = count($connectors_json);
						for($i = 0; $i<$num_con; $i++){
							$entry = $connectors_json[$i];
							$rid1 = $entry["rid1"];
							$rid2 = $entry["rid2"];
							$x_1 = $entry["x1"];
							$y_1 = $entry["y1"];
							$x_2 = $entry["x2"];
							$y_2 = $entry["y2"];

							$floor1query = "SELECT * FROM cornell_map_server_geometry WHERE building_id = $sel_buildingid and uid = $rid1" ;
							$floor1result = $mysqli->query($floor1query);

							$floor1row = $floor1result->fetch_assoc();
							$floor_number_1 = $floor1row["floor_number"];

							$floor2query = "SELECT * FROM cornell_map_server_geometry WHERE building_id = $sel_buildingid and uid = $rid2";
							$floor2result = $mysqli->query($floor2query);
							$floor2row = $floor2result->fetch_assoc();
							$floor_number_2 = $floor2row["floor_number"];

							if($floor_number_1 == $floor_number_2 && $floor_number_1 == $floor_number){
								?>

								<script>
								var x_1 = <?php echo $x_1; ?>;
								x_1 = x_1/floor_width_meter*img_width;
								var y_1 = <?php echo $y_1; ?>;
								y_1 = (floor_height_meter-y_1)/floor_height_meter*img_height;
								var x_2 = <?php echo $x_2; ?>;
								x_2 = x_2/floor_width_meter*img_width;
								var y_2 = <?php echo $y_2; ?>;
								y_2 = (floor_height_meter-y_2)/floor_height_meter*img_height;

								context.moveTo(x_1,y_1);
								context.lineTo(x_2,y_2);
								context.strokeStyle = 'black';
								context.stroke();

								</script>

								<?php
							}else{

							}
						}	
					}

					////////////////////////////////////// if second image ////////////////////////////////////////////

					if($image_url2!=""){
						$geometry_row = $getgeoresult2->fetch_assoc();
						while($geometry_row){
							$get_geo_intro = $geometry_row["introduction"];
							$get_geo_pts_str = $geometry_row["geometry"];
							$get_geo_pts_json = json_encode($get_geo_pts_str);
							$get_geo_type = $geometry_row["geometry_type"];
							$get_geo_pts = json_decode($get_geo_pts_str);
							?>
							<script>
							var get_poly_pts = <?php echo $get_geo_pts_json; ?>;
							var get_geo_type = <?php echo $get_geo_type; ?>;
							var get_poly_pts = JSON.parse(get_poly_pts);

							context.fillStyle = colors[get_geo_type];

							context.beginPath();
							console.log(get_poly_pts);
							pt0 = get_poly_pts[0];
							pt0_x = pt0.x/floor_width_meter*img_width;
							pt0_y = (floor_height_meter - pt0.y)/floor_height_meter*img_height;
							context.moveTo(pt0_x, pt0_y);
							for(var i = 1; i<get_poly_pts.length; i++){
								pt = get_poly_pts[i];
								pt_x = pt.x/floor_width_meter*img_width;
								pt_y = (floor_height_meter - pt.y)/floor_height_meter*img_height;
								context.lineTo(pt_x, pt_y);
							}
							context.closePath();
							context.fill();
							</script>
							<?php
							$geometry_row = $getgeoresult2->fetch_assoc();
						}

					}

					?>


				</div>



				<?php

				if(isset($_POST["choose"])){

					?>

					<!-- ///////////////////////////////////// add room ///////////////////////////////////////// -->

					<script>

					var canvas = new fabric.Canvas('mapcanvas2');

					</script>
					<div id="form">
						<div class = "line">
							<div id="points">
							</div>
						</div>
						<div class="line">
							<div class="geoid">
								<label for="geoid">UID:</label>
								<input type="text" name="geoid" id="geoid" value="" />
							</div>
						</div>
						<div class = "line">
							<div class = "parent_id">
								<label for="parent_id">parent UID:</label>
								<input type = "text" name = "parent_id" id = "parent_id" value = "NULL" />
							</div>
						</div>
						<div class = "line">
							<div class="geointro">
								<label for="geointro">Introduction:</label>
								<input type="text" name="geointro" id="geointro" value=""/>
							</div>
						</div>
						<div class="line">
							<div class="geodetail">
								<label for="geodetail">Detail:</label>
								<input type="text" name="geodetail" id="geodetail" value = ""/>
							</div>
						</div>
						<div class="line">
							<input type="radio" name="geo_type" value = "0" checked>Room
							<input type="radio" name="geo_type" value = "1" >Public Area
							<input type="radio" name="geo_type" value = "2" >Staircase
							<input type="radio" name="geo_type" value = "3" >Escalator
							<input type="radio" name="geo_type" value = "4" >Elevator
							<input type="radio" name="geo_type" value = "5" >Goods Shelf
							<input type="radio" name="geo_type" value = "6" >Commodities
						</div>
						<div class = "line">
							<input type = "test" id = "poly_pts" size = "200" value = "" />
						</div>
						<div class="hidden">
							<input type = "hidden" id = "buildingid" value = "<?php echo $sel_buildingid; ?>" />
							<input type = "hidden" id = "floornumber" value = "<?php echo $floor_number; ?>" />			
						</div>

						<div class="line">
							<div class="submit">
								<input type="button" name="submit" id="submit" value = "submit">
							</div>
						</div>
					</div>

					<script>

					fabric.Object.prototype.set({
						transparentCorners: false,
						cornerColor: 'rgba(102,153,255,0.2)',
						cornerSize: 12,
						padding: 7
					});

					var mode = "add",
					currentShape;

					firstline = new fabric.Line(points, {
						strokeWidth: 1,
						fill: 'black',
						stroke: 'black',
						originX: 'center',
						originY: 'center'
					});
					firstline.strokeLineCap='round';
					firstline.hasControls=false;
					firstline.hasBorders=false;
					firstline.selectable=false;
					canvas.add(firstline);

					canvas.observe("mouse:move", function (event) {
						var pos = canvas.getPointer(event.e);
						if (mode === "edit" && currentShape) {
							var points = currentShape.get("points");
							points[points.length - 1].x = pos.x - currentShape.get("left");
							points[points.length - 1].y = pos.y - currentShape.get("top");
							currentShape.set({
								points: points
							});
							if(points.length == 2){
								firstline.set({ x2: pos.x, y2: pos.y });
							}
							canvas.renderAll();
						}
					});

					canvas.observe("mouse:down", function (event) {
						var pos = canvas.getPointer(event.e);

						if (mode === "add") {
							var polygon = new fabric.Polygon([{
								x: pos.x,
								y: pos.y
							}, {
								x: pos.x + 0.5,
								y: pos.y + 0.5
							}], {
								fill: 'blue',
								opacity: 0.5,
								selectable: false,
								originX: pos.x,
								originY: pos.y
							});
							currentShape = polygon;
							canvas.add(currentShape);
							firstline.set({ x1: pos.x, y1: pos.y });
							mode = "edit";
						} else if (mode === "edit" && currentShape && currentShape.type === "polygon") {
							var points = currentShape.get("points");
							points.push({
								x: pos.x - currentShape.get("left"),
								y: pos.y - currentShape.get("top")
							});
							currentShape.set({
								points: points
							});
							canvas.renderAll();
						}
					});

					fabric.util.addListener(window, 'keyup', function (e) {
						if (e.keyCode === 27) {
							if (mode === 'edit' || mode === 'add') {
								mode = 'normal';
								currentShape.set({
									selectable: false
								});
								currentShape._calcDimensions(false);
								var minX = 0,
								minY = 0,
								maxX = 0,
								maxY = 0;
								for (var i = 0; i < currentShape.points.length; i++) {
									minX = Math.min(minX, currentShape.points[i].x);
									minY = Math.min(minY, currentShape.points[i].y);
									maxX = Math.max(maxX, currentShape.points[i].x);
									maxY = Math.max(maxY, currentShape.points[i].y);
								}
								currentShape.setCoords();

								var points = currentShape.get("points");
								console.log(points);
								var polygonCenter = currentShape.getCenterPoint();

								translatedPoints = currentShape.get('points').map(function(p) {
									return { 
										x: polygonCenter.x + p.x, 
										y: img_height - (polygonCenter.y + p.y)
									};
								});

								for(var i = 0; i<translatedPoints.length; i++){
									translatedPoints[i].x = (translatedPoints[i].x/img_width*floor_width_meter).toFixed(3);
									translatedPoints[i].y = (translatedPoints[i].y/img_height*floor_height_meter).toFixed(3);

									$("#points").append(i+". x: "+translatedPoints[i].x+" y: "+translatedPoints[i].y+"<br>");
								}
								var ptsJson = JSON.stringify(translatedPoints);
								console.log(ptsJson);
								document.getElementById("poly_pts").value = ptsJson;

							} else {
								mode = 'add';
							}
							currentShape = null;
						}
					})

</script>

<?php
}

if(isset($_POST["chs_connector"])){
	?>

	<!-- ///////////////////////////////////////////// add connector ///////////////////////////////////////// -->

	<script>

	var canvas = new fabric.Canvas('mapcanvas2', { selection: false });

	var line, isMove=false, hasUp=false, first=false, end1, end2;

	canvas.on('mouse:down', function(o){
		var pointer = canvas.getPointer(o.e);
		if (line && !isMove){
			if (Math.abs(pointer.x-line.x1)<5 && Math.abs(pointer.y-line.y1)<5){
				first=true;
				isMove=true;
			}
			else if (Math.abs(pointer.x-line.x2)<5 && Math.abs(pointer.y-line.y2)<5){
				first=false;
				isMove=true;
			}
		}
		else if (line && isMove && hasUp){
			isMove=false;
			hasUp=false;
		}      
		else{
			isMove=true;
			var points = [ pointer.x, pointer.y, pointer.x, pointer.y ];
			line = new fabric.Line(points, {
				strokeWidth: 5,
				fill: 'red',
				stroke: 'red',
				originX: 'center',
				originY: 'center'
			});
			line.strokeLineCap='round';
			line.hasControls=false;
			line.hasBorders=false;
			line.selectable=false;
			end1 = new fabric.Circle({ radius: 5, fill: '#f55', top: line.y1, left: line.x1 });
			end1.originX='center';
			end1.originY='center';
			end1.hasControls=false;
			end1.hasBorders=false;
			end1.selectable=true;
			end1.hoverCursor='pointer';
			end2 = new fabric.Circle({ radius: 5, fill: '#f55', top: line.y2, left: line.x2 });
			end2.originX='center';
			end2.originY='center';
			end2.hasControls=false;
			end2.hasBorders=false;
			end2.selectable=true;
			end2.hoverCursor='pointer';
			canvas.add(end1);
			canvas.add(line);
			canvas.add(end2);
		}
		updateControls();
	});

canvas.on('mouse:move', function(o){ 
	if (!isMove) return; 
	var pointer = canvas.getPointer(o.e);
	if (!first){
		line.set({ x2: pointer.x, y2: pointer.y });
		end2.setTop(line.y2);
		end2.setLeft(line.x2);
	}
	else{
		line.set({ x1: pointer.x, y1: pointer.y });
		end1.setTop(line.y1);
		end1.setLeft(line.x1);
	}
	canvas.renderAll();
});

canvas.on('mouse:up', function(o){ 
	if (isMove) hasUp=true;
});


</script>

<form id="form">
	<div class="line">
		<div class="rid">
			<label for="rid1">room id 1:</label>
			<input type="text" name="rid1" id="rid1" value="<?php echo $rid1; ?>" required/>
		</div>
	</div>
	<div class="line">
		<div class="x1">
			<label for="x1">x1:</label>
			<input type="text" name="x1" id="x1" value="<?php echo $x1; ?>" required/>
		</div>
		<div class="y">
			<label for="y1">y1:</label>
			<input type="text" name="y1" id="y1" value="<?php echo $y1; ?>" required/>
		</div>
	</div>
	<div class="line">
		<div class="rid">
			<label for="rid2">room id 2:</label>
			<input type="text" name="rid2" id="rid2" value="<?php echo $rid2; ?>" required/>
		</div>
	</div>
	<div class="line">
		<div class="x2">
			<label for="x2">x2:</label>
			<input type="text" name="x2" id="x2" value="<?php echo $x2; ?>" required/>
		</div>
		<div class="y">
			<label for="y2">y2:</label>
			<input type="text" name="y2" id="y2" value="<?php echo $y2; ?>" required/>
		</div>
	</div>
	<div class="hidden">
		<input type="hidden" id="hidden_connectors" value = '<?php echo $all_connectors_str; ?>' />
		<input type="hidden" id="hidden_buildingid" value = '<?php echo $sel_buildingid; ?>' />
	</div>
	<div class="line">
		<div class="submit">
			<input type="button" name="submit_con" id="submit_con" value = "submit">
		</div>
	</div>
</form>	

<script>

var rid1 = document.getElementById("rid1");
var x1 = document.getElementById("x1");
var y1 = document.getElementById("y1");
var rid2 = document.getElementById("rid2");
var x2 = document.getElementById("x2");
var y2 = document.getElementById("y2");
var rid1 = document.getElementById("rid1");
var rid2 = document.getElementById("rid2");



function updateControls() {

	var x1_val = Number((line.x1/img_width*floor_width_meter).toFixed(2));
	x1.value = x1_val;

	var y1_val = Number(((canvas.height - line.y1)/img_height*floor_height_meter).toFixed(2));
	y1.value = y1_val;
	
	var x2_val = Number((line.x2*floor_width_meter/img_width).toFixed(2));
	x2.value = x2_val;

	var y2_val = Number(((canvas.height - line.y2)/img_height*floor_height_meter).toFixed(2));
	y2.value = y2_val;



	var request_rid1 = {building_id: building_id , floor_number: floor_number, x:x1_val, y:y1_val };

	var request_rid1 = $.ajax({
		url: 'php/rid_ajax.php',
		method: 'POST',
		data: request_rid1,
		dataType: 'text',
		error: function(error) {
			console.log(error);
		}
	});

	request_rid1.success(function(data) {
		rid1.value = data;
	});

	var request_rid2 = {building_id: building_id, floor_number: floor_number, x:x2_val, y:y2_val}

	var request_rid2 = $.ajax({
		url: 'php/rid_ajax.php',
		method: 'POST',
		data: request_rid2,
		dataType: 'text',
		error: function(error) {
			console.log(error);
		}
	});

	request_rid2.success(function(data) {
		rid2.value = data;
	});

}


</script>


<?php
}	
if(isset($_POST["dbl_connector"])){
	?>

	<!-- /////////////////////////////////////// add connector for ladders /////////////////////////////////// -->

	<form id="form">
		<div class="line">
			<div class="rid">
				<label for="rid1">room id 1:</label>
				<input type="text" name="rid1" id="rid1" value="<?php echo $rid1; ?>" required/>
			</div>
		</div>
		<div class="line">
			<div class="x1">
				<label for="x1">x1:</label>
				<input type="text" name="x1" id="x1" value="<?php echo $x1; ?>" required/>
			</div>
			<div class="y">
				<label for="y1">y1:</label>
				<input type="text" name="y1" id="y1" value="<?php echo $y1; ?>" required/>
			</div>
		</div>
		<div class="line">
			<div class="rid">
				<label for="rid2">room id 2:</label>
				<input type="text" name="rid2" id="rid2" value="<?php echo $rid2; ?>" required/>
			</div>
		</div>
		<div class="line">
			<div class="x2">
				<label for="x2">x2:</label>
				<input type="text" name="x2" id="x2" value="<?php echo $x2; ?>" required/>
			</div>
			<div class="y">
				<label for="y2">y2:</label>
				<input type="text" name="y2" id="y2" value="<?php echo $y2; ?>" required/>
			</div>
		</div>
		<div class="hidden">
			<input type="hidden" id="hidden_connectors" value = '<?php echo $all_connectors_str; ?>' />
			<input type="hidden" id="hidden_buildingid" value = '<?php echo $sel_buildingid; ?>' />
		</div>
		<div class="line">
			<div class="submit">
				<input type="button" name="submit_con" id="submit_con_dbl" value = "submit">
			</div>
		</div>
	</form>	

	<script>

	var rid1 = document.getElementById("rid1");
	var x1 = document.getElementById("x1");
	var y1 = document.getElementById("y1");
	var rid2 = document.getElementById("rid2");
	var x2 = document.getElementById("x2");
	var y2 = document.getElementById("y2");
	var rid1 = document.getElementById("rid1");
	var rid2 = document.getElementById("rid2");

	var canvas = new fabric.Canvas('mapcanvas2', { selection: false });
	var canvas2 = new fabric.Canvas('mapcanvas4', { selection: false });

	canvas.on('mouse:down', function(o){
		canvas.clear();
		var pointer = canvas.getPointer(o.e);
		//var y = pointer.y;
		//var x = pointer.x;
		canvas.add(new fabric.Circle({ radius: 5, fill: 'black', top: pointer.y, left: pointer.x }));
		canvas.item(0).hasControls = false;
		canvas.setActiveObject(canvas.item(0));

		var x1_val = Number((pointer.x/img_width*floor_width_meter).toFixed(2));
		x1.value = x1_val;
		var y1_val = Number(((img_height-pointer.y)/img_height*floor_height_meter).toFixed(2));
		y1.value = y1_val;

		var request_rid1 = {building_id: building_id , floor_number: floor_number, x:x1_val, y:y1_val };

		var request_rid1 = $.ajax({
			url: 'php/rid_ajax.php',
			method: 'POST',
			data: request_rid1,
			dataType: 'text',
			error: function(error) {
				console.log(error);
			}
		});

		request_rid1.success(function(data) {
			rid1.value = data;
		});

	});

	var floor2_number = <?php echo $floor2_number; ?>;
	var img2_width = <?php echo $img2_width; ?>;
	var img2_height = <?php echo $img2_height; ?>;

	canvas2.on('mouse:down', function(o){
		canvas2.clear();
		var pointer = canvas2.getPointer(o.e);
		//var y = pointer.y;
		//var x = pointer.x;
		canvas2.add(new fabric.Circle({ radius: 5, fill: 'black', top: pointer.y, left: pointer.x }));
		canvas2.item(0).hasControls = false;
		canvas2.setActiveObject(canvas.item(0));

		var x2_val = Number((pointer.x/img2_width*floor2_width_meter).toFixed(2));
		x2.value = x2_val;
		var y2_val = Number(((img2_height-pointer.y)/img2_height*floor2_height_meter).toFixed(2));
		y2.value = y2_val;

		var request_rid2 = {building_id: building_id, floor_number: floor2_number, x:x2_val, y:y2_val}

		var request_rid2 = $.ajax({
			url: 'php/rid_ajax.php',
			method: 'POST',
			data: request_rid2,
			dataType: 'text',
			error: function(error) {
				console.log(error);
			}
		});

		request_rid2.success(function(data) {
			rid2.value = data;
		});

	});

	</script>

	<?php
}


if(isset($_POST["robot_path"])){

	// get recorded robot path on this floor



	
	?>
	<div id="polyline">polylines:<input type="button" name="submit_robot" id="submit_robot" value = "submit"><br></div>
	<input type = "hidden" id = "buildingid" value = "<?php echo $sel_buildingid; ?>" />
	<input type = "hidden" id = "floornumber" value = "<?php echo $floor_number; ?>" />
	<input type = "hidden" id = "img_width" value = "<?php echo $img_width; ?>" />
	<input type = "hidden" id = "img_height" value = "<?php echo $img_height; ?>" />
	<input type = "hidden" id = "floor_width" value = "<?php echo $floor_width_meter; ?>" />
	<input type = "hidden" id = "floor_height" value = "<?php echo $floor_height_meter; ?>" />

	<input type = "hidden" id = "num_lines" value = "">

	<script>

	var polyline_html = document.getElementById("polyline");
	
	var canvas = new fabric.Canvas('mapcanvas2', { selection: false });

	var step = 1.5;
	var horizontal_num = Math.floor(floor_height_meter/step); 
	var vertical_num = Math.floor(floor_width_meter/step);

	//alert(horizontal_num);
	//alert(vertical_num);

	for(var i = 1; i<horizontal_num+1; i++){
		var hor_line_y = img_height-step*i*img_height/floor_height_meter;
		//console.log(i+" "+hor_line_y);
		var points = [0,hor_line_y,img_width, hor_line_y];
		var hor_line = new fabric.Line(points, {
			strokeWidth: 1,
			fill: 'blue',
			stroke: 'blue',
			originX: 'center',
			originY: 'center',
			opacity: 0.3
		});
		hor_line.strokeLineCap='round';
		hor_line.hasControls=false;
		hor_line.hasBorders=false;
		hor_line.selectable=false;
		canvas.add(hor_line);
		canvas.renderAll();
	}

	for(var i = 1; i<vertical_num+1; i++){
		var ver_line_x = step*i*img_width/floor_width_meter;
		var points = [ver_line_x, 0, ver_line_x, img_height];
		var ver_line = new fabric.Line(points, {
			strokeWidth: 1,
			fill: 'blue',
			stroke: 'blue',
			originX: 'center',
			originY: 'center',
			opacity: 0.3
		});
		ver_line.strokeLineCap='round';
		ver_line.hasControls=false;
		ver_line.hasBorders=false;
		ver_line.selectable=false;
		canvas.add(ver_line);
		canvas.renderAll();
	}
	

	// first means the first point in the polyline
	var line, isMove=false, hasUp=false, first=true, end1, end2;
	var lastTime;
	var count = -1;
	var lastX;
	var lastY;

	canvas.on('mouse:down', function(o){
		var pointer = canvas.getPointer(o.e);
		var date = new Date();
		var now = date.getTime();
		if(now - lastTime < 500){
			hasUp = true;
			document.getElementById("num_lines").value = count;
		}
		lastTime = now;

		
		if (line && !isMove){
			if (Math.abs(pointer.x-line.x1)<5 && Math.abs(pointer.y-line.y1)<5){
				isMove=true;
			}
		}
		else if (line && isMove && hasUp){
			isMove=false;
		}      
		else{

			isMove=true;
			var points = [ pointer.x, pointer.y, pointer.x, pointer.y ];
			line = new fabric.Line(points, {
				strokeWidth: 1,
				fill: 'black',
				stroke: 'black',
				originX: 'center',
				originY: 'center'
			});
			line.strokeLineCap='round';
			line.hasControls=false;
			line.hasBorders=false;
			line.selectable=false;
			end1 = new fabric.Circle({ radius: 3, fill: '#fff', top: line.y1, left: line.x1 });
			end1.originX='center';
			end1.originY='center';
			end1.hasControls=false;
			end1.hasBorders=false;
			end1.selectable=true;
			end1.hoverCursor='pointer';
			end2 = new fabric.Circle({ radius: 3, fill: '#fff', top: line.y2, left: line.x2 });
			end2.originX='center';
			end2.originY='center';
			end2.hasControls=false;
			end2.hasBorders=false;
			end2.selectable=true;
			end2.hoverCursor='pointer';
			canvas.add(end1);
			canvas.add(line);
			canvas.add(end2);

			count++;

			if(!first){
				$("#polyline").append("<div id = 'div"+count+"'>");
				$("#polyline").append(count+" x:"+pointer.x+"	y:"+pointer.y+"<br>");
				$("#polyline").append("<label>Divide into parts: </label>");
				$("#polyline").append("<input class='parts' type='number' name='number"+count+"' id='number"+count+"' value = '1'/><br>");
				$("#polyline").append('<input type="radio" name="include_head'+count+'" value = "true" checked>Include head');
				$("#polyline").append('<input type="radio" name="include_head'+count+'" value = "false" >Not include head<br>');
				$("#polyline").append('<input type="radio" name="include_tail'+count+'" value = "true" checked>Include tail');
				$("#polyline").append('<input type="radio" name="include_tail'+count+'" value = "false" >Not include tail<br>');
				$("#polyline").append("<input type='hidden' name='x1"+count+"' id='x1"+count+"' value = '"+lastX+"'/>");
				$("#polyline").append("<input type='hidden' name='y1"+count+"' id='y1"+count+"' value = '"+lastY+"'/>");
				$("#polyline").append("<input type='hidden' name='x2"+count+"' id='x2"+count+"' value = '"+pointer.x+"'/>");
				$("#polyline").append("<input type='hidden' name='y2"+count+"' id='y2"+count+"' value = '"+pointer.y+"'/>");
				$("#polyline").append('<input type="button" name="divide" class="divide" id="divide'+count+'" value = "divide">');
				$("#polyline").append("</div><br><br>")
			}
			lastX = pointer.x;
			lastY = pointer.y;


		}
		first=false;	
	});

canvas.on('mouse:move', function(o){ 
	if (!isMove) return; 
	var pointer = canvas.getPointer(o.e);
	line.set({ x2: pointer.x, y2: pointer.y });
	end2.setTop(line.y2);
	end2.setLeft(line.x2);


	canvas.renderAll();
});


$(document).on("click", ".divide", function() {
	var submit_id = this.id;
	var count = submit_id.substring(6,submit_id.length);
	input_id = "number"+count;
	var divide_num = document.getElementById(input_id).value;
	x1_id = "x1"+count;
	y1_id = "y1"+count;
	var x1_val = parseFloat(document.getElementById(x1_id).value);
	var y1_val = parseFloat(document.getElementById(y1_id).value);
	x2_id = "x2"+count;
	y2_id = "y2"+count;
	var x2_val = parseFloat(document.getElementById(x2_id).value);
	var y2_val = parseFloat(document.getElementById(y2_id).value);
	var head_id = "include_head"+count;
	var include_head = $('input[name='+head_id+']:checked').val();
	var tail_id = "include_tail"+count;
	var include_tail = $('input[name='+tail_id+']:checked').val();
	//console.log("x1y1 "+x1_val+" "+y1_val);
	//console.log("x2y2 "+x2_val+" "+y2_val);
	//include head but not tail
	divide_num = parseInt(divide_num);
	//console.log(divide_num);
	if(include_head === "true" && include_tail === "false"){
		//console.log("include head not include tail");
		for(var i = 0; i<divide_num; i++){
			x = x1_val+(x2_val-x1_val)*i/divide_num;
			y = y1_val+(y2_val-y1_val)*i/divide_num;
			//console.log(i+" "+x+" "+y);
			var circle = new fabric.Circle({ radius: 2, fill: '#5ff',  top: y, left: x, opacity:0.8});
			circle.originX='center';
			circle.originY='center';
			circle.hasControls=false;
			circle.hasBorders=false;
			circle.selectable=true;
			canvas.add(circle);
			
		}
	}
	if(include_head === "false" && include_tail === "true"){
		//console.log("not include head include tail");
		for(var i = 0; i<divide_num; i++){
			x = x1_val+(x2_val-x1_val)*(i+1)/divide_num;
			y = y1_val+(y2_val-y1_val)*(i+1)/divide_num;
			//console.log(i+" "+x+" "+y);	
			var circle = new fabric.Circle({ radius: 2, fill: '#5ff',  top: y, left: x, opacity:0.8});
			circle.originX='center';
			circle.originY='center';
			circle.hasControls=false;
			circle.hasBorders=false;
			circle.selectable=true;
			canvas.add(circle);	
		}
	}
	if(include_head === "true" && include_tail === "true"){
		//console.log("include head include tail");
		for(var i = 0; i<divide_num+1; i++){
			x = x1_val+(x2_val-x1_val)*i/divide_num;
			y = y1_val+(y2_val-y1_val)*i/divide_num;
			//console.log(i+" "+x+" "+y);
			var circle = new fabric.Circle({ radius: 2, fill: '#5ff',  top: y, left: x, opacity:0.8});
			circle.originX='center';
			circle.originY='center';
			circle.hasControls=false;
			circle.hasBorders=false;
			circle.selectable=true;
			canvas.add(circle);
		}
	}
	if(include_head === "false" && include_tail === "false"){
		//console.log("not include head not include tail");
		for(var i = 0; i<divide_num-1; i++){
			x = x1_val+(x2_val-x1_val)*(i+1)/divide_num;
			y = y1_val+(y2_val-y1_val)*(i+1)/divide_num;
			//console.log(i+" "+x+" "+y);	
			var circle = new fabric.Circle({ radius: 2, fill: '#5ff',  top: y, left: x, opacity:0.8});
			circle.originX='center';
			circle.originY='center';
			circle.hasControls=false;
			circle.hasBorders=false;
			circle.selectable=true;
			canvas.add(circle);	
		}
	}

	
	canvas.renderAll();
});






</script>


<?php
}
if(isset($_POST["robot_sample"])){
	?>
	<script>
	var canvas = new fabric.Canvas('mapcanvas2', { selection: false });
	</script>

	<?php

	$robot_query = "SELECT * FROM cornell_map_server_robotpath WHERE building_id = $sel_buildingid AND floor_number = $floor_number";
	$robot_result = $mysqli->query($robot_query);
	$robot_row = $robot_result->fetch_assoc();
	while($robot_row){
		$path_str = $robot_row["path_structure"];
		$path_json = json_decode($path_str);
		//echo count($path_json);
		//print_r($path_json);
		for($j = 0; $j<count($path_json)-1; $j++){
			$point = $path_json[$j];
			$point2 = $path_json[$j+1];
			$x1 = $point->x;
			$y1 = $point->y;
			$x2 = $point2->x;
			$y2 = $point2->y;
			$x1 = $x1/$floor_width_meter*$img_width;
			$y1 = ($floor_height_meter-$y1)/$floor_height_meter*$img_height;
			$x2 = $x2/$floor_width_meter*$img_width;
			$y2 = ($floor_height_meter-$y2)/$floor_height_meter*$img_height;
			?>
			<script>
			var x1 = <?php echo $x1; ?>;
			var x2 = <?php echo $x2; ?>;
			var y1 = <?php echo $y1; ?>;
			var y2 = <?php echo $y2; ?>;
			console.log("x1: "+x1+" y1: "+y1+" x2: "+x2+" y2: "+y2);
			var points = [x1,y1,x2,y2];
			var line = new fabric.Line(points, {
				strokeWidth: 1,
				fill: 'orange',
				stroke: 'orange',
				originX: 'center',
				originY: 'center'
			});
			canvas.add(line);
			canvas.renderAll();
			</script>
			<?php
		}
		
		$robot_row = $robot_result->fetch_assoc();
	}


	$robotsample_query = "SELECT * FROM cornell_map_server_robotwifisample where building_id = $sel_buildingid and floor_number = $floor_number";
	$robotsample_result = $mysqli->query($robotsample_query);
	$robotsample_row = $robotsample_result->fetch_assoc();
	while($robotsample_row){
		$robot_x = $robotsample_row["x"];
		$robot_y = $robotsample_row["y"];
		$coord_x = $robot_x/$floor_width_meter*$img_width;
		$coord_y = ($floor_height_meter-$robot_y)/$floor_height_meter*$img_height;
		?>
		<script>
		var x = <?php echo $coord_x; ?>;
		var y = <?php echo $coord_y; ?>;
		//console.log("x: "+x+" y: "+y);
		var circle = new fabric.Circle({ radius: 2, fill: '#0000ff',  top: y, left: x, opacity:0.8});
		circle.originX='center';
		circle.originY='center';
		circle.hasControls=false;
		circle.hasBorders=false;
		circle.selectable=true;
		canvas.add(circle);
		canvas.renderAll();	
		</script>
		<?php
		$robotsample_row = $robotsample_result->fetch_assoc();
	}


}

?>

</div>

<?php
}
?>

</div>
</body>
</html>
