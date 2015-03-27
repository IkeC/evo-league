<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "players";
$subpage = "teamstats";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$name = mysql_real_escape_string($_GET['name']);

?>
    <?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, $name), "") ?>
	<table width="100%">
		<tr>
			<td colspan="2">
	<?= getBoxTop("Info", "", false, null) ?>
	<p>The left side shows <?= $name ?>'s statistics for the teams he used when playing games.
The columns show his wins, draws and defeats using this team.</p>
	
	<p>The right side shows his statistics when playing against different teams. The columns show his wins, draws and defeats against this team.</p>
	<?= getBoxBottom() ?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top; width:50%;">

<?
$sql = "select name from $playerstable where name = '$name'";
$result = mysql_query($sql);

if (empty($name)) {
	echo "<p>No player specified!</p>";        
} else if (mysql_num_rows($result) == 0) {
	echo "<p>The player <b>$name</b> could not be found in the database.</p>";
} else {
	$row = mysql_fetch_array($result);
	$name = $row['name'];
	$trend_height = 150;
	$trend_width = 390;
	$teamsArray = array();
	
	// won games
	$sql = "SELECT winnerteam, isDraw from $gamestable where (winner = '$name' or winner2 = '$name') ".
		"and deleted = 'no' and winnerteam != ''";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		if ($row['isDraw'] > 0) {
			$teamsArray = addDraw($teamsArray, $row[0]);
		} else {
			$teamsArray = addWin($teamsArray, $row[0]);
		}
	}
	
	// lost games
	$sql = "SELECT loserteam, isDraw from $gamestable where (loser = '$name' or loser2 = '$name') ".
		"and deleted = 'no' and loserteam != ''";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		if ($row['isDraw'] > 0) {
			$teamsArray = addDraw($teamsArray, $row[0]);
		} else {
			$teamsArray = addLoss($teamsArray, $row[0]);
		} 
	}
	
?>
<? $columnsArray = array('Pos','', 'Team', 'W', 'D', 'L', 'Used'); ?>
<?= getRankBoxTop($name." - Used teams", $columnsArray); ?>
<?
	arsort($teamsArray);    
    $pos = 1;
    $gamesTemp = "";
 	foreach ($teamsArray as $team => $valArray) { 
 		$teamImg = $winnerImg = getImgForTeam($team);
		$teamName = getTeamNameForId($team);
		
		$games = $valArray[0];
		$wins = $valArray[1];
		$losses = $valArray[2];
		$draws = $valArray[3];
		
		if ($games != $gamesTemp) {
			$posDisplay = $pos.".";
		} else {
			$posDisplay = ".";
		}		
		$gamesTemp = $games;
		$pos++;
 		?>
    <tr class="row">
      <td style="width:10%;text-align:right;"><?= $posDisplay ?></td>
      <td style="width:5%; text-align:middle;"><?= $teamImg ?></td>
      <td style="width:50%"><?= $teamName ?></td>
      <td style="width:7%; text-align:right;"><?= $wins ?></td>
      <td style="width:7%; text-align:right;"><?= $draws ?></td>
      <td style="width:7%; text-align:right;"><?= $losses ?></td>
      <td style="width:14%; text-align:right;"><b><?=  $games ?></b></td>
      </td>
	</tr>
 	<? } ?>
<?= getRankBoxBottom() ?>

<? $columnsArray = array('Pos','', 'Team', 'W', 'D', 'L', 'Ratio'); ?>
<?= getRankBoxTop("Winning percentage&nbsp;<span style='font-weight:normal;font-size:9px;'>(teams with 3+ games)</span>", $columnsArray); ?>
<?
	$percArray = array();
	$pos = 1;
 	foreach ($teamsArray as $team => $valArray) { 
 		$games = $valArray[0];
		$wins = $valArray[1];
		$losses = $valArray[2];
		$draws = $valArray[3];
		
		if ($games > 2) {
			$valArray[0] = ($wins/$games)*100;
			$percArray[$team] = $valArray;
		}				
 	}
 	arsort($percArray);
	$i = 0; 	
	$percentageTemp = "";
 	foreach ($percArray as $team => $valArray) {
 		
 		$teamImg = $winnerImg = getImgForTeam($team);
		$teamName = getTeamNameForId($team);
 		
 		$percentage = $valArray[0];
		$wins = $valArray[1];
		$losses = $valArray[2];
		$draws = $valArray[3];
 						
		if ($percentage != $percentageTemp) {
			$posDisplay = $pos.".";
		} else {
			$posDisplay = ".";
		}		
		$percentageTemp = $percentage;
		$pos++;
 		?>
    <tr class="row">
      <td style="width:10%;text-align:right;"><?= $posDisplay ?></td>
      <td style="width:5%; text-align:middle;"><?= $teamImg ?></td>
      <td style="width:50%"><?= $teamName ?></td>
      <td style="width:7%; text-align:right;"><?= $wins ?></td>
      <td style="width:7%; text-align:right;"><?= $draws ?></td>
      <td style="width:7%; text-align:right;"><?= $losses ?></td>
      <td style="width:14%; text-align:right;"><b><?=  sprintf("%.2f", $percentage); ?>%</b></td>
      </td>
	</tr>
 	<? } ?>
<?= getRankBoxBottom() ?>

</td><td style="vertical-align:top; width:50%">

<? 
// opponent teams

$teamsArray = array();
	
	// won games
	$sql = "SELECT loserteam, isDraw from $gamestable where (winner = '$name' or winner2 = '$name') ".
		"and deleted = 'no' and winnerteam != ''";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		if ($row['isDraw'] > 0) {
			$teamsArray = addDraw($teamsArray, $row[0]);
		} else {
			$teamsArray = addWin($teamsArray, $row[0]);
		}
	}
	
	// lost games
	$sql = "SELECT winnerteam, isDraw from $gamestable where (loser = '$name' or loser2 = '$name') ".
		"and deleted = 'no' and loserteam != ''";
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		if ($row['isDraw'] > 0) {
			$teamsArray = addDraw($teamsArray, $row[0]);
		} else {
			$teamsArray = addLoss($teamsArray, $row[0]);
		}
	}
	
?>
<? $columnsArray = array('Pos','', 'Team', 'W', 'D', 'L', 'Used'); ?>
<?= getRankBoxTop($name." - Opponent teams", $columnsArray); ?>
<?
	arsort($teamsArray);    
    $pos = 1;
 	foreach ($teamsArray as $team => $valArray) { 
 		$teamImg = $winnerImg = getImgForTeam($team);
		$teamName = getTeamNameForId($team);
		
		$games = $valArray[0];
		$wins = $valArray[1];
		$losses = $valArray[2];
		$draws = $valArray[3];
		if ($games != $gamesTemp) {
			$posDisplay = $pos.".";
		} else {
			$posDisplay = ".";
		}		
		$gamesTemp = $games;
		$pos++;
 		?>
    <tr class="row">
      <td style="width:10%;text-align:right;"><?= $posDisplay ?></td>
      <td style="width:5%; text-align:middle;"><?= $teamImg ?></td>
      <td style="width:50%"><?= $teamName ?></td>
      <td style="width:7%; text-align:right;"><?= $wins ?></td>
      <td style="width:7%; text-align:right;"><?= $draws ?></td>
      <td style="width:7%; text-align:right;"><?= $losses ?></td>
      <td style="width:14%; text-align:right;"><b><?=  $games ?></b></td>
      </td>
	</tr>
 	<? } ?>
<?= getRankBoxBottom() ?>

<? $columnsArray = array('Pos','', 'Team', 'W', 'D', 'L', 'Ratio'); ?>
<?= getRankBoxTop("Winning percentage against&nbsp;<span style='font-weight:normal;font-size:9px;'>(teams with 3+ games)</span>", $columnsArray); ?>
<?
	$percArray = array();
	$pos = 1;
 	foreach ($teamsArray as $team => $valArray) { 
 		$games = $valArray[0];
		$wins = $valArray[1];
		$losses = $valArray[2];
		$draws = $valArray[3];
		
		if ($games > 2) {
			$valArray[0] = ($wins/$games)*100;
			$percArray[$team] = $valArray;
		}				
 	}
 	arsort($percArray);
	$i = 0; 	
 	foreach ($percArray as $team => $valArray) {
 		
 		$teamImg = $winnerImg = getImgForTeam($team);
		$teamName = getTeamNameForId($team);
 		
 		$percentage = $valArray[0];
		$wins = $valArray[1];
		$losses = $valArray[2];
 		$draws = $valArray[3];
		if ($percentage != $percentageTemp) {
			$posDisplay = $pos.".";
		} else {
			$posDisplay = ".";
		}		
		$percentageTemp = $percentage;
		$pos++;
 		?>
    <tr class="row">
      <td style="width:10%;text-align:right;"><?= $posDisplay ?></td>
      <td style="width:5%; text-align:middle;"><?= $teamImg ?></td>
      <td style="width:50%"><?= $teamName ?></td>
      <td style="width:7%; text-align:right;"><?= $wins ?></td>
      <td style="width:7%; text-align:right;"><?= $draws ?></td>
      <td style="width:7%; text-align:right;"><?= $losses ?></td>
      <td style="width:14%; text-align:right;"><b><?=  sprintf("%.2f", $percentage); ?>%</b></td>
      </td>
	</tr>
 	<? } ?>
<?= getRankBoxBottom() ?>


<?
}
?>
			</td>
		</tr>
	</table>
<?= getOuterBoxBottom() ?>
<?
// Indices: 0=Games, 1=Won, 2=Lost
function addWin($teamsArray, $winnerteam) {
	if ($winnerteam != "0") {
		if (array_key_exists($winnerteam, $teamsArray)) {
			$teamArray = $teamsArray[$winnerteam];
			$teamArray[0] = $teamArray[0] + 1;
			$teamArray[1] = $teamArray[1] + 1;
			$teamsArray[$winnerteam] = $teamArray;
		} else {
			$teamsArray[$winnerteam] = array(1, 1, 0, 0);
		}
	}
	return $teamsArray;
}

function addLoss($teamsArray, $loserteam) {
	if ($loserteam != "0") {
		if (array_key_exists($loserteam, $teamsArray)) {
			$teamArray = $teamsArray[$loserteam];
			$teamArray[0] = $teamArray[0] + 1;
			$teamArray[2] = $teamArray[2] + 1;
			$teamsArray[$loserteam] = $teamArray;
		} else {
			$teamsArray[$loserteam] = array(1, 0, 1, 0);
		}
	}
	return $teamsArray;
}

function addDraw($teamsArray, $loserteam) {
	if ($loserteam != "0") {
		if (array_key_exists($loserteam, $teamsArray)) {
			$teamArray = $teamsArray[$loserteam];
			$teamArray[0] = $teamArray[0] + 1;
			$teamArray[3] = $teamArray[3] + 1;
			$teamsArray[$loserteam] = $teamArray;
		} else {
			$teamsArray[$loserteam] = array(1, 0, 0, 1);
		}
	}
	return $teamsArray;
}
?>
<? require('../bottom.php'); ?>

