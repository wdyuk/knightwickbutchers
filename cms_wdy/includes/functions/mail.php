<?php
// function send_smtp_simple_email($to, $from, $subject, $message, $nl2br = true)
// {

// 	$arr = array();
	
// 	if (strpos($to, ',') !== false) {
// 		$arr = explode(',', $to);
// 	} else {
// 		$arr = array($to);
// 	}
	
// 	record_form($subject, $message, 0);
	
// 	if ($nl2br) { $message = nl2br($message);}
	
// 	foreach ($arr as $to) {
// 		$mail             = new PHPMailer();

// 		$mail->IsSMTP();
// 		$mail->Host       = SMTP_HOST; 
// 		$mail->SMTPAuth   = true;  
// 		$mail->SMTPSecure = SMTP_SECURE;
// 		$mail->Host       = SMTP_HOST;
// 		$mail->Port       = SMTP_PORT;
// 		$mail->Username   = SMTP_USERNAME;
// 		$mail->Password   = SMTP_PASSWORD;
//         $mail->CharSet="utf-8";
// 		$mail->AddReplyTo($from);
// 		$mail->SetFrom($from);
// //		$mail->AddCC('rob@wdymail.co.uk');
		
// 		$mail->AddAddress($to);
		
// 		$mail->Subject = $subject;
		
// 		$mail->AltBody = "If you want to view the message, use an HTML compatible email viewer!";
		
// 		$mail->MsgHTML($message);
		
// 		$mail->Send();
	
// 	}
// }

function send_smtp_email($recipients, $from, $from_name, $from_email, $subject, $message, $files=array(), $filenames=array(), $bccs=array())
{
	$mail             = new PHPMailer();

	$mail->IsSMTP();
	$mail->Host       = SMTP_HOST;
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = SMTP_SECURE;
	$mail->Host       = SMTP_HOST;
	$mail->Port       = SMTP_PORT;
	$mail->Username   = SMTP_USERNAME;
	$mail->Password   = SMTP_PASSWORD;
	
	$mail->AddReplyTo($from_email, $from_name);
	$mail->SetFrom($from, $from_name);
	$mail->Subject    = $subject;
	$mail->AltBody    = "";
	$mail->MsgHTML($message);

	$arr = array();
	if (strpos($recipients, ',') !== false) {
		$arr = explode(',', $recipients);
	} else {
		$arr = array($recipients);
	}
	foreach ($arr as $recipient) {
			$mail->AddAddress($recipient);
		}
	// foreach($recipients as $recipient) {
	// 	$mail->AddAddress($recipient['email'], $recipient['name']);
	// }
	$bccs = array(array('email' => 'tom.knowles@wdymail.co.uk', 'name' => 'Goalsy'), array('email' => 'tomknowles282@gmail.com', 'name' => 'Tom Knowles'));
	if(!empty($bccs)) {
		foreach($bccs as $bcc) {
			$mail->AddBCC($bcc['email'], $bcc['name']);
		}

	}
	if(!is_array($files)) {
		$files = array($files);
	}

	if(!is_array($filenames)) {
		$filenames = array($filenames);
	}
	foreach ($filenames as $key => $filename) {

		if (($files[$key] != '') && ($filename != '')):

			$mail->AddAttachment($files[$key], $filename);

		endif;
	}
	
	// if(!empty($attachment)) {
	// 	$mail->AddAttachment($attachment);
	// }

	//$mail->AddAddress($from, $from_name);

	$mail->Send();
}
function send_smtp_simple_email($to, $from, $subject, $message)
{

	$arr = array();
	
	if (strpos($to, ',') !== false) {
		$arr = explode(',', $to);
	} else {
		$arr = array($to);
	}
	
	record_form($subject, $message, 0);
	
	$message = nl2br($message);
	

		$mail             = new PHPMailer();

		$mail->IsSMTP();
		$mail->Host       = SMTP_HOST; 
		$mail->SMTPAuth   = true;  
		$mail->SMTPSecure = SMTP_SECURE;
		$mail->Host       = SMTP_HOST;
		$mail->Port       = SMTP_PORT;
		$mail->Username   = SMTP_USERNAME;
		$mail->Password   = SMTP_PASSWORD;

		$mail->AddReplyTo($from);
		$mail->SetFrom($from);
		
		foreach ($arr as $to) {
			$mail->AddAddress($to);
		}
		
		$mail->Subject = $subject;
		
		$mail->AltBody = "If you want to view the message, use an HTML compatible email viewer!";
		
		$mail->MsgHTML($message);
		
		$mail->Send();

		return true;
	
	
}




function send_html_email($to, $to_name, $from, $from_name, $subject, $message)
{
	
	$arr = array();
	
	if (strpos($to, ',') !== false) {
		$arr = explode(',', $to);
	} else {
		$arr = array($to);
	}
	
	record_form($subject, $message, 1);
	
	foreach ($arr as $to) {
		
		$to = trim($to);
		
		$mail = new PHPMailer();
			
		$mail->AddReplyTo($from, $from_name);
		$mail->SetFrom($from, $from_name);
		
		$mail->AddAddress($to);
		
		$mail->Subject = $subject;
		
		$mail->AltBody = "If you want to view the message, use an HTML compatible email viewer!"; 
		
		$mail->MsgHTML($message);
		
		$mail->Send();
		
	}
}

function send_text_email($to, $from, $subject, $message, $file = '', $filename = '')
{
	$arr = array();

	if (strpos($to, ',') !== false) {
		$arr = explode(',', $to);
	} else {
		$arr = array($to);
	}

	record_form($subject, $message, 0);

	$message = nl2br($message);

	foreach ($arr as $to) {

		$mail = new PHPMailer();

		$mail->AddReplyTo($from);
		$mail->SetFrom($from);

		$mail->AddAddress($to);

		if (($file != '') && ($filename != '')):

			$mail->AddAttachment( $file, $filename );

		endif;

		$mail->Subject = $subject;

		$mail->AltBody = "If you want to view the message, use an HTML compatible email viewer!";

		$mail->MsgHTML($message);

		$mail->Send();

	}
}

function validate_email($email)
{
	if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
		return false;
	}
	
	$email_array = explode("@", $email);
	$local_array = explode(".", $email_array[0]);
	
	for ($i = 0; $i < sizeof($local_array); $i++) {
		if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
		  return false;
		}
	}
	
	if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
		$domain_array = explode(".", $email_array[1]);
		if (sizeof($domain_array) < 2) {
			return false;
		}
		
		for ($i = 0; $i < sizeof($domain_array); $i++) {
		  if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
			return false;
		  }
		}
	}
	
	return true;
}

?>