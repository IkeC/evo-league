#!/usr/bin/php

<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');

$log = new KLogger('/var/www/yoursite/http/log/report/', KLogger::INFO);	

if ($maintenance == 'yes') {
	$log->logInfo('maintenance=yes, not reporting anything');
  die();
}

$start = time();

$log->LogInfo('--- Finished games');

// finished games
$sql = "
	SELECT sm.*, smp.home, wp.name, sp1.id AS patchId 
	FROM six_matches sm
	LEFT JOIN six_patches sp1 ON sm.hashHome = sp1.hash
	LEFT JOIN six_patches sp2 ON sm.hashAway = sp2.hash
	LEFT JOIN six_matches_played smp ON smp.match_id=sm.id 
	LEFT JOIN six_profiles sp ON smp.profile_id=sp.id 
	LEFT JOIN weblm_players wp ON wp.player_id=sp.user_id  
	WHERE sm.played_on>date_sub(now(), INTERVAL 24 HOUR) 
  AND sp1.id=sp2.id 
  AND sp1.autoReport=1 
	AND sp2.autoReport=1 
	AND sm.reported=0
	ORDER BY sm.id ASC, smp.home ASC";

// $log->logInfo('sql='.$sql);

$result = mysql_query($sql);

$smId = 0;
$winner1 = '';
$winner2 = '';
$loser1 = '';
$loser2 = '';
$teamLeft = '';
$teamRight = '';
$scoreLeft = '';
$scoreRight = '';
$homeLeft = true;
$patchId = 0;

while ($row = mysql_fetch_array($result)) {
	$smId = $row['id'];
	$home = $row['home'];
	$name = $row['name'];
  
	if ($smId <> $oldMatchId) {
		if ($oldMatchId > 0 && (empty($winner3) && empty($loser3))) {
			ReportToLadder($oldMatchId, $teamLeft, $teamRight, $winner1, $winner2, $loser1, $loser2, $scoreLeft, $scoreRight, $scoreLeftExt, $scoreRightExt, $patchId, $log, true);
		}
		
		$oldMatchId = $smId;
		$teamHome = $row['team_id_home'];
		$teamAway = $row['team_id_away'];
		$scoreHome = $row['score_home_reg'];
		$scoreAway = $row['score_away_reg'];
		$scoreHomeExt = $row['score_home'];
		$scoreAwayExt = $row['score_away'];
		$hashHome = $row['hashHome'];
		$hashAway = $row['hashAway'];
		$patchId = $row['patchId'];
    
		if ($scoreHome < $scoreAway) {
			$homeLeft = false;
			$teamLeft = $teamAway;
			$teamRight = $teamHome;
			$scoreLeft = $scoreAway;
			$scoreRight = $scoreHome;
			$scoreLeftExt = $scoreAwayExt;
			$scoreRightExt = $scoreHomeExt;
			$hashLeft = $hashAway;
			$hashRight = $hashHome;
		} else {
			$homeLeft = true;
			$teamLeft = $teamHome;
			$teamRight = $teamAway;
			$scoreLeft = $scoreHome;
			$scoreRight = $scoreAway;
			$scoreLeftExt = $scoreHomeExt;
			$scoreRightExt = $scoreAwayExt;
			$hashLeft = $hashHome;
			$hashRight = $hashAway;
		}
		
		$winner1 = '';
		$winner2 = '';
    $winner3 = '';
		$loser1 = '';
		$loser2 = '';
    $loser3 = '';
		
		if ($home == 1 && $homeLeft || $home == 0 && !$homeLeft) {
			$winner1 = $name;	
		} else {
			$loser1 = $name;
		}
		
	} else {
		// new row for existing game
		if ($home == 1 && $homeLeft || $home == 0 && !$homeLeft) {
			if ($winner1 == '') {
				$winner1 = $name;
			} elseif ($winner2 == '') {
				$winner2 = $name;
			} else {
        $winner3 = $name;
      }
		} else {
			if ($loser1 == '') {
				$loser1 = $name;
			} elseif ($loser2 == '') {
				$loser2 = $name;
			} else {
        $loser3 = $name;
      }
		}
	}
}

if ($smId > 0 && (empty($winner3) && empty($loser3))) {
	ReportToLadder($smId, $teamLeft, $teamRight, $winner1, $winner2, $loser1, $loser2, $scoreLeft, $scoreRight, $scoreLeftExt, $scoreRightExt, $patchId, $log, true);
}

$log->LogInfo('--- Unfinished games');

// unfinished games
$sql = "
	SELECT sms.*, sp1.id AS patchId 
	FROM six_matches_status sms
	LEFT JOIN six_patches sp1 ON sms.hashHome = sp1.hash 
	LEFT JOIN six_patches sp2 ON sms.hashAway = sp2.hash 
	WHERE sms.updated>date_sub(now(), INTERVAL 24 HOUR) 
	AND sms.updated<date_sub(now(), INTERVAL 10 MINUTE) 
  AND sp1.id=sp2.id 
  AND sp1.autoReport=1 
	AND sp2.autoReport=1 
	AND sms.reported=0 
  AND sms.dc IS NOT NULL
  AND sms.dc>0 
  AND sms.profileHome2=0 
  AND sms.profileAway2=0 
  AND sms.profileHome3=0 
  AND sms.profileAway3=0 
  AND sms.lobbyName<>'Training' 
	ORDER BY sms.id ASC";

$result = mysql_query($sql);

$smId = 0;
$winner1 = '';
$winner2 = '';
$loser1 = '';
$loser2 = '';
$teamLeft = '';
$teamRight = '';
$scoreLeft = '';
$scoreRight = '';
$homeLeft = true;
$patchId = 0;

while ($row = mysql_fetch_array($result)) {
	$smId = $row['id'];
  
  $log->LogInfo('Unfinished Game #'.$smId);
  
	$profileHome = $row['profileHome'];
  $profileHome2 = $row['profileHome2'];
	$profileAway = $row['profileAway'];
  $profileAway2 = $row['profileAway2'];
  $teamHome = $row['teamHome'];
	$teamAway = $row['teamAway'];
	$scoreHome = $row['scoreHomeReg'];
	$scoreAway = $row['scoreAwayReg'];
	$scoreHomeExt = $row['scoreHome'];
	$scoreAwayExt = $row['scoreAway'];
	$hashHome = $row['hashHome'];
	$hashAway = $row['hashAway'];
	$patchId = $row['patchId'];
	$minutes = $row['minutes'];
	$dc = $row['dc'];
  
  $playerHome1 = "";
  $playerHome1Points = 0;
  $playerAway1 = "";
  $playerAway1Points = 0;
  $playerHome2 = "";
  $playerAway2 = "";
  $winner1 = "";
  $loser1 = "";
  $winner2 = "";
  $loser2 = "";

	$sqlPrefix = "SELECT wp.name, wp.ra2pes5 FROM weblm_players wp ".
    "LEFT JOIN six_profiles sp ON sp.user_id=wp.player_id ".
    "WHERE sp.id=";
    
  // Get winners and losers
	$sql = $sqlPrefix.$profileHome;
  $row = mysql_fetch_array(mysql_query($sql));
  $playerHome1 = $row[0];
  $playerHome1Points = $row[1];
  $log->LogInfo('name='.$row[0].' sql='.$sql);
  
  if ($profileHome2 > 0) {
    $sql = $sqlPrefix.$profileHome2;
    $row = mysql_fetch_array(mysql_query($sql));
    $playerHome2 = $row[0];
    $log->LogInfo('name='.$row[0].' sql='.$sql);
  }

  $sql = $sqlPrefix.$profileAway;
  $row = mysql_fetch_array(mysql_query($sql));
  $playerAway1 = $row[0];
  $playerAway1Points = $row[1];
  $log->LogInfo('name='.$row[0].' sql='.$sql);
  
  if ($profileAway2 > 0) {
    $sql = $sqlPrefix.$profileAway2;
    $row = mysql_fetch_array(mysql_query($sql));
    $playerAway2 = $row[0];
    $log->LogInfo('name='.$row[0].' sql='.$sql);
  }
  
  $loserDC = false;
  
  if ($scoreHome < $scoreAway) {
		$homeLeft = false;
		$teamLeft = $teamAway;
		$teamRight = $teamHome;
		$scoreLeft = $scoreAway;
		$scoreRight = $scoreHome;
		$scoreLeftExt = $scoreAwayExt;
		$scoreRightExt = $scoreHomeExt;
		$hashLeft = $hashAway;
		$hashRight = $hashHome;
    $winner1 = $playerAway1;
    $loser1 = $playerHome1;
    if ($profileAway2 > 0) {
      $winner2 = $playerAway2;
    }
    if ($profileHome2 > 0) {
      $loser2 = $playerHome2;
    }
    if ($dc == 1) {
      $loserDC = true;
    }
	} else {
		$homeLeft = true;
		$teamLeft = $teamHome;
		$teamRight = $teamAway;
		$scoreLeft = $scoreHome;
		$scoreRight = $scoreAway;
		$scoreLeftExt = $scoreHomeExt;
		$scoreRightExt = $scoreAwayExt;
		$hashLeft = $hashHome;
		$hashRight = $hashAway;
    $winner1 = $playerHome1;
    $loser1 = $playerAway1;
    if ($profileHome2 > 0) {
      $winner2 = $playerHome2;
    }
    if ($profileAway2 > 0) {
      $loser2 = $playerAway2;
    }
    
    if ($scoreHome == $scoreAway) {
      //report draw?
      if (($playerHome1Points > $playerAway1Points && $dc == 1) || ($playerHome1Points < $playerAway1Points && $dc == 2)) {
        $loserDC = true;
      }
    } elseif ($dc == 2) {
      $loserDC = true;
    }
    
	}

  $log->LogInfo('dc='.$dc.' loserDC='.$loserDC);
  
	if ((($minutes >= 75) || IsFishySixserverGame($scoreLeft, $scoreRight, $minutes)) && $loserDC) {
    	ReportToLadder($smId, $teamLeft, $teamRight, $winner1, $winner2, $loser1, $loser2, $scoreLeft, $scoreRight, $scoreLeftExt, $scoreRightExt, $patchId, $log, false);
	} else {
		$log->LogInfo('Game not fishy, skipping: #'.$smId);
    $sql = "UPDATE six_matches_status SET reported=1, updated=updated WHERE id=".$smId;
    $res = mysql_query($sql);
	}
}

function ReportToLadder($matchId, $teamLeft, $teamRight, $winner1, $winner2, $loser1, $loser2, $scoreLeft, $scoreRight, $scoreLeftExt, $scoreRightExt, $patchId, $log, $finished) {
	// Settings correct?
  $sql = "SELECT matchTime FROM six_matches_info WHERE matchId=$matchId AND type=";
  if ($finished) {
    $sql .= "'F'";
  } else {
    $sql .= "'U'";
  }
  $log->logInfo('ReportToLadder: sql='.$sql);
  $matchTime = 1;
  $res = mysql_query($sql);
  if ($row = mysql_fetch_array($res)) {
    $matchTime = $row['matchTime'];
    $log->logInfo('ReportToLadder: six_matches_info.matchTime='.$matchTime);
  }
  
  if ($matchTime <> 1) {
    $log->logInfo('ReportToLadder: Not reporting match #'.$matchId.' - MatchTime is set to '.$matchTime);
  } 
  else {
    
    // new match -> save the old one
    $winnerTeam = 0;
    $loserTeam = 0;
    
    // teams known?
    $sql = "SELECT st.ladderTeamId FROM six_teams st ". 
            "WHERE st.patchId=".$patchId." AND st.sixTeamId=".$teamLeft;
            
    $log->logInfo('ReportToLadder: sql='.$sql);
    $res = mysql_query($sql);
    if ((mysql_num_rows($res)) > 0) {
      $row = mysql_fetch_array($res);
      $winnerTeam = $row[0];
    }
    $sql = "SELECT st.ladderTeamId FROM six_teams st ". 
            "WHERE st.patchId=".$patchId." AND st.sixTeamId=".$teamRight;
            
    $log->logInfo('ReportToLadder: sql='.$sql);
    $res = mysql_query($sql);
    if ((mysql_num_rows($res)) > 0) {
      $row = mysql_fetch_array($res);
      $loserTeam = $row[0];
    }
    
    $commentSuffix = "";
    if (($scoreLeftExt > $scoreLeft) || ($scoreRightExt > $scoreRight)) {
      $commentSuffix = " (score after regular time)";	
    }
     
    if ($finished) {
      $commentPrefix = "Sixserver Game";
    } else {
      $commentPrefix = "Unfinished Sixserver Game";
    }
    
    $gamesplayedplayer = 0;
    $gamesmaxdayplayer = 6;
    if (empty($winner2) && empty($loser2)) {
      // check max. games
      $dateday = date("d/m/Y");
      $sql = "SELECT game_id FROM weblm_games ".
        "WHERE ((winner = '$winner1' and loser = '$loser1') OR (winner = '$loser1' and loser = '$winner1')) ".
        "AND deleted='no' and dateday = '$dateday'";
      $result = mysql_query($sql);
      $gamesplayedplayer = mysql_num_rows($result); 
    }
    
    $log->logInfo("ReportToLadder: matchId=".$matchId." winner1=".$winner1." loser1=".$loser1." winner2=".$winner2." loser2=".$loser2." teamLeft=".$teamLeft." teamRight=".$teamRight." scoreLeft=".$scoreLeft." scoreRight=".$scoreRight." finished=".$finished);
    $log->logInfo("ReportToLadder: gamesmaxdayplayer=".$gamesmaxdayplayer." gamesplayedplayer=".$gamesplayedplayer);
    if ($gamesplayedplayer >= $gamesmaxdayplayer) {
      $log->logInfo("ReportToLadder: limit reached, not reporting game");
    } else {
      ReportGame($winner1, $winner2, $loser1, $loser2, time(), $commentPrefix.' #'.$matchId.$commentSuffix, null, $scoreLeft, $scoreRight, 'B', 5, 'H', $winnerTeam, $loserTeam, $matchId);
    }
  }
  if ($finished) {
    $sql = "UPDATE six_matches SET reported=1, played_on=played_on WHERE id=".$matchId;
  } else {
    $sql = "UPDATE six_matches_status SET reported=1, updated=updated WHERE id=".$matchId;
  }
  $log->logInfo("ReportToLadder: ".$sql);
  $res = mysql_query($sql);
}

$log->logInfo('Duration: '.sprintf("%07.3f", microtime(true) - $start));
?>
