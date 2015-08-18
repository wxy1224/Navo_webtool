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
?>