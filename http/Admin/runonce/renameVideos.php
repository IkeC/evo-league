<?php

$page = "renameVideos";

require ('../../variables.php');
require ('../../variablesdb.php');
require_once ('../functions.php');
require_once ('./../../functions.php');
require_once ('./../../files/functions.php');
require ('../../top.php');
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Rename Videos", ""); ?>
<?


$goalsDir = "./../../files/goals/";
$thumbsDir = "./../../files/goals/thumbnails/";

$filesList = getAllFiles($goalsDir);

$filesListRev = array ();
foreach ($filesList as $fileArray) {
	$modified = $fileArray[1];
	$filesListRev[] = array (
		$fileArray[1],
		$fileArray[0]
	);
}

$fullList = $filesListRev;

asort($fullList);
$i = 0;

foreach ($fullList as $fileArray) {
	// DEBUG
	if ($i == 0) {
		
		echo "<p>" . $fileArray[0] . " - " . $fileArray[1] . "</p>";
		$fileName = $fileArray[1];
		$modified = $fileArray[0];
		if ($fileName != "." && $fileName != "..") {
			$index = strpos($fileName, "_");
			$name1 = substr($fileName, 0, $index);
			if (strstr($fileName, "the_gunner") > -1) {
				$name1 = "the-gunner";
			} else
				if (strstr($fileName, "xXEvo-BorgXx") > -1) {
					$name1 = "Triple-G";
				} else
					if (strstr($fileName, "xXEvo_BorgXx") > -1) {
						$name1 = "Triple-G";
					} else
						if (strstr($fileName, "Team-PsyKick") > -1) {
							$name1 = "Triple-G";
						} else
							if (strstr($fileName, "stikone") > -1) {
								$name1 = "Sentence";
							} else
								if (strstr($fileName, "STIKKONE") > -1) {
									$name1 = "Sentence";
								} else
									if (strstr($fileName, "iSSCHaMP") > -1) {
										$name1 = "Der-Meister";
									} else
										if (strstr($fileName, "Stolarz+PL") > -1) {
											$name1 = "StolarzPL";
										} else
											if (strstr($fileName, "Christav") > -1) {
												$name1 = "AChris";
											} else
												if (strstr($fileName, "Barcafan") > -1) {
													$name1 = "[Hun]Barcafan";
												} else
													if (strstr($fileName, "Bad_Religion") > -1) {
														$name1 = "Bad Religion";
													} else
														if (strstr($fileName, "Triple_G") > -1) {
															$name1 = "Triple-G";
														} else
															if (strstr($fileName, "iSSChaMP") > -1) {
																$name1 = "Der-Meister";
															}

			$sql = "SELECT player_id from $playerstable where name = '$name1'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) == 1) {
				// DEBUG
				// $i++;
				$row = mysql_fetch_array($result);
				$fileDesc = substr($fileName, strrpos($fileName, "."));
				$fileDesc = str_replace("+", " ", $fileDesc);
				$fileDesc = str_replace("_", " ", $fileDesc);
				$fileDesc = substr($fileDesc, 0, strlen($fileDesc) - 4);
				$extension = strtolower(substr($fileName, strrpos($fileName, ".") + 1));
				$id = $row[0];
				$newName = $id . "_" . $modified . "." . $extension;
				echo "<p>$name1 ID: " . $id . " Filedesc: <b>$fileDesc</b> NewName: $newName</p>";

				// update forum posts
				$sqlf = "SELECT post_id, post_text from phpbb_posts_text where post_text like '%$fileName%'";
				$resultf = mysql_query($sqlf);
				while ($rowf = mysql_fetch_array($resultf)) {
					$postId = $rowf[0];
					$postText = $rowf[1];
					$postTextNew = str_replace($fileName, $newName, $postText);
					$postTextNew = addslashes($postTextNew);
					$sqlnew = "UPDATE phpbb_posts_text SET post_text = '$postTextNew' where post_id = $postId";
					// echo "<p>$sqlnew</p>";
					$resnew = mysql_query($sqlnew);
					echo "<p>Updating post - Result: <b>$resnew</b></p>";
				}
				
				// rename file
				$src = $goalsDir.$fileName;
				$dest = $goalsDir.$newName;
				echo "<p>Trying to rename $src to $dest... ";
				$renameWork = rename($src, $dest);
				chmod($dest, 0644);
				echo "<b>$renameWork</b></p>";
					
				// insert goal table entry
				$sqlg = "INSERT INTO $goalstable (player_id, uploaded, extension, comment) " .
				"VALUES ('$id', '$modified', '$extension', '$fileDesc')";
				$resg = mysql_query($sqlg);
				echo "<p>Inserting in goals table - Result: <b>$resg</b></p>";

			} else {
				echo "<p><b>NO ID for $name1</b></p>";
			}
		}
	}
}
?>
<?= getOuterBoxBottom() ?>
<?


require ('../../bottom.php');
?>