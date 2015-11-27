<?php
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
?>