<?php
function getimgsize($url, $referer = ''){
	$headers = array(
		'Range: bytes=0-32768'
		);

	/* Hint: you could extract the referer from the url */
	if (!empty($referer)) array_push($headers, 'Referer: '.$referer);

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($curl);
	curl_close($curl);

	$image = imagecreatefromstring($data);

	$return = array(imagesx($image), imagesy($image));

	imagedestroy($image);

	return $return;
}


function insidePoly($x,$y,$poly){
	$n = count($poly);
	$inside = false;
	$x1 = floatval($poly[0]->x);
	$y1 = floatval($poly[0]->y);
	for($i = 0; $i<$n+1; $i++){
		$x2 = floatval($poly[$i%$n]->x);
		$y2 = floatval($poly[$i%$n]->y);
		if($y>min($y1, $y2) && $y<=max($y1, $y2) && $x<=max($x1, $x2)){
			if($y1!==$y2){
				$xints = ($y-$y1)*($x2-$x1)/($y2-$y1)+$x1;
			}
			if($x1 === $x2 || $x < $xints){
				$inside = !$inside;
			}
		}
		$x1 = $x2;
		$y1 = $y2;
	}
	return $inside;
}
?>