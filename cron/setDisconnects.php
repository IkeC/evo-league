#!/usr/bin/php
<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/dc/', KLogger::INFO);	

$sql = "SELECT season FROM six_stats";
$sixSeason = mysql_fetch_array(mysql_query($sql))[0];

$profiles = array();
$users = array();
		
$warningsIds = GetActiveWarningsIds();
$bansIds = GetActiveBansIds();

$sql = "SELECT sms.dc, sms.season, sms.minutes, sms.scoreHome, sms.scoreAway, sms.profileHome, sms.profileAway, ".
	"sp1.user_id AS userIdHome, sp2.user_id AS userIdAway, ".
	"UNIX_TIMESTAMP( sms.updated ) AS updatedTS ".
	"FROM six_matches_status sms ".
	"LEFT JOIN six_profiles sp1 ON sp1.id = sms.profileHome ".
	"LEFT JOIN six_profiles sp2 ON sp2.id = sms.profileAway ".
	"WHERE sms.updated < date_sub(now(), INTERVAL 15 MINUTE) ".
	"AND sms.profileHome2=0 ".
	"AND sms.profileAway2=0 ".
  "AND sms.dc IS NOT NULL ".
  "AND sms.lobbyName <> 'Training'";
  
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
  $scoreHome = $row['scoreHome'];
  $scoreAway = $row['scoreAway'];
  $minutes = $row['minutes'];
  $updated = $row['updatedTS'];
  $season = $row['season'];
  $dcgame = $row['dc'];
  
  $dcHome = false;
  $dcAway = false;
  if ($dcgame == 1) {
    $dcHome = true;
  } elseif ($dcgame == 2) {
    $dcAway = true;
  }
  
  if ($scoreHome > $scoreAway) {
    $scoreLeft = $scoreHome;
    $scoreRight = $scoreAway;
    $profileIdLoser = $row['profileAway'];
    $userIdLoser = $row['userIdAway'];
    $dcLeft = $dcHome;
    $dcRight = $dcAway;
  } else {
    $scoreLeft = $scoreAway;
    $scoreRight = $scoreHome;
    $profileIdLoser = $row['profileHome'];
    $userIdLoser = $row['userIdHome'];
    $dcLeft = $dcAway;
    $dcRight = $dcHome;
  }
  
  if (IsFishySixserverGame($scoreLeft, $scoreRight, $minutes)) {
 	  
    $punish = false;
    if (($scoreLeft >= $scoreRight) && $dcRight) {
      $punish = true;
    } elseif (($scoreLeft == $scoreRight) && $dcLeft)  {
      $punish = true;
      $profileIdLoser = $row['profileAway'];
      $userIdLoser = $row['userIdAway'];
    }
    
    if ($punish) {
      if (array_key_exists($userIdLoser, $profiles)) {
        if (array_key_exists($profileIdLoser, $profiles[$userIdLoser])) {
          $profiles[$userIdLoser][$profileIdLoser]['totalDC'] = $profiles[$userIdLoser][$profileIdLoser]['totalDC'] + 1;
        } else {
          $profiles[$userIdLoser][$profileIdLoser] = array('totalDC' => 1, 'weekDC' => 0, 'monthDC' => 0, 'seasonDC' => 0, 'profileId' => $profileIdLoser);
        }
      } else {
        $profiles[$userIdLoser] = array($profileIdLoser => array('totalDC' => 1, 'weekDC' => 0, 'monthDC' => 0, 'seasonDC' => 0, 'profileId' => $profileIdLoser));
      }

      if (array_key_exists($userIdLoser, $users)) {
        $users[$userIdLoser]['totalDC'] = $users[$userIdLoser]['totalDC'] + 1;
      } else {
        $users[$userIdLoser] = array('totalDC' => 1, 'weekDC' => 0, 'monthDC' => 0, 'seasonDC' => 0, 'userId' => $userIdLoser);
      }
        
      
      if ((time() - $updated) < 60*60*24*7) {
        $profiles[$userIdLoser][$profileIdLoser]['weekDC'] = $profiles[$userIdLoser][$profileIdLoser]['weekDC'] + 1;
        $users[$userIdLoser]['weekDC'] = $users[$userIdLoser]['weekDC'] + 1; 
      } 
      
      if ((time() - $updated) < 60*60*24*7*30) {
        $profiles[$userIdLoser][$profileIdLoser]['monthDC'] = $profiles[$userIdLoser][$profileIdLoser]['monthDC'] + 1;
        $users[$userIdLoser]['monthDC'] = $users[$userIdLoser]['monthDC'] + 1;
      }
      
      if ($season == $sixSeason) {
        $profiles[$userIdLoser][$profileIdLoser]['seasonDC'] = $profiles[$userIdLoser][$profileIdLoser]['seasonDC'] + 1;
        $users[$userIdLoser]['seasonDC'] = $users[$userIdLoser]['seasonDC'] + 1;
      }
    }
  }
}

usort($users, function($a, $b) {
    return $b['weekDC'] - $a['weekDC'];
});

// print_r($profiles);
// print_r($users);

foreach ($profiles as $userId => $userArray) {
	foreach ($userArray as $profileId => $profileArray) {
	
	  $sql = "SELECT six_profiles.name AS profileName, six_profiles.points, six_profiles.rating, six_profiles.disconnects " .
	    "FROM six_profiles " .
	    "WHERE six_profiles.id=".$profileArray['profileId'];
	
	  $result = mysql_query($sql);
	  $row = mysql_fetch_array($result);
	  
	  $dcOld = $row['disconnects'];
	  $dcChange = $profileArray['seasonDC']-$dcOld;
	  
    if ($dcChange <> 0) {
      $sql = "UPDATE six_profiles SET disconnects=".$profileArray['seasonDC']." WHERE id=".$profileId;
      mysql_query($sql);

      $log->logInfo('setDisconnects: sql='.$sql);
      $log->logInfo('setDisconnects: '.$row['profileName'].' ('.$profileId.'): disconnects='.$profileArray['seasonDC'].' dcOld='.$dcOld.' affected='.mysql_affected_rows());
      
      // recalculate points and rating
      $wins = getSixserverWins($profileId);
      $draws = getSixserverDraws($profileId);
      $losses = getSixserverLosses($profileId);
      $dc = $profileArray['seasonDC'];
      $pointsOld = $row['points'];
      $pointsCalcOld = getSixserverPoints($wins, $draws, $losses+$dcOld);
      $pointsNew = getSixserverPoints($wins, $draws, $losses+$dc);
      $log->logInfo("wins=$wins draws=$draws losses=$losses dc=$dc");
      $log->logInfo("setDisconnects: pointsOld=$pointsOld pointsCalcOld=$pointsCalcOld pointsNew=$pointsNew");

      $sql2 = "SELECT sum(wins), sum(draws), sum(losses), sum(dc) FROM six_history WHERE profileId=".$profileId;
      $result2 = mysql_query($sql2);
      $row2 = mysql_fetch_array($result2);

      $wins = $wins+$row2[0];
      $draws = $draws+$row2[1];
      $losses = $losses+$row2[2];
      $dc = $dc+$row2[3];
      $ratingOld = $row['rating'];
      $ratingCalcOld = getSixserverPoints($wins, $draws, $losses+$dcOld+$row2[3]);
      $ratingNew = getSixserverPoints($wins, $draws, $losses+$dc);
      $log->logInfo("wins=$wins draws=$draws losses=$losses dc=$dc");
      $log->logInfo("setDisconnects: ratingOld=$ratingOld ratingCalcOld=$ratingCalcOld ratingNew=$ratingNew");
      
      $sql = "UPDATE six_profiles SET points=$pointsNew, rating=$ratingNew WHERE id=$profileId";
      $log->logInfo('setDisconnects: sql='.$sql);
      mysql_query($sql);
   }
	}
}

foreach ($users as $userId => $userArray) {
  // $log->logInfo('id='.$playerId.' name='.$name.' warned='.$warned.' banned='.$banned);
  $playerId = $userArray['userId'];
	
  $warned = in_array($playerId, $warningsIds);
  $banned = in_array($playerId, $bansIds);
  $warnPlayer = false;
  $banPlayerTemp = false;
  
  // warnings/bans
  if (!$banned) {
    if ($userArray['weekDC'] >= 7) {
      $banPlayerTemp = true;
    } elseif (($userArray['weekDC'] >= 3) && !$warned) {
      $warnPlayer = true;
    }
    
    if ($warnPlayer || $banPlayerTemp) {
      $date = time();
      $type = "";
      $expireDate = "";
      
      $sql = "SELECT name FROM $playerstable WHERE player_id=".$playerId;
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $name = $row['name'];

      $log->logInfo('id='.$playerId.' name='.$name.' warnPlayer='.$warnPlayer.' banPlayerTemp='.$banPlayerTemp);
      
      if ($warnPlayer) {
        $type = "W";
        $expireDate = time() + (60*60*24*7);

        $sql = "INSERT INTO $playerstatustable (userId, userName, type, active, date, expireDate, forumLink, reason) ".
          "VALUES ('$playerId', '$name', '$type', 'Y', '$date', '$expireDate', '', 'Disconnecting')";
        $result = mysql_query($sql);
        $log->logInfo('result='.$result.' sql='.$sql);
      } 
      elseif ($banPlayerTemp) {

      // was player already banned within the last week?
        $banDays = 1;
        $bannedLastWeek = false;
        $sql = "SELECT * FROM $playerstatustable WHERE userId=$playerId AND active='N' AND type='B'";
        $res = mysql_query($sql);
        while ($row = mysql_fetch_array($res)) {
          $banDays++;
          $banStart = $row['date'];
          if ($banStart > (time() - (60*60*24*7))) {
            $bannedLastWeek = true;
          }
        }
        $banMultiplier = ceil($userArray['monthDC'] / 10);
        
        $log->logInfo('id='.$playerId.' name='.$name.' banDays='.$banDays.' banMultiplier='.$banMultiplier.' bannedLastWeek='.$bannedLastWeek);
        
        if (!$bannedLastWeek) {
          $type = "B";
          $expireDate = time() + (60*60*24*$banDays*$banMultiplier);

          $sql = "INSERT INTO $playerstatustable (userId, userName, type, active, date, expireDate, forumLink, reason) ".
              "VALUES ('$playerId', '$name', '$type', 'Y', '$date', '$expireDate', '', 'Disconnecting')";
          $result = mysql_query($sql);
          $log->logInfo('result='.$result.' sql='.$sql);
          
          $sql = "UPDATE $playerstable SET approved='no' WHERE player_id=".$playerId;
          $result = mysql_query($sql);
          $log->logInfo('result='.$result.' sql='.$sql);
        }
      }
      $log->logInfo('-');
    }
  }
}

?>