<?php

	error_reporting(E_ALL);
	try {
	$messages = array();

	if (isset($_POST['email'])) {
		$email = $_POST['email'];
		$where = sprintf('email = "%s" and status=1', mysqli_real_escape_string($db,$email));
		$account = table_fetch_row('accounts', $where,'id DESC');
		$to = $email;
		$from = $store_settings['company_from_email'];
		$subject = '';
		$message = '';

		if ($account == false) {
			$messages[] = 'Invalid email, email is not registered.';
		} else {
			$messages[] = 'Email sent successfully to your account.';
			$password = random_word(9);

			$fields = array('password');
			$values = array('password' => md5($password));
			$where = sprintf('id = %d', $account['id']);
			table_update('accounts', $fields, $values, $where);



			$subject = 'Forgot Password?';

			$message = "Email: ".$email."\n";
			$message .= "Password: ".$password."\n";
		}

		send_smtp_simple_email($to, $from, $subject, $message);
	}
 } catch(Exception $e) {
 	echo $e->getMessage();
 }

?>
      <div class="container">
    	<div class="col-sm-12">
        	<form id="forgotpassword-form" class="validate-form form" method="post" action="/forgot-password">
           	<?php if(!empty($messages)) : ?>
            <div class="alert alert-info"><?php foreach($messages as $message) { echo '<p>'.$message.'</p>';}; ?></div>
        	<?php endif; ?>

            <div class="form-group">
            	<label for="email">Email Address:</label>
               <input class="required email form-control" size="35" maxlength="100" type="email" name="email" id="email" value="" required/></td>
            </div>
            <div class="form-group">
            	<input class="btn-more-details-green-square" name="forgot_password" type="submit" value="Retrieve"/>
            </div>
            </form>
        </div>
        <div class="col-sm-12">
        	<a href="/login"><p>Click here to Login!</p></a>
        </div>
    </div>