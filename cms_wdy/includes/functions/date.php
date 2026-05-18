<?php

function convert_date($date)
{
	$arr = explode('/', $date);
	return $arr[2] . '-' . $arr[1] . '-' . $arr[0];
}

?>