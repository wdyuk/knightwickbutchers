<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

use Cart\Cart;
use Cart\Storage\SessionStore;
use Cart\CartItem;

require_once ('../vendor/autoload.php');
require '../cms_wdy/application.php';


$params = array_map("sanitize_sql_string",$_POST);
$order = table_fetch_row('orders','status = "PENDING" AND id = '.$params['orderid'].'');

if (!$order) {
    header('Location: /checkout?no-order-details');
    exit();
}
?>
<!DOCTYPE html>

<html lang="en">

    <head> 
    <meta charset="utf-8">
    <title><?= SITE_NAME; ?> | Order Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="" />
    <meta name="description" content="">
    <meta name="author" content="">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/main.css" />
    <link rel="stylesheet" href="../assets/lightbox/css/lightbox.min.css">
    <script src="../assets/js/jquery.min.js"></script>
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
    <body class="is-preload">
    <!-- Page Wrapper -->
    <div id="page-wrapper">
        
    <!-- Header -->
<?php include('../includes/template/header.php'); ?>
<section id="wrapper">
    <section id="one" class="wrapper spotlight style1">
        <div class="inner">
                                    
                        <div class="row">
                            <div class="col-sm-12 col-sm-12 col-xs-12">
<?php 

$thisorderid = $order['id'];

$cartid = strtolower(preg_replace("/[^a-zA-Z0-9]+/", "", SITE_NAME)).'-wdy-cart';

$cartSessionStore = new SessionStore();

$cart = new Cart($cartid, $cartSessionStore);
if(isset($_SESSION[$cartid])) {
    $cart->restore();
}

$cartItems = $cart->all();
$cartTotal = $cart->total();
$cartTotalItems = $cart->totalItems();

if(!isset($_POST) || empty($_POST) || $cartTotalItems == 0) {
    header('Location: /checkout?no-cart-items');
    exit();
}



if (isset($params['collect'])) {
    //do collection
    $websitetoken = generateRandomToken();
    $fields = array(
                    'customer_comments',
                    'status',
                    'updated_at',
                    'websitetoken'
                    );

    $values = array(
                    'customer_comments' => $params['customer_comments'],
                    'status' => 'PLACED FOR COLLECTION',        
                    'updated_at' => date('Y-m-d H:i:s'),
                    'websitetoken' => $websitetoken
                    );

    table_update('orders', $fields, $values,'id="'.$thisorderid.'"');
    process_order($thisorderid);
    $cart->clear();
    header('Location: /order-confirmation?order_id='.$thisorderid.'&verification='.$websitetoken);
} else {
    echo '<p>Something went wrong (collection parameter not set). Please try placing the order again.</p>';
}
?>
</div>
</div>
</div>
</section>
</section>