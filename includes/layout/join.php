<?php //if ($_SERVER['REMOTE_ADDR'] == '213.123.212.108') {

	if (isset($_POST['step2'])) {
	   include('includes/layout/join-confirm-details.php');
	} 
	// elseif (isset($_POST['step3'])) {
	// 	include('includes/template/join-payment-details.php');
	// }
	else {
		include('includes/layout/join-enter-details.php');
	}
//}
?>

