<?php
	ob_start();
	date_default_timezone_set('Europe/London');
	if (!isset($_SESSION)) {
		session_start();
	}
	define('SITE_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
	define('ADMIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	
	define('INCLUDES_DIR', ADMIN_DIR . 'includes' . DIRECTORY_SEPARATOR);
	
	define('UPLOADS_DIR', SITE_DIR . 'uploads' . DIRECTORY_SEPARATOR);
	define('UPLOADS_URL', '/uploads/');

	define('MEDIA_DIR', SITE_DIR . 'media' . DIRECTORY_SEPARATOR);
	define('MEDIA_URL', '/media/');
	
	require INCLUDES_DIR . 'config.php';
	require INCLUDES_DIR . 'functions.all.php';
	
	$_SESSION['application_file'] = __FILE__;
	//$_SESSION['language'] = get_settings_value('language');

?>