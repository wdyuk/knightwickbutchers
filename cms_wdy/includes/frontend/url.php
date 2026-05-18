<?php

function get_seo_link_title($title)
{
	$title = str_replace('_', '{7}', $title);
	$title = str_replace('-', '_', $title);
	$title = str_replace(' ', '-', $title);
	$title = str_replace('?', '{1}', $title);
	$title = str_replace(':', '{2}', $title);
	$title = str_replace('/', '{3}', $title);
	$title = str_replace("'", '{4}', $title);
	$title = str_replace("&", '{5}', $title);
	$title = str_replace("#", '{6}', $title);
	
	return $title;
}

function clear_seo_link_title($title)
{
	$title = str_replace('-', ' ', $title);
	$title = str_replace('_', '-', $title);
	$title = str_replace('{1}', '?', $title);
	$title = str_replace('{2}', ':', $title);
	$title = str_replace('{3}', '/', $title);
	$title = str_replace('{4}', "'", $title);
	$title = str_replace('{5}', "&", $title);
	$title = str_replace('{6}', "#", $title);
	$title = str_replace('{7}', "_", $title);
	
	return $title;
}

?>