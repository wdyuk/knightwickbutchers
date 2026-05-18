<?php

	require 'application.php';
	
	$messages = array();
	$errors = array();
	
	if (isset($_POST['email'])) {
		if(isset($_POST['g-recaptcha-response'])) 
        {

            $captcha=$_POST['g-recaptcha-response'];

            if(!$captcha){
              $errors[] = 'Please check the captcha form';
          
            }
            $response=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".GOOGLE_RECAPTCHA_SECRET."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);

            if($response['success'] == false)
            {
              $errors[] = 'Captcha Failed';
            }
            else
            {	
				$email = $_POST['email'];
				$where = sprintf('email = "%s"', mysqli_real_escape_string($db,$email));
				$admin = table_fetch_row(TBL_ADMIN, $where);
				
				if ($admin == false) {
					$messages[] = 'Invalid email, email is not registered.';
				} else {
					$messages[] = 'Email sent successfully to your account.';
					$password = random_word(6);
					
					$fields = array('password');
					$values = array('password' => md5($password));
					$where = sprintf('id = %d', $admin['id']);
					table_update(TBL_ADMIN, $fields, $values, $where);
					
					$to = $email;
					$from = $store_settings['company_from_email'];;
					$subject = 'Forgot Password?';
					
					$message = "<p>Email: {$email}<br>
						Password: {$password}</p>";
				}

				send_smtp_simple_email($to, $from, $subject, $message);
			}
		}
	}

?>
<!DOCTYPE html>
<html>
	<?php 
	$title = "WDY CMS Forgot Password : ".APP_TITLE;
	require 'includes/head.php'; 

	?>

	<body class="full-background">

		<div class="small-container">
		    <section id="content">
		    	<div class="row">
		            <div class="col-md-4 offset-md-4">

			        	<form id="login-form" class="form" method="post" action="login.php">
			                <div class="logo-container-large">
			                   <a href="<?php echo ADMIN_URL; ?>/"><img src="resources/images/wdy-logo.png" class="img-responsive"/></a>
			                </div>
				            <h1>Forgot Password</h1>
				            <?php show_messages($messages); ?>
				            <?php show_errors($errors); ?>
				            <div class="form-group">
				                <label for="email">Email Address:</label>
				                <input class="required form-control" sie="35" maxlength="100" type="text" name="email" id="email" value="" required />   
				            </div>
				            
				            <div class="form-group">
				                <div class="g-recaptcha" data-sitekey="<?= GOOGLE_RECAPTCHA_SITE; ?>"></div>  
				            </div>
				            <button class="btn-wdy btn-primary" type="submit">Retrieve</button>
				            <a href="login.php">Click Here to Login</a>
			            
			             </form>
		            </div>
		        </div>
		    </section>
		    
		    <div class="clear">&nbsp;</div>
		</div>

		<?php require 'includes/footer.php'; ?>

	</body>

</html>