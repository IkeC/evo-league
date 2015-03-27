<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "checkPoints";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./../top.php');

?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Check Points", ""); ?>

<?

if (!$isAdminFull) {
  echo "<p>Access denied.</p>";
} else {

	$sql = "Select name, ra2pes5 from $playerstable WHERE pes5games > 0 order by ra2pes5 desc";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$ra2pes5  = $row['ra2pes5'];
		$sql1 = "SELECT sum( winpoints ) as winpts FROM $gamestable ".
		"WHERE (winner LIKE '$name' OR winner2 LIKE '$name') AND deleted = 'no' AND teamLadder=0 AND season = '$season'";		
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		$winpts = $row1['winpts'];
		
		$sql2 = "SELECT sum(losepoints) as losepts1 FROM $gamestable ".
		"WHERE (loser LIKE '$name') AND deleted = 'no' AND teamLadder=0 AND season = '$season'";		
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_array($result2);
		$losepts1 = $row2['losepts1'];

		$sql3 = "SELECT sum(losepoints2) as losepts2 FROM $gamestable ".
		"WHERE (loser2 LIKE '$name') AND deleted = 'no' AND teamLadder=0 and season = '$season'";		
		$result3 = mysql_query($sql3);
		$row3 = mysql_fetch_array($result3);
		$losepts2 = $row3['losepts2'];
		$total = $winpts-$losepts1-$losepts2;
		echo "<p><b>$name - </b> $winpts-$losepts1-$losepts2 = <b>$total</b> <-> ";
		if ($total != $ra2pes5) {
			echo '<span style="color:red;">';
		} else {
			echo '<span style="color:green;">';
		}
		$diff = $ra2pes5-$total;
		echo "<b>".$ra2pes5."</b></span> (".$diff.")</p>";
	}
}
?>

<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>

