<section class="probootstrap-section pt-6 pb-6 dark-background hidden-xs">
  <div class="row">
      <div class="col-md-12 pt-3 pb-3">
      </div>
    </div>
</section>
<section class="probootstrap-section  probootstrap-bg-white pt-2 pb-5 basket-page">
  <div class="container">
    <div class="row">
    	<div class="col-xs-12 probootstrap-animate">
	    	<h3>Your Basket</h3>
	    </div>
	</div>
	<div class="row">
    	<div class="col-xs-12 probootstrap-animate">
    		<?php if (isset($_GET['delivery']) && ($_GET['delivery'] == 'unavailable')) { ?>
				<div class="alert alert-danger alert-dismissable">
					<div style="display: table-cell; vertical-align: middle;"><i class="icon-info pr-2"></i></div>
					<div style="display: table-cell; vertical-align: middle;">
						Your Delivery address is out of our delivery radius, please give us a call to discuss this order.
						<?php if (isset($_GET['miles'])) {
							// echo '<br>Your postcode shows that you are x miles away';
						}; ?>
					</div>
				</div>
			<?php } ?>
			<?php if (isset($_GET['delivery']) && ($_GET['delivery'] == 'needmorequantity')) { ?>
				<div class="alert alert-danger alert-dismissable">
					<div style="display: table-cell; vertical-align: middle;"><i class="icon-info pr-2"></i></div>
					<div style="display: table-cell; vertical-align: middle;">
						Your delivery location requires a minimum order amount of £xx.xx. Please add more items to continue or choose to collect instead.
					</div>
				</div>
			<?php } ?>
			<?php if (isset($_GET['delivery']) && ($_GET['delivery'] == 'postcodenotrecognised')) { ?>
				<div class="alert alert-danger alert-dismissable">
					<div style="display: table-cell; vertical-align: middle;"><i class="icon-info pr-2"></i></div>
					<div style="display: table-cell; vertical-align: middle;">
						We're sorry, there was a problem calculating a delivery distance from your delivery postcode <?= urldecode($_GET['postcode']) ;?>.<br>Please check this is a valid postcode and try again. Otherwise please give us a call to discuss this order.
					</div>
				</div>
			<?php } ?>
			<?php if (isset($_GET['stocklevel']) && ($_GET['stocklevel'] == 'none')) { ?>
				<div class="alert alert-danger alert-dismissable">
					<div style="display: table-cell; vertical-align: middle;"><i class="icon-info pr-2"></i></div>
					<div style="display: table-cell; vertical-align: middle;">
						Sorry, there's not enough stock to add this item to the basket. Please <a href="#" onclick="history.go(-1);return false;">go back</a> and try again.
					</div>
				</div>

			<?php } ?>
			<?php if (isset($_GET['stocklevel']) && ($_GET['stocklevel'] == 'notenough')) { ?>
				<div class="alert alert-danger alert-dismissable">
					<div style="display: table-cell; vertical-align: middle;"><i class="icon-info pr-2"></i></div>
					<div style="display: table-cell; vertical-align: middle;">
					Sorry there wasn't enough stock to add your desired quantity. We've added the available amount to your basket.
					</div>
				</div>

			<?php } ?>
			<?php 

				if ($pre_auth_required) { ?>

					
					<div class="alert alert-info alert-dismissable">
						<div style="display: table-cell; vertical-align: middle;"><i class="icon-info pr-2"></i></div>
						<div style="display: table-cell; vertical-align: middle;">This order contains an item where the actual price may vary slightly once the item has been prepared.<br><strong><em>At the payment stage we will ask for your debit/credit card details (We only save an encrypted token on our servers and not your actual card details) and take the payment once the exact amount is known.</em></strong><br>
						You will then receive a confirmation email of the order with the final payment amount for your records.</div>
					</div>
						 
					<?php 
				}

			?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 probootstrap-animate">
			<?php if (count($cartItems) > 0) {
				echo '<ul class="basket-items">';
				$total = 0;
				//$postagecosts = 0;
				$cantpost = false;
				$collection = false;
			    foreach ($cartItems as $item) {
			    	$itemfulldetails = table_fetch_row('products','id="'.$item->sku.'"');
			    	if ($itemfulldetails['collection'] == '1') {
			    	 	$collection = true;
			    	}
			    	$itemtotal = (($item->price + $item->tax) * $item->quantity);
			    	$itemtotalWithoutTax = ($item->price * $item->quantity);
			    	$itemTax = ($item->tax);
			    	$total += $itemtotal;


			    	
			    	
			    	$image = get_image('product/' . $item->sku);
					if (strlen($image) > 0):
						$path = $image;
					else: 
						$path = 'assets/theme/img/placeholder.jpg';
					endif;
			       ?>
			       <div class="row pt-2 pb-2" style="border-bottom: 1px solid #F0F1DB;">
			        	<div class="col-md-4 col-sm-4 col-xs-12 ">
			        		<label><?= $item->name; ?></label>
			        		<img src="<?= $path; ?>"  style="height: 50px !important;" class="img-responsive cart-thumbnail mt-1 mb-1" alt="<?= $item->name; ?>" />
			        	</div> 
			        		
			        	<div class="col-md-4 col-sm-4 col-xs-12 text-right">
							
							<?php if (!isset($item->weight_id)) { ?>
								<form method="POST" action="basket">
									<div class="form-group">
										<label>QTY</label>
										<input type="number" name="qty" class="pull-right text-center mb-1" style="width: 100px;" value="<?= $item->quantity; ?>" />
									
										<input name="product-id" type="hidden" value="<?= $item->sku; ?>" />
										<?php if (isset($item->weight) && strlen($item->weight) > 0) {
											?>
											<input name="weight" type="hidden" value="<?= $item->weight; ?>" />

											<?php
										}
										?>
									
										<input type="submit" name="update-product-quantity" class="mt-1 mb-1 btn btn-secondary pull-right mr-2" value="update qty" />
									</div>
								</form>
							<?php }  ?>
						</div>
						<div class="col-md-4 col-sm-4 col-xs-12 text-right">
							<label>TOTAL</label>
							<p class="price text-right mb-3 mt-2 d-block">&pound;<?= number_format($itemtotal,2); ?></p>
							<p class="text-right mb-1 d-block"><a class="btn btn-danger" href="?remove-item=<?= $item->id; ?>">Remove <i class="icon-times"></i></a></p>
						</div>
	
					</div>
			       <?php
			    }
			    echo '</ul>';
			} else {
				echo '<p style="font-size: 20px;">You have no items in your basket. <a href="/#buy" style="color: #fff; text-decoration: none">Continue shopping.</a></p>';
			} ?>  
		</div>

	</div>
	<div class="row">
		<div class="col-xs-12 probootstrap-animate text-right mt-3 mb-1">
			<label>Have a Discount Code?</label>
			<div id="discount-applied" style="color: #22dd22;"><?php echo(isset($_SESSION['discount']['code']) ? $_SESSION['discount']['code'].' applied' : ''); ?></div>
			<input type="text" id="voucher_code" name="voucher_code" class="text-right pull-right mb-1" style="width: 250px;" placeholder="Enter Discount Code" value="<?php echo(isset($_SESSION['discount']['code']) ? $_SESSION['discount']['code'] : ''); ?>" /><br>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 probootstrap-animate text-right mt-1 mb-3">
			<?php if(isset($_SESSION['discount']['code'])) { ?>
				<a href="#" id="remove_discount" class="btn btn-danger pull-right"><small>x</small> Remove Discount Code</a>
			<?php } else { ?>
				<a href="#" id="apply_discount" class="btn btn-primary pull-right">Apply Discount Code</a>
			<?php } ?>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 probootstrap-animate basket-confirm ">
			<?php if (count($cartItems) > 0) {
				$grandTotal = $cartTotal;
				if (isset($_SESSION['discount']['fixed_discount'])) {
					$fixed_discount = $_SESSION['discount']['fixed_discount'];
					if (($grandTotal - $fixed_discount) < 1) {
						$discountAmount = ($grandTotal - 1);
						$grandTotal = 1;
					} else {
						$discountAmount = $fixed_discount;
						$grandTotal = $grandTotal - $fixed_discount;
					}
					$_SESSION['discount']['grandTotal'] = $grandTotal;
					$_SESSION['discount']['discountAmount'] = $discountAmount;
				}
				if (isset($_SESSION['discount']['percentage_discount'])) {
					$percentage_discount = (($grandTotal / 100) * $_SESSION['discount']['percentage_discount']);
					if (($grandTotal - $percentage_discount) < 1) {
						$discountAmount = ($grandTotal - 1);
						$grandTotal = 1;

					} else {
						$discountAmount = $percentage_discount;
						$grandTotal = $grandTotal - $percentage_discount;
					}
					$_SESSION['discount']['grandTotal'] = $grandTotal;
					$_SESSION['discount']['discountAmount'] = $discountAmount;
					
				}
				if (isset($discountAmount)) { ?>
					<p class="blue-name text-right"><strong>Discount Applied: - &pound;<?= number_format($discountAmount,2); ?></strong></p>
				<?php }
			 ?>
				<p class="blue-name text-right"><strong>Subtotal: &pound;<?= number_format($grandTotal,2); ?></strong></p>
				<p class="blue-name text-right">Your shipping costs will be calculated based on your shipping delivery details.</p>
				<a href="/checkout?guest-details" style="width: auto;"  class="btn btn-primary pull-right pl-3 pr-3 ml-3">PROCEED TO CHECKOUT</a>
				<a href="/#buy"  style="width: auto;" class="btn btn-primary pull-right pl-3 pr-3">CONTINUE SHOPPING</a>

			<?php } ?>
		</div>
	</div>
  </div>
</section>

