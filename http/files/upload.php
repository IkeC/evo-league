<?php


// this page allows users to upload files

$page = "upload";
$subpage = "upload";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('./functions.php');
require ('../top.php');

if (isset($_POST['submit'])) {
	$submit = mysql_real_escape_string($_POST['submit']);
} else {
	$submit = "";
}

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), ""); ?>

<table width="100%">
<tr><td width="50%">
<?php
$minFrames = 4;
$maxFrames = 40;

$back = '<p><a href="javascript:history.back()">go back</a></p>';
$contact = "<p>Please contact me at " . $adminmail . " or in the forum if the problem persists." . $back;

if ($cookie_name == 'null') {
	echo $membersonly;
} else {
	if (isset($_GET['type']) && strcmp($_GET['type'], "retry") == 0 && !empty($_POST['fileName'])) {
		$fileName = mysql_real_escape_string($_POST['fileName']);
		$fileNameStripped = strtolower(substr($fileName, 0, strrpos($fileName, "_")));
		$player_id = strtolower(substr($fileName, 0, strpos($fileName, "_")));
		$thumb = 'goals/thumbnails/'.$fileNameStripped."_thumb.png";
		if (file_exists($thumb)) {
			unlink($thumb);
			echo "<p>Deleted $thumb</p>";
		}
		$ani = 'goals/thumbnails/'.$fileNameStripped."_anim.gif";
		if (file_exists($ani)) {
			unlink($ani);
			echo "<p>Deleted $ani</p>";
		}
	
		$genImgRes = generateVideoThumbImages($fileName, $player_id);
		$imgFilesArray = getUserFiles($player_id."_", $wwwroot . $videoThumbsTempDir);
		if ($imgFilesArray == null || (count($imgFilesArray) == 0)) {
			echo "<p>Video frame grabbing failed. The used Codec is probably unsupported.</p>".$back;
		} else {
			printFramesForm($minFrames, $maxFrames, $subpage, $imgFilesArray, $player_id, $fileName);
		}
	} else if (!empty ($_GET['submit'])) {
		$submit = mysql_real_escape_string($_GET['submit']);
		if (strcmp($_GET['type'], "goal") == 0) {

			if (empty ($_POST['shortDesc'])) {
				echo '<p>Please enter the video description.</p>' . $back;
			} else
				if (empty ($_FILES['goal']['size'])) {
					echo '<p>The uploaded file was empty.</p>' . $contact;
	echo "<p>Upload: " . $_FILES["file"]["name"] . "<br>";
    echo "Type: " . $_FILES["file"]["type"] . "<br>";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
    echo "Stored in: " . $_FILES["file"]["tmp_name"];
				} else {

					$shortDesc = mysql_real_escape_string($_POST['shortDesc']);

					$f1_size = $_FILES['goal']['size'];
					$f1_name = $_FILES['goal']['name'];
					$f1_tmpname = $_FILES['goal']['tmp_name'];

					$ext = strtolower(substr($f1_name, strrpos($f1_name, ".") + 1));

					$validExtensions = array (
						$valid_goal_extension1,
						$valid_goal_extension2,
						$valid_goal_extension3,
						$valid_goal_extension4
					);

					if (empty ($_POST['adminPrefix'])) {
						$name = $cookie_name;
					} else {
						$name = mysql_real_escape_string($_POST['adminPrefix']);
					}

					$sqlp = "SELECT player_id from $playerstable where name = '$name'";
					$resp = mysql_query($sqlp);
					if (mysql_num_rows($resp) == 0) {
						echo '<p>Could not find <b>' . $name . '</b> in the database.</p>' . $back;
					} else
						if ($f1_size > $maxsize_goal_upload && empty ($_POST['adminPrefix'])) {
							echo '<p>Your file is too big.<br>' . $f1_size .
							'KB, Maximum: ' . $maxsize_goal_upload . ' KB' . $back;
						} else {
							if (!in_array($ext, $validExtensions)) {
								echo 'Invalid video extension \'' . $ext . '\'.<br>Valid extensions are: ' .
								$valid_goal_extension1 . ", " . $valid_goal_extension2 . ", " .
								$valid_goal_extension3 . ", " . $valid_goal_extension4 . " " . $back;
							} else {
								$rowp = mysql_fetch_array($resp);
								$player_id = $rowp['player_id'];

								$uploaded = time();
								$newName = $player_id . "_" . $uploaded . "." . $ext;

								$copywork = rename($f1_tmpname, "./goals/$newName");

								if (!$copywork) {
									echo "<p>Upload failed.</p>" . $contact;
								} else {
									chmod("./goals/$newName", 0644);
									$webPath = $directory.'/files/goals/' . $newName; 
									echo '<p>Upload of <b><a href="'.$webPath.'">'.$webPath.'</a></b> successful.</p>';
									
									// add goaltable entry
									$type = mysql_real_escape_string($_POST['type']);
									$desc = mysql_real_escape_string(htmlentities($shortDesc));
									
									$sqli = "INSERT INTO $goalstable (type, player_id, uploaded, extension, comment) ".
										"VALUES ('$type', '$player_id', '$uploaded', '$ext', '$desc')";
									
									mysql_query($sqli);
									
									$genImgRes = generateVideoThumbImages($newName, $player_id);
									$imgFilesArray = getUserFiles($player_id."_", $wwwroot . $videoThumbsTempDir);
									if ($imgFilesArray == null || (count($imgFilesArray) == 0)) {
										echo "<p>Video frame grabbing failed. The used Codec is probably unsupported.</p>".$back;
									} else {
										printFramesForm($minFrames, $maxFrames, $subpage, $imgFilesArray, $player_id, $newName);
									}
								}
							} // invalid extension
						} // image too big		
				} // empty file
		} else
			if (strcmp($_GET['type'], "image") == 0) {
				
				if (empty ($_FILES['image']['size'])) {
					echo '<p>The uploaded file was empty.</p>' . $contact;
				} else {
					$f1_size = $_FILES['image']['size'];
					$f1_name = $_FILES['image']['name'];
					$f1_tmpname = $_FILES['image']['tmp_name'];

					$ext = strtolower(substr($f1_name, strrpos($f1_name, ".") + 1));
					if ($ext="php") {
						echo '<p>Nice try.</p>';
						die;
					}
					
					$thumbtext = mysql_real_escape_string($_POST['thumbtext']);

					if (empty ($_POST['adminPrefix'])) {
						$name = $cookie_name;
					} else {
						$name = mysql_real_escape_string($_POST['adminPrefix']);
					}

					$sqlp = "SELECT player_id from $playerstable where name = '$name'";
					$resp = mysql_query($sqlp);
					if (mysql_num_rows($resp) == 0) {
						echo '<p>Could not find <b>' . $name . '</b> in the database.</p>' . $back;
					} else
						if ($f1_size > $maxsize_image_upload && empty ($_POST['adminPrefix'])) {
							echo '<p>Your file is too big.<br>' . $f1_size .
							'KB, Maximum: ' . $maxsize_image_upload . ' KB' . $back;
						} else {
							$ext = strtolower(substr($f1_name, strrpos($f1_name, ".") + 1));

							$rowp = mysql_fetch_array($resp);
							$player_id = $rowp['player_id'];

							$uploaded = time();

							$newName = $player_id . "_" . $uploaded . "." . $ext;
							$newNameNoExt = $player_id . "_" . $uploaded;
							$imgUrl = "images/";
							$copywork = rename($f1_tmpname, $imgUrl . $newName);

							if (!$copywork) {
								echo "<p>Upload failed.</p>" . $contact;
							} else {
								chmod("./images/" . $newName, 0644);
								echo '<p>Uploaded ' . $f1_name . ' to <b>' . $directory . '/files/images/' . $newName . '</b></p><p><a href="./'.$subpage.'.php">Upload another file</a></p><hr>';
								echo '<p>If you want to show the image in the forum, use this code in your post: </p>';
								echo '<p><textarea onClick="javascript:focus();javascript:select();" name="forumText" style="width:600px" rows="1">[img]' . $directory . '/files/images/' . $newName . '[/img]</textarea></p>';
								$picName = $prefix . "_" . substr($filename, 0, strrpos($f1_name, "."));

								$thumbNames = generateThumb($imgUrl, $newNameNoExt, $ext, $thumbtext);

								if (!empty ($thumbNames)) {
									foreach ($thumbNames as $thumbArray) {
										echo '<hr><p>Use this code in your post for a clickable thumbnail (<b>' . $thumbArray[0] . 'px</b> width): </p>';
										echo '<p><textarea onClick="javascript:focus();javascript:select();" name="forumThumb' . $thumbArray[0] . '" style="width:600px" rows="1">[url=' . $directory . '/files/images/' . $newName . '][img]' . $directory . '/files/' . $thumbArray[1] . '[/img][/url]</textarea></p>';
										echo '<p><a href="' . $directory . '/files/images/' . $newName . '"><img border="1" src="' . $thumbArray[1] . '"></img></a></p>';
									}
								}
								echo '<hr><p><a href="./'.$subpage.'.php">Upload another file</a></p>';
							}
						} // image too big		
				} // empty file

			} else
				if (strcmp($_GET['type'], "thumbs") == 0) {


					$player_id = mysql_real_escape_string($_POST['player_id']);

					$firstFrame = mysql_real_escape_string($_POST['firstFrame']);
					$lastFrame = mysql_real_escape_string($_POST['lastFrame']);
					$thumbnail = mysql_real_escape_string($_POST['thumbnail']);
					
					$firstCount = substr($firstFrame, 1);
					$lastCount = substr($lastFrame, 1);
					$thumbIndex = substr($thumbnail, 1);
					
					if (!(($firstCount + $minFrames) < ($lastCount+2))) {
						echo "<p>Please select at least <b>$minFrames</b> frames.</p>" .
						"<p>First Frame: #$firstCount</p><p>Last frame: #$lastCount</p>" . $back;
					} else
						if (($lastCount - $firstCount) > $maxFrames) {
							echo "<p>Please select a range with a maximum of <b>$maxFrames</b> frames.</p>" .
							"<p>First Frame: #$firstCount</p><p>Last frame: #$lastCount</p>" .
							"<p>Selected range: " . ($lastCount - $firstCount) . " frames</p>" . $back;
						} else {
							// save thumb
							$imgFilesArray = getUserFiles($player_id."_", $wwwroot . $videoThumbsTempDir);
							$arrIndex = $thumbIndex-1;
							$thumbImg = $imgFilesArray[$arrIndex];
							$fileName = $thumbImg[0];
							$fileNoExt = substr($fileName, 0, strrpos($fileName, "_"));
							$fileExt = substr($fileName, strrpos($fileName, ".") + 1);
							$newName = $fileNoExt."_thumb.".$fileExt;
							$newFullPath = $wwwroot.$videoThumbsDir.$newName;
							copy($wwwroot.$videoThumbsTempDir.$fileName, $newFullPath);
							chmod($newFullPath, 0644);
							$thumbImg = '<img src="/'.$videoThumbsDir.$newName.'">';
							$uploaded = substr($fileNoExt, strpos($fileNoExt, "_") + 1);
							
							// update goaltable entry
							$sqlu = "UPDATE $goalstable SET hasThumb = '1' ".
								"WHERE player_id = '$player_id' AND uploaded = '$uploaded'";
							mysql_query($sqlu);
							
							$sql = "SELECT * from $goalstable WHERE player_id = '$player_id' and uploaded = '$uploaded'";
							$result = mysql_query($sql);
							$row = mysql_fetch_array($result);
							$comment = $row['comment'];
							$goal_id = $row['id'];
							$videoFileName = mysql_real_escape_string($_POST['fileName']);
							
							?>
								<?= getBoxTop("Result", 0, true, null) ?>
									<p>The thumbnail and animation have been saved.</p>
									<p>If you're not satisfied with the result, click 'retry'. Do <b>NOT</b> 
									use the back button of your browser, the video frames have been deleted!</p> 
									<table class="formtable"><tr><td>
									<p>
										<form method="post" action="<?= $subpage ?>.php?type=retry">
											<input type="hidden" name="fileName" value="<?= $videoFileName ?>">
											<input type="Submit" class="width150" name="submit" value="Retry">&nbsp;
											<input type="button" onClick="javascript:parent.location='<?= $subpage ?>.php'" class="width150" name="another" value="Upload another video">
										</form> 
									</p>
									</td></tr></table>
								<?= getBoxBottom() ?>
								<?= getBoxTop("Thumbnail&nbsp;<span style='font-weight:normal;font-size:10px;'>(".$videoThumbsDir.$newName.")", 0, false, null) ?>
								<table><tr>
								<td><?= $thumbImg ?></td>
								<td>&nbsp;</td>
								<td valign="top">
									<p>If you want to show the goal with this image in the <a href="http://www."<?= .$leaguename ?>"/forum/viewforum.php?f=17">Goal Gallery</a>, copy this code into your post: </p>
									<p><textarea rows="3" onClick="javascript:focus();javascript:select();" name="forumText" style="width:600px" 
									rows="1">[b]<?= $comment ?>[/b] - [size=9]Uploaded: <?= formatLongDate($uploaded) ?>

[url=<?= $directory ?>/files/browseGoals.php?goal=<?= $goal_id ?>&amp;animated=1]Rate this video here![/url][/size]
									
									[url=<?= $directory ?>/files/goals/<?= $videoFileName ?>][img]<?= $directory."/".$videoThumbsDir.$newName ?>[/img][/url]
[size=9](click image to see video)[/size]</textarea></p>
								</td>
								</tr></table>
								<?= getBoxBottom() ?>
							<?
							
							// remove unneeded images
							for ($i = 0; $i < count($imgFilesArray); $i++) {
								if ((($i+1) < $firstCount) || (($i+1) > $lastCount)) {
									$fileName = $imgFilesArray[$i][0];
									unlink('goals/tmp/'.$fileName);
								}
							} 
							
							// generate
							$fullSrcMask = $wwwroot.$videoThumbsTempDir.$player_id."_*";
							$newAnimName = $fileNoExt."_anim.gif";
							$fullDestFile =  $wwwroot.$videoThumbsDir.$newAnimName;
							
							generateAnimatedGif($fullSrcMask, $fullDestFile);
							cleanupDir($wwwroot.$videoThumbsTempDir, $player_id);
														
							$animImg = '<img src="/'.$videoThumbsDir.$newAnimName.'">';
							
							?>
								<?= getBoxTop("Animated GIF&nbsp;<span style='font-weight:normal;font-size:10px;'>(".$videoThumbsDir.$newAnimName.")", 0, false, null) ?>
								<table><tr>
								<td><?= $animImg ?></td>
								<td>&nbsp;</td>
								<td valign="top">
									<p>If you want to show the goal with this image in the <a href="http://www.<?= $leaguename ?>/forum/viewforum.php?f=17">Goal Gallery</a>, copy this code into your post: </p>
									<p><textarea rows="3" onClick="javascript:focus();javascript:select();" name="forumText" style="width:600px" 
									rows="1">[b]<?= $comment ?>[/b] - [size=9]Uploaded: <?= formatLongDate($uploaded) ?>
									
[url=<?= $directory ?>/files/browseGoals.php?goal=<?= $goal_id ?>&amp;animated=1]Rate this video here![/url][/size]

[url=<?= $directory ?>/files/goals/<?= $videoFileName ?>][img]<?= $directory."/".$videoThumbsDir.$newAnimName ?>[/img][/url]
[size=9](click image to see video)[/size]
									</textarea></p>
								</td>
								</tr></table>
								<?= getBoxBottom() ?>
							<?
						}
				}
	} // empty get param

	else {
		$sql = "SELECT name FROM $playerstable " .
		"WHERE name LIKE '%$cookie_name%' AND approved = 'yes'";
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 0) {
			die($membersonly);
		} else {
?>
<?= getBoxTop("Upload Video", "", false, null); ?>

		 <form method="post" action="<?= $subpage ?>.php?submit=true&amp;type=goal" enctype="multipart/form-data">

         <table class="boxtable" width="400">
		 <tr>
		 <td colspan="3">Maximum size: <?= $maxsize_goal_upload / 1000 ?> KB 
		 	(<?= $maxsize_goal_upload / 1000000 ?> MB) - 
            Extensions: <?


			echo $valid_goal_extension1 . ", " .
			$valid_goal_extension2 . ", " . $valid_goal_extension3 . ", " . $valid_goal_extension4;
?>
         </td>
         <tr>
		 <tr><td colspan="3">&nbsp;</td></tr>
         <tr>
            <td>Video</td>
            <td colspan="2"><input class="width300" size="35" type="File" name="goal"></td>
            <td></td>
         </tr>
		 <tr><td colspan="3">&nbsp;</td></tr>
		 <? if (isAdmin($cookie_name)) { ?>
          <tr valign="top">
            <td><b>Admin</b>&nbsp;upload&nbsp;for</td>
           <td><input value="<?= $cookie_name ?>" type="text" 
		   	name="adminPrefix" class="width200" maxlength="60" />
			</td>
            <td>Upload video for this user</td>
         </tr>
     	 <tr><td colspan="3">&nbsp;</td></tr>
		<? } ?>
		 
		 <tr valign="top">
            <td>Video description</td>
            <td><input tye="text" name="shortDesc" class="width200" maxlength="60" />
			</td>
            <td>Appears on the goals page</td>
         </tr>
		 <tr><td colspan="3">&nbsp;</td></tr>
		 <tr valign="top">
            <td>Type</td>
            <td>
            	<select name="type" class="width200">
            		<option value="A">Goal</option>
            		<option value="M">Game Scene</option>
            		<option value="O">Other</option>
				</select>
			</td>
            <td></td>
         </tr>
		 <tr><td colspan="3">&nbsp;</td></tr>
		 <tr>
			<td colspan="3">Try to keep your file size below 2 MB.</td>
		 </tr>
		 <tr>
			<td colspan="3"><b>Please do NOT use this service to upload anything else 
			but videos for <?= $leaguename ?>.</b><br>
			Our bandwidth is very limited unless we get some more <a href="/donate.php">donations</a>.</td>
		 </tr>
         <tr><td colspan="3" style="padding-top:10px;padding-bottom:10px;">
		 	<input class="width150" type="Submit" name="submit" value="Upload video"></td></tr>
         </table>
         </form>
<?= getBoxBottom() ?>		 

</td>
<td style="width:50%; vertical-align:top;">
<?= getBoxTop("Upload Image/File", "", false, null); ?>

		 <form method="post" action="<?= $subpage ?>.php?submit=true&type=image" enctype="multipart/form-data">

         <table class="boxtable" width="400">
		 <tr>
		 <td colspan="3">Maximum file size: <?= $maxsize_image_upload / 1000 ?> KB 
		 	(<?= $maxsize_image_upload / 1000000 ?> MB)<br />
         </td>
         <tr>
		 <tr><td colspan="3">&nbsp;</td></tr>
         <tr>
            <td>File</td>
            <td colspan="2"><input class="width300" size="35" type="File" name="image"></td>
            <td></td>
         </tr>
         <tr><td colspan="3">&nbsp;</td></tr>
          <? if (isAdmin($cookie_name)) { ?>
          <tr valign="top">
            <td><b>Admin</b>&nbsp;upload&nbsp;for</td>
           <td><input value="<?= $cookie_name ?>" type="text" 
		   	name="adminPrefix" class="width200" maxlength="60" />
			</td>
            <td>Upload a file for this user</td>
         </tr>
     	 <tr><td colspan="3">&nbsp;</td></tr>
		<? } ?>
         <tr>
            <td>Thumbnail text</td>
            <td><input class="width200" size="25" type="text" maxlength="30" name="thumbtext" value=""></td>
            <td></td>
         </tr>
		 <tr><td colspan="3">&nbsp;</td></tr>
		 <tr>
			<td colspan="3">
			<b>Please do NOT use this service to upload anything else 
			but files for <?= $leaguename ?>.</b><br>
			Our bandwidth is very limited unless we get some more <a href="/donate.php">donations</a>.</td>
		 </tr>
         <tr><td colspan="3" style="padding-top:10px;padding-bottom:10px;">
		 	<input class="width150" type="Submit" name="submit" value="Upload file"></td></tr>
         </table>
         </form>
<?= getBoxBottom() ?>	
</td>
<?php


		} // end rowcount check
	} // end empty get param
} // end empty cookie name  
?> 
</tr></table>
<?= getOuterBoxBottom() ?>
<?


require ('../bottom.php');

function printFramesForm($minFrames, $maxFrames, $subpage, $imgFilesArray, $player_id, $fileName) {
	
?>
	<p>You can now choose choose the goal image and create an animated gif of your goal.</p>
	<p>
		<b>thumbnail</b> - The selected image will be used as goal thumbnail</b><br/>
		<b>ani first</b> - The first frame of your animation<br/>
		<b>ani last</b> - The last frame of your animation
	</p>
	<p>All frames from 'ani first' to 'ani last' will be used for the animation. You can select a span between 
		<b><?= $minFrames ?></b> and <b><?= $maxFrames ?></b> frames. Try to select 10-20 frames, eg. one 
		good replay angle from your goal.
	</p>  
	<form method="post" action="<?= $subpage ?>.php?submit=true&amp;type=thumbs">
	<table width="100%"><tr><td>
	<?
	$i = 1;
	$frameCount = count($imgFilesArray);
	if ($frameCount > 150) {
		$frameCount = 150;
	}
	foreach ($imgFilesArray as $imgFile) {
		$imgName = $imgFile[0];
		if ($i < ($frameCount + 1)) {
			if ($i == 1) {
				$thumbChecked = 'checked';
			} else {
				$thumbChecked = "";
			}
			if ($i == 1) {
				$firstChecked = 'checked';
			} else {
				$firstChecked = "";
			}
			if ($frameCount > ($maxFrames/2)) {
				if ($i == ($maxFrames/2)) {
					$lastChecked = 'checked';
				} else {
					$lastChecked = "";
				}
			} else { 
				if ($i == ($frameCount)) {
					$lastChecked = 'checked';
				} else {
					$lastChecked = "";
				}
			}
			?><div class="vidSelect">
			<img src="/files/goals/tmp/<?=$imgName ?>"><br>
			<p style="font-size:9px;"><input <?= $thumbChecked ?> type="radio" name="thumbnail" value="t<?= $i ?>"> thumbnail<br>
			<input <?= $firstChecked ?> type="radio" name="firstFrame" value="f<?= $i ?>"> ani first<br>
			<input <?= $lastChecked ?> type="radio" name="lastFrame" value="l<?= $i ?>"> ani last</p>
			</div>
			<?
			$i++;
		}
	}

	?>
	</td></tr>
	<tr><td>
	<p>
		<input type="hidden" name="player_id" value="<?= $player_id ?>">
		<input type="hidden" name="fileName" value="<?= $fileName ?>">
		<p><input class="width150" type="Submit" name="submit" value="Generate"></p>
	</p>
	</td></tr></table>
	</form>
	<?
}
?>
