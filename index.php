<?php

require_once("vendor/autoload.php");
require_once("includes/initialize.php");

?>

<!DOCTYPE html>

<html lang="en">

	<?php include("includes/template/head.php");?>

	<body class="is-preload <?= $bodyclass; ?>">
    
		
	<?php
	include("includes/template/header.php");

    $layouturl = explode('/',$rewriteData['url']);
    $countpieces = (count($layouturl) - 1);
    $layoutfile = '/'.str_replace('/','-',$layouturl[$countpieces]).'.php';

    if ($rewriteData['table_name'] == 'product') {
        include('includes/layout/product-page.php');
    }
    elseif($rewriteData['table_name'] == 'category') {

        include('includes/layout/category-page.php');
    }
    elseif ($rewriteData['url'] == '/login') {
        include('includes/layout/accounts/login.php');
    }

    elseif ($rewriteData['url'] == '/forgot-password') {
        include('includes/layout/accounts/forgot-password.php');
    }

    elseif ($rewriteData['url'] == '/resend-activation') {
        include('includes/layout/accounts/resend-activation.php');
    } 

    elseif ($rewriteData['url'] == '/activate-account') {
        include('includes/layout/accounts/activate_account.php');
    }
    
    elseif ($rewriteData['url'] == '/account') {
        include('includes/layout/accounts/account.php');
    }
    elseif ($rewriteData['url'] == '/') {
        include('includes/layout/home.php');
    }
    elseif (file_exists('includes/layout/' . $layoutfile)) {
        include('includes/layout/' . $layoutfile);
    }

    else { 
        include('includes/layout/inner.php');
    }

	include("includes/template/footer.php"); 

	?>

</body>
</html>