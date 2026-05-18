<!-- Wrapper -->
<section id="wrapper">
  	<section id="one" class="wrapper style1">
      	<div class="inner">
      		<div class="row">
		    	<div class="col-md-12">
		
					<h2 class="inner-head"><?php echo $pageData['h1_title']; ?></h2>
					<?php if(isset($_GET['accountexists'])) { ?>
					<div class="alert alert-warning">There is already an account with this email address.  If you have forgotten your password please click the forgot password link on the login page to reset your password.</div>
					<?php } ?>
		
					<form method="POST" class="join-form" action="/join"><!-- class="form join-form join-form-step1"-->
						<div class="row">
							
								<div class="col-md-6 col-sm-12">
									
									<div class="field">
										<label>Title *</label>
										<select name="title" class="form-control mb-4" required>
											<option class="form-control">Please Select a Title</option>
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
									</div>
	
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>First Name *</label>
									<input type="text" name="firstname" value="" size="50" class="form-control mb-4" placeholder="First Name" required>
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>Last Name *</label>
									<input type="text" name="lastname" value="" size="50"  class="form-control mb-4" placeholder="Last Name" required>
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>Email Address *</label>
									<input type="email" name="email" value="" size="50" class="form-control mb-4" placeholder="Email Address">
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>Mobile Number *</label>
									<input type="text" name="mobile_number" value="" size="50"  placeholder="Mobile Number" class="form-control mb-4" required>
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>Landline Number *</label>
									<input type="text" name="telephone_number" value="" size="50"  placeholder="Landline Number" class="form-control mb-4" required>
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>Address Line 1 *</label>
									<input type="text" name="address_line_1" value="" size="50"  placeholder="Address Line 1" class="form-control mb-4" required>
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>Address Line 2</label>
									<input type="text" name="address_line_2" value="" size="50"  placeholder="Address Line 2" class="form-control mb-4">
								</div>
				 				<div class="col-md-6 col-sm-12 field">
				 					<label>Town 1 *</label>
									<input type="text" name="town" value="" size="50" class="form-control mb-4" placeholder="Town" required>
								</div>
								<div class="col-md-6 col-sm-12 field">
									<label>Postcode *</label>
									<input type="text" name="postcode" value="" size="50"  class="form-control mb-4" placeholder="Postcode" required>
								</div>
							
						</div>
						<div class="row">
							
					  			<div class="col-md-12 no-padding" id="pwd-container">
					  				<div class="row">
						  				<div class="col-md-6 field">
						  					<label>Password *</label>
							          		<input type="password" name="password" class="form-control mb-4" id="passwd1" placeholder="Password" required />       
						    			</div>
						    			<div class="col-md-6 field">
						    				<label>Confirm Password *</label>
							          		<input type="password" name="confirm_password" class="form-control mb-4" id="confirm_password" placeholder="Confirm Password" required />       
						    			</div>
						    		</div>
						    		<div class="row">
						    			<div class="col-md-12 field">
						    				<div class="pwstrength_viewport_progress"></div>
						    			</div>
						    		</div>
					  			</div>
					  		
				  		</div>
				  		<div class="row">
				  			
								<div class="col-sm-12 field">
									<div class="form-group mt-10">
										<input class="mt-10" type="checkbox" name="terms" value="1" style="width:1em; background: #fff;" required> I agree to the <a href="/terms-conditions" style="color: #F1F1DC;" target="_new">Terms &amp; Conditions</a> of <?= SITE_NAME; ?>.<em>*</em>
										</div>
									</div>
								</div>
							
						</div>
				  		<div class="row">	
				  			
								<div class="col-sm-12 field">
									<input type="hidden" name="step2" value="1" />
									<input type="submit" class="red-btn-proceed pull-right btn-more-details-green-square" value="Proceed" />
								</div>
							
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</section>
	