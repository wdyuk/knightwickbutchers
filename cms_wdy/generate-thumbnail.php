<?php

define('SITE_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);

$file = $_GET['file'];
$width = $_GET['width'];
$height = $_GET['height'];

if (strlen(trim($file)) == 0) {
	die();
}

$file = realpath(SITE_DIR . substr($file, 1));

if (strpos($file, SITE_DIR) === false) {
	die();
}


function img_resize($path, $cached, $width, $height) 
{
	$gis = getimagesize($path); 
	$type = $gis[2];
	
	$image_width = $gis[0];
	$image_height = $gis[1];
	
	if ($image_width <= $width) {
		echo file_get_contents($path);
		die();
	}
	
	$propotional = $width / $image_width;
	$height = $image_height * $propotional;
	
	switch($type) 
	{ 
		case '1':
			$imorig = imagecreatefromgif($path);
			break;
			
		case '2':
			$imorig = imagecreatefromjpeg($path);
			break;
			
		case '3':
			$imorig = imagecreatefrompng($path);
			break;
		
		default:
			$imorig = imagecreatefromjpeg($path); 
			breakl;
	} 
	
	$im = imagecreatetruecolor($width, $height);
	
	if (imagecopyresampled($im, $imorig,0,0,0,0, $width, $height, $gis[0], $gis[1])) {
		imagejpeg($im, $cached, 100);
		echo file_get_contents($cached);
	}
}

header('Content-Type: image/' . substr($file, -3));
img_resize($file, $cached, $width, $height);

?>