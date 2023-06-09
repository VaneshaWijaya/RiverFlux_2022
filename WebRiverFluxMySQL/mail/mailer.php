<?php
function send_mail($to, $body, $subject){	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require "vendor/autoload.php";
	$mail = new PHPMailer;
	
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'vaneshawijaya17@gmail.com';                 // SMTP username
	$mail->Password = 'Believe17';                           // SMTP password
	$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 465;                                    // TCP port to connect to
	
	$mail->FromName = 'Nama Anda';
	$mail->addAddress($to);               // Name is optional
	$mail->addReplyTo('Nama Anda', 'Reply');
	
	$mail->Subject = $subject;
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Body    = $body;
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	
	if(!$mail->send()) {
		echo 'gagal mengirim.';
	} else {
		?> <script type="text/javascript">alert('Email Berhasil Dikirim!');</script><?php
	}
}
?>
