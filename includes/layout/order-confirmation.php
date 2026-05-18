<?php $params = array_map("sanitize_sql_string",$_GET);

if(!isset($_GET['order_id']) || (!isset($_GET['verification']))) {
	header('Location: /');
};
$order_id = (int) $params['order_id'];
$verificationToken = $params['verification'];
$order = table_fetch_row('orders','id="'.$order_id.'" AND (status="PAID" or status="CARD AUTHORISED" or status="PLACED FOR COLLECTION") AND websitetoken="'.$verificationToken.'"');
?>
<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
  <div class="row">
      <div class="col-xs-12 pt-3 pb-3">
      </div>
    </div>
</section>
<section class="probootstrap-section  probootstrap-bg-white pt-2 pb-5 basket-page">
  <div class="container">
    <div class="row">
        <div class="col-xs-12 probootstrap-animate">
			<?php 			      		
			if ($order) {
				if ($order['email_sent'] == NULL) {
					$orderdetails = table_fetch_rows('order_items','order_id="'.$order['id'].'"');
					if ($orderdetails) {
						
						?>

						<div class="row">
							<div class="col-sm-12 col-sm-12 col-xs-12">
								<h1><?= $pageData['h1_title']; ?></h1>
								<?= $pageData['content']; ?>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 col-sm-12 col-xs-12">
								<?php
								$headercontent = "<style type='text/css'>.table {border: 1px solid #B5A593 !important; padding:0px; width: 100%;} .table td, th { padding: .75rem; border: 1px solid #574A3D;} .table th {background-color:#22bb99; color: #fff;} p {text-align: left; margin: 10px 0px;} </style>";
								$content = '';
								if ($order['status'] == 'CARD AUTHORISED') {
									$content .= "<p>Your order is being prepared shortly and the final price will be as close to the estimated total price below as possible.</p>";
								}
								if ($order['shipping_option_id'] == 0) {
									$content .= "<p><strong>A member of our team will be in touch shortly to arrange delivery. If you have any further questions please call us on ".$store_settings['company_contact_no']."</strong></p>";
								} else {
									$content .= "<p><strong>A member of our team will be in touch shortly to arrange collection. If you have any further questions please call us on ".$store_settings['company_contact_no']."</strong></p>";
									
									$content .= "<h2>Collection Details</h2>";
									$content .= "<p>Please collect from: ".$store_settings['company_collect_address']."</p>";
									
									$content .= "<p>Payment is required on collection.</p>";
									
								}
								$content .= "<h2>Order Details</h2>";
								$content .= "<table class='table table-striped w-100' align='center'>";
								$content .= "<tr><th>Order Ref</th><td>#".$order['id']."</td></tr>";
								$content .= "<tr><th>Full Name:</th><td>".$order['firstname']." ".$order['lastname']."</td></tr><tr>
								<th>Email:</th><td>".$order['email']."</td></tr><tr>
								<th>Mobile:</th><td>".$order['mobile_number']."</td></tr><tr>
								<th>Telephone:</th><td>".$order['telephone_number']."</td></tr></table>";


								$content .= "<table class='table table-striped w-100' cellpadding='0' cellspacing='0'>";

								$content .= "<tr><th>Quantity</th><th>Item</th><th>Item Price</th><th>Total</th></tr>";
								foreach($orderdetails as $od) {
									$product = table_fetch_row('products','id="'.$od['product_id'].'"');
									
									$content .= "<tr><td>".$od['quantity']."</td><td>".$od['name']."</td><td>&pound;".$od['price']."<td>&pound;".$od['total']."</td>";
								};
								if ($order['status'] == 'CARD AUTHORISED') {
									$content .= "<tr><td></td><td>Estimated Sub Total:</td><td></td><td>&pound;".$order['subtotal']."</td>";
								} else {
									$content .= "<tr><td></td><td>Sub Total:</td><td></td><td>&pound;".$order['subtotal']."</td>";
								}

								$content .= "<tr><td></td><td>Shipping Costs:</td><td></td><td>&pound;".$order['shipping_total']."</td>";
								if (isset($order['voucher_discount']) && is_numeric($order['voucher_discount'])) {
						           $content .= "<tr><td colspan='3'>Voucher Code</td><td>Discount</td></tr>";
						            $content .= "<tr><td colspan='3'>".$order['voucher_code']."</td><td>- &pound;".$order['voucher_discount']."</td>";
						         }
								if ($order['status'] == 'CARD AUTHORISED') {
									$content .= "<tr><td></td><td>Estimated Grand Total:</td><td></td><td>&pound;".$order['total']."</td>";
								} else {
									$content .= "<tr><td></td><td>Grand Total:</td><td></td><td>&pound;".$order['total']."</td>";
								}
								
								$content .= "</table>";
								$content .= "<h2>Billing Details</h2>";
								$content .= "<table class='table table-striped w-100' align='center'>";
								$content .= "<tr><th>Name:</th><td>".$order['firstname']." ".$order['lastname']."</td></tr>";
								$content .= "<tr><th>Address Line 1:</th><td>".$order['billing_address_line_1']."</td></tr>";
								$content .= "<tr><th>Address Line 2:</th><td>".$order['billing_address_line_2']."</td></tr>";
								$content .= "<tr><th>Town:</th><td>".$order['billing_town']."</td></tr>";
								$content .= "<tr><th>Postcode:</th><td>".$order['billing_postcode']."</td></tr>";
								$content .= "</table>";
								if ($order['shipping_option_id'] == 0) {
									$content .= "<h2>Delivery Details</h2>";
									$content .= "<table class='table table-striped w-100' align='center'>";
									$content .= "<tr><th>Name:</th><td>".$order['firstname']." ".$order['lastname']."</td></tr>";
									$content .= "<tr><th>Address Line 1:</th><td>".$order['shipping_address_line_1']."</td></tr>";
									$content .= "<tr><th>Address Line 2:</th><td>".$order['shipping_address_line_2']."</td></tr>";
									$content .= "<tr><th>Town:</th><td>".$order['shipping_town']."</td></tr>";
									$content .= "<tr><th>Postcode:</th><td>".$order['shipping_postcode']."</td></tr>";
									$content .= "</table>";
								}
								$content .= '<h2>Order Comments</h2>';
								$content .= '<p>'.$order['customer_comments'].'</p>';

								
								echo $headercontent.$content; 

								echo $store_settings['embed_collection_map']; ?>
							</div>
						</div>
						<?php
						$email_content = $headercontent."<p>Thank you, your order with ".SITE_NAME." has been received.</p>";
						$email_content .= $content;


						$email_content .= "<p>Many thanks,</p><p>".SITE_NAME."</p>";

						$title = 'Order Confirmation - '.SITE_NAME;
						$preheader = 'Thank you for your order! #'.$order['id'];
						$heading = 'Thank You!';
						$companyemail = $store_settings['company_email'];
						$companyaddress = $store_settings['company_postal_address'];
						$message = file_get_contents('includes/email_templates/base_site.html');
						
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
						
						try {
							send_smtp_simple_email($order['email'], $store_settings['company_from_email'], $title, $message);
							$to = $store_settings['company_to_emails'];
							send_smtp_simple_email($to, $store_settings['company_from_email'], $title, $message);
							
							table_update('orders', array('email_sent'), array('email_sent' => date('Y-m-d H:i:s')), 'id="'.$order['id'].'" AND websitetoken="'.$verificationToken.'"');
						} catch (Exception $e) {
							echo $e->getMessage();
						}

						
						
					}
				} else { ?>
					<div class="row">
						<div class="col-sm-12 col-sm-12 col-xs-12">
							<h1><?= $pageData['h1_title']; ?></h1>
							<p>Order confirmation email has already been sent and should be received shortly, please check your inbox and spam folders.</p><p> To return to the homepage please <a href="/">click here</a>.</p>
						</div>
					</div>
					<?php
				}

			}
			?>
		</div>
	</div>
  </div>
</section>