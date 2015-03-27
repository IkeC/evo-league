<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "checkCounts";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./../top.php');

?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Check Counts", ""); ?>

<?
	
if (!$isAdminFull) {
  echo "<p>Access denied.</p>";
} else {

	$sql = "SELECT * FROM $gamestable WHERE deleted = 'no' and season = $season";
	$result = mysql_query($sql);
	
	$playersWins = array();
	$playersLosses = array();
	$playerDraws = array();
	
	while ($row = mysql_fetch_array($result)) {
		
		$winner = $row['winner'];
		$loser = $row['loser'];
		$winner2 = $row['winner2'];
		$loser2 = $row['loser2'];
		
		$isDraw = $row['isDraw'];
		
		if ($isDraw == 0) {
			if (array_key_exists($winner, $playersWins)) {
				$playersWins[$winner] = $playersWins[$winner]+1; 	
			} else {
				$playersWins[$winner] = 1;
			}
			if (!empty($winner2)) {
				if (array_key_exists($winner2, $playersWins)) {
					$playersWins[$winner2] = $playersWins[$winner2]+1; 	
				} else {
					$playersWins[$winner2] = 1;
				}
			}
			if (array_key_exists($loser, $playersWins)) {
				$playersLosses[$loser] = $playersLosses[$loser]+1; 	
			} else {
				$playersLosses[$loser] = 1;
			}
			if (!empty($loser2)) {
				if (array_key_exists($loser2, $playersWins)) {
					$playersWins[$loser2] = $playersWins[$loser2]+1; 	
				} else {
					$playersWins[$loser2] = 1;
				}
			}
		} else {
			if (array_key_exists($winner, $playersDraws)) {
				$playersDraws[$winner] = $playersDraws[$winner]+1; 	
			} else {
				$playersDraws[$winner] = 1;
			}
			if (!empty($winner2)) {
				if (array_key_exists($winner2, $playersDraws)) {
					$playersDraws[$winner2] = $playersDraws[$winner2]+1; 	
				} else {
					$playersDraws[$winner2] = 1;
				}
			}
			if (array_key_exists($loser, $playersDraws)) {
				$playersDraws[$loser] = $playersDraws[$loser]+1; 	
			} else {
				$playersDraws[$loser] = 1;
			}
			if (!empty($loser2)) {
				if (array_key_exists($loser2, $playersDraws)) {
					$playersDraws[$loser2] = $playersDraws[$loser2]+1; 	
				} else {
					$playersDraws[$loser2] = 1;
				}
			}
		}
		
	}
	
ksort($playersWins);
ksort($playersLosses);
ksort($playersDraws);
	
	foreach ( $playersWins as $key => $value ) {
		$sql = "SELECT pes5wins from $playerstable where name = '$key'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$wins = $row['pes5wins'];
		echo 'wins - Player: <a href="/profile.php?name='.$key.'">'.$key."</a> - Val:$value - wins: $wins";
		if ($value != $wins) {
			echo " --> ".($wins-$value);
		}
		echo "<br>";
		
	}
	echo "<br>";
	foreach ( $playersLosses as $key => $value ) {
		$sql = "SELECT pes5losses from $playerstable where name = '$key'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$losses = $row['pes5losses'];
		echo 'losses - Player: <a href="/profile.php?name='.$key.'">'.$key."</a> - Val:$value - losses: $losses";
		if ($value != $losses) {
			echo " --> ".($losses-$value);
		}
		echo "<br>";
		
	}
	echo "<br>";
	foreach ( $playersDraws as $key => $value ) {
		$sql = "SELECT draws from $playerstable where name = '$key'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$draws = $row['draws'];
		echo 'draws - Player: <a href="/profile.php?name='.$key.'">'.$key."</a> - Val:$value - draws: $draws";
		if ($value != $draws) {
			echo " --> ".($draws-$value);
		}
		echo "<br>";
		
	}

}
?>

<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>

