<?php
require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$page = "leagues";
$subpage = "leagues";

$id = 0;
if (!empty($_GET['id'])) {
  $id = mysql_real_escape_string($_GET['id']);
}
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> League", "") ?>
<?

$sql = "SELECT * FROM weblm_leagues_meta WHERE leagueId=".$id;
$result = mysql_query($sql);
if (!$row = mysql_fetch_array($result)) {
  echo "<p>League not found.</p>";
} else {
  $leagueShortName = $row['leagueName'];
  $forumLink = "/forum/viewforum.php?f=".$row['forumId'];
?>


<? $seperator = "<option value=''>------------------------------------</option>";

$sql = "SELECT * from $leaguestable where league = '$id'";
$result = mysql_query($sql);
$teamPlayerArray = array();

while($row = mysql_fetch_array($result)){
	$team = $row['team'];
	$player = $row['player'];
	$teamPlayerArray[$team] = $player;
} // while

?>
<table width="100%">
<tr>
<td width="80%">
For game reports, deadlines and discussion, please visit the <a href="<?= $forumLink ?>"><b><?= $leagueShortName ?> forum</b></a>!
</td>
<td><a name="standings" /></td>
</tr>
<tr>
	<td></td><td></td>
</tr>
<tr><td>
<? $columnsArray = array('', '', 'Team', 'Player', 'Games', 'W', 'D', 'L', '+/-', 'Points'); ?>
<?= getRankBoxTop($leagueShortName." Standings ".$status, $columnsArray); ?>
<?
	$standingsArray = array();
	$sql = "SELECT * from $leaguegamestable where league=$id order by id";
	$result = mysql_query($sql);

	while ($row = mysql_fetch_array($result)) {
		$winteam = $row['winteam'];
		$loseteam = $row['loseteam'];
		$winresult = $row['winresult'];
		$loseresult = $row['loseresult'];
		
		$goaldiff = $winresult - $loseresult;

		if ($goaldiff != 0) {
		    $winpts = 3;
			$losepts = 0;
			$win = 1;
			$loss = 1;
			$draw = 0;
		} else {
			$winpts = 1;
			$losepts = 1;
			$win = 0;
			$loss = 0;
			$draw = 1;
		}
		
		if (empty($standingsArray[$winteam])) {
		    $standingsArray[$winteam] = array($winpts, $goaldiff, 1, 
				$win, $draw, 0, $winteam);
			
		} else {
			$teamArray = $standingsArray[$winteam];
			$teamArray[0] = $teamArray[0] + $winpts;
			$teamArray[1] = $teamArray[1] + $goaldiff;
			$teamArray[2] = $teamArray[2] + 1;
			$teamArray[3] = $teamArray[3] + $win;
			$teamArray[4] = $teamArray[4] + $draw;
			// no change to loss
			$standingsArray[$winteam] = $teamArray;
		}
		
		if (empty($standingsArray[$loseteam])) {
		    $standingsArray[$loseteam] = array($losepts, -$goaldiff, 1, 
				0, $draw, $loss, $loseteam);
		} else {
			$teamArray = $standingsArray[$loseteam];
			$teamArray[0] = $teamArray[0] + $losepts;
			$teamArray[1] = $teamArray[1] - $goaldiff;
			$teamArray[2] = $teamArray[2] + 1;
			// no change to win
			$teamArray[4] = $teamArray[4] + $draw;
			$teamArray[5] = $teamArray[5] + $loss;
			$standingsArray[$loseteam] = $teamArray;
		}
	}
	
	$sql = "SELECT * from $leaguestable where league=$id order by player asc";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$team = $row['team'];
		if (!array_key_exists($team, $standingsArray)) {
			$standingsArray[$team] = array(0, 0, 0, 0, 0, 0, $team);
		}
	}
	
	arsort($standingsArray);
	$pos = 1;
	$posDef = "";
	foreach ($standingsArray as $teamArray) {
		$team = $teamArray[6];
		$teamImg = $winnerImg = getImgForTeam($team);
		$teamName = getTeamNameForId($team);
		$playerName = $teamPlayerArray[$team];
		$playerLink = "<a href='/profile.php?name=".$playerName."'>".$playerName."</a>";
		$posdefCurrent = $teamArray[2].$teamArray[1].$teamArray[0];
		
		if ($posdefCurrent != $posDef) {
			$posDisplay = $pos;
		 	$posDef = $posdefCurrent;
		} else {
			$posDisplay = "";
		}
	?>
		<tr<? if (strcmp($cookie_name, $playerName) == 0) {
			echo " class='row_active'";
		} 
		else {
			echo " class='row'";
		}
		?>>
			<td width="5%" align="right"><?= $posDisplay ?>.</td>
			<td width="5%" align="center"><?= $teamImg ?></td>
			<td width="15%"><?= $teamName ?></td>
			<td width="15%"><?= $playerLink ?></td>
			<td width="4%" align="right"><?= $teamArray[2] // Games ?></td>
			<td width="4%" align="right"><?= $teamArray[3] ?></td> 
			<td width="4%" align="right"><?= $teamArray[4] ?></td>
			<td width="4%" align="right"><?= $teamArray[5] ?></td>
			<td width="4%" align="right"><?= $teamArray[1] // +/- ?></td>
			<td width="5%" align="right"><b><?= $teamArray[0] // points ?></b></td>
		</tr>
	<?			
		$pos++;
	}	
?>	

<?= getRankBoxBottom() ?>


<?
$sql = "SELECT * from $leaguestable where league = '$id'";
$result = mysql_query($sql);
$teamPlayerArray = array();

while($row = mysql_fetch_array($result)){
	$team = $row['team'];
	$player = $row['player'];
	$teamPlayerArray[$team] = $player;
} // while

$sql = "SELECT team, player from $leaguestable where league=$id order by Team ASC";
$result = mysql_query($sql);
$rowcount = mysql_num_rows($result);
$teamsArray = array();
$teamsIndex = array();
$imgArray = array();
$imgArray[] = "";
$index = 0;

$emptyRowArray = array();
$rowsArray = array();
for ($i = 0; $i < $rowcount; $i++) {
	$emptyRowArray[] = "-";
}
$myIndex = -1;
while ($row = mysql_fetch_array($result)) {
	
	$team = $row['team'];
	$player = $row['player'];
	if (strcmp($cookie_name, $player) == 0) {
		$myIndex = $index;
	}
	$rowsArray[] = $emptyRowArray;
	$teamsIndex[$team] = $index;
	$imgArray[] = getImgForTeamPlayerName($team, $player);
	$index++;	
}
	$sql = "SELECT * from $leaguegamestable where league=$id order by id asc";
	$result = mysql_query($sql);

	while ($row = mysql_fetch_array($result)) {
		$winteam = $row['winteam'];
		$loseteam = $row['loseteam'];
		$winresult = $row['winresult'];
		$loseresult = $row['loseresult'];
		
		$winnerIndex = $teamsIndex[$winteam];
		$loserIndex = $teamsIndex[$loseteam];
		$winnerRow = $rowsArray[$winnerIndex];
		$loserRow = $rowsArray[$loserIndex];
		
		if ($winnerRow[$loserIndex] == "-") {
			$winnerRow[$loserIndex] = $winresult."-".$loseresult;
		} else {
			$winnerRow[$loserIndex] = $winnerRow[$loserIndex]."<br>".$winresult."-".$loseresult;
		}
		if ($loserRow[$winnerIndex] == "-") {
			$loserRow[$winnerIndex] = $loseresult."-".$winresult;
		} else {
			$loserRow[$winnerIndex] = $loserRow[$winnerIndex]."<br>".$loseresult."-".$winresult;
		}	
		$rowsArray[$winnerIndex] = $winnerRow;
		$rowsArray[$loserIndex] = $loserRow;
	}
	$sql = "SELECT * from $leaguegamestable where league=$id order by id DESC";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	$playedGames = "&nbsp;<span style='font-weight:normal;font-size:10px;'>(".$num." games played)</span>";
?>
	<a name="crosstable" /><br></td>
	<td style="text-align:right;vertical-align:top"><a href="#crosstable">Crosstable &darr;&nbsp;&nbsp;</a><br><a href="#games">Games &darr;&nbsp;&nbsp;</a></td>
</tr>
<tr>
	<td>

<table width="<?= (sizeof($teamsIndex)+1)*35 ?>"><tr><td>
<?= getCrosstabTop($leagueShortName." Games - Crosstable ".$status.$playedGames, $imgArray, ""); ?>

<?	
array_shift($imgArray);
$i = 0;

foreach ($rowsArray as $row) { 
	if ($myIndex == $i) { ?>
		<tr class="row_active">
	
	<? } else { ?>
		<tr class="row">
	<? } ?>
		<td style="background-color:#7393cd;"><?= array_shift($imgArray) ?></td>
		<?	$j = 0;
			foreach ($row as $cell) { 
			if ($i == $j) { ?>
				<td style="background-color:#eeeeee;"></td> 
			<? } else { 
				if ($j == $myIndex) { ?>
				<td class="cell_active" style="text-align:center;font-size:9px;padding:3 3 3 3;"><?= $cell ?></td>
				<? } else { ?>
				<td style="text-align:center;font-size:9px; padding:3 3 3 3;"><?= $cell ?></td>
				<? } ?>
			<? } 
			$j++; 	
		} ?>
	</tr>		
<?	
$i++;
} ?>	

<?= getRankBoxBottom() ?>
</td></tr></table>

<? $columnsArray = array('Id', 'Added', 'Player', '', '', 'Result', '', '', 'Player'); ?>
<?
	$sql = "SELECT * from $leaguegamestable where league=$id order by id DESC";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	$playedGames = "&nbsp;<span style='font-weight:normal;font-size:10px;'>(".$num." games played)</span>";
?>
	<a name="games" /><br></td>
	<td style="text-align:right;vertical-align:top"><a href="#standings">Standings &uarr;&nbsp;&nbsp;</a><br />
	<a href="#games">Games &darr;&nbsp;&nbsp;</a></td>
</tr>
<tr>
	<td>

<?= getRankBoxTop($leagueShortName." Games - List ".$status.$playedGames, $columnsArray); ?>
<?
	while ($row = mysql_fetch_array($result)) {
		$id = $row['id'];
		$winteam = $row['winteam'];
		$loseteam = $row['loseteam'];
		$winresult = $row['winresult'];
		$loseresult = $row['loseresult'];
		$reportDate = $row['reportDate'];
		
		$dateFormatted = formatLongDate($reportDate);
		$winnerImg = getImgForTeam($winteam);
		$loserImg = getImgForTeam($loseteam);	
		$winnerTeamname = getTeamNameForId($winteam);
		$loserTeamname = getTeamNameForId($loseteam);
		$gameResult = $winresult."&nbsp;-&nbsp;".$loseresult;
		$winner = $teamPlayerArray[$winteam];
		$loser = $teamPlayerArray[$loseteam];
		$winnerlink = "<a href='/profile.php?name=".$winner."'>".$winner."</a>";
		$loserlink = "<a href='/profile.php?name=".$loser."'>".$loser."</a>";
	?>
	<tr<? if (strcmp($cookie_name, $winner) == 0 
		|| strcmp($cookie_name, $loser) == 0) {
		echo " class='row_active'";
	} 
	else {
		echo " class='row'";
	}
	?>>
			<td width="5%" align="right"><?= $id ?></td>
			<td width="10%" nowrap><?= $dateFormatted ?></td>
			<td width="15%" align="right"><?= $winnerlink ?></td>
			<td width="19%" align="right"><?= $winnerTeamname ?></td>
			<td width="5%" align="center"><?= $winnerImg ?></td>
			<td width="7%" align="center"><?= $gameResult ?></td>
			<td width="5%" align="center"><?= $loserImg ?></td>	
			<td width="19%"><?= $loserTeamname ?></td>
			<td width="15%"><?= $loserlink ?></td>
		</tr>
	<?
	} // while
?>
<?= getRankBoxBottom() ?>

<td style="text-align:right;vertical-align:top"><a href="#standings">Standings &uarr;&nbsp;&nbsp;</a><br />
	<a href="#crosstable">Crosstable &uarr;&nbsp;&nbsp;</a></td>
</td></tr></table>
<? } // end no id
?>
<?= getOuterBoxBottom() ?>
<?
require('../bottom.php');
?>