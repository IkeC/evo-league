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


if ($cookie_name == 'null') {
	echo $membersonly;
} else {
	
	if (!empty($_GET['name'])) {
		$name = mysql_real_escape_string($_GET['name']);
	} else {
		$name = $cookie_name;
	}
	$sqlp = "SELECT player_id from $playerstable where name = '$name'";
	$resultp = mysql_query($sqlp);
	if (mysql_num_rows($resultp) < 1) {
		echo "<p>Couldn't find <b>$name</b>!</p>" . $back;
	} else {
		$rowp = mysql_fetch_array($resultp);
		$player_id = $rowp[0];
		$prefix = $player_id . "_";

		$imgDir = "images/";
		$thumbsDir = "images/thumbnails/";

		$filesList = getUserFiles($prefix, $imgDir);
		$thumbsList = getUserFiles($prefix, $thumbsDir);

		$fullList = array ();
		foreach ($filesList as $fileArray) {
			$fileThumbnails = array ();
			$hasThumbnails = false;
			$filename = $fileArray[0];
			$picName = substr($filename, 0, strrpos($filename, "."));
			$ext = substr($filename, strrpos($filename, ".") + 1);
			foreach ($thumbsList as $thumbsArray) {
				$thumbname = $thumbsArray[0];
				$hasThumbnails = (strstr($thumbname, $picName . "_") == $thumbname);
				if ($hasThumbnails) {
					break;
				}
			}
			if (!$hasThumbnails && $ext != "fm") {
				// generate them
				//echo "generate:$picName<p>";
				generateThumb($imgDir, $picName, $ext, "");
				//echo "ok:$picName<p>";
			} else {
				//echo "n-ok:$picName<p>";
				
			}
		}

		$thumbsList = getUserFiles($prefix, $thumbsDir);

		foreach ($filesList as $fileArray) {
			$fileThumbnails = array ();
			$filename = $fileArray[0];
			$lastModified = $fileArray[1];
			$picName = substr($filename, 0, strrpos($filename, "."));
			$ext = substr($filename, strrpos($filename, ".") + 1);
			if (in_array_nocase($ext, $valid_picture_extensions)) {
				foreach ($thumbsList as $thumbsArray) {
					$thumbname = $thumbsArray[0];
					$isThumbnail = (strstr($thumbname, $picName . "_") == $thumbname);
					if ($isThumbnail) {
						$fileThumbnails[] = $thumbname;
					}
				}

				asort($fileThumbnails);
				$fullList[] = array (
					$lastModified,
					$filename,
					$fileThumbnails
				);
			} else {
				$fullList[] = array (
					$lastModified,
					$filename,
					null
				);
			}

		}

		arsort($fullList);
		$thumbWidths = getThumbnailWidths();
		if (empty ($fullList)) {
			echo "<p>No files found for <b>" . $name . "</b>.</p>";
			echo "<p>If you think this is an error, please post in the forum.</p>" . $back;
		} else {
			foreach ($fullList as $fileArray) {
				$lastModified = $fileArray[0];
				$filename = $fileArray[1];
				$thumbsArray = $fileArray[2];
				$boxTitle = $filename . "&nbsp;&nbsp;<span style='font-weight:normal;font-size:10px;'>(" . formatLongDate($lastModified) . ")";
?>
				<?= getBoxTop($boxTitle, 30, false, null) ?>
				<table class="layouttable" style="width:100%">
				<tr>
					<? if (!empty($thumbsArray)){ ?>
					<td style="width:250px;vertical-align:top;">
					<a href="<?= $directory ?>/files/images/<?= $filename ?>">
					<img src="<?= $thumbsDir.$thumbsArray[0] ?>"></a></td>
					<td style="vertical-align:top; align:left;">
					<?

				$i = 0;
?>
						<table class="layouttable" width="100%">
						<tr>
							<td width="100">Forum - Full size</td>
							<td width="400" align="left"><textarea onClick="javascript:focus();javascript:select();" name="forumImg<?= $filename ?>" style="width:400px" 
								rows="1">[img]<?= $directory ?>/files/images/<?= $filename ?>[/img]</textarea>
							</td>
							<td with="100%" align="right">
								<form action="./deleteFile.php" method="post">
									<input type="submit" class="width100" value="delete">
									<input type="hidden" name="filename" value="<?= $filename ?>">
									<input type="hidden" name="player_id" value="<?= $player_id ?>">
								</form>
							</td>
						</tr>
					<?

				foreach ($thumbsArray as $thumb) {
					$width = $thumbWidths[$i++];
?>
						<tr>
							<td width="150">Thumbnail - <?= $width ?> px</td>
							<td width="400"><textarea onClick="javascript:focus();javascript:select();" name="forumThumb<?= $thumb ?>" style="width:400px" 
							rows="1">[url=<?= $directory ?>/files/images/<?= $filename ?>][img]<?= $directory ?>/files/images/thumbnails/<?= $thumb ?>[/img][/url]</textarea>
							</td>
							<td>&nbsp;</td>
						</tr>
					<?

				}
?>
						</table>
					</td>
					<? } else {?>
						<td>
							<table class="layouttable" width="100%">
								<tr>
									<td>File link - <a href="<?= $directory ?>/files/images/<?= $filename ?>"><?= $directory ?>/files/images/<?= $filename ?></a></td>
									<td with="100%" align="right">
											<form action="./deleteFile.php" method="post">
												<input type="submit" class="width100" value="delete">
												<input type="hidden" name="filename" value="<?= $filename ?>"> 
												<input type="hidden" name="player_id" value="<?= $player_id ?>">
											</form>
									</td>
								</tr>
							</table>
						</td>
					<? } ?>
				</tr>
				</table>
				<?= getBoxBottom() ?>
			<?

			} // end loop
		} // end no files
	} // end player not in db
} // end empty cookie name  
?> 
</tr></table>
<?= getOuterBoxBottom() ?>
<?

require ('../bottom.php');
?>
