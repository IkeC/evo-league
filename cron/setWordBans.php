#!/usr/bin/php
<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/insults/', KLogger::INFO);	

$sql = "SELECT season FROM six_stats";
$sixSeason = mysql_fetch_array(mysql_query($sql))[0];

$profiles = array();
$users = array();
		
$warningsIds = GetActiveWarningsIds();
$bansIds = GetActiveBansIds();

$subject = shell_exec('logtail /var/log/sixserver/sixserver.log|grep -i "'.$badWordsGrep.'"');
foreach(preg_split("/((\r?\n)|(\r\n?))/", $subject) as $line){
	if (strlen($line) > 0) {
		$log->logInfo('Handling line: '.$line);
		$badLine = stristr($line, ")");
		if (strlen($badLine) > 0) {
			$badLine = trim(substr($badLine, 1));
		}
		$profileName = stristr(stristr($line, "("), ")", true);		
		if (strpos($profileName, " -> ") != false) {
			$profileName = stristr($profileName, " -> ", true);
		}
		if (strlen($profileName) > 0) {
			$profileName = substr($profileName, 1);
			$sql = "SELECT user_id FROM six_profiles WHERE name='".$profileName."'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) == 0) {
				$log->logInfo('Profile not found: '.$profileName);
			} else {
				$row = mysql_fetch_array($result);
				$playerId = $row[0];
				if (in_array($playerId, $bansIds)) {
					$log->logInfo('Already banned: '.$profileName." (player_id=".$playerId.")");
				} else {
				      $sql = "SELECT name FROM $playerstable WHERE player_id=".$playerId;
				      $result = mysql_query($sql);
				      $row = mysql_fetch_array($result);
				      $name = $row['name'];
					$banDays = 1;
				        $sql = "SELECT * FROM $playerstatustable WHERE userId=$playerId AND active='N' AND type='B'";
				        $res = mysql_query($sql);
				        while ($row = mysql_fetch_array($res)) {
				          $banDays = $banDays+2;
				        }

				      $log->logInfo('id='.$playerId.' name='.$name.' banDays='.$banDays.' badLine='.$badLine);
				      $type = "B";
					$date = time();
				      $expireDate = time() + (60*60*24*$banDays);

				          $sql = "INSERT INTO $playerstatustable (userId, userName, type, active, date, expireDate, forumLink, reason, additionalInfo) ".
				              "VALUES ('$playerId', '$name', '$type', 'Y', '$date', '$expireDate', '', 'Insulting','$badLine')";
				          $result = mysql_query($sql);
				          $log->logInfo('result='.$result.' sql='.$sql);
				
				          $sql = "UPDATE $playerstable SET approved='no' WHERE player_id=".$playerId;
				          $result = mysql_query($sql);
				          $log->logInfo('result='.$result.' sql='.$sql);
					  $bansIds[] = $playerId;

				}
			}

		} else {
			$log->logInfo('Could not parse name');
		}
	}
} 

?>
