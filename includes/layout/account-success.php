<!-- Wrapper -->
<section id="wrapper">
  <section id="one" class="wrapper style1">
      <div class="inner">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12 mt-5">
            	
				<?php
			
				if (isset($_POST['step3'])) {

					$params = array_map("sanitize_sql_string",$_POST);
					
					echo $params['newaccount_id'];
					$account = table_fetch_row('accounts','id="'.$params['newaccount_id'].'"');
					echo '<pre>'.print_r($account,true).'</pre>';

						//free membership
						$activation_code = generateRandomToken();
						table_update('accounts',array('status','activation_code'),array('status' => 1,'activation_code' => $activation_code),'id="'.$params['newaccount_id'].'"');
						table_insert('account_log',array('account_id','action','m_date'),array('account_id' => $account['id'],'action' => 'NEW MEMBER','m_date' => date('Y-m-d H:i:s')));
						?>
						<h2 class="inner-head"><?php echo $pageData['h1_title']; ?></h2>
						<br/>
						<?php echo $pageData['content']; 
						echo '<p>Congratulations!</p>';
						echo '<p>You now have an account with '.SITE_NAME.'.</p>';
						echo "<p>Your Account number is <strong>". $account['id']. "</strong></p>";
						echo "<p>For security, you will need to activate your account before logging in for the first time.</p>";
						echo "<p>You can activate your account by clicking the following link. <a href=\"".BASE_URL."activate-account?accountid=".$account['id']."&activation_code=".$activation_code."\">Activate Account</a></p>";
						echo "<p>Please head to the <a href=\"".BASE_URL."login\">login page</a> to access your new online account.</p>";
						echo "<p>If you have any queries or require any further assistance, please do not hesitate to contact one of the team on ".$store_settings['company_contact_no']." or email ".$store_settings['company_email']."</p>";
						echo "<p>Kind regards.</p>";
						echo "<p>The ".SITE_NAME." Team.</p>";

						$from = $store_settings['company_from_email'];

					    $subject = 'Account Confirmation';

					    $message = '<html><body>';
						$message .= '<img src="'.BASE_URL.ltrim(SITE_LOGO,'/').'" alt="'.SITE_NAME.'" />';
						$message .= '<p>Dear '.$account['firstname'].',</p>';
						$message .= '<p>Congratulations!</p>';
						$message .= '<p>You now have an account with '.SITE_NAME.'</p>';
						$message .= "<p>Your Account number is <strong>". $account['id']. "</strong></p>";
						$message .= "<p>For security, you will need to activate your account before logging in for the first time.</p>";
						$message .= "<p>You can activate your account by clicking the following link. <a href=\"".BASE_URL."activate-account?accountid=".$account['id']."&activation_code=".$activation_code."\">Activate Account</a></p>";
						$message .= "<p>Please head to the <a href=\"".BASE_URL."login\">login page</a> to access your new online account.</p>";
						$message .= "<p>If you have any queries or require any further assistance, please do not hesitate to contact one of the team on ".$store_settings['company_contact_no']." or email ".$store_settings['company_email']."</p>";
						$message .= "<p>Kind regards.</p>";
						$message .= "<p>The ".SITE_NAME." Team.</p>";
						$message .= "</body></html>";


						$to = $account['email'];
						send_smtp_simple_email($to,$from,$subject,$message);
					}
					else{
						header('Location: /join');
						exit();
					} 
				?>
				</div>
			</div>
		</div>
	</section>
</section>