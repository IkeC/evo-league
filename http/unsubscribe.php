<?php

// the news page showing all news entries

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "unsubscribe";

require('./variables.php');
require('./variablesdb.php');
require('./functions.php');
require('./top.php');
require('./style_rules.php');

$msg = "";

$id = mysql_real_escape_string($_GET['id']);
if (empty($id)) {
	$msg = "<p>Invalid call</p>";
} else {
	$found = false;
	$sql = "SELECT name, player_id from ".$playerstable."";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$md5_db = md5($row[0]);
		if (strcmp($id, $md5_db) == 0) {
			$sql_update = "UPDATE ".$playerstable." SET sendNewsletter = 'no' WHERE name = '".$row[0]."'";
			mysql_query($sql_update);
			$msg.= "Player <b>".$row[0]."</b> successfully unsubscribed from newsletter.";
			$found = true;
			break;
		} 
	}
	if (!$found) {
		$msg .= "Sorry, but we couldn't find you in the database. Please send an email to <b>".$adminmail.
		"</b> with <b>UNSUBSCRIBE</b> as subject to get removed from the list.";
	}	
		 
}

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>

<?= $msg ?>

<?= getOuterBoxBottom() ?>
<?php
require('bottom.php');
?>


