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
    'voucher_code',
    'voucher_percentage',
    'voucher_discount',
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
                'shipping_total' => $deliverycost,
                'subtotal' => $Carttotal,
                'voucher_code' => @$_SESSION['discount']['code'],
                'voucher_percentage' => @$_SESSION['discount']['percentage_discount'],
                'voucher_discount' => @$_SESSION['discount']['discountAmount'],
                'total' => $grandtotal,
                'status' => 'PENDING',
                'stripe_error' => '',
                // 'stripe_token' => $_POST['stripeToken'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                );

//Check if need to save the card and pay later
if ($pre_auth_required) { 
    \Stripe\Stripe::setApiKey(STRIPE_PRIVATE_KEY);

    $customer = \Stripe\Customer::create([
        'email' => $accountData['email']
    ]);

    array_push($fields, 'stripe_customer_id');
    $values['stripe_customer_id'] = $customer->id;
} 

$thisorderid = table_insert('orders', $fields, $values);
$thisorderdetails = table_fetch_row('orders', 'id="'.$thisorderid.'"');

if ($pre_auth_required) { 

    $stripe = new \Stripe\StripeClient(STRIPE_PRIVATE_KEY);

    $intent = $stripe->setupIntents->create([
        'customer' => $customer->id,
        ['metadata' => ['order_id' => $thisorderid, 'website' => SITE_NAME]]
    ]);
}

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
                    'product_weight_id' => $cartItem['weight_id'],
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
                    if (isset($_SESSION['discount']['grandTotal'])) {
                        $grandtotal = $_SESSION['discount']['grandTotal']; 
                    } else {
                        $grandtotal = $cartTotal;
                    }
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
                        <?php if ($pre_auth_required) { ?>
                            <th class="table-headers">Estimated Sub Total</th>
                        <?php } else { ?>
                            <th class="table-headers">Sub Total</th>
                        <?php } ?>
                        
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
                        <?php if ($pre_auth_required) { ?>
                            <th class="table-headers">Estimated Grand Total</th>
                        <?php } else { ?>
                            <th class="table-headers">Grand Total</th>
                        <?php } ?>


                        <td><strong>&pound;<?= number_format($grandtotal, 2);?></strong><?php if ($collection == 1) { echo '<br>(To be paid on collection)';}?></td>
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
         
        <?php if ($collection == 0) { 
            if (!$pre_auth_required) { ?>
            <div class="row">
            <div class="col-xs-12 col-md-6" style="margin-top: 15px;">
                <h3>Any Order Comments?</h3>
                <p>(Request preferred delivery date etc)</p>
                <textarea class="form-control" rows="10" style="resize: none;" id="customer_comments" name="customer_comments"></textarea>
            </div>
            <div class="col-xs-12 col-md-6" style="margin-top: 15px;">
                        <h3>Debit/Credit Card Details</h3>
                        <!-- Display a payment form -->
                        <form id="payment-form" style="margin-top: 15px;">
                          <div id="card-element"><!--Stripe.js injects the Card Element--></div>
                          <button id="submit" class="btn btn-primary pull-right" style="margin-top: 25px;">
                            <div class="spinner hidden" id="spinner">
                                <div class="sk-chase">
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                  <div class="sk-chase-dot"></div>
                                </div>
                            </div>
                            <span id="button-text">Pay Now</span>
                          </button>
                          <p id="card-error" role="alert"></p>
                          <p class="result-message hidden">
                            Payment succeeded
                          </p>
                        </form>
                  
            <?php } else { ?>
                <div class="row">
                <div class="col-xs-12 col-md-6" style="margin-top: 15px;">
                    <h3>Any Order Comments?</h3>
                    <p>(Request preferred delivery date etc)</p>
                    <textarea class="form-control" rows="10" style="resize: none;" id="customer_comments" name="customer_comments"></textarea>
                </div>
            <div class="col-xs-12 col-md-6" style="margin-top: 15px;">
                        <h3>Debit/Credit Card Details</h3>
                        <p>These details will be saved securely until your order has been prepared and the exact price is known.  We will then process your payment securely, send a confirmation email and delete your card details.</p>
                        <form class="form" id="setup-form" >
                            <div class="form-group">
                                <label>Cardholder name</label>
                                <input class="form-control" id="cardholder-name" type="text" required>
                            </div>
                            <div class="form-group">
                                <label>Card details</label>
                                <div id="card-element"></div>
                            </div>
                            <div class="form-group">
                              <p style="color: #dd2222;"><em>By submitting this order I authorise <?= SITE_NAME;?> to send instructions to the financial institution that issued my card to take payments from my card account in accordance with the <a href="/terms-conditions" style="font-weight: bold;" target="_blank" rel="noopener noreferrer"> terms of my agreement</a> with you.</em></p>
                            
                              <button class="btn btn-primary" id="submit" style="margin-top: 25px;" data-secret="<?= $intent->client_secret ?>">
                                <div class="spinner hidden" id="spinner">
                                    <div class="sk-chase">
                                      <div class="sk-chase-dot"></div>
                                      <div class="sk-chase-dot"></div>
                                      <div class="sk-chase-dot"></div>
                                      <div class="sk-chase-dot"></div>
                                      <div class="sk-chase-dot"></div>
                                      <div class="sk-chase-dot"></div>
                                    </div>
                                </div>
                                <span id="button-text">Save Card and Submit Order</span>
                              </button>
                              <p id="card-error" role="alert"></p>
                              <p class="result-message hidden">
                                Payment succeeded
                              </p>
                            </div>
                        </form>
                   
            <?php } ?>

        <?php } else { 
            //Order to be collected
        ?>
        <div class="row">
            <form method="POST" id="Confirm" action="/processor/process-collection.php">
            <div class="col-xs-12 col-md-6" style="margin-top: 15px;">
                <h3>Any Order Comments?</h3>
                <p>(Request preferred collection date etc)</p>
                <textarea class="form-control" rows="10" style="resize: none;" id="customer_comments" name="customer_comments"></textarea>
            </div>
            <div class="col-xs-12 col-md-6" style="margin-top: 15px;">
            
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                         <input type="submit" class="btn btn-primary pull-right btn-more-details-green-square" id="checkout-now" value="Place Order" />
                    </div>
                </div>
                <input type="hidden" name="cartTotal" value="<?php echo number_format($grandtotal, 2) ?>" />
                <input type="hidden" name="collect" value="1" />
                <input type="hidden" name="orderid" id="order_id" value="<?= $thisorderid; ?>" />
            </form> 
        <?php } ?>
         </div>
    </div>
  </div>
</section>
<script>
    // A reference to Stripe.js initialized with your real test publishable API key.
    var stripe = Stripe("<?= STRIPE_PUBLIC_KEY; ?>");
    var order = {
      id: <?= $thisorderid; ?>
    };
    document.querySelector("#submit").disabled = true;
    <?php if (!$pre_auth_required) { ?>
        
        fetch("/stripe/create.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify(order)
        })
          .then(function(result) {
            return result.json();
          })
          .then(function(data) {
            var elements = stripe.elements();
            var style = {
              base: {
                color: "#32325d",
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                }
              },
              invalid: {
                fontFamily: 'Arial, sans-serif',
                color: "#fa755a",
              }
            };
            var card = elements.create("card", { style: style });
            // Stripe injects an iframe into the DOM
            card.mount("#card-element");
            card.on("change", function (event) {
              // Disable the Pay button if there are no card details in the Element
              document.querySelector("#submit").disabled = event.empty;
              document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
            });
            var form = document.getElementById("payment-form");
            form.addEventListener("submit", function(event) {
              event.preventDefault();
              // Complete payment when the submit button is clicked
              payWithCard(stripe, card, data.clientSecret);
            });
          });
        // Calls stripe.confirmCardPayment
        // If the card requires authentication Stripe shows a pop-up modal to
        // prompt the user to enter authentication details without leaving your page.
        var payWithCard = function(stripe, card, clientSecret) {
          loading(true);
          stripe
            .confirmCardPayment(clientSecret, {
              payment_method: {
                card: card
              }
            })
            .then(function(result) {
              if (result.error) {
                // Show error to your customer
                showError(result.error.message);
              } else {
                // The payment succeeded!
                console.log(result);
                console.table(result);
                result.orderid = <?= $thisorderid; ?>;
                result.comments = $('#customer_comments').val();
                fetch("/processor/process-payment.php", {
                  method: "POST",
                  headers: {
                    "Content-Type": "application/json"
                  },
                  body: JSON.stringify(result)
                })
                .then(function(result2) {
                    return result2.json();
                })
                .then(function(data) {
                    if (data.status == 'ok') {
                        window.location.href = "/order-confirmation?order_id="+data.orderid+"&verification="+data.websitetoken;
                    } else {
                         window.location.href = "/checkout?already-submitted";
                    }
                    
                })
               
               }
            });
        };

    <?php } else { ?>
            var elements = stripe.elements();
            var style = {
              base: {
                color: "#32325d",
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                }
              },
              invalid: {
                fontFamily: 'Arial, sans-serif',
                color: "#fa755a",
              }
            };
            var cardElement = elements.create("card", { style: style });
            cardElement.mount('#card-element');

            
            var cardButton = document.getElementById('submit');
            var clientSecret = cardButton.dataset.secret;
            var cardholderName = document.getElementById('cardholder-name');
            cardElement.on("change", function (event) {
              // Disable the Pay button if there are no card details in the Element
              document.querySelector("#submit").disabled = event.empty;

              if (cardholderName !== null && cardholderName.value === "") {
                document.querySelector("#submit").disabled = true;
              }
              document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
            });
            $('#cardholder-name').on('change', function() {
                if (!($(this).val().length)) {
                    document.querySelector("#submit").disabled = true;
                } else {
                    document.querySelector("#submit").disabled = false;
                }
            })
            
            cardButton.addEventListener('click', function(ev) {
                loading(true);
                ev.preventDefault();
                
                stripe.confirmCardSetup(
                    clientSecret,
                    {
                      payment_method: {
                        card: cardElement,
                        billing_details: {
                          name: cardholderName.value,
                        },
                      },
                    }
                  ).then(function(result) {
                    if (result.error) {
                      // Display error.message in your UI.
                      alert(result.error.message);
                      loading(false);
                    } else {
                      // The setup has succeeded.
                      console.table(result);
                      result.orderid = <?= $thisorderid; ?>;
                      result.comments = $('#customer_comments').val();
                        fetch("/processor/process-card-save.php", {
                          method: "POST",
                          headers: {
                            "Content-Type": "application/json"
                          },
                          body: JSON.stringify(result)
                        })
                        .then(function(result2) {
                            return result2.json();
                        })
                        .then(function(data) {
                            if (data.status == 'ok') {
                                window.location.href = "/order-confirmation?order_id="+data.orderid+"&verification="+data.websitetoken;
                            } else {
                                 window.location.href = "/checkout?already-submitted";
                            }
                            
                        })
                    }
                });
            });

            
    <?php } ?>

    /* ------- UI helpers ------- */
    
    // Show the customer the error from Stripe if their card fails to charge
    var showError = function(errorMsgText) {
      loading(false);
      var errorMsg = document.querySelector("#card-error");
      errorMsg.textContent = errorMsgText;
      setTimeout(function() {
        errorMsg.textContent = "";
      }, 10000);
    };
    // Show a spinner on payment submission
    var loading = function(isLoading) {
      if (isLoading) {
        // Disable the button and show a spinner
        document.querySelector("#submit").disabled = true;
        document.querySelector("#spinner").classList.remove("hidden");
        document.querySelector("#button-text").classList.add("hidden");
      } else {
        document.querySelector("#submit").disabled = false;
        document.querySelector("#spinner").classList.add("hidden");
        document.querySelector("#button-text").classList.remove("hidden");
      }
    };
    
</script>