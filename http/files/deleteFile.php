<?php


// this page allows users to upload files

$page = "upload";
$subpage = "browseFiles";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('./functions.php');
require ('../top.php');
?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), ""); ?>

<table width="100%">
<tr><td width="50%">
<?php

$msg = "";
if ($cookie_name == 'null') {
	echo $membersonly;
} else {
	$filename = mysql_real_escape_string($_POST['filename']);
	$submit = mysql_real_escape_string($_GET['submit']);
	if (empty($submit)) {
		if (empty($filename)) {
			$msg .= "<p>No filename supplied!</p>"; 
		} else {
			$player_id = mysql_real_escape_string($_POST['player_id']);
			$sqlp = "SELECT name from $playerstable where player_id = '$player_id'";
			$resultp = mysql_query($sqlp);
			if (mysql_num_rows($resultp) < 1) {
				$msg .= "<p>Invalid player <b>#".$player_id."</b>!</p>";
			} else {
				$row = mysql_fetch_array($resultp);
				if ($row['name'] != $cookie_name) {
					$msg .= "<p>Name: ".$row['name']." - Access denied</p>";
				} else {
					$msg.= "<p>Are you sure you want to delete <b>$filename</b> and all associated thumbnails?</p>";
					$msg.= "<p>Please <b>do NOT delete images that were posted on the forums</b>, or they will disappear. ";
					$msg .= "If you're in doubt, don't delete the file.</p>";
					$msg .= '<p><form method="post" action="deleteFile.php?submit=true">';
					$msg .= '<input type="hidden" name="filename" value="'.$filename.'">';
					$msg .= '<input type="submit" class="width200" name="submit" value="Yes, delete file(s)"></form></p>';
				}
			}
		}
	} else { // submit = 1
		
		$imgDir = "files/images/";
		$thumbsDir = "files/images/thumbnails/";
		$fileNoExt = substr($filename, 0, strrpos($filename, "."));
		
		$command = "rm " . $wwwroot . $imgDir . $fileNoExt ."*";
		// $msg .= "<p>command = [".$command."]</p>";
		$result = array();
		$res = exec($command, $result);
		$msg .= "<p>Deleting file... $res</p>"; 
		
		
		$command = "rm " . $wwwroot . $thumbsDir . $fileNoExt ."*";
		$result = array();
		$res = exec($command, $result);
		// $msg .= "<p>command = [".$command."]</p>";
		$msg .= "<p>Deleting thumbnails... $res</p>"; 
		
		$msg.= "<p>Done!</p>";		
		$back = '<p><a href="browseFiles.php">Go back</a></p>';
	}
}	
?>

<?= $msg.$back ?>
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?

require ('../bottom.php');
?>
