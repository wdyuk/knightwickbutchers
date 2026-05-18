<?php 
	require 'application.php';
	
    $messages = array();
	$errors = array();
	if (isset($_POST['login'])) {
		$email = $_POST['email'];
		$password = $_POST['password'];

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
        		if (login($email, $password)) {
        			$_SESSION['admin'] = get_admin($email);
        			
        			$fields = array('last_login');
        			$values = array('last_login' => date('Y-m-d H:i:s'));
        			$where = sprintf('id=%d', $_SESSION['admin']['id']);			
        			
        			table_update(TBL_ADMIN, $fields, $values, $where);
        			redirect('control-panel.php');
        		} else {
        			$errors[] = 'Invalid login, incorrect email or password specified.';
        		}
            }
        }
	}
	
?>
<!DOCTYPE html>
<html>
<?php 
$title = "WDY CMS Login : ".APP_TITLE;
require 'includes/head.php'; ?>

<body class="full-background">

<div class="small-container">
    <section id="content">
    	<div class="row">
            <div class="col-md-4 offset-md-4">

            	<form id="login-form" class="form" method="post" action="login.php">
                    <div class="logo-container-large">
                       <a href="<?php echo ADMIN_URL; ?>/"><img src="resources/images/wdy-logo.png" class="img-responsive"/></a>
                    </div>
                <h1>Sign In</h1>
                <?php show_messages($messages); ?>
                <?php show_errors($errors); ?>
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input class="required form-control" sie="35" maxlength="100" type="text" name="email" id="email" value="" required />   
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input class="required form-control" size="35" maxlength="35" type="password" name="password" id="password" value="" required />
                
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="<?= GOOGLE_RECAPTCHA_SITE; ?>"></div>  
                </div>
                <input type="hidden" name="login" />
                <button class="btn-wdy btn-primary">Sign In</button>
                 <a href="forgot-password.php">Forgot Password?</a>
                
                
                </form>
            </div>
        </div>
    </section>
    
    <div class="clear">&nbsp;</div>
</div>

<?php require 'includes/footer.php'; ?>

</body>
</html>