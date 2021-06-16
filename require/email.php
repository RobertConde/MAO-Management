<?php
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

function sendEmail($to, $subj, $msg) {
	require_once $_SERVER['DOCUMENT_ROOT'] . "PHPMailer-6.4.1/Exception.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "PHPMailer-6.4.1/OAuth.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "PHPMailer-6.4.1/PHPMailer.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "PHPMailer-6.4.1/POP3.php";
	require_once $_SERVER['DOCUMENT_ROOT'] . "PHPMailer-6.4.1/SMTP.php";

	$email_config = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . "config.ini", true)['email'];

	//Create a new PHPMailer instance
	$mail = new PHPMailer();

	//Tell PHPMailer to use SMTP
	$mail->isSMTP();

	//Enable SMTP debugging
	//SMTP::DEBUG_OFF = off (for production use)
	//SMTP::DEBUG_CLIENT = client messages
	//SMTP::DEBUG_SERVER = client and server messages
	$mail->SMTPDebug = SMTP::DEBUG_OFF;

	//Set the hostname of the mail server
	$mail->Host = 'smtp.gmail.com';
	//Use `$mail->Host = gethostbyname('smtp.gmail.com');`
	//if your network does not support SMTP over IPv6,
	//though this may cause issues with TLS

	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	$mail->Port = 587;

	//Set the encryption mechanism to use - STARTTLS or SMTPS
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;

	//Username to use for SMTP authentication - use full email address for gmail
	$mail->Username = $email_config['email'];

	//Password to use for SMTP authentication
	$mail->Password = $email_config['password'];

	//Set who the message is to be sent from
	$mail->setFrom($email_config['email'], $email_config['name']);

	//Set who the message is to be sent to
	$mail->addAddress($to);                 // There exists second parameter to add name

	//Set the subject line
	$mail->Subject = $subj;

	//Read an HTML message body from an external file, convert referenced images to embedded,
	//convert HTML into a basic plain-text alternative body
	$mail->msgHTML($msg);

	//send the message
	return $mail->send();   //$mail->ErrorInfo
}
