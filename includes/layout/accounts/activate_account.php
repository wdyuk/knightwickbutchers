<?php 
require_once("admin/application.php"); 
require_once('vendor/autoload.php');


$params = array_map("sanitize_sql_string",$_GET);

try {
	$check = table_fetch_row('accounts','id="'.$params['accountid'].'"');
	if($check):
		if($check['activation_code'] == $params['activation_code']) {
			$update = table_update('accounts',array('account_activated'),array('account_activated' => 1), 'id="'.$check['id'].'"');
			header('Location: ../login?account_activated');
			exit();
		} else {
			echo '<p>Activation code not valid. Please contact '.SITE_NAME.'.</p>';
		}
	else:
		echo '<p>An error occured. Please contact '.SITE_NAME.'.</p>';
	endif;

} catch (Exception $e) {

	echo $e->getMessage();

}