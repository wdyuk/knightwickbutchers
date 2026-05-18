<?php
$shippingdetails = array_map("sanitize_sql_string",$_POST);
$message = array();
$collection = $_POST['collection'];

if(isset($_SESSION['account'])) {
	$accountData = $_SESSION['account'];
}
else {
	if(isset($collection) && $collection == 1){
		$_SESSION['guest'] = array_map("sanitize_sql_string",$_POST);
	}
	$accountData = $_SESSION['guest'];
}

$accountData = array_merge($accountData, $shippingdetails);

$cartData = $cartItems;
$Carttotal = 0;

foreach ($cartData as $cartItem) {
	$item = table_fetch_row('products','id="'.$cartItem['sku'].'"');

	$price = $item['price'];
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
	$basepostcode = urlencode('YO41 1QG');
	$deliverypostcode = urlencode(str_replace(' ', '', $accountData['shipping_postcode']));
	$response = file_get_contents('https://api.getaddress.io/distance/'.$basepostcode.'/'.$deliverypostcode.'?api-key=ZXLROQD0wkikPiw3hCk1Lw15735'); //Tom's Key
	// $response = file_get_contents('https://api.getaddress.io/distance/'.$basepostcode.'/'.$deliverypostcode.'?api-key=DByQG7RFpEOZ-5WbFRw_UQ15748'); //Rob's Key
	//$response = '{"from":{"latitude":54.010454,"longitude":-0.823447,"postcode":"YO41 1QG"},"to":{"latitude":53.929125,"longitude":-0.780044,"postcode":"YO42 2QN"},"metres":9486.6577775559417}';
	//echo $response;
	$obj = json_decode($response, false);

	//Get Metres
	$meters = $obj->metres;

	//Convert Meters into Miles
	$miles = $meters * 0.000621371;

	//Less than 15 Miles = £0.00
	if($miles <= 12.5){
		$deliverycost = 0.00;
	}
	//Great than 15 Miles but less than 25 Miles = £15
	elseif(($miles > 12.5) && ($miles <= 25)){
		$deliverycost = 10.00;
	} 
	//Great than 25 Miles = 10  
	else{ 
		header('Location: /basket?delivery=unavailable');
		//$message[] = 'Your Delivery address is out of our radius, please give us a call to discuss this order.';
		exit();
	}
	//$costs = '{"deliverycost":'.$deliverycost.'}';
	//echo $costs;
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

	$price = $item['price'];
	$quantity = $cartItem['quantity'];

	$linetotal = ($price * $quantity);


	$fields = array('order_id',
					'product_id',
					'price',
					'quantity',
					'total'
					);

	$values = array('order_id' => $thisorderid,
					'product_id' => $item['id'],
					'price' => $price,
					'quantity' => $quantity,
					'total' => $linetotal
					);
	table_insert('order_items',$fields,$values);
}

$Carttotal = 0;

?>
<!-- Wrapper -->
<section id="wrapper">
  	<section id="one" class="wrapper spotlight style1">
      	<div class="inner">
	      	<div class="row">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<h2 class="inner-head">Confirm Details &amp; Payment</h2>
		
					<form method="POST" id="Confirm" action="/processor/process-payment.php">
					<h3>Order Details</h3>
					<table class="table table-striped table-responsive">
					<tr>
						<th class="table-headers">Product Name</th>
						<th class="table-headers">Price</th>
						<th class="table-headers">Quantity</th>
						<th class="table-headers">Total</th>
					</tr>
					<?php foreach ($cartData as $cartItem) {
						$item = table_fetch_row('products','id="'.$cartItem['sku'].'"');
						$price = $item['price'];
						$quantity = $cartItem['quantity'];
						$subtotal = ($price * $quantity);
						$Carttotal += ($price * $quantity);
					?>
					<tr>
						<td><?= $item['name'];?></td>
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
						<td>&pound;<?= number_format($deliverycost, 2);?></td>
					</tr>
					<tr>
						<th></th>
						<th></th>
						<th class="table-headers">Grand Total</th>
						<td>&pound;<?= number_format($grandtotal, 2);?></td>
					</tr>
					</table>
					<div class="col-md-12 col-sm-12 col-xs-12 personal-details">
						<h3>Personal Details</h3>
						<table class="table table-striped table-responsive">
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
					<div class="col-md-12 col-sm-12 col-xs-12 personal-details-mob">
						<h3>Personal Details</h3>
						<table class="table table-striped table-responsive">
							<tr>
								<th class="table-headers">Title</th>
								<td><?= $accountData['title'] ;?></td>
							</tr>
							<tr>
								<th class="table-headers">First Name</th>
								<td><?= $accountData['firstname'] ;?></td>
							</tr>
							<tr>
								<th class="table-headers">Surname</th>
								<td><?= $accountData['lastname'] ;?></td>
							</tr>
							<tr>
								<th class="table-headers">Email</th>
								<td><?= $accountData['email'] ;?></td>
							</tr>
							<tr>
								<th class="table-headers">Mobile</th>
								<td><?= $accountData['mobile_number'] ;?></td>
							</tr>
							<tr>
								<th class="table-headers">Telephone</th>
								<td><?= $accountData['telephone_number'] ;?></td>
							</tr>
						</table>
					</div>
					<div class="col-md-6 col-sm-6 col-xs-12 billing-details">
						<h3>Billing Details</h3>
						<table class="table table-striped table-responsive">
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
						<table class="table table-striped table-responsive">
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
					</div>
					<input type="hidden" name="cartTotal" value="<?php echo number_format($grandtotal, 2) ?>" />
				    <input type="hidden" name="stripeToken" id="stripeToken" value="" />
				    <input type="hidden" name="orderid" id="order_id" value="<?= $thisorderid; ?>" />
				    <input type="hidden" name="stripeEmail" id="stripeEmail" value="" />
				    <input type="submit" class="red-btn-proceed pull-right btn-more-details-green-square" id="checkout-now" value="Pay Now" />
				    <script src="https://checkout.stripe.com/checkout.js"></script>
				    <script>
						var handler = StripeCheckout.configure({
						  key: '<?= STRIPE_PUBLIC_KEY ;?>',
						  image: 'https://stripe.com/img/documentation/checkout/marketplace.png',
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
						    name: <?= SITE_NAME; ?>,
						    description: 'Your Order',
						    email: '<?= $email ;?>',
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
				</form>
			</div>
		</div>
	</div>
</section>
</section>
