<?php

	session_start();
	require $_SESSION['application_file'];
	
	logout_admin();
	redirect('login.php');

?>