<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "players";
$subpage = "teamstatssix";

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
  $opponentTeamsArray = array();
	
  $sql = "SELECT six_matches.id, six_matches.season, six_matches.lobbyName, six_matches.roomName, 
  sp1.name AS patchHome, sp2.name AS patchAway, UNIX_TIMESTAMP(six_matches.played_on) as played_on, 
  six_matches.score_home, six_matches.score_away, six_matches.score_home_reg, six_matches.score_away_reg, 
  st1.ladderTeamId as ladderTeamHome, st2.ladderTeamId as ladderTeamAway 
  FROM six_matches LEFT JOIN six_patches sp1 ON six_matches.hashHome=sp1.hash 
  LEFT JOIN six_patches sp2 ON six_matches.hashAway=sp2.hash 
  LEFT JOIN six_teams st1 ON (st1.sixTeamId=six_matches.team_id_home AND st1.patchId=sp1.id) 
  LEFT JOIN six_teams st2 ON (st2.sixTeamId=six_matches.team_id_away AND st2.patchId=sp2.id) 
  LEFT JOIN six_matches_played ON six_matches_played.match_id=six_matches.id 
  LEFT JOIN six_profiles ON six_matches_played.profile_id=six_profiles.id 
  LEFT JOIN weblm_players ON weblm_players.player_id=six_profiles.user_id 
  WHERE weblm_players.name='$name' 
  ORDER BY six_matches.id DESC";
  
	$result = mysql_query($sql);
	
	while ($row = mysql_fetch_array($result)) {
		
    $id = $row['id'];
    $score_home = $row['score_home'];
    $score_away = $row['score_away'];
    $ladderTeamHome = $row['ladderTeamHome'];
    $ladderTeamAway = $row['ladderTeamAway'];
    
    $sql2 = "SELECT weblm_players.name FROM weblm_players " .
      "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
      "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
      "WHERE six_matches_played.match_id=$id " .
      "AND six_matches_played.home=1";
    
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);
    $player_home = $row2['name'];
    $profileNameHome = $row2['profileName'];
          
    $row2 = mysql_fetch_array($result2);
    if (!empty($row2)) {
      $player_home2 = $row2['name'];
    } else {
      $player_home2 = "";
    }
          
    $sql2 = "SELECT weblm_players.name FROM weblm_players " .
      "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
      "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
      "WHERE six_matches_played.match_id=$id " .
      "AND six_matches_played.home=0";
      
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);
    $player_away = $row2['name'];

    $row2 = mysql_fetch_array($result2);
    if (!empty($row2)) {
      $player_away2 = $row2['name'];
    } else {
      $player_away2 = "";
    }
    
    
    if ($player_home == $name || $player_home2 == $name) {
      if ($score_home > $score_away) {
        if (!empty($ladderTeamHome)) {
          $teamsArray = addWin($teamsArray, $ladderTeamHome);
        }
        if (!empty($ladderTeamAway)) {
          $opponentTeamsArray = addWin($opponentTeamsArray, $ladderTeamAway);
        }
      } elseif ($score_home == $score_away) {
        if (!empty($ladderTeamHome)) {
          $teamsArray = addDraw($teamsArray, $ladderTeamHome);
        }
        if (!empty($ladderTeamAway)) {
          $opponentTeamsArray = addDraw($opponentTeamsArray, $ladderTeamAway);
        }
      } else {
        if (!empty($ladderTeamHome)) {
          $teamsArray = addLoss($teamsArray, $ladderTeamHome);
        }
        if (!empty($ladderTeamAway)) {
          $opponentTeamsArray = addLoss($opponentTeamsArray, $ladderTeamAway);
        }
      }
    } else {
      if ($score_home > $score_away) {
        if (!empty($ladderTeamAway)) {
          $teamsArray = addLoss($teamsArray, $ladderTeamAway);
        }
        if (!empty($ladderTeamHome)) {
          $opponentTeamsArray = addLoss($opponentTeamsArray, $ladderTeamHome);
        }
      } elseif ($score_home == $score_away) {
        if (!empty($ladderTeamAway)) {
          $teamsArray = addDraw($teamsArray, $ladderTeamAway);
        }
        if (!empty($ladderTeamHome)) {
          $opponentTeamsArray = addDraw($opponentTeamsArray, $ladderTeamHome);
        }
      } else {
        if (!empty($ladderTeamAway)) {
          $teamsArray = addWin($teamsArray, $ladderTeamAway);
        }
        if (!empty($ladderTeamHome)) {
          $opponentTeamsArray = addWin($opponentTeamsArray, $ladderTeamHome);
        }
      }
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
	
?>
<? $columnsArray = array('Pos','', 'Team', 'W', 'D', 'L', 'Used'); ?>
<?= getRankBoxTop($name." - Opponent teams", $columnsArray); ?>
<?
	arsort($opponentTeamsArray);    
    $pos = 1;
 	foreach ($opponentTeamsArray as $team => $valArray) { 
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
 	foreach ($opponentTeamsArray as $team => $valArray) { 
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

