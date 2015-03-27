<?php

// activate account

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "activate";
$subpage ="activate";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('top.php');

$msg = "";
if(!empty($_GET['id'])) {
	$id = mysql_real_escape_string($_GET['id']);
}
if (empty($id)) {
	$msg = "No signup key supplied.";	
} else {
	$sql = "SELECT name FROM ".$playerstable." WHERE signup='".$id."'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) == 0) {
		$msg = "Signup id not found.<br><br>You've probably clicked this link before and your account is already active. Try to log in with your username and password.";
	} else {
		$row = mysql_fetch_array($result);
		$name = $row['name'];
		$sql = "UPDATE ".$playerstable." SET approved='yes', signup='' WHERE signup='".$id."'";
		$res = mysql_query($sql);
		echo "<!--".$sql."-".$res."-->";
		$msg = "Hello <b>".$name."</b>!<br><br>";
		$msg .= "You have activated your account. Log in with your username and password.<br><br>Have fun!";
	}
}

?>
		
<?= getOuterBoxTop($leaguename . " account activation", "") ?>
<div style="font-size:14px">
<?= $msg ?>
</div>
<?= getOuterBoxBottom() ?>

<?php
require('bottom.php');
?>
