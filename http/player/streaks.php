<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "players";
$subpage = "streaks";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$boxImg = "8.jpg";
$boxAlign = "left bottom";

$name = mysql_real_escape_string($_GET['name']);
?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, $name), "") ?>
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
	
?>
<table width="100%">
	<tr>
		<td style="width:50%;vertical-align:top;">
<?
	$sql = "select * from $gamestable where (winner like '$name' or winner2 like '$name' or loser like '$name' or loser2 like '$name') AND deleted = 'no' order by game_id asc";
	$result = mysql_query($sql);
	$winstreak = 0;
	$losestreak = 0;
	$startdate = "";
	$enddate = "";
	$winStreakArray = array();
	$loseStreakArray = array();
	$num = mysql_num_rows($result);
	$cur = 1;
	while ($row = mysql_fetch_array($result)) {
		$winner = $row['winner'];
		$winner2 = $row['winner2'];
		$loser = $row['loser'];
		$loser2 = $row['loser2'];
		$date = $row['date'];
		$isDraw = $row['isDraw'];
		if (strcasecmp($startdate, "") == 0) {
			$startdate = $date;
			$enddate = $date;
		}
		if ($isDraw > 0) {
			if ($losestreak > 0) {
				// losestreak ended - add entry for losestreak
				$loseStreakArray[] = addStreak($loseStreakArray, $losestreak, $startdate, $enddate);
				$losestreak = 0;
			} else if ($winstreak > 0) {
				// winstreak ended - add entry for winstreak
				$winStreakArray[] = addStreak($winStreakArray, $winstreak, $startdate, $enddate);
				$winstreak = 0;
			}
		} else if ((strcasecmp($winner, $name) == 0) || (strcasecmp($winner2, $name) == 0)) {
			$winstreak++;
			if ($winstreak == 1) {
				// winstreak started
				if ($losestreak > 0) {
					// losestreak ended - add entry for losestreak
					$loseStreakArray[] = addStreak($loseStreakArray, $losestreak, $startdate, $enddate);
					$losestreak = 0;
				}
				$startdate = $date;
			} else {
				// winstreak continues: extend enddate
				$enddate = $date;					
			}
		} else {
			$losestreak++;
			if ($losestreak == 1) {
				// losestreak started
				if ($winstreak > 0) {
					// winstreak ended - add entry for winstreak
					$winStreakArray[] = addStreak($winStreakArray, $winstreak, $startdate, $enddate);
					$winstreak = 0;
				}
				$startdate = $date;
			} else {
				// losestreak continues: extend enddate
				$enddate = $date;					
			}
		}
		if ($num == $cur) {
			if ($winstreak > 0) {
				// add entry for winstreak
				$winStreakArray[] = addStreak($winStreakArray, $winstreak, $startdate, $enddate);
				$winstreak = 0;
			}
			if ($losestreak > 0) {
				// add entry for losestreak
				$loseStreakArray[] = addStreak($loseStreakArray, $losestreak, $startdate, $enddate);
				$losestreak = 0;
			}
		}
		$cur++;
	}
	
	
	arsort($winStreakArray);
	$winStreakArray = array_slice($winStreakArray, 0, 8);
	arsort($loseStreakArray);
	$loseStreakArray = array_slice($loseStreakArray, 0, 8);
	
	$winDays = urlencode(serialize($winStreakArray));
	$loseDays = urlencode(serialize($loseStreakArray));
	?>
	  
				<? $columnsArray = array('Pos','Begin', 'End', 'Streak'); ?>
				<?= getRankBoxTop("Winning Streaks", $columnsArray); ?>
				<? 
					$pos = 1;				
					$currentStreak = "";
					foreach ($winStreakArray as $winItem => $item) { 
						$streak = $item[0];						
						if (strcmp($streak, $currentStreak) == 0) {
							$posDisplay = ".";
						} else {
							$posDisplay = $pos;
							$currentStreak = $streak;
						}						
						$pos++;
				?>
					<tr class="row">
						<td style="text-align:right"><?= $posDisplay ?></td>
						<td><?= formatNewsDate($item[1]) ?></td>
						<td><?= formatNewsDate($item[2]) ?></td>
						<td style="text-align:right"><b><?= $streak ?></b></td>
					</tr>
				<? } ?> 
				<?= getRankBoxBottom() ?>	
				
				
			<? $columnsArray = array('Pos','Begin', 'End', 'Streak'); ?>
				<?= getRankBoxTop("Losing Streaks", $columnsArray); ?>
				<? 
					$pos = 1;				
					$currentStreak = "";
					foreach ($loseStreakArray as $winItem => $item) { 
						$streak = $item[0];						
						if (strcmp($streak, $currentStreak) == 0) {
							$posDisplay = ".";
						} else {
							$posDisplay = $pos;
							$currentStreak = $streak;
						}						
						$pos++;
				?>
					<tr class="row">
						<td style="text-align:right"><?= $posDisplay ?></td>
						<td><?= formatNewsDate($item[1]) ?></td>
						<td><?= formatNewsDate($item[2]) ?></td>
						<td style="text-align:right"><b><?= $streak ?></b></td>
					</tr>
				<? } ?> 
				<?= getRankBoxBottom() ?>				
 
			
			
		</td>

		<td style="width:50%; vertical-align:top">
		
		<?= getBoxTopImg("Winning Streaks - Graph", null, false, "", '', ''); ?>
		
				 <table class="layouttable">
				    <tr>
				      <td height="170">
				      	<img src="/graph/streak.php?name=<?= $name ?>&amp;winDays=<?= $winDays ?>" border="1">
				      </td>
					</tr>
				 </table>
		<?= getBoxBottom() ?>
					
		<?= getBoxTopImg("Losing Streaks - Graph", null, false, "", '', ''); ?>
		
				 <table class="layouttable">
				    <tr>
				      <td height="170">
				      	<img src="/graph/streak.php?name=<?= $name ?>&amp;loseDays=<?= $loseDays ?>" border="1">
				      </td>
					</tr>
				 </table>
		<?= getBoxBottom() ?>
		</td>
	</tr>
</table>
<? } ?>
<?= getOuterBoxBottom() ?>
<?
function addStreak($streakArray, $streak, $startdate, $enddate) {
	return array($streak, $startdate, $enddate);
}
?>
<? require('../bottom.php'); ?>

