<?php
define('ADMIN_URL', 'cms_wdy'); // can change per site, but must also change the folder name
define('DEBUG', false); // set to false for production
date_default_timezone_set ('Europe/London'); 
error_reporting(E_ALL);

if (DEBUG == true) {

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

} else {

    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);

}

require_once(ADMIN_URL.'/application.php');