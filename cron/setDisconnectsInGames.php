#!/usr/bin/php
<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/dc-games/', KLogger::INFO);	

// Delete 0 min. matches
$sql = "DELETE FROM six_matches_status WHERE minutes=0 AND updated < date_sub(now(), INTERVAL 5 MINUTE)";
mysql_query($sql);

$sql = "SELECT id, homeExit, awayExit, homeCancel, awayCancel, profileHome2, profileHome3, profileAway2, profileAway3, scoreHome, scoreAway ".
	"FROM six_matches_status ".
  "WHERE dc IS NULL ".
  // "AND id <= 670536 AND id >= 670283 ".
  "AND updated < date_sub(now(), INTERVAL 15 MINUTE) ".
  "ORDER BY id ASC";

$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
  $id = $row['id'];
  $homeExit = $row['homeExit'];
  $awayExit = $row['awayExit'];
  $homeCancel = $row['homeCancel'];
  $awayCancel = $row['awayCancel'];
  $scoreHome = $row['scoreHome'];
  $scoreAway = $row['scoreAway'];
  $dc = 0; // 0: unknown, 1: home disconnected, 2: away disconnected
  $type = 0;
  if (empty($row['profileHome2']) && empty($row['profileHome3']) && empty($row['profileAway2']) && empty($row['profileAway3'])) {
    $diff = "?";
    if (empty($homeCancel) && !empty($awayCancel)) {
      $type = 11;
      $dc = 1;
    } elseif (!empty($homeCancel) && empty($awayCancel)) {
      $type = 12;
      $dc = 2;
    } elseif (!empty($homeCancel) && !empty($awayCancel)) {
      $diff = strtotime($homeCancel)-strtotime($awayCancel);
      if ($diff > 600) {
        $type = 21;
        $dc = 1;
      } elseif ($diff < -600) {
        $type = 22;
        $dc = 2;
      } 
      elseif (empty($homeExit) && !empty($awayExit) && strtotime($awayCancel) < strtotime($homeCancel) && (strtotime($awayExit) > strtotime($awayCancel))) {
          $type = 31;
          $dc = 1;
      } elseif (!empty($homeExit) && empty($awayExit) && strtotime($homeCancel) < strtotime($awayCancel) && (strtotime($homeExit) > strtotime($homeCancel))) {
          $type = 32;
          $dc = 2;
      } 
      else {
        $diff = strtotime($homeCancel)-strtotime($awayCancel);
        if ($diff < -2) {
          $type = 41;
          $dc = 1;
        } elseif ($diff > 2) {
          $type = 42;
          $dc = 2;
        } else {
          // punish loser
          if ($scoreHome > $scoreAway) {
            $type = 52;
            $dc = 2;
          } 
          elseif ($scoreHome > $scoreAway) {
            $type = 51;
            $dc = 1;
          }
        }
      }
    } 
    $log->logInfo('id='.$id.' type='.$type.' dc='.$dc.' diff='.$diff);
  } else {
    $log->logInfo('id='.$id.' skipping (not a 1on1 game)');
  }
  $sql = 'UPDATE six_matches_status SET updated=updated, dc='.$dc.' WHERE id='.$id;
  mysql_query($sql);
  $log->logInfo('sql='.$sql.' affected='.mysql_affected_rows());
}

?>