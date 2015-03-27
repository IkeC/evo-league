<?php

require_once('/var/www/tgaekle/yoursite/http/variables.php');
require_once('/var/www/tgaekle/yoursite/http/variablesdb.php');
require_once('/var/www/tgaekle/cron/functions.php');

// disable player accounts that have not played games in X weeks

$weeks = 52;
$disableSpan = 60*60*24*7*$weeks;

$playersquery = "select distinct winner from $gamestable " .
		"where (UNIX_TIMESTAMP() - date < $disableSpan) " .
		"UNION " .
		"select distinct loser from $gamestable " .
		"where (UNIX_TIMESTAMP() - date < $disableSpan)";
	
$result = mysql_query($playersquery);

$played_array = array();
$adminMessage = "";

while($row = mysql_fetch_array($result)) {
	$played_array[] = $row[0];
}

$alloldplayersquery = "SELECT player_id, name, mail " .
		"FROM $playerstable " .
		"WHERE approved = 'yes' " .
		"AND (UNIX_TIMESTAMP() - activeDate > $disableSpan)";
		
$playerresult = mysql_query($alloldplayersquery);

while($row = mysql_fetch_array($playerresult)) {
	$name = $row['name'];
	$userId = $row['player_id'];
	if (!in_array($name, $played_array)) {
		// has not played in X weeks
		
		$sendmail = 0;
		$mailSentResult = 0;
		
		$sql = "UPDATE $playerstable " .
				"SET approved = 'no' " .
				"where name='$name'";
				
		$updateResult = mysql_query($sql);
		
		$toAddress = $row['mail'];
				
		if (isValidEmailAddress($toAddress)) {
			
			$subject = "[$leaguename] $name account passivated";
		
			$head = "From:".$adminmail."\r\nReply-To:".$adminmail."";
		
			$message =  "Hello $name,\n" .
				"\n" .
				"Since you have not played any $leaguename games in ".$weeks." weeks, \n" .
				"your account has been passivated.\n" .
				"\n" .
				"If you want your account reactivated sometime, simply post in the forum \n" .
				"activation thread at http://www.yoursite/forum/viewtopic.php?t=1084\n".			
				"\n" .
				"\n" .
				"- The ".$leaguename." Staff\n";
												
			$sendmail = @mail ($toAddress, $subject, $message, $head);
			$mailSentResult = logSentMail($name, $toAddress, 'passivated');
		
		} // if valid address
		
		$date = time();
		$link = $directory."/info.php?#8";
		$reason = "automatically passivated on ".formatDate($date);

		$sql = "INSERT INTO $playerstatustable (userId, userName, type, active, ".
			"date, expireDate, forumLink, reason) ".
			"VALUES ('$userId', '$name', 'I', 'Y', ".
			"'$date', '', '$link', '$reason')";
		$result = mysql_query($sql);
		
		$adminMessage .= "[$name] " .
			"passivated [$updateResult] " .
			"address [$toAddress] ".
			"mail sent [$sendmail] ".
			"log [$mailSentResult] ".
			"status [$result]\n";
	}
}


$adminSubject = "[$leaguename admin] accounts passivated";

if (!empty($adminMessage)) {
	sendAdminMail($adminSubject, $adminMessage);
}

?>