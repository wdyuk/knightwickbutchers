<?php 
$messages = array();
    
    $table_id = get_id();
    
    if(isset($_POST['save']))
    {
        $fields = array('title','firstname','lastname','email','mobile_number','telephone_number','billing_address_line_1','billing_address_line_2','billing_town','billing_postcode','shipping_address_line_1','shipping_address_line_2','shipping_town','shipping_postcode','status','order_status','comments','customer_comments','delivery_collection_date');
        
        if($_POST['id'] > 0) {
            $check = table_fetch_row('orders','id="'.$table_id.'"');
            if (isset($_POST['voucher_discount']) && strlen($_POST['voucher_discount']) > 0 && is_numeric($_POST['voucher_discount']) && ($check['voucher_discount'] != $_POST['voucher_discount'])) {
  
                array_push($fields, 'voucher_code','voucher_discount','voucher_percentage','total');
                $voucher_discount = (float) $_POST['voucher_discount'];

                var_dump($voucher_discount);
                var_dump($_POST['total']);
                if (isset($check['voucher_discount']) && is_numeric($check['voucher_discount'])) {
                    if ( $voucher_discount > $check['voucher_discount'] ) {
                        $_POST['total'] = ($check['total'] - $voucher_discount);
                    } else {
                        $_POST['total'] = ($check['total'] + ($check['voucher_discount'] - $voucher_discount));
                    }
                } else {
                    $_POST['total'] = ($check['total'] - $voucher_discount);
                }
                var_dump($_POST['total']);
                $_POST['comments'] .= "<br>Amended discount to £".$voucher_discount.' on '.date('d/m/Y H:i');
                if (isset($check['voucher_code'])) {
                     $_POST['comments'] .= "<br>Old Voucher Code was ".$check['voucher_code'];
                }
                if (isset($check['voucher_percentage'])) {
                     $_POST['comments'] .= "<br>Old Voucher Percentage was ".$check['voucher_percentage'];
                }
                if (isset($check['voucher_discount'])) {
                     $_POST['comments'] .= "<br>Old Voucher Discount was ".$check['voucher_discount'];
                }
                $_POST['comments'] .= "<br>********************************";
               

                $_POST['voucher_percentage'] = NULL;
                $_POST['voucher_id'] = NULL;
                $_POST['voucher_code'] = 'ADMIN OVERRIDE';
                
            }
        }
        
        if (isset($_POST['delivery_collection_date']) && strlen($_POST['delivery_collection_date']) > 0) {
            $date = explode('/',$_POST['delivery_collection_date']);
            $_POST['delivery_collection_date'] = $date[2].'-'.$date[1].'-'.$date[0];
        } else {
            $_POST['delivery_collection_date'] = null;
        }
        if($_POST['id'] == 0) 
        {
            $table_id = table_insert('orders', $fields, $_POST);
            $messages[] = 'Saved successfully.';
        }
        else 
        {
            table_update('orders', $fields, $_POST, 'id=' . get_id());
            $messages[] = 'Saved successfully.';
        }   

    }

    if(!empty($table_id)) {
        $thisorder = table_fetch_row('orders','id="'.$table_id.'"');
        if (isset($thisorder['delivery_collection_date']) && strlen($thisorder['delivery_collection_date']) > 0) {
            $date = explode('-',$thisorder['delivery_collection_date']);
            $thisorder['delivery_collection_date'] = $date[2].'/'.$date[1].'/'.$date[0];
        }
        if ((isset($thisorder['status']) && $thisorder['status'] == "CARD AUTHORISED")) {

            $stripe = new \Stripe\StripeClient(
              STRIPE_PRIVATE_KEY
            );
            $paymentmethods = $stripe->paymentMethods->all([
              'customer' => $thisorder['stripe_customer_id'],
              'type' => 'card',
            ]);
            
            
        }

    }
$thisorderitems = table_fetch_rows('order_items','order_id="'.$table_id.'"');

?>
<form class="validate-form" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?php echo isset($thisorder['id']) ? $thisorder['id'] : 0 ; ?>" />
    <div class="row">
        <div class="col-12"><h2>Order Details: #<?= $thisorder['id']; ?></h2></div>
    </div> 
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-responsive w-100">
            <tr>
                <td colspan="4"><?php show_messages($messages); ?></td>
            </tr>
            <?php if(isset($thisorder['email_sent'])) { ?>
                <div class="row">
                    <div class="col-12">
                        <h5>Last confirmation email sent:</h5>
                        <p><?= date('d/m/Y H:i', strtotime($thisorder['email_sent'])); ?></p>
                    </div>
                </div>
            <?php } ?>
            <tr><th>Item</th><th>Price per item</th><th>Quantity</th><th>Total</th></tr>
            <?php 
            $total = 0;
            $linetotal = 0;
            foreach ($thisorderitems as $orderitem) {
               
                $product = table_fetch_row('products','id="'.$orderitem['product_id'].'"');
                $price = $orderitem['price'];

                $quantity = $orderitem['quantity'];

                //echo $quantity.'<br>';
                $linetotal = ($price * $quantity);
                $total += ($price * $quantity);

                ?>
                <?php if ((isset($thisorder['status']) && $thisorder['status'] == "CARD AUTHORISED") && $product['type'] == "totalweight") { ?>
                    <tr><td><?= $product['name']; ?><br><small>&pound;<?= number_format($product['price_per_kg'],2); ?> per kg</small></td><td>&pound;<input type="text" class="lineitemprice" data-quantity="<?= $quantity; ?>" data-item-id="<?= $orderitem['id']; ?>" name="lineitem[$orderitem['id']" value="<?= number_format($price,2); ?>" /><br><small>Or enter total order weight in kg</small><br><input type="text" class="lineitemweight" data-quantity="<?= $quantity; ?>" data-item-id="<?= $orderitem['id']; ?>" data-price-per-kg="<?= $product['price_per_kg']; ?>" name="lineitem[$orderitem['id']" value="" /></td><td><?= $quantity; ?></td><td>&pound;<span class="linetotal" data-item-id="<?= $orderitem['id']; ?>"><?= number_format($linetotal,2); ?></span></td></tr>
                <?php } else { ?>
                    <input type="hidden" class="lineitemprice" data-quantity="<?= $quantity; ?>" data-item-id="<?= $orderitem['id']; ?>" name="lineitem[$orderitem['id']" value="<?= number_format($price,2); ?>" />
                    <tr><td><?= $product['name']; ?></td><td>&pound;<?= number_format($price,2); ?></td><td><?= $quantity; ?></td><td>&pound;<span class="linetotal" data-item-id="<?= $orderitem['id']; ?>"><?= number_format($linetotal,2); ?></span></td></tr>
                <?php } ?>
            <?php
            }
                if($thisorder['shipping_total'] > 0) { ?>
                     <tr><td>Shipping: </td><td></td><td></td><td>&pound;<?= number_format($thisorder['shipping_total'],2); ?></td></tr>
                    <?php
                    $total += $thisorder['shipping_total'];
                }

            ?>
            <tr><th></th><th></th><th></th><th>Sub Total</th></tr>
            <tr><td></td><td></td><td></td><td>&pound;<span id="sub_total"><?= number_format($total,2); ?></span></td></tr>
            <tr><th colspan="3">Voucher Code</th><th>Discount</th></tr>
            <tr><td colspan="3"><?= $thisorder['voucher_code']; ?></td><td>£<input class="form-control" name="voucher_discount" id="voucher_discount" size="20" type="text" value="<?php echo isset($thisorder['voucher_discount']) ? $thisorder['voucher_discount'] : ''; ?>" /></td>
                <?php if (isset($thisorder['voucher_discount']) && is_numeric($thisorder['voucher_discount'])) {
                    $total -= $thisorder['voucher_discount'];
                } ?>
            <tr><th></th><th></th><th></th><th>Grand Total</th></tr>
            <tr><td></td><td></td><td></td><td>&pound;<span id="grand_total"><?= number_format($total,2); ?></span></td></tr>
            <?php if ((isset($thisorder['status']) && $thisorder['status'] == "CARD AUTHORISED")) { ?>
                 <tr><td></td><td></td>
                    <th>Card to charge:</th>
                    <td><?= strtoupper($paymentmethods->data[0]['card']['brand']);?> ....<?= $paymentmethods->data[0]['card']['last4']; ?> EXP <?= str_pad($paymentmethods->data[0]['card']['exp_month'],2,"0", STR_PAD_LEFT);?>/<?= $paymentmethods->data[0]['card']['exp_year'];?></td>
                </tr>
                <tr>
                    <td></td><td></td>
                    <td>
                        <button class="update-price-button btn btn-warning">Update Item Prices</button>
                    </td>
                    <td>
                        <button class="charge-customer-button btn btn-primary">Charge Customer £<span id="charge_price"><?= number_format($total,2); ?></span></button>
                    </td>
                </tr>
            <?php } ?>
            <tr class="my-5"><th>Order Comments (Internal)</th><th colspan="3"></th></tr>
            <tr><td colspan="4"><?php show_fckeditor('comments', isset($thisorder['comments']) ? $thisorder['comments'] : '' ); ?></td></tr>

            <tr class="my-5"><th>Order Comments (From Customer)</th><th colspan="3"></th></tr>
            <tr><td colspan="4"><?php show_fckeditor('customer_comments', isset($thisorder['customer_comments']) ? $thisorder['customer_comments'] : '' ); ?></td></tr>
            </table>
            
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="page_title">Delivery/Collection Date:</label>
                        <input class="form-control datepicker" name="delivery_collection_date" id="delivery_collection_date" size="100" type="text" value="<?php echo isset($thisorder['delivery_collection_date']) ? $thisorder['delivery_collection_date'] : ''; ?>" />
                    </div>
                </div>
            </div>
            <table class="table table-striped table-responsive" style="width: 100%;">
                <tr><td colspan="2"><h2>Shipping Details</h2></td></tr>
                <tr>
                    <th>Address Line 1</th>
                    <td><input class="form-control" name="shipping_address_line_1" id="shipping_address_line_1" size="100" type="text" value="<?php echo isset($thisorder['shipping_address_line_1']) ? $thisorder['shipping_address_line_1'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Address Line 2</th>
                    <td><input class="form-control" name="shipping_address_line_2" id="shipping_address_line_2" size="100" type="text" value="<?php echo isset($thisorder['shipping_address_line_2']) ? $thisorder['shipping_address_line_2'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Town</th>
                    <td><input class="form-control" name="shipping_town" id="shipping_town" size="100" type="text" value="<?php echo isset($thisorder['shipping_town']) ? $thisorder['shipping_town'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Postcode</th>
                    <td><input class="form-control" name="shipping_postcode" id="shipping_postcode" size="100" type="text" value="<?php echo isset($thisorder['shipping_postcode']) ? $thisorder['shipping_postcode'] : ''; ?>" /></td>
                </tr>
            </table>
            
            <table class="table table-striped table-responsive w-100 my-5">
                <tr><td colspan="7"><h2>Payment Details</h2></td></tr>
                <tr><th>Status</th><th>Stripe Customer ID</th><th>Stripe Setup Intent</th><th>Stripe Payment Method</th><th>Stripe Payment Intent</th><th>Stripe Error</th><th>Created at</th></tr>
                <tr>
                    <td>
                        <select name="status">
                            <option value="PAID" <?php echo (isset($thisorder['status']) && $thisorder['status'] == "PAID") ? 'selected="selected"' : ''; ?> >PAID</option>
                            <option value="PLACED FOR COLLECTION" <?php echo (isset($thisorder['status']) && $thisorder['status'] == "PLACED FOR COLLECTION") ? 'selected="selected"' : ''; ?> >PLACED FOR COLLECTION</option>
                            <option value="CARD AUTHORISED" <?php echo (isset($thisorder['status']) && $thisorder['status'] == "CARD AUTHORISED") ? 'selected="selected"' : ''; ?> >CARD AUTHORISED</option>
                            <option value="REFUNDED" <?php echo (isset($thisorder['status']) && $thisorder['status'] == "REFUNDED") ? 'selected="selected"' : ''; ?>>REFUNDED</option>
                            <option value="CANCELLED" <?php echo (isset($thisorder['status']) && $thisorder['status'] == "CANCELLED") ? 'selected="selected"' : ''; ?>>CANCELLED</option>
                            <option value="PENDING" <?php echo (isset($thisorder['status']) && $thisorder['status'] == "PENDING") ? 'selected="selected"' : ''; ?> >PENDING</option>
                        </select>
                    </td>
                    <td><?= $thisorder['stripe_customer_id'] ;?></td>
                    <td><?= $thisorder['stripe_setup_intent'] ;?></td>
                    <td><?= $thisorder['stripe_payment_intent'] ;?></td>
                    <td><?= $thisorder['stripe_payment_method'] ;?></td>
                    <td><?php if(strlen($thisorder['stripe_error']) > 0) { echo $thisorder['stripe_error']; } else { echo '--NONE--'; } ;?></td><td><?= date('d/m/Y H:i:s', strtotime($thisorder['created_at'])) ;?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <h2>Order Status</h2>
            <div class="form-group">
                <label for="order_status">Mark order as fulfilled or shipped to keep track of progress</label>
                <select name="order_status" class="form-control">
                    <option value="AWAITING" <?php echo (isset($thisorder['order_status']) && $thisorder['order_status'] == "AWAITING") ? 'selected="selected"' : ''; ?> >AWAITING</option>
                    <option value="FULFILLED" <?php echo (isset($thisorder['order_status']) && $thisorder['order_status'] == "FULFILLED") ? 'selected="selected"' : ''; ?> >FULFILLED</option>
                    <option value="SHIPPED" <?php echo (isset($thisorder['order_status']) && $thisorder['order_status'] == "SHIPPED") ? 'selected="selected"' : ''; ?> >SHIPPED</option>
                    <option value="COLLECTED" <?php echo (isset($thisorder['order_status']) && $thisorder['order_status'] == "COLLECTED") ? 'selected="selected"' : ''; ?> >COLLECTED</option>
                    
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <table class="w-100 my-4">
                <tr><td colspan="2"><h2>Customer &amp; Billing Details</h2></td></tr>
                <?php if ($thisorder['account_no'] > 0):?>
                <tr>
                <td colspan="2"><a href="">Account Details</a></td> 
                </tr>
                <?php endif;?>
                <tr>
                    <th>Title</th>
                    <td><input class="form-control" name="title" id="title" size="100" type="text" value="<?php echo isset($thisorder['title']) ? $thisorder['title'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>First Name</th>
                    <td><input class="form-control" name="firstname" id="firstname" size="100" type="text" value="<?php echo isset($thisorder['firstname']) ? $thisorder['firstname'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td><input class="form-control" name="lastname" id="lastname" size="100" type="text" value="<?php echo isset($thisorder['lastname']) ? $thisorder['lastname'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Address Line 1</th>
                    <td><input class="form-control" name="billing_address_line_1" id="billing_address_line_1" size="100" type="text" value="<?php echo isset($thisorder['billing_address_line_1']) ? $thisorder['billing_address_line_1'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Address Line 2</th>
                    <td><input class="form-control" name="billing_address_line_2" id="billing_address_line_2" size="100" type="text" value="<?php echo isset($thisorder['billing_address_line_2']) ? $thisorder['billing_address_line_2'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Town</th>
                    <td><input class="form-control" name="billing_town" id="billing_town" size="100" type="text" value="<?php echo isset($thisorder['billing_town']) ? $thisorder['billing_town'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Postcode</th>
                    <td><input class="form-control" name="billing_postcode" id="billing_postcode" size="100" type="text" value="<?php echo isset($thisorder['billing_postcode']) ? $thisorder['billing_postcode'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><input class="form-control" name="email" id="email" size="100" type="text" value="<?php echo isset($thisorder['email']) ? $thisorder['email'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Mobile Number</th>
                    <td><input class="form-control" name="mobile_number" id="mobile_number" size="100" type="text" value="<?php echo isset($thisorder['mobile_number']) ? $thisorder['mobile_number'] : ''; ?>" /></td>
                </tr>
                <tr>
                    <th>Telephone Number</th>
                    <td><input class="form-control" name="telephone_number" id="telephone_number" size="100" type="text" value="<?php echo isset($thisorder['telephone_number']) ? $thisorder['telephone_number'] : ''; ?>" /></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td colspan="2">
                        <?php show_big_button('save', 'Save'); ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</form>

<script>

    $(function() {
        $('.update-price-button').hide();
        function updateLinePrices() {
            var grandTotal = 0;
            var shippingCost = parseFloat(<?= $thisorder['shipping_total']; ?>);
            var voucherDiscount = parseFloat(<?= $thisorder['voucher_discount']; ?>);

            $('.lineitemprice').each(function() {
                
                var itemId = $(this).data('item-id');
                var itemQuantity = $(this).data('quantity');
                var price = parseFloat($(this).val());
                var total = parseFloat(price * itemQuantity).toFixed(2);
                grandTotal += parseFloat(total);
                console.log('Total: '+total+' price: '+price+' grandTotal: '+grandTotal);
            });
            grandTotal = parseFloat((grandTotal + shippingCost) - voucherDiscount).toFixed(2);
            $('#grand_total, #charge_price').html(grandTotal);
           

        }
        $('.lineitemprice').on('change', function() {
            var itemId = $(this).data('item-id');
            var itemQuantity = $(this).data('quantity');
            var price = parseFloat($(this).val());
            var total = parseFloat(price * itemQuantity).toFixed(2);
            $('.linetotal[data-item-id="'+itemId+'"]').html(total);
            $('.update-price-button').show();
            $('.charge-customer-button').attr('disabled', true);
            updateLinePrices();

        });
        $('.lineitemweight').on('change', function() {
            var weight = $(this).val();
            var price_per_kg = $(this).data('price-per-kg');
            var quantity = $(this).data('quantity');
            var price = parseFloat((weight * price_per_kg) / quantity);
            var new_price = price.toFixed(2);
           
            var element = $(this).closest('td').find('.lineitemprice');
            element.val(new_price);
            $('.lineitemprice').change();
        });

        $('.update-price-button').on('click', function(e) {
            e.preventDefault();
            updateLinePrices();
            var newPrices = [];
            var order_id = <?= $table_id; ?>;
            $('.lineitemprice').each(function() {
                
                var itemId = $(this).data('item-id');
                var itemQuantity = $(this).data('quantity');
                var price = parseFloat($(this).val());
                var total = parseFloat(price * itemQuantity).toFixed(2);

                newPrice = {};
                newPrice['id'] = itemId;
                newPrice['price'] = price;
                newPrice['total'] = total;
                newPrices.push(JSON.stringify(newPrice));
               
            });
             $.ajax({
                url: "./ajax/update-order-items.php",
                type: "POST",
                dataType: 'json',
                data: {
                    'newPrices[]': newPrices,
                    'order_id' : order_id
                },
                success: function (data) {
                   
                    if (data.status == 'ok') {
                        alert('Price changes saved');
                        $('.update-price-button').hide();
                        $('.charge-customer-button').removeAttr('disabled');
                        $('#grand_total').html(data.grandtotal);
                        $('#sub_total').html(data.subtotal);
                        $('#charge_price').html(data.grandtotal);
                    } else {
                        alert(data.message);
                    }
                    
                },  
                error: function () {
                    alert('Something went wrong and items didn\'t save');
                }
            });


        });
        $('.charge-customer-button').on('click', function(e) {
            e.preventDefault();
            $('.charge-customer-button').attr('disabled', true);
            var order_id = <?= $thisorder['id']; ?>;
             $.ajax({
                url: "./ajax/stripe-charge-card.php",
                type: "POST",
                dataType: 'json',
                data: {
                    order_id: order_id
                },
                success: function (data) {
                   
                    if (data.status == 'ok') {
                        alert('Customer charged succesfully');
                        $('.update-price-button').hide();
                        $('.charge-customer-button').hide();
                        window.location.href = "<?= BASE_URL; ?>cms_wdy/control-panel.php?module=orders&action=form&id="+order_id;
                    } else {
                        alert(data.message);
                        if (data.status == 'stripefailed') {
                            console.log(data);
                            //deal with fails and requests for authentication
                            //TODO
                        }
                    }
                    
                },  
                error: function () {
                    alert('Something went wrong the customer wasn\'t charged');
                }
            });


        });
        $('.datepicker').datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });
</script>