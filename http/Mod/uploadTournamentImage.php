<?php


// this page allows users to upload tournament schedule images, it's not linked from anywhere!
// the players that are allowed to upload images are defined in weblm_tournaments.uploaders (comma-seperated)
// they need to be logged in to access this page.
// the images are written to the /Cup folder with the name defined in weblm_tournaments.imageFilename,
// make sure web users have write access to your /Cup and /pictures folders!

$page = "uploadTournamentImage";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');
?>

<?= getOuterBoxTop("Cup Images Upload", ""); ?> 
<table width="50%">
<tr><td>
<?php


$back = '<p><a href="javascript:history.back()">go back</a></p>';
$contact = "<p>Please contact me at " . $adminmail . " or in the forum if the problem persists." . $back;

if (!$isAdminFull && !$isModFull) {
	echo "<p>Access denied.</p>";
} else {
	if (!empty ($_POST['imageSlot'])) {
		$imageSlot = mysql_real_escape_string($_POST['imageSlot']);

		if (stristr($imageSlot, '#')) {
			// slot image
			$slots = explode('#', $imageSlot);
			$tournamentId = $slots[0];
			$slotId = $slots[1];
		} else {
			// main image 
			$tournamentId = $imageSlot;
		}

		if (empty ($_FILES['image']['size'])) {
			echo '<p>The uploaded file was empty.</p>' . $contact;
		} else {
			$sql = "SELECT * FROM $tournamenttable where id = '$tournamentId'";
			$result = mysql_query($sql);
			$row = mysql_fetch_array($result);

			$f1_size = $_FILES['image']['size'];
			$f1_name = $_FILES['image']['name'];
			$f1_tmpname = $_FILES['image']['tmp_name'];

			$ext = strtolower(substr($f1_name, strrpos($f1_name, ".") + 1));

			$validExtensions = array (
				$valid_cup_extension1,
				$valid_cup_extension2
			);
			$maxsize = $row['imageMaxsize'];
			$getParam = $row['getParam'];
			if ($f1_size > $maxsize) {
				echo '<p>Your image is too big.<br>' . $f1_size . 'KB, Maximum: ' . $maxsize . ' KB' . $back;
			} else {

				if (empty ($slotId)) {
					$picturename = $row['imageFilename'] . '.' . $ext;
				} else {
					$picturename = $row['imageFilename'] . '_' . $slotId . '.' . $ext;
				}

				$copywork = rename($f1_tmpname, "../Cup/" . $picturename);
				if (!$copywork) {
					echo "<p>Upload failed.</p>" . $contact;
				} else {
					chmod("../Cup/" . $picturename, 0644);
					$updateDate = time();
					if (empty ($slotId)) {
						$sql = "update $tournamenttable set extension = '$ext', updateDate = '$updateDate' where id='$tournamentId'";
						mysql_query($sql);
					} else {
						$sql = "update $tournamentimagestable set ext = '$ext', user = '$cookie_name', updateDate='$updateDate' where slot='$slotId' and id='$tournamentId'";
						mysql_query($sql);
					}

					echo '<p>Upload of ' . $picturename . ' successful.</p><p>' .
					'Please check the <a href="/tournaments.php">tournaments page</a> to see ' .
					'if everything went correctly.</p>';
					if (!empty ($slotId)) {
						$linkUrl = $directory . '/tournaments.php?cup=' . $getParam . "#" . $slotId;
						echo doParagraph('Direct link to image on tournament page: <a href="' . $linkUrl . '">' . $linkUrl . '</a>');
					}

				}

			} // image too big
		} // empty file
	} // empty get param

	else {
?>
<?= getBoxTop("Upload", "", false, null); ?>

		 <form method="post" action="<? $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data">

         <table class="formtable">
		 <tr>
		 <td>Image Slot</td>
		 <td>
		 	<select class="width300" name="imageSlot">
<?

		if ($cookie_name != 'nilsonramirez') {
			$sql = "SELECT * FROM $tournamenttable ORDER BY id desc";
		} else {
			$sql = "SELECT * FROM $tournamenttable WHERE id=29";
		}
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			$imageMaxsize = $row['imageMaxsize'];
			$cupName = $row['cupName'];
			$tournamentId = $row['id'];
?>
	<option value="<?= $tournamentId ?>"><?= $cupName ?></option>
<?

			$sqlSlots = "SELECT * FROM $tournamentimagestable where tid = '$tournamentId'";
			$resultSlots = mysql_query($sqlSlots);
			while ($rowSlot = mysql_fetch_array($resultSlots)) {
				$slotId = $rowSlot['slot'];
				$slotDesc = $rowSlot['description'];
?>
	<option value="<?= $tournamentId ?>#<?= $slotId ?>"><?= $cupName ?> - <?= $slotDesc ?></option>
<?

			}
		}
?>	 	
		 	</select>
         </td>
         <tr>
         <tr>
            <td>Picture</td>
            <td><input class="width150" size="25" type="File" name="image"></td>
            <td></td>
         </tr>
         <tr><td colspan="3" style="padding-top:10px;padding-bottom:10px;"><input class="width150" type="Submit" name="submit" value="upload image"></td></tr>
         </table>
         </form>
<?= getBoxBottom() ?>		 
<?php


	} // end empty get param
} // end empty cookie name  
?> 
</td>
</tr></table>
<?= getOuterBoxBottom() ?>
<?

require ('../bottom.php');
?>
