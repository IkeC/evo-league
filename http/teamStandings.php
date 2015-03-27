<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "games";
$subpage = "teamStandings";

require ('variables.php');
require ('variablesdb.php');
require_once('functions.php');
require ('top.php');

$selectedSeason = '';
$selectedStart = '';
$selectedEnd = '';
if (!empty($_POST['season'])) {
 $selectedSeason = mysql_real_escape_string($_POST['season']);
}

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<a name="byPlayer" />
<table width="100%">

<tr>
	<td width="80%">
<?php

$sql = "SELECT *, teamWins/teamGames as percentage FROM $playerstable ".
	"WHERE teamGames > 0 AND approved='yes' ORDER BY teamPoints DESC, percentage DESC, teamLosses ASC";

$result = mysql_query($sql);
$num = mysql_num_rows($result);

$flagsArray = array();

if ($num > 0) {
	$columnTitlesArray = array ('Pos', 'Player', 'Points', 'W', 'D', 'L', 'Percentage', 'Streak');
	$boxTitle = "Team Standings - By Player";
?>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
<?
	$cur = 1;
	$wonPrev = "";
	$lostPrev = "";
	$drawsPrev = "";
	$pointsPrev = "";
	while ($row = mysql_fetch_array($result)) {
		
		$showplayer = true;
		$percentage = $row["percentage"];
		$name = $row["name"];

		$mail = $row["mail"];
		$icq = $row["icq"];
		$aim = $row["aim"];
		$nationality = $row["nationality"];
		$approved = $row["approved"];

		$rating = $row["rating"];
		$ra2ladder = $row["teamPoints"];
		$wins = $row["teamWins"];
		$losses = $row["teamLosses"];
		$games = $row["teamGames"];
		$draws = $row["teamDraws"];
		
		$nameClass = colorNameClass($name, $approved);

		if ($games <= 0) {
			$percentage = 0.000;
		} else {
			$percentage = $wins / $games;
		}
		$streakwins = $row["streakwins"];
		$streaklosses = $row["streaklosses"];
		if ($streakwins >= $hotcoldnum) {
			$picture = 'gfx/streakplusplus.gif';
			$streak = $streakwins;
		} else
			if ($streaklosses >= $hotcoldnum) {
				$picture = 'gfx/streakminusminus.gif';
				$streak = - $streaklosses;
			} else
				if ($streakwins > 0) {
					$picture = 'gfx/streakplus.gif';
					$streak = $streakwins;
				} else
					if ($streaklosses > 0) {
						$picture = 'gfx/streakminus.gif';
						$streak = - $streaklosses;
					} else {
						$picture = 'gfx/streaknull.gif';
						$streak = 0;
					}
					
	$flagsArray[$name] = $nationality;
?>
	<tr<?

		if (strcmp($cookie_name, $name) == 0) {
			echo " class='row_active'";
		} else {
			echo " class='row'";
		}
		
		if ($wins == $wonPrev && $losses == $lostPrev && $draws == $drawsPrev && $ra2ladder == $pointsPrev) {
			$posDisplay = ".";
		} else {
			$posDisplay = $cur.".";
		}
		
		$nameDisplay = '<table width="100%"><tr><td>';
		$nameDisplay .= '<img class="imgMargin" src="'.$directory.'/flags/'.$nationality.'.bmp" title="'.$nationality.'" align="absmiddle" border="1" /><a href="'.$directory.'/profile.php?name='.$name.'">'.$name.'</a>';
		$nameDisplay .= '</td>'; 
		if ($cur == 1) {
			$nameDisplay .= '<td>';
			$nameDisplay .= '<span style="float:right;cursor:help;" title="Current leader"><img style="align:right;" src="'.$directory.'/gfx/teamLadderPlayer.gif" /></span>';
			$nameDisplay .= '</td>';
		}
		$nameDisplay .= '</tr></table>';
?>>
	   <td width="5%" style="text-align:right;height:25px"><?= $posDisplay ?></td>
	   <td width="45%" <?= $nameClass ?>><?= $nameDisplay ?></td>
	   <td width="10%" style="text-align:right;"><?= $ra2ladder ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$wins" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$draws" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$losses" ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	   <td width="10%" style="text-align:center;"><?php echo "<img src='$directory/$picture' alt='Streak: $streak' align='absmiddle' border='1'>"?></td>
	</tr>
	<?php
		$wonPrev = $wins;
		$lostPrev = $losses;
		$drawsPrev = $draws;
		$pointsPrev = $ra2ladder;
		$cur ++;
		
	}
?>
<?= getRankBoxBottom() ?>
</td>
<td style="text-align:right;vertical-align:top"><a href="#byTeam">Standings by Team &darr;&nbsp;&nbsp;</a></td>
</tr>
<tr><td></td></tr>
<tr><td>Standings by team are calculated over ladder games played in the last 90 days.</td></tr>
<tr><td><a name="byTeam" /><br>
<?


// Team Standings
$columnTitlesArray = array ('Pos', 'Team', 'Points', 'Pts +','Pts -', 'W', 'D', 'L', 'Percentage');
$boxTitle = "Team Standings - By Team";

$teamStatSet = getTeamStandingsPerTeamArray();

?>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
<? 
$cur = 0;

$ptsDiffOld = "";

foreach ($teamStatSet as $teamStat) {
	$cur++;
	$player1 = $teamStat->player1;
	$player2 = $teamStat->player2;
	
	$teamDisplay = getPlayerLink($player1);
	if (empty($player2)) {
		$teamDisplay .= '&nbsp;<span class="grey-small">(alone)</span>';
	} else {
		$teamDisplay .= ' <span class="grey-small">&ndash;</span> '.getPlayerLink($player2);
	}
	
	$nameDisplay = '<table width="100%"><tr><td>';
	$nameDisplay .= $teamDisplay;
	$nameDisplay .= '</td>'; 
	if ($cur == 1) {
		$nameDisplay .= '<td>';
		$nameDisplay .= '<span style="float:right;cursor:help;" title="Current leaders"><img style="align:right;" src="'.$directory.'/gfx/teamLadderTeam.gif" /></span>';
		$nameDisplay .= '</td>';
	}
	$nameDisplay .= '</tr></table>';
	
	$nameClass = "";
	$ptsDiffDisplay = $teamStat->getPtsDiff();
	$ptsWonDisplay = $teamStat->ptsWon;
	$ptsLostDisplay = $teamStat->ptsLost;
	$wonDisplay = $teamStat->won;
	$drawDisplay = $teamStat->draw;
	$lostDisplay = $teamStat->lost;
	$games = $teamStat->won+$teamStat->draw+$teamStat->lost;
	
	$percentageDisplay = sprintf("%.2f", ($teamStat->won/($games)*100));
	
	if ($ptsDiffDisplay == $ptsDiffOld) {
		$posDisplay = ".";
	} else {
		$posDisplay = $cur.".";
		$ptsDiffOld = $ptsDiffDisplay;
	}
	
	// ('Pos', 'Team', 'Pts', 'Pts +','Pts -', 'W', 'D', 'L', 'Percentage');
?>
	  <tr<?

		if (strcmp($cookie_name, $teamStat->player1) == 0 || strcmp($cookie_name, $teamStat->player2) == 0) {
			echo " class='row_active'";
		} else {
			echo " class='row'";
		}
		
?>>
		   <td width="5%" height="25" style="text-align:right;height:28px"><?= $posDisplay ?></td>
		   <td width="45%" <?= $nameClass ?>><?= $nameDisplay ?></td>
		   <td width="10%" style="text-align:right;"><?= $ptsDiffDisplay ?></td>
		   <td width="6%" style="text-align:right;"><?= $ptsWonDisplay ?></td> 
		   <td width="6%" style="text-align:right;"><?= $ptsLostDisplay ?></td>
		   <td width="6%" style="text-align:right;"><?= $wonDisplay ?></td>
		   <td width="6%" style="text-align:right;"><?= $drawDisplay ?></td>
		   <td width="6%" style="text-align:right;"><?= $lostDisplay ?></td>
		   <td width="10%" style="text-align:right;"><?= $percentageDisplay ?></td>
	   </tr>
<?
}
?>
<?= getRankBoxBottom() ?>

<?
} else {
	echo "<b>No matches found</b>";
}
?>
</td><td style="text-align:right;vertical-align:top"><a href="#byPlayer"><br>Standings by Player &uarr;&nbsp;&nbsp;</a></td>
</tr></table>

<?= getOuterBoxBottom() ?>
<?php
require ('bottom.php');
?>


