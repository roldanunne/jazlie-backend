<?php
	function Send_Mail($to,$subject,$body) {
		require 'class.phpmailer.php';
		$from       = "admin@roldanunne.byethost11.com";
		$mail       = new PHPMailer();
		$mail->IsSMTP(true); // use SMTP
		$mail->IsHTML(true);
		$mail->SMTPAuth   = true; // enable SMTP authentication
		$mail->Host= "tls://smtp.gmail.com"; // GMail SMTP
		$mail->Port = 465;  // SMTP Port
		$mail->Username = "roldanunne@gmail.com";  // SMTP  Username
		$mail->Password = "r9_11111";  // SMTP Password
		$mail->SetFrom($from, 'From ');
		$mail->AddReplyTo($from,'From ');
		$mail->Subject    = $subject;
		$mail->MsgHTML($body);
		$address = $to;
		$mail->AddAddress($address, $to);
		$mail->Send(); 
		
		//if(!$mail->Send()) {
		//	echo "Mailer Error: " . $mail->ErrorInfo;
		//} else {
		//	echo "Message sent!";
		//}
	}
?>

