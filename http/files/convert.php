<?php


// this page allows users to upload files

$page = "convert";
require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('./functions.php');
require ('../top.php');
?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Convert goals", ""); ?>
<table width="100%">
<tr><td width="50%">
<?


$submit = mysql_real_escape_string($_POST['submit']);
$next = '<p><a href="convert.php">next</a></p>';
$retry = '<p><a href="convert.php">retry</a></p>';

if (strcmp($_GET['type'], "retry") == 0) {
	$fileName = mysql_real_escape_string($_GET['delete']);
	$thumb = 'goals/thumbnails/'.$fileName."_thumb.png";
	unlink($thumb);
	echo "<p>Deleted $thumb</p>";
	$ani = 'goals/thumbnails/'.$fileName."_anim.gif";
	unlink($ani);
	echo "<p>Deleted $ani</p>";
	echo $retry;
} else if (strcmp($_GET['type'], "update") == 0) {
	$goal_id = mysql_real_escape_string($_GET['goal_id']);
	$sql = "update $goalstable SET hasThumb='1' where id = '$goal_id'";
	$result = mysql_query($sql);
	echo "<p>Updating goal as revised - Result: $result</p>";
	echo $next;
} else if (empty ($submit)) {
	$sql = "SELECT * from $goalstable where hasThumb = '' ORDER BY ID DESC LIMIT 0,1";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);

	$id = $row['id'];
	$player_id = $row['player_id'];
	$uploaded = $row['uploaded'];
	$extension = $row['extension'];

	$filename = $player_id . "_" . $uploaded . "." . $extension;
	// $file = readfile("goals/" . $filename);
	
	$sql = "SELECT count(*) from $goalstable where hasThumb != ''";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);

	$sql2 = "SELECT count(*) from $goalstable";
	$result2 = mysql_query($sql2);
	$row2 = mysql_fetch_array($result2);
	
	if (empty($id)) {
		echo "<p>No goals to convert - Completed: ".$row[0]." of ".$row2[0]."</p>";
	} else {
			echo "<p>ID: #$id - Name: $filename - Completed: ".$row[0]." of ".$row2[0]."</p>";
			
			$genImgRes = generateVideoThumbImages($filename, $player_id);
			$imgFilesArray = getUserFiles($player_id . "_", $wwwroot . $videoThumbsTempDir);
			if ($imgFilesArray == null || (count($imgFilesArray) == 0)) {
				echo "<p>Video frame grabbing failed. The used Codec is probably unsupported.</p>";
				$sql = "update $goalstable SET hasThumb='0' where id = '$id'";
				$result = mysql_query($sql);
				echo "<p>Updating goal as revised - Result: $result</p>";
				echo $next;
			} else {
		?>
				<form method="post" action="convert.php?submit=true&amp;type=thumbs">
				<table><tr><td>
				<?
		
				$i = 1;
				$frameCount = count($imgFilesArray);
				if ($frameCount > 150) {
					$frameCount = 150;
				}
				foreach ($imgFilesArray as $imgFile) {
					if ($i < ($frameCount+1)) {
						$imgName = $imgFile[0];
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
						if ($i == $frameCount-1) {
							$lastChecked = 'checked';
						} else {
							$lastChecked = "";
						}
			?>
				<div class="vidSelect">
				<img src="/files/goals/tmp/<?=$imgName ?>"><br>
				<p style="font-size:9px;"><input <?= $thumbChecked ?> type="radio" name="thumbnail" value="t<?= $i ?>"> thumbnail<br>
				<input <?= $firstChecked ?> type="radio" name="firstFrame" value="f<?= $i ?>"> ani first<br>
				<input <?= $lastChecked ?> type="radio" name="lastFrame" value="l<?= $i ?>"> ani last</p>
				</div>
			<?
			
						$i++;
					}
				} // for each image
		?>
			<p><input type="hidden" name="player_id" value="<?= $player_id ?>">
			<input type="hidden" name="goal_id" value="<?= $id ?>">
			<input class="width100" type="Submit" name="submit" value="Generate"></p>
			</td></tr></table>
			</form>
		<?
			}
		}
} else {

if (strcmp($_GET['type'], "thumbs") == 0) {

			$minFrames = 5;
			$maxFrames = 40;

			$player_id = mysql_real_escape_string($_POST['player_id']);
			$goal_id = mysql_real_escape_string($_POST['goal_id']);
			$firstFrame = mysql_real_escape_string($_POST['firstFrame']);
			$lastFrame = mysql_real_escape_string($_POST['lastFrame']);
			$thumbnail = mysql_real_escape_string($_POST['thumbnail']);
			
			$firstCount = substr($firstFrame, 1);
			$lastCount = substr($lastFrame, 1);
			$thumbIndex = substr($thumbnail, 1);
			
			// echo "<p>firstCount: $firstCount | lastCount: $lastCount | thumbIndex: $thumbIndex</p>";
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
					?>
						<?= getBoxTop("Thumbnail&nbsp;<span style='font-weight:normal;font-size:10px;'>(".$videoThumbsDir.$newName.")", 0, false, null) ?>
						<?= $thumbImg ?>
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
						<?= $animImg ?>
						<?= getBoxBottom() ?>
						<?= getBoxTop("Save", 0, false, null) ?>
						
						<p>If you're satisfied with the result, click 'Save', else click 'Retry'.<p>
						<p><a href="convert.php?type=update&amp;goal_id=<?= $goal_id ?>">Save</a> | <a href="convert.php?type=retry&amp;delete=<?= $fileNoExt ?>">Retry</a></p>
						<?= getBoxBottom() ?>
					<?
				} // frames ok
		}
}


?>
</td>
</tr></table>
<?= getOuterBoxBottom() ?>
<?


	require ('../bottom.php');
?>
