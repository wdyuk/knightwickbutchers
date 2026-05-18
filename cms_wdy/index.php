<?php
	
	session_start();
	
	if (isset($_SESSION['admin'])) {
		header('Location: control-panel.php');
	} else {
		header('Location: login.php');
	}
	
?>