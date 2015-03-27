<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "newSeason";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./../top.php');

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> FixSixHistory", ""); ?>
<?
if (!$isAdminFull) {
  echo "<p>Access denied.</p>";
} else {
  /*
  $sql = "SELECT * FROM six_profiles ORDER BY points DESC";
  $result = mysql_query($sql);
  $checkSeason = 3;
  
  while ($row = mysql_fetch_array($result)) {
    $id = $row['id'];
    $w = GetSixserverWinsForSeason($id, $checkSeason);
    $l = GetSixserverLossesForSeason($id, $checkSeason);
    $d = GetSixserverDrawsForSeason($id, $checkSeason);
    
    $w2 = GetSixserverWinsHistoryForSeason($id, $checkSeason);
    $l2 = GetSixserverLossesHistoryForSeason($id, $checkSeason);
    $d2 = GetSixserverDrawsHistoryForSeason($id, $checkSeason);
    
    if (($w>0 && $w2>0 && $w<>$w2) || ($d>0 && $d2>0 && $d<>$d2) || ($l>0 && $l2>0 && $l<>$l2)) {
    // if ($id==4488 || $id==4653 || $id==5185) {
      echo "<p>ID: $id<br>";
      echo "$w $l $d<br>";
      echo "$w2 $l2 $d2</p>";
      $games = $w+$l+$d;
      $sql = "UPDATE six_history SET wins=$w, losses=$l, draws=$d, games=$games WHERE profileId=$id AND season=$checkSeason";
      echo "<p>$sql</p>";
      mysql_query($sql);
    // }
    }
  }
  echo "ok";
  */
  
  $sql = "SELECT * FROM six_profiles WHERE id=6624 ORDER BY points DESC";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $id = $row['id'];
    
    $sql2 = "SELECT sum(wins), sum(draws), sum(losses), sum(dc) FROM six_history WHERE profileId=".$id;
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);
    
    $wins = getSixserverWins($id);
    $draws = getSixserverDraws($id);
    $losses = getSixserverLosses($id);
    
    $dc = $row['disconnects'];
    $pts = $row['points'];

    $ptsNew = getSixserverPoints($wins, $draws, $losses+$dc);
    echo "<p>";
    echo "<br>$sql2";
    echo "<br>".$row2[0]."-".$row2[1]."-".$row2[2]."-".$row2[3];
    echo "<br>wins=$wins draws=$draws losses=$losses dc=$dc id=$id pts=$pts ptsNew=$ptsNew";
    
    $sql = "UPDATE six_profiles SET points2=points WHERE id=$id";
    echo "<br>$sql";
    mysql_query($sql);

    $sql = "UPDATE six_profiles SET points=$ptsNew WHERE id=$id";
    echo "<br>$sql";
    mysql_query($sql);

    $wins = $wins+$row2[0];
    $draws = $draws+$row2[1];
    $losses = $losses+$row2[2];
    $dc = $row['disconnects']+$row2[3];
    $rating = $row['rating'];
    $ratingNew = getSixserverPoints($wins, $draws, $losses+$dc);
    echo "<p>";
    echo "<br>$sql2";
    echo "<br>".$row2[0]."-".$row2[1]."-".$row2[2]."-".$row2[3];
    echo "<br>wins=$wins draws=$draws losses=$losses dc=$dc id=$id rating=$rating ratingNew=$ratingNew";
    
    $sql = "UPDATE six_profiles SET rating=$ratingNew WHERE id=$id";
    echo "<br>$sql";
    mysql_query($sql);
  }
  
}

?>

<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
  



?>

