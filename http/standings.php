<?php

// displays the standings tables, can be filtered by continent and region if the countries are
// properly defined in weblm_countries.  

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "games";
$subpage = "standings";

require ('variables.php');
require ('variablesdb.php');
require ('functions.php');
require ('top.php');

$seperator = "<option value=''>------------------------------------</option>";
$selected = " selected='selected'";
$region = "Global";
$ladder = "PES/WE";
$type = "";

if (!empty ($_POST['region'])) {
	$region = mysql_real_escape_string($_POST['region']);
} else if (!empty ($_GET['region'])) {
	$region = mysql_real_escape_string($_GET['region']);
}

if (!empty ($_GET['ladder'])) {
	$ladder = mysql_real_escape_string($_GET['ladder']);
} 

if (!empty ($_GET['type'])) {
	$type = mysql_real_escape_string($_GET['type']);
} else if (!empty ($_POST['type'])) {
	$type = mysql_real_escape_string($_POST['type']);
}

$version = getVersionForLadder($ladder);
if ($version == 'A') {
	$subpage = 'standingsPes4';
}

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<?

if (!stristr(getSupportedVersions(), $version)) {
	echo "<p>The ".$ladder." ladder is unknown or not supported anymore!</P>".$back;
} else {
?>	
<table width="80%"><tr><td>
<table class="formtable"><tr><td nowrap>
<form method="post" action="<?php echo"$directory"?>/standings.php?ladder=<?= $ladder ?>">
<span style="vertical-align:middle">Region&nbsp;</span>
<select class="width150" name="region">
	<option>Global</option>
<?php

echo $seperator;

$sql = "SELECT continent from $countriestable GROUP BY continent ORDER BY continent";

$result = mysql_query($sql);
$continentArray = array();

while ($row = mysql_fetch_array($result)) {
	$continent = $row['continent'];
	$continentArray[] = $continent;
	$isSelected = "";
	if ($continent == $region) {
		$isSelected = $selected;
	}
	echo "<option $isSelected>$continent</option>";
}
echo $seperator;

$regionArray = array();
$sql = "SELECT region from $countriestable WHERE region != '' GROUP BY region ORDER BY region";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$db_region = $row['region'];
	$regionArray[] = $db_region;
	$isSelected = "";
	if ($db_region == $region) {
		$isSelected = $selected;
	}
	echo "<option $isSelected>$db_region</option>";
}
echo $seperator;

$nationalitiesArray = array();
$sql = "SELECT country from $countriestable ORDER BY country";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$country = $row['country'];
	$nationalitiesArray[] = $country;
	$isSelected = "";
	if ($country == $region) {
		$isSelected = $selected;
	}
	echo "<option $isSelected>$country</option>";
}
?>
</select>&nbsp;&nbsp;
<input class="width100" type="Submit" name="submit" value="show" /></form>
</td></tr></table>
<br>

<?php

$pointsField = getPointsFieldForVersion($version);
$gamesField = getGamesFieldForVersion($version);
$winsField = getWinsFieldForVersion($version);
$lossesField = getLossesFieldForVersion($version);

$sortby = $pointsField." DESC, percentage DESC, $lossesField ASC";

$cur = 0;
if (in_array($region, $continentArray)) {
	$condition = "AND nationality IN (";
	$reg_sql = "select country from $countriestable where continent = '$region'";
	$reg_result = mysql_query($reg_sql);
	$num_rows = mysql_num_rows($reg_result);
	while ($cur < $num_rows) {
		$row = mysql_fetch_array($reg_result);
		$condition .= "'".$row['country']."'";
		$cur ++;
		if ($cur < $num_rows) {
			$condition .= ", ";
		}
	}
	$condition .= ")";
} else {
	if (in_array($region, $regionArray)) {
		$condition = "AND nationality IN (";
		$reg_sql = "select country from $countriestable where region = '$region'";
		$reg_result = mysql_query($reg_sql);
		$num_rows = mysql_num_rows($reg_result);
		while ($cur < $num_rows) {
			$row = mysql_fetch_array($reg_result);
			$condition .= "'".$row['country']."'";
			$cur ++;
			if ($cur < $num_rows) {
				$condition .= ", ";
			}
		}
		$condition .= ")";
	} else {
		if (in_array($region, $nationalitiesArray)) {
			$condition = "AND nationality = '$region'";
		} else {
			$condition = "";
		}
	}
}

if ($region == "WE9I") {
	$sql = "SELECT *, $winsField/$gamesField as percentage FROM $playerstable "." WHERE approved='yes' AND defaultversion='G' ORDER BY $sortby";
} else {
	$sql = "SELECT *, $winsField/$gamesField as percentage FROM $playerstable "."WHERE $gamesField > 0 $condition AND approved='yes' ORDER BY $sortby";
}

$result = mysql_query($sql);
$num = mysql_num_rows($result);

if ($num > 0) {

	$columnTitlesArray = array ('Pos', 'Player', 'Points', 'W', 'D', 'L', 'Percentage', 'Streak');
	$boxTitle = $region." Single Standings - Season ".$season;
?>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>

<?
	$cur = 1;
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
		$ra2ladder = $row[$pointsField];
		$wins = $row[$winsField];
		$losses = $row[$lossesField];
		$games = $row[$gamesField];
		if ($version != 'A') {
			$draws = $row["draws"];
		} else {
			$draws = 0;
		}
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
?>
	<tr<?

		if (strcmp($cookie_name, $name) == 0) {
			echo " class='row_active'";
		} else {
			echo " class='row'";
		}
?>>
	   <td width="5%" style="text-align:right;"><? echo $cur."." ?></td>
	   <td width="45%" <?= $nameClass ?>><?php echo "<img class='imgMargin' src='$directory/flags/$nationality.bmp' title='$nationality' align='absmiddle' border='1'>" ?><? echo "<a href='$directory/profile.php?name=$name'>$name</a>" ?></td>
	   <td width="10%" style="text-align:right;"><?= $ra2ladder ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$wins" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$draws" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$losses" ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	   <td width="10%" style="text-align:center;"><?php echo "<img src='$directory/$picture' alt='Streak: $streak' align='absmiddle' border='1'>"?></td>
	</tr>
	<?php

		$cur ++;

	}
?>
<?= getRankBoxBottom() ?>
<?

} else {
	echo "<b>No matches found</b>";
}
?>
</td>
</tr></table>
<? } // ladder supported/unsupported ?> 
<?= getOuterBoxBottom() ?>
<?php

require ('bottom.php');
?>


