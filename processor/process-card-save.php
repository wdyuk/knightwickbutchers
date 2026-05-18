<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

use Cart\Cart;
use Cart\Storage\SessionStore;
use Cart\CartItem;

require_once ('../vendor/autoload.php');
require '../cms_wdy/application.php';

$json_str = file_get_contents('php://input');
$params = json_decode($json_str, true);

$order = table_fetch_row('orders','status = "PENDING" AND id = '.$params['orderid'].'');

if (!$order) {
    header('Location: /checkout?no-order-details');
    exit();
}

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
$response = ['status' => 'failed'];
if (!isset($params['collect'])) {
    //do payment
    $alreadySubmitted = table_fetch_row('orders','stripe_setup_intent="'.$params['paymentSetup']['id'].'"');

    if ($alreadySubmitted) {
        // echo '<h2>Sorry, there has been a problem processing your payment</h2>';
        // echo '<p>A paymentintent for this payment has already been submitted</p>';
        // echo '<p>Please <a href="/checkout?already-submitted">click here to try again.</a></p>';
        
    } else {

    
        $websitetoken = generateRandomToken();

        $paymentIntentId = $params['setupIntent']['id'];
        $paymentMethodId = $params['setupIntent']['payment_method'];
        
        $fields = array(
            'customer_comments',
            'status',
            'stripe_error',
            'updated_at',
            'stripe_setup_intent',
            'stripe_payment_method',
            'websitetoken'
            );

        $values = array(
                        'customer_comments' => $params['comments'],
                        'status' => 'CARD AUTHORISED',
                        'stripe_error' => '',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'stripe_setup_intent' => $paymentIntentId,
                        'stripe_payment_method' => $paymentMethodId,
                        'websitetoken' => $websitetoken
        );
        table_update('orders', $fields, $values ,'id="'.$thisorderid.'"');
        process_order($thisorderid);
        $cart->clear();
        $response = ['status' => 'ok', 'orderid' => $thisorderid, 'websitetoken' => $websitetoken];
        
        
    }
} 
 echo json_encode($response);
?>
