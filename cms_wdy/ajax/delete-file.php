<?php
	
	require 'init.php';
	
	$file = $_GET['file'];
	unlink('../../' . $file);
	
?>