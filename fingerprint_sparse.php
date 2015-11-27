<?php
if(isset($_POST["fingerprint_sparse"])){
	?>
	<script>
	var canvas = new fabric.Canvas('mapcanvas2', { selection: false });
	</script>
	<?php
	$fingerprint_query = "SELECT * FROM cornell_map_server_sparsecellfingerprint where building_id = $sel_buildingid and floor_number = $floor_number";
	$fingerprint_result = $mysqli->query($fingerprint_query);
	$fingerprint_row = $fingerprint_result->fetch_assoc();
	while($fingerprint_row){
		$x_idx = $fingerprint_row["x_index"];
		$y_idx = $fingerprint_row["y_index"];
		$x_meter = 15 * $x_idx;
		$y_meter = 15 * $y_idx;
		$prints_str = $fingerprint_row["fingerprint"];
		if(strlen($prints_str)>2720){
			$prints_json = json_decode($prints_str);
			$trans = meter_to_coord($x_meter, $y_meter, $floor_width_meter, $floor_height_meter, $img_width, $img_height);
			$width = 15/$floor_width_meter*$img_width;
			$height = 15/$floor_height_meter*$img_height;
			$x_coord = $trans[0];
			$y_coord = $trans[1]-$height;
			
			?>
			<script>
				var x_idx = "<?php echo $x_idx; ?>";
				var y_idx = "<?php echo $y_idx; ?>";
				var x = <?php echo $x_coord; ?>;
				var y = <?php echo $y_coord; ?>;
				var width = <?php echo $width; ?>;
				var height = <?php echo $height; ?>;
				var rect = new fabric.Rect({top:y, 
											left: x, 
											width: width, 
											height: height, 
											fill: '#ffffff',
											stroke: '#ff0000',
											strokeWidth:1,
											opacity:0.7});
				rect.hasControls = false;
				rect.selectable = false;
				var text = new fabric.Text(x_idx+" "+y_idx,{top: y,
											left: x,
											fontSize: 20})
				canvas.add(rect);
				canvas.add(text);
				canvas.renderAll();
			</script>
			<?php
			for($i = 0; $i<10; $i+=2){
			for($j=0;$j<10;$j+=2){
				if(strlen(json_encode($prints_json[$i][$j]))>25){
					$x = $i*1.5 + $x_meter+0.75;
					$y = $j*1.5 + $y_meter+0.75;
					$trans = meter_to_coord($x,$y,$floor_width_meter,$floor_height_meter,$img_width,$img_height);
					$print_x_coord = $trans[0];
					$print_y_coord = $trans[1];
					
					?>
					<script>
					var x_coord = <?php echo $print_x_coord; ?>;
					var y_coord = <?php echo $print_y_coord; ?>;
					var circle = new fabric.Circle({ radius: 2, fill: '#0000ff',  top: y_coord, left: x_coord, opacity:1});
					circle.originX='center';
					circle.originY='center';
					circle.hasControls=false;
					circle.hasBorders=false;
					circle.selectable=true;
					canvas.add(circle);
					canvas.renderAll();	
					</script>
					<?php
				}			
			}
		}
		}	
		$fingerprint_row = $fingerprint_result->fetch_assoc();
	}
}
?>