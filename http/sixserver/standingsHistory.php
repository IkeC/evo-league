<?php

// displays the standings tables, can be filtered by continent and region if the countries are
// properly defined in weblm_countries.  

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "index";
$subpage = "sixstandingsHistory";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');

$seperator = "<option value=''>------------------------------------</option>";
$selected = " selected='selected'";
$seasonSel = "";

$sql = "SELECT season FROM six_stats";
$row = mysql_fetch_array(mysql_query($sql));
$sixSeason = $row[0];
$seasonLadder = 0;
if (isset($_POST['seasonLadder'])) {
  $seasonLadder = mysql_real_escape_string($_POST['seasonLadder']);
} elseif (isset($_GET['seasonLadder'])) {
  $seasonLadder = mysql_real_escape_string($_GET['seasonLadder']);
} else {
  $seasonLadder = $sixSeason-1;
}

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table width="80%">
<tr><td>
<table class="formtable"><tr><td nowrap>
<form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
<select class="width250" name="seasonLadder">
	<option value="">[Select Season]</option>
	<?= $seperator ?>
<?

$sql= "SELECT * from six_seasons WHERE season < $sixSeason ORDER BY season DESC";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
	$beginDate = $row['begindate'];
  $endDate = $row['enddate'];
  $seasonCurrent = $row['season'];
  echo "<option";
  if ($seasonLadder == $seasonCurrent) {
    echo ' selected="selected"';
  }
  echo ' value="'.$seasonCurrent.'">';
  echo "Season ".$seasonCurrent." (".$beginDate."-".$endDate.")";
  echo "</option>";
}
?>	
</select>&nbsp;&nbsp;<input class="width150" type="Submit" name="submit" value="show" /></form>
</td></tr></table>
<br>

<?php

$num = 0;

if ($seasonLadder > 0) {
	$sql = "SELECT sh.*, wp.name AS playerName, wp.nationality, sp.name AS profileName FROM six_history sh ".
		"LEFT JOIN weblm_players wp ON wp.player_id = sh.playerId ". 
    "LEFT JOIN six_profiles sp ON sp.id = sh.profileId ". 
		"WHERE sh.season=$seasonLadder ".
		"AND position > 0 ".
		"ORDER BY position ASC";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
}

if ($num > 0) {
	$columnTitlesArray = array ('Pos', 'Player', 'Profile', 'Points', 'W', 'D', 'L', 'DC', 'Percentage');
	$boxTitle = "Sixserver standings - Season ".$seasonLadder;
	echo '<table width="900"><tr><td>';
?>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
<?
	$cur = 1;
  $oldPos = -1;
	while ($row = mysql_fetch_array($result)) {
		
    $position = $row['position'];
    
    if ($oldPos != $position) {
      $oldPos = $position;
      $pos = $position;
    } else {
      $pos = "";
    }

    $name = $row["playerName"];
		$profileName = $row['profileName'];
    $points = $row['points'];
    $games = $row["games"];
    $wins = $row["wins"];
		$draws = $row["draws"];
		$losses = $row["losses"];
    $dc = $row["DC"];
    
		$nationality = $row["nationality"];
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
     <td width="7%" style="text-align:right;"><?php echo "$dc" ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	</tr>
<?
		}
    
		$cur++;
	}
?>
<?= getRankBoxBottom() ?>
</td></tr></table>
<?	
} // end position > 0


if ($seasonLadder > 0) {
	$sql = "SELECT sh.*, wp.name AS playerName, wp.nationality, sp.name AS profileName FROM six_history sh ".
		"LEFT JOIN weblm_players wp ON wp.player_id = sh.playerId ". 
    "LEFT JOIN six_profiles sp ON sp.id = sh.profileId ". 
		"WHERE sh.season=$seasonLadder ".
		"AND position=0 ".
		"ORDER BY sh.points DESC";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
}

if ($num > 0) {
	$columnTitlesArray = array ('Player', 'Profile', 'Points', 'W', 'D', 'L', 'DC', 'Percentage');
	$boxTitle = "Inactive at season end";
	echo '<table width="900"><tr><td>';
?>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
<?
	$cur = 1;
  $oldPos = -1;
	while ($row = mysql_fetch_array($result)) {
		
    $name = $row["playerName"];
		$profileName = $row['profileName'];
    $points = $row['points'];
    $games = $row["games"];
    $wins = $row["wins"];
		$draws = $row["draws"];
		$losses = $row["losses"];
    $dc = $row["DC"];
    
		$nationality = $row["nationality"];
    if ($games > 0) {
			$percentage = $wins / ($games+$dc);
?>
	<tr<?

			if (strcmp($cookie_name, $name) == 0) {
				echo " class='row_active'";
			} else {
				echo " class='row'";
			}
?>>
	   <td width="35%" <?= $nameClass ?>><?php echo "<img class='imgMargin' src='$directory/flags/$nationality.bmp' title='$nationality' align='absmiddle' border='1'>" ?><? echo "<a href='$directory/profile.php?name=$name'>$name</a>" ?></td>
	   <td width="20%" class="darkgrey-small"><?= $profileName ?></td>
	   <td width="10%" style="text-align:right;"><?= $points ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$wins" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$draws" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$losses" ?></td>
     <td width="7%" style="text-align:right;"><?php echo "$dc" ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	</tr>
<?
		}
    
		$cur++;
	}
?>
<?= getRankBoxBottom() ?>
</td></tr></table>
<?	
} // end position == 0
?>
</td>
</tr></table>
<?= getOuterBoxBottom() ?>
<?php

require ('../bottom.php');

function getOption($season, $ladder, $seasonLadder) {
	if (strcmp($season.":".$ladder, $seasonLadder) == 0) {
		$selected = 'selected="selected"';
	}
	$result .= '<option '.$selected.' value="'.$season.':'.$ladder.'">Season '.$season.'&nbsp;- '.$ladder.'</option>';
	return $result;
}
?>


