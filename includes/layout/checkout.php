<?php
 if (count($cartItems) == 0) { header('Location: /#shop'); };
	
	if((isset($_POST['payment'])) && (!isset($_POST['delivery']))){
		include('includes/layout/payment.php');
	}
	elseif((isset($_POST['delivery'])) && (!isset($_POST['payment']))){
		include('includes/layout/delivery.php');
	}
	else {
		include('includes/layout/guest-details.php');
	}


?>

