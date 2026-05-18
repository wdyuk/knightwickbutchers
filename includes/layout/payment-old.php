<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
$shippingdetails = array_map("sanitize_sql_string",$_POST);
$message = array();

if(isset($_POST['collect']) && $_POST['collect'] == 1){
	$collection = 1;
	
} else {
	$collection = 0;
}
$accountData = $_SESSION['guest'];

$accountData = array_merge($accountData, $shippingdetails);

$cartData = $cartItems;
$Carttotal = 0;

// echo '<pre>'.print_r($cartData, true).'</pre>';

foreach ($cartData as $cartItem) {
	$item = table_fetch_row('products','id="'.$cartItem['sku'].'"');
	// echo '<pre>'.print_r($item, true).'</pre>';
	$price = $cartItem['price'];
	$quantity = $cartItem['quantity'];
	//Check Stock

	if($item['stock'] < 1){
		header('Location: /basket?order_amount=unavailable');
		//$message[] = 'This item is currently out of stock please give us a call to pre-order';
		exit();
	}

	$Carttotal += ($price * $quantity);
	
}

$email = $accountData['email'];

if (!isset($accountData['id'])) {
	$accountData['id'] = 0;
}
if(isset($collection) && $collection == 1){
	$deliverycost = 0.00;
	$accountData['shipping_address_line_1'] = 'To be Collected';
	$accountData['shipping_address_line_2'] = 'To be Collected';
	$accountData['shipping_town'] = 'To be Collected';
	$accountData['shipping_postcode'] = 'To be Collected';
}

else{

	$basepostcode = trim(urlencode(str_replace(' ', '', $store_settings['company_postcode'])));
	$deliverypostcode = trim(urlencode(str_replace(' ', '', $accountData['shipping_postcode'])));
	
	// echo '<pre>'.print_r($store_settings, true).'</pre>';
	$postcode = new Postcode();
	// echo '<pre>'.print_r($postcode, true).'</pre>';
	// echo '<pre>'.print_r($basepostcode, true).'</pre>';
	// echo '<pre>'.print_r($deliverypostcode, true).'</pre>';
	$miles = $postcode->distance($basepostcode,$deliverypostcode,'');

	if (strlen($miles) == 0) {
		header('Location: /basket?delivery=postcodenotrecognised&postcode='.$deliverypostcode);
		exit();
	}
	
	$deliveryresult = get_delivery_cost($miles, $Carttotal); 
	// echo '<pre>'.print_r($deliveryresult, true).'</pre>';
	
	if ($deliveryresult['delivery'] == 'notok') {
	  	$miles = ceil($miles);
        header('Location: /basket?delivery=unavailable&miles='.$miles);
        exit();
	} else {
		$deliverycost = $deliveryresult['deliverycost'];
	}
	
}

$grandtotal = $Carttotal + $deliverycost;
//Generate Order in Database

$fields = array(
	'account_no',
	'title',
    'firstname',
    'lastname',
    'email',
    'mobile_number',
    'telephone_number',
    'billing_address_line_1',
    'billing_address_line_2',
    'billing_town',
    'billing_postcode',
    'shipping_address_line_1',
    'shipping_address_line_2',
    'shipping_town',
    'shipping_postcode',
    'shipping_option_id',
    'shipping_total',
	'subtotal',
    'total',
    'status',
    'stripe_error',
    // 'stripe_token',
    'created_at',
    'updated_at'
);

$values = array(
				'account_no' => $accountData['id'],
				'title' => $accountData['title'],
				'firstname' => $accountData['firstname'],
				'lastname' => $accountData['lastname'],
				'email' => $accountData['email'],
				'mobile_number' => $accountData['mobile_number'],
				'telephone_number' => $accountData['telephone_number'],
				'billing_address_line_1' => $accountData['address_line_1'],
				'billing_address_line_2' => $accountData['address_line_2'],
				'billing_town' => $accountData['town'],
				'billing_postcode' => $accountData['postcode'],
				'shipping_address_line_1' => $accountData['shipping_address_line_1'],
				'shipping_address_line_2' => $accountData['shipping_address_line_2'],
				'shipping_town' => $accountData['shipping_town'],
				'shipping_postcode' => $accountData['shipping_postcode'],
				'shipping_option_id' => $collection,
				'shipping_total' => number_format($deliverycost,2),
				'subtotal' => number_format($Carttotal,2),
				'total' => number_format($grandtotal,2),
				'status' => 'PENDING',
				'stripe_error' => '',
				// 'stripe_token' => $_POST['stripeToken'],
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
				);

$thisorderid = table_insert('orders', $fields, $values);
$thisorderdetails = table_fetch_row('orders', 'id="'.$thisorderid.'"');

foreach ($cartData as $cartItem) {
	$item = table_fetch_row('products','id="'.$cartItem['sku'].'"');

	$price = $cartItem['price'];
	$quantity = $cartItem['quantity'];

	$linetotal = ($price * $quantity);
	if (!isset($cartItem['weight_id'])) {
		$cartItem['weight_id'] = 0;
	}
	if (!isset($cartItem['weight_text'])) {
		$cartItem['weight_text'] = '';
	}

	$fields = array('order_id',
					'product_id',
					'name',
					'product_weight_id',
					'weight_text',
					'price',
					'quantity',
					'total'
					);

	$values = array('order_id' => $thisorderid,
					'product_id' => $item['id'],
					'name' => $cartItem['name'],
					'product_weight' => $cartItem['weight_id'],
					'weight_text' => $cartItem['weight_text'],
					'price' => $price,
					'quantity' => $quantity,
					'total' => $linetotal
					);
	table_insert('order_items',$fields,$values);
}

$Carttotal = 0;

?>
<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
  <div class="row">
      <div class="col-md-12 pt-3 pb-3">
      </div>
    </div>
</section>
<section class="probootstrap-section  probootstrap-bg-white pt-2 pb-5 basket-page">
  <div class="container">
    <div class="row">
        <div class="col-12 probootstrap-animate">
            <h3>Confirm Order Details <?php if ($collection == 0) { echo '&amp; Payment'; };?></h3>
        </div>
    </div>
    <form method="POST" id="Confirm" action="/processor/process-payment.php">
	    <div class="row">
	        <div class="col-12 probootstrap-animate">		
			
				<h3>Order Details</h3>
				<table class="table table-striped w-100">
					<tr>
						<th class="table-headers">Product Name</th>
						<th class="table-headers">Price</th>
						<th class="table-headers">Quantity</th>
						<th class="table-headers">Total</th>
					</tr>
					<?php foreach ($cartData as $cartItem) {
						$item = table_fetch_row('products','id="'.$cartItem['sku'].'"');
						$price = $cartItem['price'];
						$quantity = $cartItem['quantity'];
						$subtotal = ($price * $quantity);
						$Carttotal += ($price * $quantity);
						
						?>
					<tr>
						<td><?= $cartItem['name'];?></td>
						<td>&pound;<?= number_format($price, 2);?></td>
						<td><?= $quantity;?></td>
						<td>&pound;<?= number_format($subtotal, 2);?></td>
					</tr>
					<?php }
					$total_for_stripe = ($grandtotal * 100);
					?>
					<tr>
						<th> </th>
						<th> </th>
						<th class="table-headers"> </th>
						<th> </th>
					</tr>
					<tr>
						<th></th>
						<th></th>
						<th class="table-headers">Sub Total</th>
						<td>&pound;<?= number_format($Carttotal, 2);?></td>
					</tr>
					<tr>
						<th></th>
						<th></th>
						<th class="table-headers">Shipping Costs</th>
						<td>&pound;<?= number_format($deliverycost, 2);?><?php if ($collection == 1) { echo '<br>(Customer Collecting)';}?></td>
					</tr>
					<tr>
						<th></th>
						<th></th>
						<th class="table-headers">Grand Total</th>
						<td><strong>&pound;<?= number_format($grandtotal, 2);?></strong><?php if ($collection == 1) { echo '<br>(To be paid in cash on collection)';}?></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12 personal-details">
				<h3>Personal Details</h3>
				<table class="table table-striped">
					<tr>
						<th class="table-headers">Title</th>
						<td><?= $accountData['title'] ;?></td>
						<th class="table-headers">First Name</th>
						<td><?= $accountData['firstname'] ;?></td>
					</tr>
					<tr>
						<th class="table-headers">Surname</th>
						<td><?= $accountData['lastname'] ;?></td>
						<th class="table-headers">Email</th>
						<td><?= $accountData['email'] ;?></td>
					</tr>
					<tr>
						<th class="table-headers">Mobile</th>
						<td><?= $accountData['mobile_number'] ;?></td>
						<th class="table-headers">Telephone</th>
						<td><?= $accountData['telephone_number'] ;?></td>
					</tr>
				</table>
			</div>
		</div>
			
		<div class="row">

			<div class="col-md-6 col-sm-6 col-xs-12 billing-details">
				<h3>Billing Details</h3>
				<table class="table table-striped">
					<tr>
						<th class="table-headers">Address 1</th>
						<td><?= $accountData['address_line_1'] ;?></td>
						<th class="table-headers">Address 2</th>
						<td><?= $accountData['address_line_2'] ;?></td>
					</tr>
					<tr>
						<th class="table-headers">Town</th>
						<td><?= $accountData['town'] ;?></td>
						<th class="table-headers">Post Code</th>
						<td><?= $accountData['postcode'] ;?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6 col-sm-6 col-xs-12 shipping-details">
				<h3>Shipping Details</h3>
				<?php if ($collection == 0) { ?>
				<table class="table table-striped">
					<tr>
						<th class="table-headers">Address 1</th>
						<td><?= $accountData['shipping_address_line_1'] ;?></td>
						<th class="table-headers">Address 2</th>
						<td><?= $accountData['shipping_address_line_2'] ;?></td>
					</tr>
					<tr>
						<th class="table-headers">Town</th>
						<td><?= $accountData['shipping_town'] ;?></td>
						<th class="table-headers">Post Code</th>
						<td><?= $accountData['shipping_postcode'] ;?></td>
					</tr>
				</table>
				<?php } else { ?>
					<p>Customer is collecting from <?= $store_settings['company_collect_address']; ?></p>
				<?php } ?>
			</div>
		</div>
		<?php if ($collection == 0) { ?>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12">
					 <input type="submit" class="btn btn-success pull-right" id="checkout-now" value="Pay Now" />
				</div>
			</div>
			<input type="hidden" name="cartTotal" value="<?php echo number_format($grandtotal, 2) ?>" />
		    <input type="hidden" name="stripeToken" id="stripeToken" value="" />
		    <input type="hidden" name="orderid" id="order_id" value="<?= $thisorderid; ?>" />
		    <input type="hidden" name="stripeEmail" id="stripeEmail" value="" />
		   
		    <script src="https://checkout.stripe.com/checkout.js"></script>
		    <script>
				var handler = StripeCheckout.configure({
				  key: '<?= STRIPE_PUBLIC_KEY ;?>',
				  locale: 'auto',
				  token: function(token) {
				     $("#stripeToken").val(token.id);
			         $("#stripeEmail").val(token.email);
			         $("#Confirm").submit();
				  }
				});

				document.getElementById('checkout-now').addEventListener('click', function(e) {
				  // Open Checkout with further options:
				  handler.open({
				    name: "<?= SITE_NAME; ?>",
				    description: "Your Order",
				    email: "<?= $email ;?>",
				    zipCode: true,
				    currency: 'gbp',
				    amount: <?= $total_for_stripe; ?>
				  });
				  e.preventDefault();
				});

				// Close Checkout on page navigation:
				window.addEventListener('popstate', function() {
				  handler.close();
				});

				$('#checkout-now').on('click', function (e) {
					e.preventDefault();
				});
			</script>
		<?php } else { ?>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12">
					 <input type="submit" class="red-btn-proceed pull-right btn-more-details-green-square" id="checkout-now" value="Place Order" />
				</div>
			</div>
			<input type="hidden" name="cartTotal" value="<?php echo number_format($grandtotal, 2) ?>" />
		    <input type="hidden" name="collect" value="1" />
		    <input type="hidden" name="orderid" id="order_id" value="<?= $thisorderid; ?>" />
		<?php } ?>
	</form>	
  </div>
</section>