<?php
if(isset($_POST["dbl_connector"])){
	?>
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
		<div class="line">
			<input type="radio" name="dbl_con_dir" value = "1" >One way connector
			<input type="radio" name="dbl_con_dir" value = "2" checked>Two way connector
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
		canvas.add(new fabric.Circle({ radius: 3, fill: 'black', top: pointer.y, left: pointer.x }));
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
		canvas2.add(new fabric.Circle({ radius: 3, fill: 'black', top: pointer.y, left: pointer.x }));
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

?>