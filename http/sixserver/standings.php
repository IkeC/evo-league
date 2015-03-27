<?php

$page = "index";
$subpage = "sixstandings";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('./functions.php');
require ('../top.php');

$seperator = "<option value=''>------------------------------------</option>";
$selected = " selected='selected'";
$type = "s"; // s=season, r=ranking
$region = "Global";
$group = "";
if (!empty ($_POST['type'])) {
	$type = mysql_real_escape_string($_POST['type']);
} else if (!empty ($_GET['type'])) {
	$type = mysql_real_escape_string($_GET['type']);
}
if (!empty ($_POST['region'])) {
	$region = mysql_real_escape_string($_POST['region']);
} else if (!empty ($_GET['region'])) {
	$region = mysql_real_escape_string($_GET['region']);
}
if (!empty ($_POST['group'])) {
	$group = mysql_real_escape_string($_POST['group']);
} else if (!empty ($_GET['group'])) {
	$group = mysql_real_escape_string($_GET['group']);
}

if ($type == "points2") {
  $pointsArray = GetStandingsArray(true);
  $orderByField = "points2";
  $colTitle = "Points2";
} else if ($type == "s") {
  $pointsArray = GetStandingsArray(true);
  $orderByField = "points";
  $colTitle = "Points";
} else {
  $pointsArray = GetStandingsArray(false);
  $orderByField = "rating";
  $colTitle = "Rating";
}
$left = $subNavText.getRaquo().getSubNavigation($subpage, null);
$right = "our time is <b>".date("m/d h:i a")." ".$timezone."</b>";
		
?>
<?= getOuterBoxTop($left, $right) ?>
<?
$columnTitlesArray = array ('Pos', 'Player', 'Profile', $colTitle, 'W', 'D', 'L', 'DC', 'Percentage');
	$boxTitle = "Sixserver Standings";
?>
<table width="900"><tr><td>

<table class="formtable"><tr><td nowrap>
<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">

<span style="vertical-align:middle">Type&nbsp;</span>
<select class="width150" name="type">
	<option value="s" <? if ($type=="s") { echo $selected; } ?>>Current Season</option>
  <option value="r" <? if ($type=="r") { echo $selected; } ?>>All-Time Rating</option>
</select>

<span style="vertical-align:middle">&nbsp;&nbsp;Region&nbsp;</span>
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
</select>
<span style="vertical-align:middle">&nbsp;&nbsp;Group&nbsp;</span>
<select class="width150" name="group">
	<option <? if (group == '') { echo $selected; } ?>></option>
<?
$sql = "SELECT forum, cnt FROM (SELECT count(*) AS cnt, forum FROM weblm_players WHERE hash6<>'' AND forum<>'' and approved='yes' GROUP BY forum) AS Derived ".
    "WHERE cnt > 10 ORDER BY forum ASC";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$forum = $row['forum'];
  $cnt = $row['cnt'];
	$isSelected = "";
	if ($forum == $group) {
		$isSelected = $selected;
	}
	echo '<option value="'.$forum.'" '.$isSelected.'>'.$forum.' ('.$cnt.')</option>';
}
?>  
</select>
&nbsp;&nbsp;
<input class="width100" type="Submit" name="submit" value="show" /></form>
</td></tr></table>
<br>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
<?
	
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
  
  if ($group <> '') {
    $condition .= " AND weblm_players.forum='".$group."' ";
  }
  
  $sql = "SELECT six_profiles.id as profileId, six_profiles.".$orderByField.", six_profiles.disconnects, six_profiles.name as profileName, six_profiles.rank, six_profiles.rating, " .
				"weblm_players.name AS playerName, weblm_players.nationality, weblm_players.approved FROM six_profiles " .
				"LEFT JOIN weblm_players ON weblm_players.player_id=six_profiles.user_id " .
				"WHERE weblm_players.approved='yes' ".
        $condition." ".
        "ORDER BY six_profiles.".$orderByField." DESC, playerName ASC";
        
		$result = mysql_query($sql);
		$oldPoints = -1;
		$compteur = 1;
	
	while ($row = mysql_fetch_array($result)) {
		$col = "color:#555555";
		
		if (!empty ($row["nationality"])) {
			$nationality = $row["nationality"];
		} else {
			$nationality = 'No country';
		}
		
		$name = $row['playerName'];
		$profileName = $row['profileName'];
		$profileId = $row['profileId'];
		$nameClass = colorNameClass($name, $row['approved']);
		$points = $row[$orderByField];
		
		$pos = "";
		if ($oldPoints != $points) {
			$pos = $compteur;
			$oldPoints = $points;
		}
		
		$wins = $pointsArray[$profileId]['w'];
    if (is_null($wins)) { $wins = 0;}
		$losses = $pointsArray[$profileId]['l'];
    if (is_null($losses)) { $losses = 0;}
		$draws = $pointsArray[$profileId]['d'];
    if (is_null($draws)) { $draws = 0;}
    $dc = $row['disconnects'];
    if ($type == "r") {
      $dc = $dc + GetSixserverDCHistory($profileId);
    }
		$games = $wins + $losses + $draws + $dc;
		
		if ($games > 0) {
			$percentage = $wins / $games;
?>
	<tr<?

			if (strcmp($cookie_name, $name) == 0) {
				echo " class='row_active'";
			} else {
				echo " class='row'";
			}
?>>
	   <td width="5%" style="text-align:right;"><? echo $pos."." ?></td>
	   <td width="35%" <?= $nameClass ?>><?php echo "<img class='imgMargin' src='$directory/flags/$nationality.bmp' title='$nationality' align='absmiddle' border='1'>" ?><? echo "<a href='$directory/profile.php?name=$name'>$name</a>" ?></td>
	   <td width="20%" class="darkgrey-small"><?= $profileName ?></td>
	   <td width="10%" style="text-align:right;"><?= $points ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$wins" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$draws" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$losses" ?></td>
     <td width="7%" style="text-align:right;"><?= $dc ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	</tr>
<?
		}
		$compteur ++;
	}
?>
<?= getRankBoxBottom() ?>
</td></tr></table>
<?= getOuterBoxBottomLinks($linksBottomLeft, $linksBottomRight) ?>
<?php
		require ('../bottom.php');
?>

