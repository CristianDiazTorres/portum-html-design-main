<?php
header('Content-type: application/json');
require_once('php-mailer/PHPMailerAutoload.php'); // Include PHPMailer

$mail = new PHPMailer();
$emailTO = $emailBCC =  $emailCC = array(); $formEmail = '';

### Enter Your Sitename 
$sitename = 'Portum';

### Enter your email addresses: @required
$emailTO[] = array( 'email' => 'youremail@yoursite.com', 'name' => 'Your Name' ); 

### Enable bellow parameters & update your BCC email if require.
//$emailBCC[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' );

### Enable bellow parameters & update your CC email if require.
//$emailCC[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' );

### Enter Email Subject
$subject = "Subscribe on Portum" . ' - ' . $sitename;

### If your did not recive email after submit form please enable below line and must change to your correct domain name. eg. noreply@example.com
//$formEmail = 'noreply@yoursite.com';

### Success Messages
$msg_success = "<strong>Thank you</strong> for Subscribing our update.";

if( $_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST["youremail"]) && $_POST["youremail"] != '') {
		### Form Fields
		$cf_email = $_POST["youremail"];

		$honeypot 	= isset($_POST["form-anti-honeypot"]) ? $_POST["form-anti-honeypot"] : 'bot';
		$bodymsg = '';
		
		if ($honeypot == '' && !(empty($emailTO))) {
			$mail->IsHTML(true);
			$mail->CharSet = 'UTF-8';

			$mail->From = ($formEmail !='') ? $formEmail : $cf_email;
			$mail->FromName = $cf_name . ' - ' . $sitename;
			$mail->AddReplyTo($cf_email);
			$mail->Subject = $subject;
			
			foreach( $emailTO as $to ) {
				$mail->AddAddress( $to['email'] , $to['name'] );
			}
			
			### if CC found
			if (!empty($emailCC)) {
				foreach( $emailCC as $cc ) {
					$mail->AddCC( $cc['email'] , $cc['name'] );
				}
			}
			
			### if BCC found
			if (!empty($emailBCC)) {
				foreach( $emailBCC as $bcc ) {
					$mail->AddBCC( $bcc['email'] , $bcc['name'] );
				}				
			}

			### Include Form Fields into Body Message
			$bodymsg .= isset($cf_email) ? "Email: $cf_email<br><br>" : '';
			$bodymsg .= $_SERVER['HTTP_REFERER'] ? '<br>---<br><br>This email was sent from [ICO]: ' . $_SERVER['HTTP_REFERER'] : '';
			
			$mail->MsgHTML( $bodymsg );
			$is_emailed = $mail->Send();

			if( $is_emailed === true ) {
				$response = array ('result' => "success", 'message' => $msg_success);
			} else {
				$response = array ('result' => "error", 'message' => $mail->ErrorInfo);
			}
			echo json_encode($response);
			
		} else {
			echo json_encode(array ('result' => "error", 'message' => "Bot <strong>Detected</strong>! Clean yourself Botster!"));
		}
	} else {
		echo json_encode(array ('result' => "error", 'message' => "Please <strong>Fill up</strong> all required fields and try again."));
	}
}