<?php
if(isset($_POST["robot_path"])){	
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

	for(var i = 1; i<horizontal_num+1; i++){
		var hor_line_y = img_height-step*i*img_height/floor_height_meter;
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
	//include head but not tail
	divide_num = parseInt(divide_num);
	if(include_head === "true" && include_tail === "false"){
		//console.log("include head not include tail");
		for(var i = 0; i<divide_num; i++){
			x = x1_val+(x2_val-x1_val)*i/divide_num;
			y = y1_val+(y2_val-y1_val)*i/divide_num;
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
		for(var i = 0; i<divide_num; i++){
			x = x1_val+(x2_val-x1_val)*(i+1)/divide_num;
			y = y1_val+(y2_val-y1_val)*(i+1)/divide_num;	
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
		for(var i = 0; i<divide_num-1; i++){
			x = x1_val+(x2_val-x1_val)*(i+1)/divide_num;
			y = y1_val+(y2_val-y1_val)*(i+1)/divide_num;	
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
?>