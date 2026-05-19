<?php

	$_SESSION['guest'] = array_map("sanitize_sql_string",$_POST);
	$accountData = $_SESSION['guest'];
    $preferredDateLabel = (!empty($accountData['collect']) && (int) $accountData['collect'] === 1) ? 'Collection Date' : 'Shipping Date';
    $preferredDateMin = get_preferred_fulfilment_min_date();
    $preferredDateValue = isset($accountData['delivery_collection_date']) ? $accountData['delivery_collection_date'] : '';
    $preferredDateDisplay = preferred_fulfilment_date_display($preferredDateValue);
    $collectionText = str_replace('Please state your collection date on the next page.', '', $store_settings['company_collect_text']);

?>
<link rel="stylesheet" href="/cms_wdy/resources/js/lib/jquery-ui/jquery-ui.min.css">
<style>
    .preferred-date-field label {
        display: block;
        font-weight: 700;
        color: #000;
        margin-bottom: 8px;
    }

    .preferred-date-note {
        margin-bottom: 16px;
    }
</style>
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
            <h3>Shipping Details</h3>
            <p>Once you have placed your order with us a member of the team will contact you to arrange a suitable delivery date and time.</p>
            <?= $pageData['content']; ?>
            <?= $pageData['content_2']; ?>
            <?php if (isset($deliveryDateError) && strlen($deliveryDateError) > 0): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($deliveryDateError, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php 

				if ($pre_auth_required) { ?>

					
					<div class="alert alert-info alert-dismissable">
						<div style="display: table-cell; vertical-align: middle;"><i class="icon-info pr-2"></i></div>
						<div style="display: table-cell; vertical-align: middle;">This order contains an item where the actual price may vary slightly once the item has been prepared.<br><strong><em>If you choose delivery rather than collection, at the payment stage we will ask for your debit/credit card details (We only save an encrypted token on our servers and not your actual card details) and take the payment once the exact amount is known.</em></strong><br>
						You will then receive a confirmation email of the order with the final payment amount for your records.</div>
					</div>
						 
					<?php 
				}

			?>
        </div>
    </div>
    <div class="row">
        <div class="col-12 probootstrap-animate">	
					
			<form method="POST" action="/checkout?payment">
				
				<div class="row">
                    <div class="col-sm-12 contact-form-fields mb-4 preferred-date-field">
                        <label for="preferred_fulfilment_date" id="preferred-fulfilment-label"><?= $preferredDateLabel; ?> *</label>
                        <input
                            type="text"
                            name="delivery_collection_date"
                            id="delivery_collection_date"
                            value="<?= htmlspecialchars($preferredDateDisplay, ENT_QUOTES, 'UTF-8'); ?>"
                            class="form-control-contact"
                            placeholder="Select a date"
                            autocomplete="off"
                            required
                        >
                        <p class="preferred-date-note"><em>We do not allow Sunday or Monday selections. Same-day is not available, and next-day selection is only available before 8pm.</em></p>
                    </div>
					<div class="col-md-5 col-sm-12 deliver-side">
						<h2>Delivery</h2>
						<p class="shipping-address mb-1">Click the checkbox below if your shipping address is the same as your billing address </p>
						<p class="mb-2"><input type="checkbox" class="form-control checkbox" name="same-address" id="same-address" value="1" style="display: inline-block;"></p>

						<div class="row">
							<div class="col-md-12 col-sm-12 contact-form-fields mb-4">
								<input type="text" name="shipping_address_line_1" id="shipping_address_line_1" value="<?php echo isset($accountData['shipping_address_line_1']) ? $accountData['shipping_address_line_1'] : ''; ?>" size="50"  class="form-control-contact required-field" required><span class="floating-label">Address Line 1</span>
							</div>
							<div class="col-md-12 col-sm-12 contact-form-fields mb-4">
								<input type="text" name="shipping_address_line_2" id="shipping_address_line_2" value="<?php echo isset($accountData['shipping_address_line_2']) ? $accountData['shipping_address_line_2'] : ''; ?>" size="50"  class="form-control-contact"><span class="floating-label">Address Line 2</span>
							</div>
								<div class="col-md-12 col-sm-12 contact-form-fields mb-4">
								<input type="text" name="shipping_town" id="shipping_town" value="<?php echo isset($accountData['shipping_town']) ? $accountData['shipping_town'] : ''; ?>" size="50" class="form-control-contact required-field" required><span class="floating-label">Town</span>
							</div>
							<div class="col-md-12 col-sm-12 contact-form-fields mb-4">
								<input type="text" name="shipping_postcode" id="shipping_postcode" value="<?php echo isset($accountData['shipping_postcode']) ? $accountData['shipping_postcode'] : ''; ?>" size="50"  class="form-control-contact required-field" required><span class="floating-label">Postcode</span>
							</div>
							<!-- <div class="col-md-12 col-sm-12 contact-form-fields mb-2">
								<select name="shipping_zone" class="filter-input">
					    			<option value="1">United Kingdom</option>
					    			<option value="2">Islands &amp; Highlands</option>
					    			<option value="3">Ireland & Northern Ireland</option>
					    			<option value="4">Rest of the World</option>
							</div> -->
							
						</div>
						<p class="mb-2"><em>Please make sure that your delivery address falls within our delivery areas, otherwise we may not be able to deliver your order.</em></p>
						<p class="mb-2">Hover over or press the map below to zoom in.</p>
						<div class="text-center"><img src="/assets/theme/img/delivery.jpg" id="delivery-zoom" style="width: 300px; height: auto;" class="section-image" data-zoom-image="/assets/theme/img/delivery-large.jpg"/></div>
						
					</div>
					<div class="col-md-2 col-sm-12 middle-or">
						<h2>OR</h2>
					</div>
					<div class="col-md-5 col-sm-12 collect-side">
						<h2>Collection</h2>
						<p class="collect-order mb-1">Click the checkbox below if you would prefer to collect your order from us.</p>
						<p class="mb-2"><input type="checkbox" name="collect-order" class="form-control checkbox" id="collect-order" value="1" style="display: inline-block;"></p>
						<p class="collect-order mb-1 text-left"><?= $collectionText; ?></strong></p>
						
						<p><?= $store_settings['company_collect_address']; ?></p>
						<?= $store_settings['embed_collection_map']; ?>
					</div>
					
				</div>
				<div class="row">
					
					<div class="col-sm-12 contact-form-fields mb-2 text-center">
						<input type="hidden" name="payment" value="1" />
						<input type="hidden" id="collect-value" name="collect" value="0" />
						<input type="submit" id="proceed-submit" class="btn btn-primary text-center mt-3" value="Proceed to Payment" />
							
					</div>
					
				</div>
			</form>
			<!-- <div class="col-md-6 col-sm-12 contact-form-fields mb-2">
				<button class="btn btn-primary" data-loading-text="Fetching..." id="get-button">Get Delivery Cost</button>
			</div>
			<div class="col-md-6 col-sm-12 contact-form-fields mb-2">
				<div id="delivery_cost"></div>
			</div> -->
				
		</div>
	  </div>
	</div>
</section>
    <script src="/cms_wdy/resources/js/lib/jquery-ui/jquery-ui.min.js"></script>
	<script>
		var formFields = $('.contact-form-fields');
  
		 formFields.each(function() {
		    var field = $(this);
		    var input = field.find('input');
		    var label = field.find('span.floating-label');
		    
		    function checkInput() {
		      var valueLength = input.val().length;
		      
		      if (valueLength > 0 ) {
		        label.addClass('freeze')
		      } else {
		        label.removeClass('freeze')
		      }
		    }
		    
		    input.change(function() {
		      checkInput()
		    })
		 });
		$(function() {
            var preferredDateInput = $('#delivery_collection_date');
            var preferredDateLabel = $('#preferred-fulfilment-label');
            var minDate = new Date('<?= $preferredDateMin->format('Y-m-d'); ?>T00:00:00');

            preferredDateInput.datepicker({
                dateFormat: 'dd/mm/yy',
                minDate: minDate,
                beforeShowDay: function(date) {
                    var day = date.getDay();
                    var normalized = new Date(date.getFullYear(), date.getMonth(), date.getDate());

                    if (day === 0 || day === 1) {
                        return [false, '', 'Unavailable'];
                    }

                    if (normalized < minDate) {
                        return [false, '', 'Unavailable'];
                    }

                    return [true, '', 'Available'];
                }
            });

            if (preferredDateInput.val().length > 0) {
                preferredDateInput.datepicker('setDate', preferredDateInput.val());
            }
			
			$('.check-map').on( 'click', function(e) {
				e.preventDefault();
				$('.delivery-radius-map').show();
			});
			$('#same-address').change( function() {
				if ($(this).prop('checked')) {
					$('#shipping_address_line_1').val('<?= $accountData['address_line_1'] ;?>');
					$('#shipping_address_line_2').val('<?= $accountData['address_line_2'] ;?>');
					$('#shipping_town').val('<?= $accountData['town'] ;?>');
					$('#shipping_postcode').val('<?= $accountData['postcode'] ;?>');
					var formFields = $('.contact-form-fields');
  
					 formFields.each(function() {
					    var field = $(this);
					    var input = field.find('input');
					    var label = field.find('span.floating-label');
					    
					    
					      var valueLength = input.val().length;
					      
					      if (valueLength > 0 ) {
					        label.addClass('freeze')
					      } else {
					        label.removeClass('freeze')
					      }
					    
					    
					 });
				} else {
					$('#shipping_address_line_1').val('');
					$('#shipping_address_line_2').val('');
					$('#shipping_town').val('');
					$('#shipping_postcode').val('');
					var formFields = $('.contact-form-fields');
  
					 formFields.each(function() {
					    var field = $(this);
					    var input = field.find('input');
					    var label = field.find('span.floating-label');
					    
					    
					      var valueLength = input.val().length;
					      
					      if (valueLength > 0 ) {
					        label.addClass('freeze')
					      } else {
					        label.removeClass('freeze')
					      }
					    
					    
					 });
				}
			})
			$('#collect-order').change( function() {
				if ($(this).prop('checked')) {
					$('.required-field').removeAttr('required');
                    $('input[type="text"]').attr('disabled',true);
                    preferredDateInput.attr('disabled', false);
                    $('.deliver-side').hide(200);
					$('.middle-or').hide(200);
					$('#collect-value').val(1);
					$('#proceed-submit').val('Proceed to Confirm Order');
                    preferredDateLabel.text('Collection Date *');
				} else {
					$('.required-field').attr('required',true);
					$('input[type="text"]').attr('disabled',false);
                    preferredDateInput.attr('disabled', false);
					$('.deliver-side').show(200);
					$('.middle-or').show(200);
					$('#collect-value').val(0);
					$('#proceed-submit').val('Proceed to Payment');
                    preferredDateLabel.text('Shipping Date *');

				}
			});
			/*$('#get-button').click(function() {

            var txt = $('#shipping_postcode').val();

                if (txt != '') {
                        $.ajax({
                            url: "ajax/check-delivery.php",
                            method: "post",
                            data: {deliverypostcode: txt},
                            dataType: "json",
                            success: function (data) {
                                $('#delivery_cost').hide()(data.deliverycost).show(200);
                            }
                        });
                }
                else {
                    $('#delivery_cost').hide(200)('');
                }


        })*/
	});
	</script>

</div>
