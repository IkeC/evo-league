<?php

// functions used for the admin panel

function sendAdminMail($adminSubject, $adminMessage) {
	require('/var/www/yoursite/http/variables.php');
	require('/var/www/yoursite/http/variablesdb.php');
	
	$head = "From:".$adminmail."\r\nReply-To:".$adminmail."";

	$mailres = @mail ($adminmail, $adminSubject, $adminMessage, $head, $mailconfig);

}

// sends a new user a sign-up mail
function sendSignupMail($toAddress, $signupLink, $adminName) {
	require ('/var/www/yoursite/http/variables.php');
	require ('/var/www/yoursite/http/variablesdb.php');
	
	$fromAddress = $admin_signup."@".$leaguename.".com";
	$head = "From:".$fromAddress."\r\nReply-To:".$fromAddress."";
	$subject = "[$leaguename] sign-up";
	$message = "Hi player!

Below is your personal sign-up link to create a player account on ".$leaguename.". It's only valid once and only for you, so don't send it to anyone. Follow this sign-up link to enter your account details.

".$signupLink ."

Have fun!

- The yoursite staff";

	@ mail($toAddress, $subject, $message, $head, $mailconfig);
	 
	$res = logSentMail($adminName, $toAddress, "signup");
	return getTextForSendResult($res);
}

// sends a new user a sign-up mail
function sendApprovedMail($toAddress, $playerName) {
	require ('/var/www/yoursite/http/variables.php');
	require ('/var/www/yoursite/http/variablesdb.php');
	
	$fromAddress = $admin_signup."@".$leaguename.".com";
	$head = "From:".$fromAddress."\r\nReply-To:".$fromAddress."";
	$subject = "[$leaguename] account approved";
	$message = "Hi ".$playerName."!

Your ".$leaguename." account has been checked and activated. Visit our chatroom to find other players. 

Enjoy your stay!

- The ".$leaguename." staff";

	@ mail($toAddress, $subject, $message, $head, $mailconfig);
	 
	$res = logSentMail($playerName, $toAddress, "approved");
	return getTextForSendResult($res);
}

function sendHtmlMail($toAddress, $subject, $htmlContent, $plainContent) {

// Grab our config settings
require_once($_SERVER["DOCUMENT_ROOT"] . '/phpmailer-2.0.2/config.php');
require_once($_SERVER["DOCUMENT_ROOT"] .'/phpmailer-2.0.2/FreakMailer.inc');

$result = "";
 
// Grab the FreakMailer class
// instantiate the class
$mailer = new FreakMailer();

$mailer->IsHTML(true);
$mailer->CharSet = "ISO-8859-1";
$mailer->ClearAttachments();

$mailer->From = 'ike@yoursite';
$mailer->FromName = 'yoursite staff';

// bounces!!
$mailer->Sender = 'ike@yoursite';

// Set the subject
$mailer->Subject = $subject;

// Body
$mailer->Body = $htmlContent;
$mailer->AltBody = $plainContent; 

// Add an address to send to.
$mailer->AddAddress($toAddress);

$mailer->Send();

$result = $mailer->ErrorInfo;

$mailer->ClearAddresses();
$mailer->ClearAttachments();

return $result;

}

function getLastOnline($user, $user2, $limit) {
	require('/var/www/yoursite/http/variables.php');
	require('/var/www/yoursite/http/variablesdb.php');
	$res = "";
	if (empty($user2)) {
    $sql = "select user, accesstime, ip, logType from $logtable where user like '%$user%' or ip like '%$user%' order by id desc limit 0,$limit";
    $userDisplay = $user;
	} else {
    $sql = "select user, accesstime, ip, logType from $logtable where user like '%$user%' or ip like '%$user%' or user like '%$user2%' or ip like '%$user2%' order by id desc limit 0,$limit";
    $userDisplay = $user."/".$user2;
  }
  
  $result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$f_user = $row['user'];
		$f_accesstime = $row['accesstime'];
		$f_ip = $row['ip'];
    $f_logType = $row['logType'];
		$res .= "<b>".formatDate($f_accesstime)."</b>&nbsp;(".$f_logType.")&nbsp;&nbsp;-&nbsp;&nbsp;".$f_user."&nbsp;&nbsp;-&nbsp;&nbsp;".$f_ip."<br>"; 
	}
	
  if (empty($res)) {
		$res = "No access logged from <b>".$userDisplay."</b>!<br>";
	} else {
		$res = "<p>Last $limit logged site hits for <b>$userDisplay</b>:</p><p>".$res."</p>";
	}
	return $res;
}

function getLastOnlineIP($ip, $limit) {
	require('/var/www/yoursite/http/variables.php');
	require('/var/www/yoursite/http/variablesdb.php');
	$res = "";
	$sql = "select user, accesstime, ip, logType from $logtable where ip like '$ip' order by id desc limit 0,$limit";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$f_user = $row['user'];
		$f_accesstime = $row['accesstime'];
		$f_ip = $row['ip'];
    $f_logType = $row['logType'];
		$res .= "<b>".formatDate($f_accesstime)."</b>&nbsp;(".$f_logType.")&nbsp;&nbsp;-&nbsp;&nbsp;".$f_user."&nbsp;&nbsp;-&nbsp;&nbsp;".$f_ip."<br>"; 
	}
	if (empty($res)) {
		$res = "No access logged from <b>".$ip."</b>!<br>";
	} else {
		$res = "<p>Last $limit logged site hits for <b>$ip</b>:</p><p>".$res."</p>";
	}
	return $res;
}

function isValidIp($ip_addr) {
 $num="(\*|[0-9]{1,3}|^1?\d\d$|2[0-4]\d|25[0-5])";
 if(preg_match("/$num\.$num\.$num\.$num/",$ip_addr,$matches)){
    return $matches[0];
 } else {
    return false;
 }
}

function replacePlayerLinks(&$news) {
	$playertag_open = "<pl>";
	$playertag_close = "</pl>";
	while (stristr($news, $playertag_open)) {
		$startindex = strpos($news, $playertag_open);
		$endindex = strpos($news, $playertag_close);
		$startpos = $startindex + strlen($playertag_open);
		$name = substr($news, $startpos, $endindex - $startpos);
		$playerId = getIdForPlayer($name);
		if (!empty($playerId)) {
			$nameLink = getPlayerLinkId($name, $playerId);
			$news = str_replace($playertag_open.$name.$playertag_close, $nameLink, $news);
		} else {
			echo getBoxTop("Info", 0, true, '');
			echo "<p><b>Error:</b> Player <b>".$name."</b> not found!";
			echo getBoxBottom();
			return;
		}
	}
}


function GetFishyAccessRows($days) {
	require ('/var/www/yoursite/http/variables.php');
	require ('/var/www/yoursite/http/variablesdb.php');

  $ago = time()-60*60*24*$days;
  $sql = "SELECT user, ip, accesstime, logType from weblm_log_access ".
      "WHERE accesstime > ".$ago." ".
      "group by ip, user, logType order by ip asc, accesstime desc";
  
  //echo $sql;
  
  $result = mysql_query($sql);
  $prev = array('user' => '', 'ip' => '', 'accesstime' => '', 'logType' => '', 'fishyFlag' => 0);
  $prevLine = "";
  $fishy = array();

  while ($row = mysql_fetch_array($result)) {
    $user = $row['user'];
    $ip = $row['ip'];
    $accesstime = $row['accesstime'];
    $logType = $row['logType'];
    $style = '';
    $fishyFlag = 0;
    if ($ip == $prev['ip'] && $user != $prev['user']) {
      $style = 'style="color:red;"';
      $fishy[] = $prev;
      $fishyFlag = 1;
    } elseif ($prev['fishyFlag'] == 1) {
      $fishy[] = $prev;
    }
    $prev = array('user' => ''.$user, 'ip' => ''.$ip, 'accesstime' => $accesstime, 'logType' => $logType, 'fishyFlag' => $fishyFlag);
  }
  
  return $fishy;
}

function StartSixserverSeason() {
  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'./../variables.php');
  require($appRoot.'./../variablesdb.php');
  require($appRoot.'./../sixserver/functions.php');
  
  $msg = "";

  $pointsArray = GetStandingsArray(true);
  
  $sql = "SELECT season FROM six_stats";
  $sixSeason = mysql_fetch_array(mysql_query($sql))[0];
  
  $sql = "SELECT sp.id as profileId, sp.points, sp.disconnects, " .
				"weblm_players.player_id, weblm_players.name AS playerName, weblm_players.nationality, weblm_players.approved FROM six_profiles sp " .
				"LEFT JOIN weblm_players ON weblm_players.player_id=sp.user_id " .
				"ORDER BY sp.points DESC, playerName ASC";
  $msg .= "<p>".$sql;
  
  $result = mysql_query($sql);
  $oldPoints = -1;
  $counter = 1;
	$pos = -1;
  
	while ($row = mysql_fetch_array($result)) {
		
		$profileId = $row['profileId'];
    $playerId = $row['player_id'];
		$points = $row['points'];
    $disconnects = $row['disconnects'];
		if (isset($pointsArray[$profileId]['w'])) {
      $wins = $pointsArray[$profileId]['w'];
    } else {
      $wins = 0;
    }
    if (isset($pointsArray[$profileId]['l'])) {
      $losses = $pointsArray[$profileId]['l'];
    } else {
      $losses = 0;
    }
		if (isset($pointsArray[$profileId]['d'])) {
      $draws = $pointsArray[$profileId]['d'];
    } else {
      $draws = 0;
    }
		$games = $wins + $losses + $draws;
    $approved = $row['approved'] == 'yes';

    if (!$approved) {
      $pos = 0;
    } else {
      if ($oldPoints != $points) {
        $pos = $counter;
        $oldPoints = $points;
      }
      $counter++;
    }
		
		if ($games > 0) {
			$percentage = $wins / $games;
      
      $sql = "INSERT INTO six_history (playerId, profileId, season, position, points, games, wins, losses, draws, DC) " .
        "VALUES ('$playerId', '$profileId', '$sixSeason', '$pos', '$points', '$games', '$wins', '$losses', '$draws', '$disconnects')";
      $msg .= "<p>".$sql."</p>".PHP_EOL;
      mysql_query($sql);
		}
	}
  
  // set points and dc to zero
  $update_sql = "UPDATE six_profiles set points=0, disconnects=0";
  $result = mysql_query($update_sql);
  $msg .= "<p>set points to zero [".mysql_affected_rows()."]<p>".PHP_EOL;
  
    // update season var
  $update_sql = "UPDATE six_stats set season=season+1";
  $result = mysql_query($update_sql);
  $msg .= "<p>update season var [".mysql_affected_rows()."]<p>".PHP_EOL;
  
  return $msg;
}

function StartLadderSeason() {

    $appRoot = realpath( dirname( __FILE__ ) ).'/';
    require($appRoot.'./../variables.php');
    require($appRoot.'./../variablesdb.php');
    require($appRoot.'./../functions.php');
    
    $msg = "";
    
    $sql = "SELECT ladders FROM weblm_seasons WHERE season=".$season;
    $result = mysql_query($sql);
    $ladder2 = mysql_fetch_array($result)[0];
    
		// set maintenance
		$sql = "update $varstable set maintenance = 'yes'";
		$result = mysql_query($sql);
		
		$version2 = getVersionForLadder('H'); // PES 6
		$pointsField2 = getPointsFieldForVersion($version2);
		$gamesField2 = getGamesFieldForVersion($version2);
		$winsField2 = getWinsFieldForVersion($version2);
		$lossesField2 = getLossesFieldForVersion($version2);

		$sortby = $pointsField2." DESC, percentage DESC, $lossesField2 ASC";
		$sql = "SELECT *, $winsField2/$gamesField2 as percentage FROM $playerstable "."WHERE $gamesField2 > 0 ORDER BY $sortby";
		$result = mysql_query($sql);
		
		$pos = 0;
		
		// iterate over players and update history
		while($row = mysql_fetch_array($result)) {
		
			$player_id = $row['player_id'];
			$name = $row['name'];
			$points = $row[$pointsField2];
			$games = $row[$gamesField2];
			$wins = $row[$winsField2];
			$losses = $row[$lossesField2];
			$draws = $row["draws"];
			$approved = $row['approved'] == 'yes';
		
			if ($approved) {
				$pos++;	    
				$position = $pos;
			}
			else {
				$position = 0;
			}
			
			$msg .= "<p>".PHP_EOL;
      
			// goals as winner / 1on1
			$games_sql = "SELECT sum(winnerresult) as goals_for, sum(loserresult) as goals_against ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND winner = '$name' AND winner2 = '' AND loser2 = '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForWinner1on1 = $games_row['goals_for'];
			
      $msg .= "$name goalsForWinner1on1: $goalsForWinner1on1<br>".PHP_EOL;
			
      $goalsAgainstWinner1on1 = $games_row['goals_against'];
			
      $msg .= "$name goalsAgainstWinner1on1: $goalsAgainstWinner1on1<br>".PHP_EOL;
			
      $goals_for = $goalsForWinner1on1;
			$goals_against = $goalsAgainstWinner1on1;

			// goals as winner / 1on2
			$games_sql = "SELECT sum(winnerresult) as goals_for, sum(loserresult) as goals_against ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND winner = '$name' AND winner2 = '' AND loser2 != '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForWinner1on2 = $games_row['goals_for'];
			
      $msg .= "$name goalsForWinner1on2: $goalsForWinner1on2<br>".PHP_EOL;
			
      $goals_for += $goalsForWinner1on2;
			$goalsAgainstWinner1on2 = $games_row['goals_against']/2;
			
      $msg .= "$name goalsAgainstWinner1on2: $goalsAgainstWinner1on2<br>".PHP_EOL;
			
      $goals_against += $goalsAgainstWinner1on2;

			// goals as winner / 2on1
			$games_sql = "SELECT sum(winnerresult) as goals_for, sum(loserresult) as goals_against ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND (winner = '$name' OR winner2 = '$name') ".
				"AND winner2 != '' AND loser2 = '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForWinner2on1 = $games_row['goals_for']/2;
			
      $msg .=  "$name goalsForWinner2on1: $goalsForWinner2on1<br>".PHP_EOL;
			
      $goals_for += $goalsForWinner2on1;
			$goalsAgainstWinner2on1 = $games_row['goals_against'];
			
      $msg .=  "$name goalsAgainstWinner2on1: $goalsAgainstWinner2on1<br>".PHP_EOL;
			
      $goals_against += $goalsAgainstWinner2on1;

			// goals as winner / 2on2
			$games_sql = "SELECT sum(winnerresult) as goals_for, sum(loserresult) as goals_against ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND (winner = '$name' OR winner2 = '$name') ".
				"AND winner2 != '' AND loser2 != '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForWinner2on2 = $games_row['goals_for']/2;
			
      $msg .=  "$name goalsForWinner2on2: $goalsForWinner2on2<br>".PHP_EOL;
			
      $goals_for += $goalsForWinner2on2;
			$goalsAgainstWinner2on2 = $games_row['goals_against']/2;
			
      $msg .=  "$name goalsAgainstWinner2on2: $goalsAgainstWinner2on2<br>".PHP_EOL;
			
      $goals_against += $goalsAgainstWinner2on2;
			
			// goals as loser / 1on1
			$games_sql = "SELECT sum(winnerresult) as goals_against, sum(loserresult) as goals_for ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND loser = '$name' AND loser2 = '' AND winner2 = '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForLoser1on1 = $games_row['goals_for']; 
			
      $msg .=  "$name goalsForLoser1on1: $goalsForLoser1on1<br>".PHP_EOL;
			
      $goals_for += $goalsForLoser1on1;
			$goalsAgainstLoser1on1 = $games_row['goals_against'];
			
      $msg .=  "$name goalsAgainstLoser1on1: $goalsAgainstLoser1on1<br>".PHP_EOL;
			
      $goals_against += $goalsAgainstLoser1on1;
			
			// goals as loser / 1on2
			$games_sql = "SELECT sum(winnerresult) as goals_against, sum(loserresult) as goals_for ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND (loser = '$name' OR loser2 = '$name') AND loser2 != '' AND winner2 = '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForLoser1on2 = $games_row['goals_for']/2;
			
      $msg .=  "$name goalsForLoser1on2: $goalsForLoser1on2<br>".PHP_EOL;
			
      $goals_for += $goalsForLoser1on2;
			$goalsAgainstLoser1on2 = $games_row['goals_against'];
			
      $msg .=  "$name goalsAgainstLoser1on2: $goalsAgainstLoser1on2<br>".PHP_EOL;
			
      $goals_against += $goalsAgainstLoser1on2;
			
			// goals as loser / 2on1
			$games_sql = "SELECT sum(winnerresult) as goals_against, sum(loserresult) as goals_for ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND loser = '$name' AND loser2 = '' AND winner2 != '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForLoser2on1 = $games_row['goals_for'];
			
      $msg .= "$name goalsForLoser2on1: $goalsForLoser2on1<br>".PHP_EOL;
			
      $goals_for += $goalsForLoser2on1;
			$goalsAgainstLoser2on1 = $games_row['goals_against']/2;
			
      $msg .= "$name goalsAgainstLoser2on1: $goalsAgainstLoser2on1<br>".PHP_EOL;
			
      $goals_against += $goalsAgainstLoser2on1;

			// goals as loser / 2on2
			$games_sql = "SELECT sum(winnerresult) as goals_against, sum(loserresult) as goals_for ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND (loser = '$name' OR loser2 = '$name') AND loser2 != '' AND winner2 != '' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$goalsForLoser2on2 = $games_row['goals_for']/2;
			$msg .= "$name goalsForLoser2on2: $goalsForLoser2on2<br>".PHP_EOL;
			$goals_for +=  $goalsForLoser2on2;
			$goalsAgainstLoser2on2 = $games_row['goals_against']/2;
			$msg .= "$name goalsAgainstLoser2on2: $goalsAgainstLoser2on2<br>".PHP_EOL;
			$goals_against += $goalsAgainstLoser2on2;
			
			$msg .= "</p>".PHP_EOL;
			
			// aggregate games
			$games_sql = "SELECT count(*) as aggregate ".
				"FROM $gamestable " .
				"WHERE season = '$season' " .
				"AND (loser = '$name' or loser2 = '$name' or winner ='$name' or winner2 = '$name') AND deleted = 'no' AND host = 'A'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			$aggregate = $games_row['aggregate'];
				
			$msg .= "<p>[$name] goals for: [$goals_for] / ".
				"goals against: [$goals_against] / ".
				"aggregate: [$aggregate]<br>".PHP_EOL;
			
			$history_sql = "INSERT INTO $historytable ".
				"(id, player_id, player_name, season, ladder, position, points, games, aggregate, wins, ".
				"losses, draws, goals_for, goals_against) ".
				"VALUES ".
				"('', '$player_id', '$name', '$season', '$ladder2', '$position', '$points', '$games', '$aggregate', " .
				"'$wins', '$losses', '$draws', '$goals_for', '$goals_against')";
			
      $msg .= "SQL: [$history_sql]<br>".PHP_EOL;
      
			$history_result = mysql_query($history_sql);
				
			$msg .= "Insert for [$name] result [$history_result] position [$position]</p>".PHP_EOL;
		}
		
		$msg .= "<p>".$ladder2." ladder done!</p>".PHP_EOL;
		
		$msg .= "<p><hr></p>".PHP_EOL;
		
		// update players and set games to 0
		$update_sql = "UPDATE $playerstable SET $gamesField2 = 0, $winsField2 = 0, $lossesField2 = 0, $pointsField2 = 0, draws = 0";
		$result = mysql_query($update_sql);
		$msg .= "<p>Set games to 0 - Result [$result]<p>".PHP_EOL;

		$msg .= "<p><hr></p>".PHP_EOL;
		
		// update season var
		$update_sql = "UPDATE $varstable set season = season + 1";
		$result = mysql_query($update_sql);
		$msg .= "<p>update season var [$result]<p>".PHP_EOL;
		
		// unset maintenance
		$sql = "update $varstable set maintenance = 'no'";
		$result = mysql_query($sql);
		$msg .= "Unset maintenance - Result [$result]<p>".PHP_EOL;
    
    return $msg;
}

function DeleteFinishedSixserverGame($sixGameId) {

  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'./../variables.php');
  require($appRoot.'./../variablesdb.php');
  require_once($appRoot.'./../functions.php');

  // current season
  $sql = "SELECT season FROM six_stats";
  $sixSeason = mysql_fetch_array(mysql_query($sql))[0];

  $sql = "SELECT * FROM six_matches WHERE id=$sixGameId AND season=$sixSeason";
  $row = mysql_fetch_array(mysql_query($sql));
  $profiles = array();
  
  $resultMsg = "";
  
  if (empty($row)) {
    $resultMsg.= "Finished Sixserver game $sixGameId not found";
  } else {
    $sql = "SELECT smp.profile_id, sp.disconnects, sp.points, sp.rating FROM six_matches_played smp ".
      "LEFT JOIN six_profiles sp ON sp.id=smp.profile_id ".
      "WHERE smp.match_id=".$sixGameId;
    // echo "<p>SQL: $sql</p>";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
      $profiles[] = array('profileId' => $row['profile_id'], 'dc' => $row['disconnects'], 'pts' => $row['points'], 'rating' => $row['rating']);
    }
    // delete match
    $sql = "DELETE FROM six_matches_played WHERE match_id=$sixGameId";
    $result = mysql_query($sql);
    $resultMsg.= "<p>SQL: $sql - Affected: ".mysql_affected_rows()."</p>"; 
    
    $sql = "DELETE FROM six_matches WHERE id=$sixGameId";
    $result = mysql_query($sql);
    $resultMsg.= "<p>SQL: $sql - Affected: ".mysql_affected_rows()."</p>"; 

    foreach ($profiles as $profile) {
      $resultMsg .= RecalculatePointsForProfile($profile['profileId'], $profile['dc'], $profile['pts'], $profile['rating']);
      $resultMsg .= RecalculateStreak($profile['profileId']);
    }
  }
  return $resultMsg;
}

function DeleteUnfinishedSixserverGame($sixGameId) {

  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'./../variables.php');
  require($appRoot.'./../variablesdb.php');
  require_once($appRoot.'./../functions.php');

   // current season
  $sql = "SELECT season FROM six_stats";
  $sixSeason = mysql_fetch_array(mysql_query($sql))[0];

  $sql = "SELECT * FROM six_matches_status WHERE id=$sixGameId AND season=$sixSeason";
  $row = mysql_fetch_array(mysql_query($sql));
  $profiles = array();
  
  $resultMsg = "";
  
  if (empty($row)) {
    $resultMsg.= "Unfinished Sixserver game $sixGameId not found";
  } else {
    $profiles[] = $row['profileHome'];
    $profiles[] = $row['profileHome2'];
    $profiles[] = $row['profileHome3'];
    $profiles[] = $row['profileAway'];
    $profiles[] = $row['profileAway2'];
    $profiles[] = $row['profileAway3'];
    
    // Delete game
    $sql = "DELETE FROM six_matches_status WHERE id=$sixGameId";
    mysql_query($sql);
    $resultMsg.= "<p>SQL: $sql - Affected: ".mysql_affected_rows()."</p>"; 
    
    foreach ($profiles as $profile) {
      if (!empty($profile)) {
        $resultMsg .= RecalculateDcForProfile($profile);
        
        $sql = "SELECT disconnects, points, rating FROM six_profiles WHERE id=$profile";
        $result = mysql_query($sql);
        $resultMsg.= "<p>SQL: $sql - Affected: ".mysql_affected_rows()."</p>"; 
        
        if ($row = mysql_fetch_array($result)) {
          $resultMsg .= RecalculatePointsForProfile($profile, $row['disconnects'], $row['points'], $row['rating']);
          $resultMsg .= RecalculateStreak($profile);
        } else {
          $resultMsg .= "<p>No profile found for profileId=$profileId!</p>";
        }
      }
    }
  }
  return $resultMsg;
}


function RemoveLadderGame($removeGameId, $reason) {

  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'./../variables.php');
  require($appRoot.'./../variablesdb.php');
  require_once($appRoot.'./../functions.php');

  $resultMsg = "<p>Removing Game #" . $removeGameId . ", Reason: '".$deleteReason."'</p>";

  $sql = "select * from $gamestable where game_id = '$removeGameId'";
  $result = mysql_query($sql);

  $num = mysql_num_rows($result);
  if ($num != 1) {
      $resultMsg .= "<p>Invalid query, found $num results for '$sql'</p>";
  } else {
    $row = mysql_fetch_array($result);
    $deleted = $row['deleted'];
    $row_season = $row['season'];
    $isDraw = $row['isDraw'];
    if ($row_season != $season) {
      $resultMsg .=  "<p>Game #".$removeGameId." is not from this season!</p>";
    } else if ($deleted == 'yes') {
      $resultMsg = "<p>Game #".$removeGameId." is already deleted!</p>";
    } else {
          
  $version = $row["version"];
  $ratingdiff = $row['ratingdiff'];
  $winpoints = $row['winpoints'];
  $losepoints = $row['losepoints'];
  $losepoints2 = $row['losepoints2'];
  $winner = $row['winner'];
  $winner2 = $row['winner2'];
  $loser = $row['loser'];
  $loser2 = $row['loser2'];
  $teamLadder = $row['teamLadder'];
  $comment = $row['comment'];
  $sixGameId = $row['sixGameId'];

  if (stristr($comment, "Unfinished") && !empty($sixGameId)) {
    $resultMsg .= DeleteUnfinishedSixserverGame($sixGameId);
  } elseif (stristr($comment, "Sixserver Game") && !empty($sixGameId)) {
    $resultMsg .= DeleteFinishedSixserverGame($sixGameId);
  }

  if (!empty($deleteReason)) {
    $sql = "UPDATE $gamestable SET deleted = 'yes', " .
      "deletedBy = '$username', deleteReason = '$deleteReason' where game_id = '$removeGameId'";
  } else {
    $sql = "UPDATE $gamestable SET deleted = 'yes' where game_id = '$removeGameId'";
  }
  $result = mysql_query($sql);

  if ($result != 1) {
      $resultMsg .= '<p>Error updating game!</p>';
  } else {
    $resultMsg .= "<p>Game #$removeGameId deleted.</p>";
  } 

  $winner_streakArray = getStreak($winner);
  $winner_winstreak = $winner_streakArray[0];
  $winner_losestreak = $winner_streakArray[1];
  $resultMsg .= "<p>New streak for $winner: Won " . $winner_winstreak . ", lost " . $winner_losestreak . "</p>";

  $loser_streakArray = getStreak($loser);
  $loser_winstreak = $loser_streakArray[0];
  $loser_losestreak = $loser_streakArray[1];
  $resultMsg .= "<p>New streak for $loser: Won " . $loser_winstreak . ", lost " . $loser_losestreak . "</p>";

  if ($teamLadder == 0) {
    $pointsField = getPointsFieldForVersion($version);
    $winsField = getWinsFieldForVersion($version);
    $lossesField = getLossesFieldForVersion($version);
    $gamesField = getGamesFieldForVersion($version);
    $drawsField = "draws";
  } else {	
    $pointsField = "teamPoints";
    $winsField = "teamWins";
    $lossesField = "teamLosses";
    $drawsField = "teamDraws";
    $gamesField = "teamGames";
  }

    $sql = "SELECT $pointsField from $playerstable where name='$winner'";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    $ra2ladder = $row[$pointsField];
    $newra2ladder = $ra2ladder - $winpoints;
    if ($newra2ladder < 0) {
        $newra2ladder = 0;
    } 
    
    if ($isDraw > 0) {
      $winLoss = 0;
      $draw = 1;
    } else {
      $winLoss = 1;
      $draw = 0;
    }
    
    $sql = "UPDATE $playerstable SET $winsField=$winsField-$winLoss,
    totalwins = totalwins-$winLoss, $gamesField = $gamesField-1, totalgames =
    totalgames-1, streakwins = $winner_winstreak, 
    $drawsField = $drawsField-$draw, totaldraws = totaldraws-$draw, 
    streaklosses = $winner_losestreak, 
    rating = rating - $ratingdiff, $pointsField = $newra2ladder WHERE name='$winner'";

    $result = mysql_query($sql);
    if ($result != 1) {
        $resultMsg .= '<p>Error updating winner: $result</p>';
    } 

    $sql = "UPDATE $playerstable SET $lossesField =
    $lossesField-$winLoss, totallosses = totallosses-$winLoss, $gamesField= $gamesField-1, 
    totalgames = totalgames-1,  streakwins = $loser_winstreak, streaklosses = $loser_losestreak, 
    $drawsField = $drawsField-$draw, totaldraws = totaldraws-$draw, 
    rating = rating + $ratingdiff, $pointsField = $pointsField + $losepoints WHERE
    name='$loser'";

    $result = mysql_query($sql);
    if ($result != 1) {
        $resultMsg .= '<p>Error updating loser: $result</p>';
    } 
    
    if (!empty($winner2)) {
      $winner2_streakArray = getStreak($winner2);
      $winner2_winstreak = $winner2_streakArray[0];
      $winner2_losestreak = $winner2_streakArray[1];
      $resultMsg .= "<p>New streak for $winner2: Won " . $winner2_winstreak . ", lost " . $winner2_losestreak . "</p>";
    
      $sql = "SELECT $pointsField from $playerstable where name='$winner2'";
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $ra2ladder = $row[$pointsField];
      $newra2ladder = $ra2ladder - $winpoints;
      if ($newra2ladder < 0) {
          $newra2ladder = 0;
      } 
      $sql = "UPDATE $playerstable SET $winsField=$winsField-$winLoss,
      totalwins = totalwins-$winLoss, $gamesField = $gamesField-1, totalgames =
      totalgames-1, streakwins = $winner2_winstreak, $drawsField = $drawsField-$draw, totaldraws = totaldraws-$draw, streaklosses = $winner2_losestreak, 
      rating = rating - $ratingdiff, $pointsField = $newra2ladder WHERE name='$winner2'";

      $result = mysql_query($sql);
      if ($result != 1) {
          $resultMsg .= '<p>Error updating winner2: $result</p>';
      } 
    }
            
    if (!empty($loser2)) {
      $loser2_streakArray = getStreak($loser2);
      $loser2_winstreak = $loser2_streakArray[0];
      $loser2_losestreak = $loser2_streakArray[1];
      $resultMsg .= "<p>New streak for $loser2: Won " . $loser2_winstreak . ", lost " . $loser2_losestreak . "</p>";
    
       $sql = "UPDATE $playerstable SET $lossesField =
      $lossesField-$winLoss, totallosses = totallosses-$winLoss, $gamesField= $gamesField-1, totalgames
      = totalgames-1,  streakwins = $loser2_winstreak, streaklosses = $loser2_losestreak, $drawsField = $drawsField-$draw, totaldraws = totaldraws-$draw, 
      rating = rating + $ratingdiff, $pointsField = $pointsField + $losepoints2 WHERE
      name='$loser2'";

        $result = mysql_query($sql);
        if ($result != 1) {
            $resultMsg .= '<p>Error updating loser2: $result</p>';
        } 
      }
    }
  }
  return $resultMsg;

}

function RestoreLadderGame($restoreGameId) {

  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'./../variables.php');
  require($appRoot.'./../variablesdb.php');
  require_once($appRoot.'./../functions.php');

  $resultMsg = "Restoring Game #".$restoreGameId."<br>";

  $sql = "select * from $gamestable where game_id = '$restoreGameId'";
  $result = mysql_query($sql);

  $num = mysql_num_rows($result);

  if ($num != 1) {
      $resultMsg .= "Invalid query, found $num results for '$sql'";
  } else {
      $row = mysql_fetch_array($result);
      $deleted = $row['deleted'];
  $row_season = $row['season'];
  $isDraw = $row['isDraw'];
  if ($row_season != $season) {
    $resultMsg .=  "<p>Game #".$restoreGameId." is not from this season!</p>";
  }
      else if ($deleted == 'no') {
          $resultMsg .=  "<p>Game #".$restoreGameId." is not deleted!</p>";
      } else {
          $ratingdiff = $row['ratingdiff'];
          $winpoints = $row['winpoints'];
          $losepoints = $row['losepoints'];
          $losepoints2 = $row['losepoints2'];
          $winner = $row['winner'];
          $winner2 = $row['winner2'];
          $loser = $row['loser'];
          $loser2 = $row['loser2'];
  $teamLadder = $row['teamLadder'];
          
          $sql = "UPDATE $gamestable SET deleted = 'no' where game_id = '$restoreGameId'";
          $result = mysql_query($sql);

          if ($result != 1) {
              $resultMsg .= '<p>Error updating game!</p>';
          } else {
              $resultMsg .=  "<p>Game #$restoreGameId restored.</p>";
          } 

  $sql = "select version from $gamestable where game_id ='$restoreGameId'";
          $result = mysql_query($sql);
          while ($row = mysql_fetch_array($result)) {
            $version = $row["version"];
          }
          
  if ($teamLadder == 0) {
  $pointsField = getPointsFieldForVersion($version);
  $winsField = getWinsFieldForVersion($version);
  $lossesField = getLossesFieldForVersion($version);
  $gamesField = getGamesFieldForVersion($version);
  $drawsField = "draws";
  } else {	
  $pointsField = "teamPoints";
  $winsField = "teamWins";
  $lossesField = "teamLosses";
  $drawsField = "teamDraws";
  $gamesField = "teamGames";
  }
                   
          $winner_streakArray = getStreak($winner);
          $winner_winstreak = $winner_streakArray[0];
          $winner_losestreak = $winner_streakArray[1];
          $resultMsg .= "<p>New streak for $winner: Won " . $winner_winstreak . ", lost " . $winner_losestreak . "</p>";

          $loser_streakArray = getStreak($loser);
          $loser_winstreak = $loser_streakArray[0];
          $loser_losestreak = $loser_streakArray[1];
          $resultMsg .= "<p>New streak for $loser: Won " . $loser_winstreak . ", lost " . $loser_losestreak . "</p>";

          $sql = "SELECT $pointsField from $playerstable where name='$loser'";
          $result = mysql_query($sql);
          $row = mysql_fetch_array($result);
          $ra2ladder = $row[$pointsField];
          $newra2ladder = $ra2ladder - $losepoints;
          if ($newra2ladder < 0) {
              $newra2ladder = 0;
          } 
          
          if ($isDraw > 0) {
            $winLoss = 0;
            $draw = 1;
          } else {
            $winLoss = 1;
            $draw = 0;
          }
          
          $sql = "UPDATE $playerstable SET $lossesField = $lossesField + $winLoss," . 
  "totallosses = totallosses + $winLoss, " . 
  "$drawsField=$drawsField+$draw, totaldraws = totaldraws + $draw, ".
  "$gamesField = $gamesField + 1, " . "totalgames = totalgames + 1, " . 
  "streakwins = $loser_winstreak, " . 
  "streaklosses = $loser_losestreak, " . 
  "rating = rating - $ratingdiff, " . 
  "$pointsField = $newra2ladder WHERE name='$loser'";

          $result = mysql_query($sql);
          if ($result != 1) {
              $resultMsg .= '<p>Error updating loser: $result</p>';
          } 

          $sql = "UPDATE $playerstable " . "SET $winsField = $winsField+ $winLoss, " . 
  "totalwins = totalwins + $winLoss, " . 
  "$gamesField = $gamesField + 1, " . 
  "totalgames = totalgames + 1, " . 
  "$drawsField=$drawsField+$draw, totaldraws = totaldraws + $draw, ".
  "streakwins = $winner_winstreak, " . 
  "streaklosses = $winner_losestreak, " . 
  "rating = rating + $ratingdiff, " . 
  "$pointsField = $pointsField + $winpoints " . "WHERE name='$winner'";
          $result = mysql_query($sql);

          if ($result != 1) {
              $resultMsg .=  '<p>Error updating winner: '.$result.'</p>';
          } 
          
          // Winner2 / Loser2
          
          if (!empty($loser2)) {
            $loser2_streakArray = getStreak($loser2);
            $loser2_winstreak = $loser2_streakArray[0];
            $loser2_losestreak = $loser2_streakArray[1];
            $resultMsg .= "<p>New streak for $loser2: Won " . $loser2_winstreak . ", lost " . $loser2_losestreak . "</p>";
            
            $sql = "SELECT $pointsField from $playerstable where name='$loser2'";
            $result = mysql_query($sql);
            $row = mysql_fetch_array($result);
            $ra2ladder = $row[$pointsField];
            $newra2ladder = $ra2ladder - $losepoints2;
            if ($newra2ladder < 0) {
                $newra2ladder = 0;
            } 
            $sql = "UPDATE $playerstable SET $lossesField = $lossesField + $winLoss," . 
    "totallosses = totallosses + $winLoss, " . 
    "$gamesField = $gamesField + 1, " . "totalgames = totalgames + 1, " . 
    "$drawsField = $drawsField + $draw, totaldraws = totaldraws + $draw, ".
    "streakwins = $loser_winstreak, " . 
    "streaklosses = $loser_losestreak, " . 
    "rating = rating - $ratingdiff, " . 
    "$pointsField = $newra2ladder WHERE name='$loser2'";
            $result = mysql_query($sql);
            if ($result != 1) {
                $resultMsg .= '<p>Error updating loser2: $result</p>';
            } 
          }
           if (!empty($winner2)) {
  $winner2_streakArray = getStreak($winner2);
            $winner2_winstreak = $winner2_streakArray[0];
            $winner2_losestreak = $winner2_streakArray[1];
            $resultMsg .= "<p>New streak for $winner2: Won " . $winner2_winstreak . ", lost " . $winner2_losestreak . "</p>";
            
            $sql = "UPDATE $playerstable " . "SET $winsField = $winsField + $winLoss, " . 
    "totalwins = totalwins + $winLoss, " . 
    "$gamesField = $gamesField + 1, " . 
    "totalgames = totalgames + 1, " . 
    "$drawsField = $drawsField + $draw, totaldraws = totaldraws + $draw, ".
    "streakwins = $winner2_winstreak, " . 
    "streaklosses = $winner2_losestreak, " . 
    "rating = rating + $ratingdiff, " . 
    "$pointsField = $pointsField + $winpoints " . "WHERE name='$winner2'";
        $result = mysql_query($sql);

        if ($result != 1) {
            $resultMsg .=  '<p>Error updating winner: '.$result.'</p>';
        } 
      }
    } 
  } 
  return $resultMsg;
}

?>