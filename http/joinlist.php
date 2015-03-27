<?php

// displays the standings tables, can be filtered by continent and region if the countries are
// properly defined in weblm_countries.  

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "players";
$subpage = "joinlist";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('top.php');

$seperator = "<option value=''>------------------------------------</option>";

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table width="90%">
<tr><td>
<p>
	<a href="joinlist.php">Show last 100</a> | 
	<a href="joinlist.php?limit=last500">Show last 500</a> | 
	<a href="joinlist.php?limit=first100">Show first 100</a> | 
	<a href="joinlist.php?limit=first500">Show first 500</a> | 
	<a href="joinlist.php?limit=none">Show all</a>
</p>
</td></tr>
<tr><td></td></tr>
<tr><td>
<? 
if (isset($_GET['limit'])) {
	$show = mysql_real_escape_string($_GET['limit']);
} else {
	$show = "";
}

$sql = "SELECT * FROM $playerstable ";
if ($show == 'last500') {
	$sql .= " ORDER BY player_id DESC LIMIT 0, 500";
}
else if ($show == 'first100') {
	$sql .= " ORDER BY player_id ASC LIMIT 0, 100";
}
else if ($show == 'first500') {
	$sql .= " ORDER BY player_id ASC LIMIT 0, 500";
}
else if ($show == 'none') {
	$sql .= " ORDER BY player_id DESC";
}
else {
	$sql .= " ORDER BY player_id DESC LIMIT 0, 100";
}

$result = mysql_query($sql);
$num = mysql_num_rows($result);

if ($num > 0) {

	$columnTitlesArray = array('Id', 'Nat.', 'Loc.', 'Versions', 'Name', 'Alias', 'Games', 'Joined');
	?>
	<?= getRankBoxTop("Players List", $columnTitlesArray) ?>
	
	<?
	$cur = 1;
	while ($row = mysql_fetch_array($result)) {
		$id = $row['player_id'];
		$nationality = $row['nationality'];
		$country = $row['country'];
		$name = $row['name'];
	  $nameClass = colorNameClass($name, $row['approved']);
		$alias = $row['alias'];
		$games = $row['totalgames'];
		$joined = formatDate($row['joindate']);
		$versionsImg = getVersionsImages($row['versions']);
		
		?>
		<tr<? if (strcmp($cookie_name, $name) == 0) {
			echo " class='row_active'";
		} 
		else {
			echo " class='row'";
		}
		?>>
		   <td width="5%" style="text-align:right;"><?= $id ?></td>
		   <td width="5%" style="text-align:center;">
		   	<?= "<img src='$directory/flags/$nationality.bmp' title='Nationality: $nationality' align='absmiddle' border='1'>" ?>
		   </td>
		   <td width="5%" style="text-align:center;">
		   	<?= "<img src='$directory/flags/$country.bmp' title='Location: $country' align='absmiddle' border='1'>" ?>
		   </td>
		   <td width="12%" nowrap><?= $versionsImg ?></td>
		   <td width="25%" <?= $nameClass ?>><?= "<a href='$directory/profile.php?name=$name'>$name</a>" ?></td>
		   <td width="20%" nowrap><?= $alias ?></td>
		   <td width="8%" style="text-align:right;"><?= $games ?></td>
		   <td width="20%" nowrap><?= $joined ?></td>
		</tr>
		<?php
			$cur++;
		}
		
	?>
	<?= getRankBoxBottom() ?>
	<?  
	}
else {
	echo "<b>No matches found</b>";
}
?>
</td>
</tr></table>

<?= getOuterBoxBottom() ?>
<?php
require('bottom.php');
?>

