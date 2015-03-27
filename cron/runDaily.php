#!/usr/bin/php
<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/cron/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/cron/', KLogger::INFO);

// PES

$log->logInfo('runDaily: start');

// delete old log entries
$sql = "DELETE FROM weblm_log_access WHERE accesstime < ( UNIX_TIMESTAMP( ) - (60*60*24*180))";
mysql_query($sql);
$log->logInfo('runDaily: Deleted old log entries: '.mysql_affected_rows());

// send email with played games to all users that selected this in their profile
$dateday = date("d/m/Y");
$yesterday = date("d/m/Y", time()-60*60*12);

$timespan = 60*60*24;

$playersquery = "select distinct winner from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' " .
		"UNION " .
		"select distinct winner2 from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' " .
		"UNION " .
		"select distinct loser from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' " .
		"UNION " .
		"select distinct loser2 from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' ";
	
$result = mysql_query($playersquery);
$playerscount = mysql_num_rows($result);

$adminMessage = "";

$sql_total = "select count(*) AS c from $gamestable " .
		"WHERE deleted = 'no' " .
		"AND dateday = '$yesterday' " .
		"ORDER BY date DESC";
$res_total = mysql_query($sql_total);
$row_total = mysql_fetch_array($res_total);
$count_total = $row_total['c'];

$sql_deleted = "select count(*) AS c from $gamestable " .
		"WHERE deleted = 'yes' " .
		"AND dateday = '$yesterday' " .
		"ORDER BY date DESC";
$res_deleted = mysql_query($sql_deleted);
$row_deleted = mysql_fetch_array($res_deleted);
$count_deleted = $row_deleted['c'];

while ($row = mysql_fetch_array($result)) {
	// for each player that played do...
	$logged = 0;
	$sendmail = 0;
	
	$name = $row[0];
	
	if (!empty($name)) {
	$profileurl = $directory."/editprofile.php";
		
		$gamesquery = "select * from $gamestable " .
				"WHERE (winner = '$name' OR winner2 = '$name' OR loser = '$name' OR loser2 = '$name') " .
				"AND deleted = 'no' " .
				"AND dateday = '$yesterday' " .
				"ORDER BY date DESC";
				
		$playergames = mysql_query($gamesquery);
		$gamescount = mysql_num_rows($playergames);
	
		$playerquery = "SELECT mail, sendGamesMail, invalidEmail from $playerstable ".
				"WHERE name = '$name'";
	
		$player = mysql_query($playerquery);
		$playerresult = mysql_fetch_array($player);
		
		$toAddress = $playerresult['mail'];
		$sendGamesMail = $playerresult['sendGamesMail'] == 'yes';
    $invalidEmail = $playerresult['invalidEmail'] == 1;
		
		if (isValidEmailAddress($toAddress) && $sendGamesMail && !$invalidEmail) {
	
			$subject = "[$leaguename] $name's games for $yesterday";
		
			$head = "From:".$adminmail."\r\nReply-To:".$adminmail."\r\nReturn-Path:".$adminmail;
		
			$message = "Hello $name,\n" .
					"\n" .
					"here's a summary of your $leaguename ladder games reported today.\n" .
					"\n";
		
			while ($row_game = mysql_fetch_array($playergames)) {
		
					$gametime = date("h:i a", $row_game['date']);
					
					$winpoints = $row_game['winpoints'];
					$winner = $row_game['winner'];
					$winner2 = $row_game['winner2'];			
					if (strlen($winner2) > 0) {
						$winner = $winner."/".$winner2;
					}
					$winnerresult = $row_game['winnerresult'];
					$loserresult = $row_game['loserresult'];
					$loser = $row_game['loser'];
					$loser2 = $row_game['loser2'];
					$losepoints = $row_game['losepoints'];
					if (strlen($loser2) > 0) {
						$loser = $loser."/".$loser2;
						if ($name == $loser2) {
							$losepoints = $row_game['losepoints2'];
						}
					}
					
					$message .= "[$gametime] " .
						"(+$winpoints) $winner " .
						"$winnerresult - $loserresult " .
						"$loser (-$losepoints)\n";
			}
			
			$message .= "\n" .
				"If you think these results are not correct, please post it in \n" .
				"the forum ($directory/forum/viewforum.php?f=5).\n" . 
				"Reporting these issues helps us keep the ladder clean!\n" .
				"\n" .
				"You will receive this summary every day if you have played a game.\n" .
				"If you wish to cancel this, you can disable it by editing your profile at \n" .
				"$profileurl\n" .
				"\n" .
				"\n" .			
				"- The ".$leaguename." Staff\n";
												
			$sendmail = @mail ($toAddress, $subject, $message, $head, $mailconfig);
			$logged = logSentMail($name, $toAddress, 'games');
	
		} // if valid address
		
		$adminMessage .= "user [$name] played [$gamescount] address [$toAddress] mailSent [$sendmail] log [$logged]\n";
	}
}
if ($playerscount > 0) {
	$playerscount--;
}
$adminSubject = "[$leaguename PES] g=[$count_total] p=[$playerscount] d=[$count_deleted]";

if ($count_total > 0) {
	// sendAdminMail($adminSubject,$adminMessage);
}

// prune unused

// require('/var/www/yoursite/cron/pruneUnused.php');

// expire signup links

$timeBorder = time() - 60*60*96;
$sql = "update $signuptable set expired='yes' where sid < '$timeBorder'";
$result = mysql_query($sql);

// expire

$date = time();

$sql = "SELECT * FROM $playerstatustable WHERE $date > expireDate AND expireDate > 0 AND active='Y'";
$result = mysql_query($sql);
$expireText = "";
while ($row = mysql_fetch_array($result)) {
	$id = $row['id'];
	$userName = $row['userName'];
	$reason = $row['reason'];
	$sql = "UPDATE $playerstatustable SET active='N' where id='$id'";
	$result2 = mysql_query($sql);
	$type = $row['type'];
	$expireText .= "[".$userName."] expired [".$result2."]";
	if ($type == 'B') {
		// other active bans?
    $sql = "SELECT count(*) FROM $playerstatustable WHERE userName='".$userName."' AND active='Y' AND type='B'";
    $res = mysql_query($sql);
    $rowCount = mysql_fetch_array($res);
    if ($rowCount[0] == 0) {
      // activate player
      $sql = "UPDATE $playerstable SET approved='yes' where name='$userName'";
      $result3 = mysql_query($sql);
      $expireText .= " approved [".$result3."]";
    }
	}
	$expireText .= "\n"."reason [".$reason."]"."\n\n";
}

if (!empty ($expireText)) {
	$adminSubject = "[$leaguename admin] bans expired";
	// sendAdminMail($adminSubject, $expireText);
}

// add entries for unapproved players if they don't already have one
/*

$sql = "SELECT name, player_id, joindate from $playerstable ".
	"WHERE approved = 'no' ".
	"ORDER BY joindate asc";
$result = mysql_query($sql);


while ($row = mysql_fetch_array($result)) {
	$name = $row['name'];
	$userId = $row['player_id'];
	$date = time();
	$joindate = $row['joindate'];
	$reason = "inactive (joined ". formatDate($joindate). ")";
	$sql2 = "SELECT * from $playerstatustable where userName='$name' and type != 'W'";
	$result2 = mysql_query($sql2);
	if (mysql_num_rows($result2) == 0) {
		// player is inactive but has no entry, create one
		$sql3 = "INSERT INTO $playerstatustable (userId, userName, type, active, ".
			"date, expireDate, forumLink, reason) "."VALUES ('$userId', '$name', 'I', 'Y', ".
			"'$date', '', '', '$reason')";
		$result3 = mysql_query($sql3);
	}
}
*/

// remove negative expire values
// $sql = "UPDATE $playerstatustable SET expireDate = 0 ".
// 	"WHERE UNIX_TIMESTAMP( ) > expireDate ".
// 	"AND expireDate > 0";
// $result = mysql_query($sql);

$log->logInfo('runDaily: check points');

// check points
$sql = "Select name, ra2pes5 from $playerstable where approved = 'yes' and pes5games > 0 order by ra2pes5 desc";
$result = mysql_query($sql);
$checkPointsRes = "";
$vers = "'H', 'I', 'J', '6', '7', '8'";

while ($row = mysql_fetch_array($result)) {
	$name = $row['name'];
	$ra2pes5  = $row['ra2pes5'];
	$sql1 = "SELECT sum( winpoints ) as winpts FROM $gamestable ".
	"WHERE (winner LIKE '$name' OR winner2 LIKE '$name') AND deleted = 'no' AND season = '$season' AND version in (".$vers.") AND teamLadder = 0";		
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	$winpts = $row1['winpts'];
	
	$sql2 = "SELECT sum(losepoints) as losepts1 FROM $gamestable ".
	"WHERE (loser LIKE '$name') AND deleted = 'no' AND season = '$season' AND version in (".$vers.") AND teamLadder = 0";		
	$result2 = mysql_query($sql2);
	$row2 = mysql_fetch_array($result2);
	$losepts1 = $row2['losepts1'];

	$sql3 = "SELECT sum(losepoints2) as losepts2 FROM $gamestable ".
	"WHERE (loser2 LIKE '$name') AND deleted = 'no' and season = '$season' AND teamLadder = 0";		
	$result3 = mysql_query($sql3);
	$row3 = mysql_fetch_array($result3);
	$losepts2 = $row3['losepts2'];
	$total = $winpts-$losepts1-$losepts2;
	if ($total != $ra2pes5) {
		$checkPointsRes .= "player [".$name."] has [".$ra2pes5."] points but should have [".$total."]\n"; 
	} 
}

if (!empty($checkPointsRes)) {
	$adminSubject = "[$leaguename PES] errors detected";
	sendAdminMail($adminSubject, $checkPointsRes);
}

require('/var/www/yoursite/http/fifa/variables.php');
require('/var/www/yoursite/http/fifa/variablesdb.php');

// FIFA
$log->logInfo('runDaily: start FIFA');

// send email with played games to all users that selected this in their profile
$dateday = date("d/m/Y");
$yesterday = date("d/m/Y", time()-60*60*12);

$timespan = 60*60*24;

$playersquery = "select distinct winner from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' " .
		"UNION " .
		"select distinct winner2 from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' " .
		"UNION " .
		"select distinct loser from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' " .
		"UNION " .
		"select distinct loser2 from $gamestable where deleted='no' " .
		"AND dateday = '$yesterday' ";
	
$result = mysql_query($playersquery);
$playerscount = mysql_num_rows($result);

$adminMessage = "";

$sql_total = "select count(*) AS c from $gamestable " .
		"WHERE deleted = 'no' " .
		"AND dateday = '$yesterday' " .
		"ORDER BY date DESC";
$res_total = mysql_query($sql_total);
$row_total = mysql_fetch_array($res_total);
$count_total = $row_total['c'];

$sql_deleted = "select count(*) AS c from $gamestable " .
		"WHERE deleted = 'yes' " .
		"AND dateday = '$yesterday' " .
		"ORDER BY date DESC";
$res_deleted = mysql_query($sql_deleted);
$row_deleted = mysql_fetch_array($res_deleted);
$count_deleted = $row_deleted['c'];

while ($row = mysql_fetch_array($result)) {
	// for each player that played do...
	$logged = 0;
	$sendmail = 0;
	
	$name = $row[0];
	
	if (!empty($name)) {
	$profileurl = $directory."/editprofile.php";
		
		$gamesquery = "select * from $gamestable " .
				"WHERE (winner = '$name' OR winner2 = '$name' OR loser = '$name' OR loser2 = '$name') " .
				"AND deleted = 'no' " .
				"AND dateday = '$yesterday' " .
				"ORDER BY date DESC";
				
		$playergames = mysql_query($gamesquery);
		$gamescount = mysql_num_rows($playergames);
	
		$playerquery = "SELECT mail, sendGamesMail from $playerstable ".
				"WHERE name = '$name'";
	
		$player = mysql_query($playerquery);
		$playerresult = mysql_fetch_array($player);
		
		$toAddress = $playerresult['mail'];
		$sendGamesMail = $playerresult['sendGamesMail'] == 'yes';
		
		if (isValidEmailAddress($toAddress) && $sendGamesMail) {
	
			$subject = "[$leaguename] $name's games for $yesterday";
		
			$head = "From:".$adminmail."\r\nReply-To:".$adminmail."\r\nReturn-Path:".$adminmail;
		
			$message = "Hello $name,\n" .
					"\n" .
					"here's a summary of the $leaguename games you played today.\n" .
					"\n";
		
			while ($row_game = mysql_fetch_array($playergames)) {
		
					$gametime = date("h:i a", $row_game['date']);
					
					$winpoints = $row_game['winpoints'];
					$winner = $row_game['winner'];
					$winner2 = $row_game['winner2'];			
					if (strlen($winner2) > 0) {
						$winner = $winner."/".$winner2;
					}
					$winnerresult = $row_game['winnerresult'];
					$loserresult = $row_game['loserresult'];
					$loser = $row_game['loser'];
					$loser2 = $row_game['loser2'];
					$losepoints = $row_game['losepoints'];
					if (strlen($loser2) > 0) {
						$loser = $loser."/".$loser2;
						if ($name == $loser2) {
							$losepoints = $row_game['losepoints2'];
						}
					}
					
					$message .= "[$gametime] " .
						"(+$winpoints) $winner " .
						"$winnerresult - $loserresult " .
						"$loser (-$losepoints)\n";
			}
			
			$message .= "\n" .
				"If you think these results are not correct, please post it in \n" .
				"the forum ($directory/forum/viewforum.php?f=5).\n" . 
				"Reporting these issues helps us keep the league clean!\n" .
				"\n" .
				"You will receive this summary every day if you have played a game.\n" .
				"If you wish to cancel this, you can disable it by editing your profile at \n" .
				"$profileurl\n" .
				"\n" .
				"\n" .			
				"- The ".$leaguename." Staff\n";
												
			$sendmail = @mail ($toAddress, $subject, $message, $head, $mailconfig);
			$logged = logSentMail($name, $toAddress, 'games');
	
		} // if valid address
		
		$adminMessage .= "user [$name] played [$gamescount] address [$toAddress] mailSent [$sendmail] log [$logged]\n";
	}
}
if ($playerscount > 0) {
	$playerscount--;
}
$adminSubject = "[$leaguename FIFA] g=[$count_total] p=[$playerscount] d=[$count_deleted]";

if ($count_total > 0) {
	sendAdminMail($adminSubject,$adminMessage);
}

// remove negative expire values
// $sql = "UPDATE $playerstatustable SET expireDate = 0 ".
// 	"WHERE UNIX_TIMESTAMP( ) > expireDate ".
// 	"AND expireDate > 0";
// $result = mysql_query($sql);

// check points
$sql = "Select name, ra2pes5 from $playerstable where approved = 'yes' and pes5games > 0 order by ra2pes5 desc";
$result = mysql_query($sql);
$checkPointsRes = "";
$vers = "'A', 'B', 'C'";

while ($row = mysql_fetch_array($result)) {
	$name = $row['name'];
	$ra2pes5  = $row['ra2pes5'];
	$sql1 = "SELECT sum( winpoints ) as winpts FROM $gamestable ".
	"WHERE (winner LIKE '$name' OR winner2 LIKE '$name') AND deleted = 'no' AND season = '$season' AND version in (".$vers.") AND teamLadder = 0";		
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	$winpts = $row1['winpts'];
	
	$sql2 = "SELECT sum(losepoints) as losepts1 FROM $gamestable ".
	"WHERE (loser LIKE '$name') AND deleted = 'no' AND season = '$season' AND version in (".$vers.") AND teamLadder = 0";		
	$result2 = mysql_query($sql2);
	$row2 = mysql_fetch_array($result2);
	$losepts1 = $row2['losepts1'];

	$sql3 = "SELECT sum(losepoints2) as losepts2 FROM $gamestable ".
	"WHERE (loser2 LIKE '$name') AND deleted = 'no' and season = '$season' AND teamLadder = 0";		
	$result3 = mysql_query($sql3);
	$row3 = mysql_fetch_array($result3);
	$losepts2 = $row3['losepts2'];
	$total = $winpts-$losepts1-$losepts2;
	if ($total != $ra2pes5) {
		$checkPointsRes .= "player [".$name."] has [".$ra2pes5."] points but should have [".$total."]\n"; 
	} 
}

if (!empty($checkPointsRes)) {
	$adminSubject = "[$leaguename FIFA] errors detected";
	sendAdminMail($adminSubject, $checkPointsRes);
}

$log->logInfo('runDaily: end');

?>