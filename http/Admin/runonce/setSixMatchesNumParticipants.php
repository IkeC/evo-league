<?php
$page = "setSixMatchesNumParticipants";

require ('../../variables.php');
require ('../../variablesdb.php');
require_once ('../functions.php');
require_once ('./../../functions.php');
require ('../../top.php');

$log = new KLogger('/var/www/yoursite/http/log/runonce/', KLogger::INFO);

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Fix NumParticipants", ""); ?>
<?

$sql = "SELECT id FROM six_matches WHERE numParticipants=0 ORDER BY Id ASC";
$resultX = mysql_query($sql);
while ($row = mysql_fetch_array($resultX)) {
  $matchId = $row[0];
  $sql = "SELECT COUNT(*) FROM six_matches_played where match_id=$matchId";
  $log->logInfo('matchId='.$matchId.' sql='.$sql);
  $rescount = mysql_query($sql);
  $count = mysql_fetch_array($rescount)[0];
  $sql = "UPDATE six_matches SET numParticipants=$count, played_on=played_on WHERE id=$matchId";
  $log->logInfo('matchId='.$matchId.' sql='.$sql);
  mysql_query($sql);
}

?>
<?= getOuterBoxBottom() ?>
<?


require ('../../bottom.php');
?>