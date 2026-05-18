<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

require 'init.php';
require_once("../../vendor/autoload.php");

$params = $_POST;

if (isset($params['order_id'])) {
    $order_id = (int) $params['order_id'];

    $thisorder = table_fetch_row('orders','id="'.$order_id.'"');

    if ($thisorder['status'] == "CARD AUTHORISED" && isset($thisorder['stripe_customer_id']) && isset($thisorder['stripe_payment_method']) && !isset($thisorder['stripe_payment_intent'])) {

      $store_settings = table_fetch_row('store_settings','id=1');
        
        // Set your secret key. Remember to switch to your live secret key in production!
        // See your keys here: https://dashboard.stripe.com/account/apikeys
       
        $stripe = new \Stripe\StripeClient(
          STRIPE_PRIVATE_KEY
        );
        
        $total_for_stripe = $thisorder['total'] * 100;

        try {
          $charge = $stripe->paymentIntents->create([
            'amount' => $total_for_stripe,
            'currency' => 'gbp',
            'customer' => $thisorder['stripe_customer_id'],
            'payment_method' => $thisorder['stripe_payment_method'],
            'off_session' => true,
            'confirm' => true,
          ]);

          //Mark order as paid
          table_update('orders',['status','updated_at','stripe_payment_intent','email_sent'],['status' => 'PAID', 'updated_at' => $now, 'stripe_payment_intent' => $charge->id,'email_sent' => date('Y-m-d H:i:s')],'id="'.$thisorder['id'].'"');
          
          $orderdetails = table_fetch_rows('order_items','order_id="'.$thisorder['id'].'"');


          $headercontent = "<style type='text/css'>.table {border: 1px solid #B5A593 !important; padding:0px; width: 100%;} .table td, th { padding: .75rem; border: 1px solid #574A3D;} .table th {background-color:#22bb99; color: #fff;} p {text-align: left; margin: 10px 0px;} </style>";
          $content = '';
          if ($thisorder['status'] == 'CARD AUTHORISED') {
            $content .= "<p>Your order has been prepared and your card has been charged the amount shown.</p>";
          }
          if ($thisorder['shipping_option_id'] == 0) {
            $content .= "<p><strong>A member of our team will be in touch shortly to arrange delivery. If you have any further questions please call us on ".$store_settings['company_contact_no']."</strong></p>";
          } else {
            $content .= "<p><strong>A member of our team will be in touch shortly to arrange collection. If you have any further questions please call us on ".$store_settings['company_contact_no']."</strong></p>";
            
            $content .= "<h2>Collection Details</h2>";
            $content .= "<p>Please collect from: ".$store_settings['company_collect_address']."</p>";
            
            $content .= "<p>Payment is required on collection.</p>";
            
          }
          $content .= "<h2>Order Details</h2>";
          $content .= "<table class='table table-striped w-100' align='center'>";
          $content .= "<tr><th>Order Ref</th><td>#".$thisorder['id']."</td></tr>";
          $content .= "<tr><th>Full Name:</th><td>".$thisorder['firstname']." ".$thisorder['lastname']."</td></tr><tr>
          <th>Email:</th><td>".$thisorder['email']."</td></tr><tr>
          <th>Mobile:</th><td>".$thisorder['mobile_number']."</td></tr><tr>
          <th>Telephone:</th><td>".$thisorder['telephone_number']."</td></tr></table>";


          $content .= "<table class='table table-striped w-100' cellpadding='0' cellspacing='0'>";

          $content .= "<tr><th>Quantity</th><th>Item</th><th>Item Price</th><th>Total</th></tr>";
          foreach($orderdetails as $od) {
            $product = table_fetch_row('products','id="'.$od['product_id'].'"');
            
            $content .= "<tr><td>".$od['quantity']."</td><td>".$od['name']."</td><td>&pound;".$od['price']."<td>&pound;".$od['total']."</td>";
          };
         
          $content .= "<tr><td></td><td>Sub Total:</td><td></td><td>&pound;".$thisorder['subtotal']."</td>";
          

          $content .= "<tr><td></td><td>Shipping Costs:</td><td></td><td>&pound;".$thisorder['shipping_total']."</td>";
          if (isset($thisorder['voucher_discount']) && is_numeric($thisorder['voucher_discount'])) {
           $content .= "<tr><td colspan='3'>Voucher Code</td><td>Discount</td></tr>";
            $content .= "<tr><td colspan='3'>".$thisorder['voucher_code']."</td><td>- &pound;".$thisorder['voucher_discount']."</td>";
          }
          $content .= "<tr><td></td><td>Grand Total:</td><td></td><td>&pound;".$thisorder['total']."</td>";
          
          
          $content .= "</table>";
          $content .= "<h2>Billing Details</h2>";
          $content .= "<table class='table table-striped w-100' align='center'>";
          $content .= "<tr><th>Name:</th><td>".$thisorder['firstname']." ".$thisorder['lastname']."</td></tr>";
          $content .= "<tr><th>Address Line 1:</th><td>".$thisorder['billing_address_line_1']."</td></tr>";
          $content .= "<tr><th>Address Line 2:</th><td>".$thisorder['billing_address_line_2']."</td></tr>";
          $content .= "<tr><th>Town:</th><td>".$thisorder['billing_town']."</td></tr>";
          $content .= "<tr><th>Postcode:</th><td>".$thisorder['billing_postcode']."</td></tr>";
          $content .= "</table>";
          if ($thisorder['shipping_option_id'] == 0) {
            $content .= "<h2>Delivery Details</h2>";
            $content .= "<table class='table table-striped w-100' align='center'>";
            $content .= "<tr><th>Name:</th><td>".$thisorder['firstname']." ".$thisorder['lastname']."</td></tr>";
            $content .= "<tr><th>Address Line 1:</th><td>".$thisorder['shipping_address_line_1']."</td></tr>";
            $content .= "<tr><th>Address Line 2:</th><td>".$thisorder['shipping_address_line_2']."</td></tr>";
            $content .= "<tr><th>Town:</th><td>".$thisorder['shipping_town']."</td></tr>";
            $content .= "<tr><th>Postcode:</th><td>".$thisorder['shipping_postcode']."</td></tr>";
            $content .= "</table>";
          }

          $content .= '<h2>Order Comments</h2>';
          $content .= '<p>'.$thisorder['customer_comments'].'</p>';

          $email_content = $headercontent."<p>Thank you, your order and payment have been processed.</p>";
          $email_content .= $content;


          $email_content .= "<p>Many thanks,</p><p>".SITE_NAME."</p>";

          $title = 'Order & Payment Processed - '.SITE_NAME;
          $preheader = 'Your order #'.$thisorder['id'].' has been processed';
          $heading = 'Thank You!';
          $companyemail = $store_settings['company_email'];
          $companyaddress = $store_settings['company_postal_address'];
          $message = file_get_contents('../../includes/email_templates/base_site.html');
          
          $message = nl2br($message);
          $message = str_replace('{{BASE_URL}}', BASE_URL, $message);       
          $message = str_replace('{{SITE_LOGO}}', ltrim(SITE_LOGO_ALT,'/'), $message);        
          $message = str_replace('{{TITLE}}', $title, $message);
          $message = str_replace('{{PREHEADER}}', $preheader, $message);
          $message = str_replace('{{HEADING}}', $heading, $message);
          $message = str_replace('{{CONTENT}}', $email_content, $message);
          $message = str_replace('{{COMPANYEMAIL}}', $companyemail, $message);
          $message = str_replace('{{COMPANYADDRESS}}', $companyaddress, $message);
          $message = str_replace("<br>","",$message);
          $message = str_replace("<br />","",$message);
          $message = str_replace("\n","",$message);
          
          
          send_smtp_simple_email($thisorder['email'], $store_settings['company_from_email'], $title, $message);
          $to = $store_settings['company_to_emails'];
          send_smtp_simple_email($to, $store_settings['company_from_email'], $title, $message);
          
         
          //update order and delete card
          $now = date('Y-m-d H:i:s');
          table_update('orders',['email_sent'],['email_sent' => date('Y-m-d H:i:s')],'id="'.$thisorder['id'].'"');

          $response = ['status' => 'ok', 'message' => 'Charged successfully', 'data' => $charge];

        } catch (\Stripe\Exception\CardException $e) {

          // Error code will be authentication_required if authentication is needed
          $error_code = $e->getError()->code;
          $payment_intent_id = $e->getError()->payment_intent->id;
          $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

          $response = ['status' => 'failed', 'message' => 'Not charged - There was an error '.$error_code, 'data' => $payment_intent, 'payment_intent_id' => $payment_intent_id];
        }
    } else {
        $response = ['status' => 'notok', 'message' => 'Not charged - Order is not in the correct state to be charged.'];
    }


} else {
    $response = ['status' => 'notok', 'message' => 'Not charged - incorrect params provided.'];
}

echo json_encode($response);

?>