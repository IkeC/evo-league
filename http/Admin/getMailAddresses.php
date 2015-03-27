<?php


header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "getMailAddresses";

require ('./../variables.php');
require ('./../variablesdb.php');
require_once ('../functions.php');
require_once ('./functions.php');
require ('./../top.php');

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Get mail addresses", ""); ?>
<?

if (!$isAdminFull) {
  echo "<p>Access denied.</p>";
} else {

	$sql = "SELECT mail, name, player_id, msn from $playerstable  ".
		"where player_id order by player_id asc";
	$result = mysql_query($sql);
	$i = 1;
	$block = 200;
	while($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$mail = $row['mail'];
		if (!isValidEmailAddress($mail)) {
			$mail = $row['msn'];
		}
		if (!isValidEmailAddress($mail)) {
			// echo "<p>Invalid mail address for [$player_id] $name: $mail</p>";
		}
		else {
			$player_id = $row['player_id'];
				// echo "<p>Player: [$player_id] $name | Mail: $mail</p>";
				
			$sql2 = "SELECT userId from $playerstatustable ".
				"where userId = '$player_id' and type='B' and active='Y'";
			$result2 = mysql_query($sql2);
			if (mysql_num_rows($result2) > 0) {
			    // echo "<p>Player ".$name." is banned!</p>";
			}
			else {
				echo $mail.",";
			}
			
			$i++;
			
			if (($i % $block) == 0) {
				echo "<p>block: ".$block."</p>";
			}
		}
	}
}
?>
<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>

