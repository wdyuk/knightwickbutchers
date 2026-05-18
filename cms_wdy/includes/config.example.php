<?php

	define('APP_NAME', 'Administration');
	define('APP_TITLE', APP_NAME);
	define('SITE_NAME', 'New Website');
	
	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', '');
	define('DB_PASSWORD', '');
	define('DB_DATABASE', '');

    $conn = new PDO('mysql:host='.DB_SERVER.';dbname=' . DB_DATABASE .';charset=utf8', DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	define('TBL_TRANSLATIONS', 'translations');
	define('TBL_ADMIN', 'admin');
	define('TBL_PHOTOGALLERY', 'photogallery');
	define('TBL_PHOTOS', 'photos');
	define('TBL_PAGE', 'page');
	define('TBL_RIGHTCOLUMN', 'right_column');
	define('TBL_SETTINGS', 'settings');

	
	define('SMTP_HOST', '');
	define('SMTP_PORT', 25);	
	define('SMTP_SECURE', '');	
	define('SMTP_USERNAME', '');	
	define('SMTP_PASSWORD', '');

	if (!defined('ADMIN_URL')) {
		define('ADMIN_URL', ''); //just enter the folder name for the cms eg 'wdy_cms'
	}

	define('DEBUG_ADMIN', true);

	define('BASE_URL', ''); // with trailing slash eg) http://website.co.uk/
    define('BASE_DIR', ''); // with beginning and trailing slash eg) /home/sitename/public_html/
    define('SITE_LOGO', ''); // relative link with preceding slash eg) assets/theme/imglogo.png
    define('SITE_LOGO_ALT', ''); // relative link with preceding slash eg) assets/theme/imglogo.png

    define('GOOGLE_API_KEY', '');
    define('GOOGLE_RECAPTCHA_SITE', '');
    define('GOOGLE_RECAPTCHA_SECRET', '');
    define('GOOGLE_ANALYTICS', '');
    define('GOOGLE_SITE_VERIFICATION', '');


    //TEST STRIPE
    define('STRIPE_PRIVATE_KEY', '');
    define('STRIPE_PUBLIC_KEY', '');

    // //LIVE STRIPE
    // define('STRIPE_PRIVATE_KEY', '');
    // define('STRIPE_PUBLIC_KEY', '');


	error_reporting(E_ALL);

    if (DEBUG_ADMIN === true) {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);

    } else {

        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);

    }
?>
