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
							<input type = "test" id = "poly_pts" size = "100" value = "" />
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
								//console.log(points);
								var polygonCenter = currentShape.getCenterPoint();

								translatedPoints = currentShape.get('points').map(function(p) {
									return { 
										x: polygonCenter.x + p.x, 
										y: img_height - (polygonCenter.y + p.y)
									};
								});

								for(var i = 0; i<translatedPoints.length; i++){
									translatedPoints[i].x = parseFloat((translatedPoints[i].x/img_width*floor_width_meter).toFixed(3));
									translatedPoints[i].y = parseFloat((translatedPoints[i].y/img_height*floor_height_meter).toFixed(3));

									$("#points").append(i+". x: "+translatedPoints[i].x+" y: "+translatedPoints[i].y+"<br>");
								}
								var ptsJson = JSON.stringify(translatedPoints);

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
?>