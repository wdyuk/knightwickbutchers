
<div class="container">
    <div class="col-md-12">
<?php 

$params = array_map("sanitize_sql_string",$_POST);


if (isset($params['resend-activation-ba'])) {
    $check = table_fetch_row('accounts','email="'.$params['emailaddress'].'" and status=1','id DESC');
$error = false;
//check if member already exists
if ($check) {
     if ($active == 1) {
        $error = true;
        $message = 'Account already activated. Please login or if you have forgot your password, click the forgot password link on the login page.';
     } else {
        // reactivate
        if (strlen($params['activation_code']) == 0) {
            $activation_code = generateRandomToken();
            table_update('accounts',array('activation_code'),array('activation_code' => $activation_code),'id="'.$check['id'].'"');
        };
         $from = $store_settings['company_from_email'];

            $subject = 'Account Reactivation';

            $message = '<html><body>';
            $message .= '<img src="images/logo.png" alt="" />';
            $message .= '<p>Dear '.$check['firstname'].',</p>';
          
            $message .= "<p>You have requested us to resend the activation link for you account.</p><p>You can activate your account by clicking the following link. <a href=\"/accounts/activate_account.php?memberid=".$check['id']."&activation_code=".$activation_code."\">Activate Account</a></p>";
            $message .= "<p>Please then head to the <a href=\"/login\">login page</a> to access your new online account.</p>";
            $message .= "<p>If you have any queries or require any further assistance, please do not hesitate to contact one of the team on 01759 302907 or email info@uk4x4tyres.co.uk</p>";
            $message .= "<p>Kind regards.</p>";
            $message .= "<p>The UK 4 x 4 Tyres Team.</p>";
            $message .= "</body></html>";

            $to = $check['email'];
            send_smtp_simple_email($to,$from,$subject,$message); 

            echo "<p>We have sent an email with the activation link to the address provided.  Please check your email for this.</p>";
            echo "<p>Please click the link in the email and then head to the <a href=\"/login\">login page</a> to access your new online account.</p>";
            echo "<p>If you have any queries or require any further assistance, please do not hesitate to contact one of the team on 01759 302907 or email info@uk4x4tyres.co.uk</p>";
            echo "<p>Kind regards.</p>";
            echo "<p>The UK 4 x 4 Tyres Team.</p>";

        
     }
    } else {
        echo '<p>Invalid email, email is not registered.</p>';
    }
    

} else {
if ($error == true) {
    echo '<div class="alert alert-warning">'.$message.'</div>';
}
?>

    <form method="POST">
        <input type="text"c class="form-control" name="emailaddress" placeholder="Enter email address" required/>
        <br />
        <input type="submit" class="btn-more-details-green-square" name="resend-activation-ba" value="Resend Activation Link" />
    </form>
<?php } ?>

</div>

</div>