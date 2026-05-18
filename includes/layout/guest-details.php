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
            <h3>Customer Details</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-12 probootstrap-animate">		
    		<form method="POST" action="/checkout"><!-- class="form join-form join-form-step1"-->
        		<div class="row">
        			<div class="col-md-6 col-sm-12 contact-form-fields">
        				<select name="title" class="form-control-contact" required>
        					<option>Please Select a Title</option>
        					<option value="Mr">Mr</option>
        					<option value="Mrs">Mrs</option>
        					<option value="Miss">Miss</option>
        					<option value="Master">Master</option>
        					<option value="Dr">Dr</option>
        					<option value="Sir">Sir</option>
        					<option value="Lord">Lord</option>
        					<option value="Lady">Lady</option>
        					<option value="Madame">Madame</option>
        					<option value="Ms">Ms</option>
        				</select>
        				<span class="floating-label no-display">Title *</span>
        			</div>
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="email" name="email" value="<?php echo isset($accountData['email']) ? $accountData['email'] : ''; ?>" size="50" class="form-control-contact"/><span class="floating-label">Email Address</span>
        			</div>
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="firstname" value="<?php echo isset($accountData['firstname']) ? $accountData['firstname'] : ''; ?>" size="50" class="form-control-contact" required><span class="floating-label">First Name *</span>
        			</div>
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="lastname" value="<?php echo isset($accountData['lastname']) ? $accountData['lastname'] : ''; ?>" size="50"  class="form-control-contact" required><span class="floating-label">Last Name *</span>
        			</div>
        			
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="mobile_number" value="<?php echo isset($accountData['mobile_number']) ? $accountData['mobile_number'] : ''; ?>" size="50" class="form-control-contact" required><span class="floating-label">Mobile Number *</span>
        			</div>
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="telephone_number" value="<?php echo isset($accountData['telephone_number']) ? $accountData['telephone_number'] : ''; ?>" size="50" class="form-control-contact"><span class="floating-label">Landline Number</span>
        			</div>
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="address_line_1" value="<?php echo isset($accountData['address_line_1']) ? $accountData['address_line_1'] : ''; ?>" size="50" class="form-control-contact" required><span class="floating-label">Address Line 1 *</span>
        			</div>
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="address_line_2" value="<?php echo isset($accountData['address_line_2']) ? $accountData['address_line_2'] : ''; ?>" size="50" class="form-control-contact"><span class="floating-label">Address Line 2</span>
        			</div>
        				<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="town" value="<?php echo isset($accountData['town']) ? $accountData['town'] : ''; ?>" size="50" class="form-control-contact" required/> <span class="floating-label">Town *</span>
        			</div>
        			<div class="col-md-6 col-sm-12 contact-form-fields mb-4">
        				<input type="text" name="postcode" value="<?php echo isset($accountData['postcode']) ? $accountData['postcode'] : ''; ?>" size="50"  class="form-control-contact" required><span class="floating-label">Postcode *</span>
        			</div>
        			<div class="col-sm-12 contact-form-fields mb-2">
        			
                        <div class="form-group">
                            <label><input type="checkbox" name="terms" value="1" style="width:1em; position: relative; top: 6px; display: inline-block;" required="" class="checkbox form-control mr-1"> I agree to the <a href="/terms-conditions" target="_new">Terms &amp; Conditions</a> of <?= SITE_NAME;?>.<em>*</em> </label>
    
                        </div>
        			</div>

        			<div class="col-sm-12 contact-form-fields mb-2">
        				<input type="hidden" name="guest" value="1" />
        				<input type="hidden" name="delivery" value="1" />
        				
        				<input type="submit" class="btn btn-primary btn-lg pull-right" value="Proceed" />
        			</div>
        		</div>
    		</form>
		</div>
    </div>
  </div>
</section>
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
    input.keyup(function() {
      checkInput()
    });
    input.change(function() {
      checkInput()
    })
  });
</script>