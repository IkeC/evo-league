<?php


// displays the standings tables, can be filtered by continent and region if the countries are
// properly defined in weblm_countries.  

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "games";
$subpage = "standingsHistory";

require ('variables.php');
require ('variablesdb.php');
require ('functions.php');
require ('top.php');

$seperator = "<option value=''>------------------------------------</option>";
$selected = " selected='selected'";
$seasonSel = "";
if (!empty($_POST['seasonLadder'])) {
	$seasonLadder = mysql_real_escape_string($_POST['seasonLadder']);
	$seasonLadderArray = explode(":", $seasonLadder);
	$seasonSel = $seasonLadderArray[0];
	$ladder = $seasonLadderArray[1];
}

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table width="80%">
<tr><td>
<table class="formtable"><tr><td nowrap>
<form method="post" action="standingsHistory.php">
<select class="width200" name="seasonLadder">
	<option value="">[Select Season]</option>
	<?= $seperator ?>
<?

$sql= "SELECT season, ladders from $seasonstable WHERE season < $season order by season desc";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$seasonOld = $row['season'];
	$ladders = $row['ladders'];
	if (stristr($ladders, " ")) {
		$laddersArray = explode(" ", $ladders);
		$ladder1 = $laddersArray[0];
		echo getOption($seasonOld, $ladder1, $seasonLadder);
		$ladder2 = $laddersArray[1];
		echo getOption($seasonOld, $ladder2, $seasonLadder);
	} else {
		echo getOption($seasonOld, $ladders, $seasonLadder);
	} 
}
?>	
</select>&nbsp;&nbsp;<input class="width150" type="Submit" name="submit" value="show" /></form>
</td></tr></table>
<br>

<?php
	
if (!empty($seasonLadder)) {
	$sql = "SELECT h.*, p.country, p.nationality from $historytable h ".
		"LEFT JOIN $playerstable p ON p.player_id = h.player_id ". 
		"where season = '$seasonSel' ".
		"AND ladder = '$ladder' ".
		"AND position > 0 ".
		"ORDER BY position asc";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
}

if (isset($num) && $num > 0) {
	$columnTitlesArray = array ('Pos', 'Player', 'Points', 'W', 'D', 'L', 'Percentage', 'Goals For', 'Goals Against');
	$boxTitle = $ladder." Standings - Season ".$seasonSel;
	
?>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>

<?

	$cur = 1;
	while ($row = mysql_fetch_array($result)) {
		$showplayer = true;
		$position = $row["position"];
		$name = $row["player_name"];
		$ra2ladder = $row["points"];
		$wins = $row["wins"];
		$losses = $row["losses"];
		$draws = $row["draws"];
		$goalsFor = $row["goals_for"];
		$goalsAgainst = $row["goals_against"];
		$country = $row["country"];
		$nationality = $row["nationality"];
		$games = $wins + $losses;
		if ($games <= 0) {
			$percentage = 0.000;
		} else {
			$percentage = $wins / $games;
		}
?>
	<tr<?

		if (strcmp($cookie_name, $name) == 0) {
			echo " class='row_active'";
		} else {
			echo " class='row'";
		}
?>>
	   <td width="5%" style="text-align:right;"><? echo $position."." ?></td>
	   <td width="45%"><?php echo "<img class='imgMargin' src='$directory/flags/$nationality.bmp' title='$nationality' align='absmiddle' border='1'>" ?><? echo "<a href='$directory/profile.php?name=$name'>$name</a>" ?></td>
	   <td width="10%" style="text-align:right;"><?= $ra2ladder ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$wins" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$draws" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$losses" ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	   <td width="10%" style="text-align:right;"><?= $goalsFor ?></td>
	   <td width="10%" style="text-align:right;"><?= $goalsAgainst ?></td>
	</tr>
	<?
		$cur ++;
	}
?>
<?= getRankBoxBottom() ?>
<?

	$sql = "SELECT h.*, p.country, p.nationality from $historytable h ".
		"LEFT JOIN $playerstable p ON p.player_id = h.player_id ". 
		"where season = '$seasonSel' ".
		"AND ladder = '$ladder' ".
		"AND position = 0 ".
		"ORDER BY points desc";

	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	
	if ($num > 0) {
		$boxTitle = "Inactive at season end";
	?>
	<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
<?

	$cur = 1;
	while ($row = mysql_fetch_array($result)) {
		$showplayer = true;
		$position = $cur;
		$name = $row["player_name"];
		$ra2ladder = $row["points"];
		$wins = $row["wins"];
		$draws = $row["draws"];
		$losses = $row["losses"];
		$goalsFor = $row["goals_for"];
		$goalsAgainst = $row["goals_against"];
		$country = $row["country"];
		$nationality = $row["nationality"];
		$games = $wins + $losses;
		if ($games <= 0) {
			$percentage = 0.000;
		} else {
			$percentage = $wins / $games;
		}
?>
	<tr<?

		if (strcmp($cookie_name, $name) == 0) {
			echo " class='row_active'";
		} else {
			echo " class='row'";
		}
?>>
	   <td width="5%" style="text-align:right;"><? echo $position."." ?></td>
	   <td width="45%"><?php echo "<img class='imgMargin' src='$directory/flags/$nationality.bmp' title='$nationality' align='absmiddle' border='1'>" ?><? echo "<a href='$directory/profile.php?name=$name'>$name</a>" ?></td>
	   <td width="10%" style="text-align:right;"><?= $ra2ladder ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$wins" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$draws" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$losses" ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	   <td width="10%" style="text-align:right;"><?= $goalsFor ?></td>
	   <td width="10%" style="text-align:right;"><?= $goalsAgainst ?></td>
	</tr>
	<?
		$cur++;
	}
?>
<?= getRankBoxBottom() ?>
<?	
	} // end position == 0

} else if (!empty($_POST['seasonLadder'])) {
	
	echo "<b>No matches found</b>";
}
?>
</td>
</tr></table>
<?= getOuterBoxBottom() ?>
<?php

require ('bottom.php');

function getOption($season, $ladder, $seasonLadder) {
	if (strcmp($season.":".$ladder, $seasonLadder) == 0) {
		$selected = 'selected="selected"';
	}
	$result .= '<option '.$selected.' value="'.$season.':'.$ladder.'">Season '.$season.'&nbsp;- '.$ladder.'</option>';
	return $result;
}
?>


