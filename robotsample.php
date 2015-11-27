<?php
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
			//console.log("x1: "+x1+" y1: "+y1+" x2: "+x2+" y2: "+y2);
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