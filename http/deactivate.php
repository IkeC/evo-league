<?php

// this page will show if players click the 'set inactive' button when editing their profile.
// if they confirm they want to be inactive, the field 'approved' for their entry in the weblm_players table
// will be set to 'no' (= inactive).

$page = 'editprofile';
$subpage = 'editprofile';

   header("Cache-Control: no-cache");
   header("Pragma: no-cache");

   require('./variables.php');
   require('./variablesdb.php');
   require('./functions.php');
   require('./top.php');
?> 
 
 <?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, $name), "") ?>
<table width="60%"><tr><td valign="top">
<?  
if ($_GET['passivate'] == 'true') {
		$sql = "UPDATE $playerstable " .
				"SET approved = 'no' " .
				"where name='$cookie_name'";
		$result = mysql_query($sql);
		
		$date = time();
		$reason = "set himself inactive on ".formatDate($date);
		
		$sql = "SELECT player_id as userId from $playerstable where name = '$cookie_name'";
		$id_result = mysql_query($sql);
		$row = mysql_fetch_array($id_result);
		$userId = $row['userId'];
		$sql = "INSERT INTO $playerstatustable (userId, userName, type, active, " .
				"date, expireDate, forumLink, reason) ".
				"VALUES ('$userId', '$cookie_name', 'I', 'Y', " .
				"'$date', '', '', '$reason')";
				
		$result2 = mysql_query($sql);
		
	if ($result == 1 && $result2 == 1) {
?>
	<p>Account <b><? echo $cookie_name ?></b> has been deactivated.</p>
<?
	}
	else {
?>
	<p>An error occured trying to passivate your account.</p><p>Try again later or contact the webmaster.</p>
<?
	}		
} 
else { ?>
	<?= getBoxTop("Deactivation Info", "", false, null) ?>
	<p>If you deactivate your account, your name will disappear from the standings table, statistics 
	 and players list. You can still log in, but you will not be able to report games. 
	</p>
	<p>Your profile, games and points will remain in the database, and you will not lose any 
	 points for inactivity. This means you can set yourself inactive if you go on holiday or just
	 don't want to play for a while, but also if you don't want to play anymore at all.
	</p>
	<p>Please note that you cannot reactivate your account yourself. If you want your account back 
 	just contact one of the admins to get it activated. Do <b>not</b> sign up for another account!
 	</p>
	<?= getBoxBottom() ?>
	
	<?= getBoxTop("Confirmation", "", true, null) ?>
	<p>
		<b>Really deactivate account?</b>
	</p>
	<p>
	<form method="post" action="./deactivate.php?passivate=true<?
	if ($name != null) {
		echo "?name=".$name;
	}
	?>">
		<span class="padding-button"><input type="Submit" name="submit" value="Yes" class="width150"></span>
	</form>
	<form method="post" action="./editprofile.php">
		<span class="padding-button"><input type="Submit" name="submit2" value="No" class="width150"></span>
	</form>
	</p>
	<?= getBoxBottom() ?>
<? 	
}
?>
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?php
require('./bottom.php');
?>
