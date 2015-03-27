<?php

// running this will start a new season. all current season info will be written to the history table.
// the 1-6th place will get an award image defined in /variables.php
// when you start a new season, add a new row in the weblm_seasons table for correct display of 
// season start/end dates.
// you cannot properly remove games from old seasons, so check if all games are ok before starting a
// new season! 


header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "newSeason";

require('./../variables.php');
require('./../variablesdb.php');
require('./functions.php');
require('./../functions.php');
require('./../top.php');

?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> New Season", ""); ?>

<?

if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {

	if (isset($_GET['action']) && $_GET['action'] == 'start') {
    $msg = StartLadderSeason();
    $log->logInfo('StartLadderSeason: '.$msg);
    echo $msg;
	} 
  
  // Sixserver
  elseif (isset($_GET['action']) && $_GET['action'] == 'startSix') {
    $msg = StartSixserverSeason();
    $log->logInfo('StartSixserverSeason: '.$msg);
    echo $msg;
  }

	else {
		?>
		<p><b>Are you sure you want to start the new LADDER season?</b></p>
		<p class="boxlink"><a href="newSeason.php?action=start" style="font-size:20px;">[YES, kick it!]</a></p>
		<p class="boxlink"><a href="index.php" style="font-size:20px;">[NO way man!]</a></p>
		<p><b>Are you sure you want to start the new SIXSERVER season?</b></p>
		<p class="boxlink"><a href="newSeason.php?action=startSix" style="font-size:20px;">[YES, kick it!]</a></p>
		<p class="boxlink"><a href="index.php" style="font-size:20px;">[NO way man!]</a></p>

		<?
	}
}

?>

<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>

