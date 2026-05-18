<?php

	session_start();
	require $_SESSION['application_file'];
	
	if (!isset($_SESSION['admin'])) {
		die();
	}
	
?>