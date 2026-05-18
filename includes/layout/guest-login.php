
<?php $collection = $_GET['collection'];?>
<!-- Wrapper -->
<section id="wrapper">
  	<section id="one" class="wrapper spotlight style1">
      	<div class="inner">
	      	<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h2 class="inner-head-accounts">Login to Your Account</h2>
						<div class="col-md-12">
				            <?php if (!empty($messages)) {
				                echo '<div class="alert alert-warning"><ul>';
				                foreach($messages as $message) {
				                    echo '<li>'.$message.'</li>';
				                };
				                echo '</ul></div>';
				            }; ?>
		        		</div>
					<form id="login-form" class="validate-form form" method="post">
						<?php if(isset($collection) && $collection == 1){?>
							<input type="hidden" name="payment" value="1" />
						<?php } ?>
						<input type="hidden" name="delivery" value="1" />
			            <div class="form-group">
			                <label>Email Address:</label>
			                <input class="required email form-control" size="35" maxlength="100" type="text" name="email" id="email" value="" />
			            </div>
			            <div class="form-group">
			                <label>Password:</label>
			                <input class="required form-control" size="35" maxlength="35" type="password" name="password" id="password" value="" />
			            </div>
			            <div class="form-group">
			             <p class="mt-0 mb-2"><a href="/join" style="color: #F1F1DC;">Create an Account</a></p>
			             <p class="mt-0 mb-2"><a href="/forgot-password" style="color: #F1F1DC;">Forgot Password?</a></p>
			                <input name="login" class="auto-width" type="submit" value="Login">
			            </div>
			        </form>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<h2 class="inner-head-accounts">Checkout as Guest</h2>
					<form id="login-form" class="validate-form form" method="post" action="/checkout?guest-details">
						<input type="hidden" name="guest" value="1" />
						<input type="hidden" name="collection" value="<?= $_GET['collection'];?>" />
						<input name="guest" class="auto-width" type="submit" value="Checkout as Guest">
					</form>
				</div>
			</div>
		</div>
	</section>
</section>