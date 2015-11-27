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
						$get_geo_nav = $geometry_row["navigation_region"];
						?>
						<script>
						var get_poly_pts = <?php echo $get_geo_pts_json; ?>;
						var get_geo_type = <?php echo $get_geo_type; ?>;
						var get_poly_pts = JSON.parse(get_poly_pts);
						var get_geo_name = "<?php echo $get_geo_name; ?>";
						var get_geo_uid = <?php echo $get_geo_uid; ?>;
						var get_geo_nav = <?php echo $get_geo_nav; ?>;

						context.fillStyle = colors[get_geo_type];
						context.strokeStyle = colors[get_geo_type];
						if(get_geo_type>7){
							context.fillStyle = default_color;
							context.strokeStyle = default_color;
						}
						if(nav == 1){
							context.fillStyle = colors[get_geo_nav];
							context.strokeStyle = colors[get_geo_nav];
						}

						context.beginPath();
						//console.log(get_poly_pts);
						pt0 = get_poly_pts[0];
						pt0_x = pt0.x/floor_width_meter*img_width;
						pt0_y = (floor_height_meter - pt0.y)/floor_height_meter*img_height;
						context.moveTo(pt0_x, pt0_y);
						for(var i = 1; i<get_poly_pts.length; i++){
							pt = get_poly_pts[i];
							//console.log(floor_width_meter);
							pt_x = pt.x/floor_width_meter*img_width;
							//console.log(pt_x);
							pt_y = (floor_height_meter - pt.y)/floor_height_meter*img_height;
							context.lineTo(pt_x, pt_y);
						}
						context.closePath();
						context.stroke();
						context.fill();

						var poly_center = getCentroid(get_poly_pts);
						//console.log(poly_center);
						context.font = "8px Comic Sans MS";
						context.fillStyle = "black";
						context.textAlign = "center";
						context.fillText(get_geo_name+" "+get_geo_uid, poly_center.x/floor_width_meter*img_width, (floor_height_meter - poly_center.y)/floor_height_meter*img_height); 
						</script>
						<?php
						$geometry_row = $getgeoresult->fetch_assoc();
					}
					?>