<?php
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
					?>