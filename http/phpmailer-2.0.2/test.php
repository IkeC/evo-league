<?php


// Grab our config settings
require_once ('./config.php');

// Grab the FreakMailer class
require_once ('./FreakMailer.inc');

// instantiate the class
$mailer = new FreakMailer();

// Set the subject
$mailer->Subject = 'This is a test';

// Body
$mailer->Body = 'This is a test of my mail system!';

// Add an address to send to.
$mailer->AddAddress('king.of.all.cosmos@gmail.com', 'Eric Rosebrock');

if (!$mailer->Send()) {
	echo 'There was a problem sending this mail!';
} else {
	echo 'Mail sent!';
}
$mailer->ClearAddresses();
$mailer->ClearAttachments();
?>