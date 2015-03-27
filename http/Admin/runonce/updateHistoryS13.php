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
require('./../functions.php');
require('./../top.php');

$username = GetInfo($idcontrol,'admin_username');
$password = GetInfo($idcontrol,'admin_password');
$admin_full = GetInfo($idcontrol, 'admin_full');
$admin_season = GetInfo($idcontrol, 'admin_season');

$sql="SELECT * FROM $admintable WHERE name = '$username' AND password = '$password'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
$season = 13;
?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Update", ""); ?>

<?
if (($number == "1") && (($admin_full == 'yes') || ($admin_season == 'yes'))) {

	
		
		$sql = "SELECT * FROM $historytable where season = $season";
		$result = mysql_query($sql);
		
		$pos = 0;
		
		// iterate over players and update history
		while($row = mysql_fetch_array($result)) {
			$id = $row['id'];		
			$player_id = $row['player_id'];
			$name = $row['player_name'];
			
			// goals as winner
			$games_sql = "SELECT sum(winnerresult) as goals_for, sum(loserresult) as goals_against ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND winner = '$name' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			
			$goals_for = $games_row['goals_for'];
			$goals_against = $games_row['goals_against'];

			// goals as loser
			$games_sql = "SELECT sum(winnerresult) as goals_against, sum(loserresult) as goals_for ".
				"FROM $gamestable " .
				"WHERE season = '$season' AND loser = '$name' AND deleted = 'no'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			
			$goals_for += $games_row['goals_for'];
			$goals_against += $games_row['goals_against'];
		
			// aggregate games
			$games_sql = "SELECT count(*) as aggregate ".
				"FROM $gamestable " .
				"WHERE season = '$season' " .
				"AND (loser = '$name' or winner ='$name') AND deleted = 'no' AND host = 'A'";
			$games_result = mysql_query($games_sql);
			$games_row = mysql_fetch_array($games_result);
			
			$aggregate = $games_row['aggregate'];
				
			echo "<p>[$name] goals for: [$goals_for] / ".
				"goals against: [$goals_against] / ".
				"aggregate: [$aggregate]<br>";
			
			$history_sql = "UPDATE $historytable SET goals_for = $goals_for, goals_against = $goals_against, aggregate = $aggregate ".
				"WHERE season = $season AND player_name = '$name'";
			echo "sql: [$history_sql]<br>";
			$history_result = mysql_query($history_sql);
		}
		
		echo "<p>".$ladder." ladder done!</p>";
		echo "<p><hr></p>";
	
}
else {
echo "<p class='header'>$adminonly<br><br>".
	"<p class='text'><a href='$directory/Admin/index.php'><font color='$color1'>log-in</font></a></p>";
}



?>

<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>

