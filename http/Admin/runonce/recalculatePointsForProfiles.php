<?php
$page = "recalculatePointsForProfiles";

require ('../../variables.php');
require ('../../variablesdb.php');
require_once ('../functions.php');
require_once ('./../../functions.php');
require ('../../top.php');

$log = new KLogger('/var/www/yoursite/http/log/runonce/', KLogger::INFO);

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Recalculate Points For Profiles", ""); ?>
<?

$sql = "SELECT * FROM six_profiles where id=591 ORDER BY Id ASC";
$resultX = mysql_query($sql);
while ($profile = mysql_fetch_array($resultX)) {
  $profileId = $profile['id'];
  $log->logInfo('profileId='.$profileId);
  $log->logInfo('RecalculatePointsForProfile='.RecalculatePointsForProfile($profile['id'], $profile['disconnects'], $profile['points'], $profile['rating']));
  $log->logInfo('RecalculateStreak='.RecalculateStreak($profile['id']));
}

?>
<?= getOuterBoxBottom() ?>
<?


require ('../../bottom.php');
?>