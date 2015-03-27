<?php
require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./../top.php');
 
$page = "gamesCheck";

if (!empty($_POST['ago'])) {
	$ago = $_POST['ago'];	
} else {
	$ago = 0;
}

$day = date("d/m/Y", time()-60*60*24*$ago);
$before = 60 * 1;
$after = 60 * 2;
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Games access check", ""); ?>
<?
if (!$isAdminFull) {
  echo "<p>Access denied.</p>";
} else {
?>
<p>
<form method="post" action="gamesCheck.php?submit=1">
	<select name="ago" class="width200">
		<?= getOptionsDays($ago, 0) ?>
	</select>
	<input class="width100" type="submit" name="submit" value="Go"/>
</form>
</p>
<?= getOuterBoxBottom() ?>

<? if (!empty($_GET['submit'])) { ?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Result", ""); ?>
<?

$playersquery = "select distinct winner from $gamestable where deleted='no' " .
		"AND dateday = '$day' " .
		"UNION " .
		"select distinct winner2 from $gamestable where deleted='no' " .
		"AND dateday = '$day' " .
		"UNION " .
		"select distinct loser from $gamestable where deleted='no' " .
		"AND dateday = '$day' " .
		"UNION " .
		"select distinct loser2 from $gamestable where deleted='no' " .
		"AND dateday = '$day' ";

$result = mysql_query($playersquery);
$playerscount = mysql_num_rows($result);

$resultMsg .= doParagraph(doBold("Date: ".$day)." &ndash; Check timeframe is <b>".$before."</b> minutes before to <b>".$after."</b> minutes after a game");

$sql_total = "select count(*) AS c from $gamestable " .
		"WHERE deleted = 'no' " .
		"AND dateday = '$day' " .
		"ORDER BY date DESC";
$res_total = mysql_query($sql_total);
$row_total = mysql_fetch_array($res_total);
$count_total = $row_total['c'];

$sql_deleted = "select count(*) AS c from $gamestable " .
		"WHERE deleted = 'yes' " .
		"AND dateday = '$day' " .
		"ORDER BY date DESC";
$res_deleted = mysql_query($sql_deleted);
$row_deleted = mysql_fetch_array($res_deleted);
$count_deleted = $row_deleted['c'];

while ($row = mysql_fetch_array($result)) {
	$name = $row[0];
	if (!empty($name)) {
		// for each player that played do...
		$logged = 0;
		$sendmail = 0;
		
		$profileurl = $directory."/editprofile.php";
		
		$gamesquery = "select * from $gamestable " .
				"WHERE (winner = '$name' OR winner2 = '$name' OR loser = '$name' OR loser2 = '$name') " .
				"AND deleted = 'no' " .
				"AND dateday = '$day' " .
				"ORDER BY date DESC";
				
		$playergames = mysql_query($gamesquery);
		$gamescount = mysql_num_rows($playergames);
		
		$playerquery = "SELECT mail, sendGamesMail from $playerstable ".
				"WHERE name = '$name'";
	
		$player = mysql_query($playerquery);
		$playerresult = mysql_fetch_array($player);
		
		$resultMsg .= doParagraph("Player: ".doBold($name));
	
		while ($row_game = mysql_fetch_array($playergames)) {
				
				$gameId = $row_game['game_id'];
				$gameDate = $row_game['date'];
				$gametime = date("h:i a", $gameDate);
				
				$winpoints = $row_game['winpoints'];
				$winner = $row_game['winner'];
				$winner2 = $row_game['winner2'];			
				if (strlen($winner2) > 0) {
					$winnerDisplay = $winner."/".$winner2;
				} else {
					$winnerDisplay = $winner;
				}
				$winnerresult = $row_game['winnerresult'];
				$loserresult = $row_game['loserresult'];
				$loser = $row_game['loser'];
				$loser2 = $row_game['loser2'];
				$losepoints = $row_game['losepoints'];
				if (strlen($loser2) > 0) {
					$loserDisplay = $loser."/".$loser2;
					if ($name == $loser2) {
						$losepoints = $row_game['losepoints2'];
					}
				} else {
					$loserDisplay = $loser;
				}
				
				$comment = $row_game['comment'];
				$starttime = $gameDate - 60 * $before;
	            $endtime = $gameDate + 60 * $after;
	            
	            if (stristr($comment, "[reported by")) {
					$checkname = $winner;
					$drawFlip = " &ndash; draw reported by loser, checked winner";
				} else {
					$checkname = $loser;
					$drawFlip = "";
				}
				
				$sql_acc = "SELECT count(*) from $logtable " . "WHERE (user = '$checkname') " . "AND (accesstime BETWEEN $starttime and $endtime) " . "ORDER BY accesstime desc";
				$result_acc = mysql_query($sql_acc);
				$row_acc = mysql_fetch_array($result_acc);
				$accessCount = $row_acc[0];
				if ($accessCount > 0) {
					$hits = '<font color="green">'.$accessCount.' site hits from <b>'.$checkname.'</b> between '.formatTime($starttime).' and '.formatTime($endtime).'</font>'.$drawFlip;
				} else {
					$hits = '<font color="red">No access from <b>'.$checkname.'</b> between '.formatTime($starttime).' and '.formatTime($endtime).'</b></font>'.$drawFlip;
				}               
				
				$line = "<b>#$gameId</b> [$gametime] " .
					"(+$winpoints) $winnerDisplay " .
					"$winnerresult - $loserresult " .
					"$loserDisplay (-$losepoints) &ndash; " .
					$hits;
	
				$resultMsg .= $line."<br>";
		}
	}
}

?><?= $resultMsg ?>
<? } ?>
<?= getOuterBoxBottom() ?>
<? } ?>
<?

require('./../bottom.php');

function getOptionsDays($ago, $offset) {
	$result = "";
	$timestamp = time();
	for ($i = 0; $i < 14; $i++) {
		if ($i != 0) {
			$weekday = date('m/d (l)', $timestamp-60*60*24*$i-$offset);
		} else {
			$weekday = "Today";
		}
		$result .= '<option value="'.$i.'" ';
		if ($i.'' == $ago) {
			$result .= 'selected';
		}
		$result .= ">".$weekday;
		$result .= '</option>';
	}
	return $result;
}

?>