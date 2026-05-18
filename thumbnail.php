<?php

require 'admin/application.php';

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

function img_resize($path, $width, $height) 
{
	$gis = getimagesize($path); 
	$type = $gis[2];
	
	$image_width = $gis[0];
	$image_height = $gis[1];
	
	if ($image_width <= $width) {
		echo file_get_contents($path);
		die();
	}
	
	if (isset($_GET['width'])) {
		$propotional = $width / $image_width;
		$height = $image_height * $propotional;
	} elseif (isset($_GET['height'])) {
		$propotional = $height / $image_height;
		$width = $image_width * $propotional;
	} else {
		// use the same width and height as passed on
	}
	
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
			break;
	} 
	
	$im = imagecreatetruecolor($width, $height);
	imagealphablending($im, false);
	imagesavealpha($im,true);
	
	$transparent = imagecolorallocatealpha($im, 255, 255, 255, 127);
	
	imagefilledrectangle($im, 0, 0, $width, $height, $transparent);
	imagecopyresampled($im, $imorig, 0, 0, 0, 0, $width, $height, $gis[0], $gis[1]);
	
	imagepng($im);
}

header('Content-Type: image/png');
img_resize($file, $width, $height);

?>