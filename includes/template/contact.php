<script src="https://www.google.com/recaptcha/api.js?render=<?= GOOGLE_RECAPTCHA_SITE_V3;?>"></script>
<?php
$messages = [];
$errors = [];
$scroll_to_form = false;
if (isset($_POST['submit_contact_form'])) :
  if(strlen($_POST['email_2']) > 0 ){
    die();
  }
  else{
    if(isset($_POST['recaptcha_response'])){
        $recaptcha_response = $_POST['recaptcha_response'];
        if(!$recaptcha_response){
            $errors[] = 'Please check the captcha form';
        }
        // Prepare data for the reCAPTCHA verification request
          $data = array(
              'secret' => GOOGLE_RECAPTCHA_SECRET_V3,
              'response' => $recaptcha_response,
              'remoteip' => $_SERVER['REMOTE_ADDR']
          );
        // Make the verification request to Google's reCAPTCHA API
          $verify = curl_init();
          curl_setopt($verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
          curl_setopt($verify, CURLOPT_POST, true);
          curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
          curl_setopt($verify, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
          $response = curl_exec($verify);
          
          $response=json_decode($response,true);
          if(!$response['success']){
            $success = 0;
          }
          else{
            $success = $response['success'];
          }
           
          $table_insert = table_insert('contact_recaptcha', array('email','score','fail_success'), array('email' => $_POST['contact_email'],'score' => $response['score'], 'fail_success' => $success));
          if (!$response['success'] || $response['score'] < 0.5) { 
            // Adjust score threshold as needed
                $errors[] = 'Captcha verification failed. Please try again.';
                $scroll_to_form = true;
            }
          else
          {

            $email = $store_settings['company_from_email'];
            $subject = 'Contact Form : '.SITE_NAME;
            $message = '';
            $message .= "Name: " . $_POST['contact_name'] . "\n";
            $message .= "Contact Email: " . $_POST['contact_email'] . "\n";
            $message .= "Contact Number: " . $_POST['contact_number'] . "\n";
            $message .= "Message: " . $_POST['contact_message'] . "\n";
            $to = $store_settings['company_to_emails'];
           
            if (send_smtp_simple_email($to, $email, $subject, $message)) {
                $messages[] = "Thank you for contacting us. We will be in touch before you know it!";
                
                $fields = ['contact_name','contact_number','contact_message','contact_email','status','sent_to'];
                $_POST['sent_to'] = $to;
                $_POST['status'] = 1;

                $clientto = $_POST['contact_email'];
                $clientsubject = 'Thank you for your interest';
                $clientmessage = '';
                $clientmessage .= 'Hi ' . $_POST['contact_name'] . "!\n";
                $clientmessage .= "Thank you for contacting us. One of the team will be in touch shortly to discuss your query.". "\n\n";
                $clientmessage .= "Many thanks,". "\n";
                $clientmessage .= SITE_NAME. "\n";
                table_insert('contact', $fields, $_POST );
                send_smtp_simple_email($clientto, $email, $clientsubject, $clientmessage);
                $scroll_to_form = true;
            }
            else {
                $errors[] = "Sorry, Your mail could not be sent. Please try again later!";
                $scroll_to_form = true;
            }
        }
    }
  }
endif; ?>

<section class="probootstrap-section pt-3" data-section="contact">
  <div class="container">
    <div class="row">
      <div class="col-md-5 text-center probootstrap-animate">
        <div class="probootstrap-heading dark">
          <h1 class="primary-heading">Contact</h1>
          <h3 class="secondary-heading">Let's Chat</h3>
        </div>
        <ul class="contact">
            <li class="pt-2"><a href="tel:<?= $store_settings['company_contact_no']; ?>"><i class="icon-phone pr-3"></i><?= $store_settings['company_contact_no']; ?></a></li>
            <li class="pt-2"><a href="mailto:<?= $store_settings['company_email']; ?>"><i class="icon-envelope pr-3"></i><?= $store_settings['company_email']; ?></a><li>
            <li class="pt-2"><a href="/faqs"><i class="icon-question pr-3"></i>FAQ's</a></li>
        </ul>
      </div>
      <div class="col-md-6 col-md-push-1 probootstrap-animate">
        <?php if (!empty($messages)) {
          ?>
          <div class="alert alert-success">
            <?php foreach($messages as $message) {
              echo '<p>'.$message.'</p>';
            } ?>
          </div>
         
        <?php }; ?>
        <?php if (!empty($errors)) {
          ?>
          <div class="alert alert-danger">
            <?php foreach($errors as $error) {
              echo '<p>'.$error.'</p>';
            } ?>
          </div>
         
        <?php }; ?>
        <form method="post" class="probootstrap-form">
          <div class="form-group">
            <label for="contact_name">Your Name *</label>
            <div class="form-field">
              <input type="text" id="contact_name" name="contact_name" class="form-control" required>
            </div>
          </div>
          <div class="form-group">
            <label for="contact_email">Your Email *</label>
            <div class="form-field">
              <input type="email" id="contact_email" name="contact_email" class="form-control" required>
              <input type="hidden" name="email_2" placeholder="Email">
            </div>
          </div>
          <div class="form-group">
            <label for="contact_number">Your Phone Number *</label>
            <div class="form-field">
              <input type="text" id="contact_number" name="contact_number" class="form-control" required>
            </div>
          </div>
          <div class="form-group">
            <label for="contact_message">Your Message *</label>
            <div class="form-field">
              <textarea name="contact_message" id="contact_message" cols="30" rows="10" class="form-control" required></textarea>
            </div>
          </div>
          <input name="status" type="hidden" value="1" />
          <div class="form-group">
            <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            <input name="submit_contact_form" type="submit" id="c_submit" value="Send Message" class="btn btn-primary btn-lg">
          </div>
        </form>
      </div>
    </div>
  </div>
</section>