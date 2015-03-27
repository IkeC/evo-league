<?php
$page = "fixSixMatchesStatus";

require ('../../variables.php');
require ('../../variablesdb.php');
require_once ('../functions.php');
require_once ('./../../functions.php');
require ('../../top.php');

$log = new KLogger('/var/www/yoursite/http/log/runonce/', KLogger::INFO);

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Fix SMS", ""); ?>
<?

// ladder games 163322-163609

$sql = "SELECT * FROM weblm_games WHERE game_id < 163607 and game_id >= 163322 ORDER BY game_id DESC";
$resultX = mysql_query($sql);
while ($row = mysql_fetch_array($resultX)) {
  $removeGameId = $row['game_id'];
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

    $sql = "UPDATE $gamestable SET deleted = 'yes' where game_id = '$removeGameId'";

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
    $row2 = mysql_fetch_array($result);
    $ra2ladder = $row2[$pointsField];
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
      $row2 = mysql_fetch_array($result);
      $ra2ladder = $row2[$pointsField];
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
/*
$sql = "SELECT userId FROM weblm_playerstatus where id > 20469 order by id asc";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
  $userId = $row['userId'];
  $sql = "UPDATE weblm_players set approved='yes' where player_id=$userId";

  $log->logInfo('userId='.$userId.' sql='.$sql);

  mysql_query($sql);
}
*/

/*
$sql = "SELECT id, updated from six_matches_status_bkp order by id asc";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)) {
  $id = $row['id'];
  $updated = $row['updated'];
  
  $sql = "UPDATE six_matches_status SET updated='$updated' WHERE id=$id";

  $log->logInfo('id='.$id.' updated='.$updated.' sql='.$sql);

  mysql_query($sql);
}
*/
?>
<?= getOuterBoxBottom() ?>
<?


require ('../../bottom.php');
?>