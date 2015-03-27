<?php

function logSentMail($user, $toAddress, $type) {
	require('/var/www/yoursite/http/variables.php');
	require('/var/www/yoursite/http/variablesdb.php');
	
	$sql = "INSERT INTO $mailtable (id, user, toAddress, mailType, logTime) " .
			"VALUES ('', '$user', '$toAddress', '$type', '".time()."')";
	$result = mysql_query($sql);
	
	return $result;	
}

function sendAdminMail($adminSubject, $adminMessage) {
	require('/var/www/yoursite/http/variables.php');
	require('/var/www/yoursite/http/variablesdb.php');
	
	$head = "From:".$adminmail."\r\nReply-To:".$adminmail."";
	@mail ($adminmail, $adminSubject, $adminMessage, $head, $mailconfig);
}

function isValidEmailAddress($address) {
	if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $address)) {
  		return true; 
	}
	else {
  		return false;
	}
}

function formatDate($date) {
       return date('m/d h:i a', $date);
}

?>