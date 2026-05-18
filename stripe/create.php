<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
require __DIR__.'/../cms_wdy/application.php';
require __DIR__.'/../vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_PRIVATE_KEY);

header('Content-Type: application/json');

try {
  
  // retrieve JSON from POST body
  $json_str = file_get_contents('php://input');
  $json_obj = json_decode($json_str);

  $order = table_fetch_row('orders','id="'.$json_obj->id.'"');
  $amount = ($order['total'] * 100);

  $paymentIntent = \Stripe\PaymentIntent::create([
    'amount' => $amount,
    'currency' => 'gbp'
  ]);

  $output = [
    'clientSecret' => $paymentIntent->client_secret,
  ];

  echo json_encode($output);

} catch (Error $e) {

  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);

}