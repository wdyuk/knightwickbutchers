<?php

function random_word($length)
{
	$arr = array_merge(range('a','z'), array('A','Z'), array(0,9));
	
	$random_keys = array_rand($arr, $length);
	
	$str = '';
	foreach ($random_keys as $index) {
		$str .= $arr[$index];
	}
	
	return $str;
}

?>