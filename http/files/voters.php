<?php


// this page allows users to upload files

$page = "goals";
$subpage = "voters";
 
require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('./functions.php');
require ('../top.php');

$columnTitlesArray = array ('Pos', 'Player', 'Votes');
?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), ""); ?>
<table width="100%">
<tr><td width="50%">
<?= getRankBoxTop("Voters", $columnTitlesArray); ?>
<?
	$sql = "SELECT count( v.id ) AS count, p.name ".
		"FROM $votestable v ".
		"LEFT JOIN $playerstable p ON v.player_id = p.player_id ".
		"GROUP BY v.player_id ".
		"ORDER BY count DESC";

	$result = mysql_query($sql);
	$i = 0;
	$oldCount = "";
	while ($row = mysql_fetch_array($result)) {
		$i++;
		$name = $row['name'];
		$count = $row['count'];
		if ($count != $oldCount) {
			$pos = $i;
		} else {
			$pos = "";
		}
		$oldCount = $count;
	?>
	<tr class="row">
		<td width="10%" style="text-align:right;"><? echo $pos."." ?></td>
		<td width="70%"><?= $name ?></td>
		<td style="text-align:right;" width="20%"><?= $count ?></td>
	</tr>
	<?
	}
?>
<?= getRankBoxBottom() ?>
</td>
<td width="50%"></td>
</tr></table>
<?= getOuterBoxBottom() ?>
<?


	require ('../bottom.php');
?>
