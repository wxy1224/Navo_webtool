<?php
session_start();

require("php/function.php");

require_once 'php/config.php';
$mysqli= new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

if ($mysqli->connect_error) {
	echo "Connection failed: " . $mysqli->connect_error;
} 

$building_id = 6;
$floor_number = 1;

$getgeoquery = "SELECT * FROM cornell_map_server_geometry WHERE building_id = $building_id and floor_number = $floor_number";
$getgeoresult = $mysqli->query($getgeoquery);

$floor_width_meter = 1300;
$floor_height_meter = 1300;

?>

<!DOCTYPE html>
<html>
<head>
	<title>index</title>
	<script src="../js/function.js"></script>
</head>
<body>
	<div class = "canvas">
		<?php
		$img_width = 1000;
		$img_height = 1000;
		echo '<canvas id="mapcanvas1" width = "'.$img_width.'" height = "'.$img_height.'" style = "position: absolute; left: 50; top: 0px; z-index = 0;" ></canvas>';
		?>
		<script>
		var canvas = document.getElementById("mapcanvas1");
		var context = canvas.getContext("2d");

		//context.rect(20,20,150,100);
		//context.stroke()

		var floor_width_meter = <?php echo $floor_width_meter; ?>;
		var floor_height_meter = <?php echo $floor_height_meter; ?>;
		var img_width = <?php echo $img_width; ?>;
		var img_height = <?php echo $img_height; ?>;
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
			pt0 = get_poly_pts[0];
			pt0_x = pt0.x/floor_width_meter*img_width;
			pt0_y = pt0.y/floor_height_meter*img_height;
			context.moveTo(pt0_x, pt0_y);
			for(var i = 1; i<get_poly_pts.length; i++){
				pt = get_poly_pts[i];
				pt_x = pt.x/floor_width_meter*img_width;
				pt_y = pt.y/floor_height_meter*img_height;
				context.lineTo(pt_x, pt_y);
			}
			context.closePath();
			context.stroke();
			context.fill();

			var poly_center = getCentroid(get_poly_pts);
			console.log(poly_center);
			context.font = "8px Comic Sans MS";
			context.fillStyle = "black";
			context.textAlign = "center";
			context.fillText(get_geo_name+" "+get_geo_uid, poly_center.x/floor_width_meter*img_width, poly_center.y/floor_height_meter*img_height); 
			</script>
			<?php
			$geometry_row = $getgeoresult->fetch_assoc();
		}
		?>
		</div>

		</body>
		</html>