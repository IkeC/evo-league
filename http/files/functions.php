<?php
function getUserFiles($prefix, $directory) {
	$filesList = array ();
	if ($handle = opendir($directory)) {
		/* This is the correct way to loop over the directory. */
		while (false !== ($file = readdir($handle))) {
			$isUserFile = (strstr($file, $prefix) == $file);
			if ($isUserFile) {
				$lastModified = filemtime($directory . $file);
				
				$filesList[$file] = $lastModified;
			}
		}
		closedir($handle);
	}
	
	ksort($filesList);
	
	$sortedList = array();
	while ($listElement = current($filesList)) {
		$sortedList[] = array(key($filesList), $listElement);
		next($filesList);
	}
	
	return $sortedList;
}

function getAllFiles($directory) {
	$filesList = array ();
	if ($handle = opendir($directory)) {
		/* This is the correct way to loop over the directory. */
		while (false !== ($file = readdir($handle))) {
			$lastModified = filemtime($directory . $file);
			if ($file != "." && $file != ".." && $file != "thumbnails" && $file != "tmp") {
				$filesList[] = array (
					$file,
					$lastModified
				);
			}
		}
		closedir($handle);
	}
	asort($filesList);
	return $filesList;
}

function getThumbnailWidths() {
	return array (
		200,
		300,
		400
	);
}

function generateThumb($imgDir, $picName, $extension, $thumbtext) {
	require_once ('../thumb/phpthumb.class.php');
	require ('../variables.php');
	$thumbNames = array ();

	$imgUrl = $imgDir . $picName . "." . $extension;
	$thumbDir = "thumbnails/";
	// echo "<p>$imgUrl</p>";
	// create 3 sizes of thumbnail
	$thumbnail_widths = getThumbnailWidths();
	foreach ($thumbnail_widths as $thumbnail_width) {

		// Note: If you want to loop through and create multiple
		//   thumbnails from different image sources, you should
		//   create and dispose an instance of phpThumb() each time
		//   through the loop and not reuse the object.
		$phpThumb = new phpThumb();

		// set data
		$phpThumb->setSourceFilename($imgUrl);
		// or $phpThumb->setSourceData($binary_image_data);
		// or $phpThumb->setSourceImageResource($gd_image_resource);
		// $gd_img = imagecreatefrom($imgPath.$picturename);
		// $phpThumb->setSourceImageResource($gd_img);
		// set parameters (see "URL Parameters" in phpthumb.readme.txt)
		$phpThumb->setParameter('w', $thumbnail_width);
		//$phpThumb->setParameter('h', 100);

		$fParam = 'wmt|Click to open (^Xx^Y)|1|B|FFFFFF||100|0||000000|100|x';
		if (!empty ($thumbtext)) {
			$fParamTop = 'wmt| ' . $thumbtext . ' |2|T|000000||100|0||CCCCCC|100|x';
			$phpThumb->setParameter('fltr', $fParamTop);
		}

		$phpThumb->setParameter('fltr', $fParam);

		$phpThumb->setParameter('fltr', 'bord|1');
		// set options (see phpThumb.config.php)
		// here you must preface each option with "config_"
		$phpThumb->setParameter('config_output_format', 'png');
		// $phpThumb->setParameter('config_imagemagick_path', '/usr/local/bin/convert');
		// $phpThumb->setParameter('config_imagemagick_path', '.');
		//$phpThumb->setParameter('config_allow_src_above_docroot', true); // needed if you're working outside DOCUMENT_ROOT, in a temp dir for example

		// generate & output thumbnail
		$output_filename = $imgDir . $thumbDir . $picName . '_' . $thumbnail_width . '.' . $phpThumb->config_output_format;
		if ($phpThumb->GenerateThumbnail()) { // this line is VERY important, do not remove it!
			if ($output_filename) {
				if ($capture_raw_data && $phpThumb->RenderOutput()) {
					// RenderOutput renders the thumbnail data to $phpThumb->outputImageData, not to a file or the browser
					// mysql_query("INSERT INTO `table` (`thumbnail`) VALUES ('".mysql_escape_string($phpThumb->outputImageData)."') WHERE (`id` = '".$id."'");
				}
				elseif ($phpThumb->RenderToFile($output_filename)) {
					$thumbNames[] = array (
						$thumbnail_width,
						$output_filename
					);

				} else {
					// do something with debug/error messages
					// echo 'Failed (size=' . $thumbnail_width . '):<pre>' . implode("\n\n", $phpThumb->debugmessages) . '</pre>';
				}
			} else {
				$phpThumb->OutputThumbnail();
			}
		} else {
			// do something with debug/error messages
						//echo '<p>Thumbnail generation failed (size='.$thumbnail_width.').</p>';
						//echo '<div style="background-color:#FFEEDD; font-weight: bold; padding: 10px;">'.$phpThumb->fatalerror.'</div>';
						//echo '<form><textarea rows="10" cols="60" wrap="off">'.htmlentities(implode("\n* ", $phpThumb->debugmessages)).'</textarea></form><hr>';
		}

		// remember to unset the object each time through the loop
		unset ($phpThumb);
	}

	return $thumbNames;
}

function generateVideoThumbImages($fileName, $player_id) {
	require ('../variables.php');
	$fileNoSuffix = substr($fileName, 0, strrpos($fileName, "."));
	$mask = "%03d";
	$type = "png";
	$imgPath = $wwwroot."files/goals/tmp/";
	
	cleanupDir($imgPath, $player_id);

		// echo "<p>";
		foreach ($result as $line) {
			// echo "[".$line."]<br>";
		}
		// echo "</p>";

	$command = "nice /usr/bin/ffmpeg -i \"" . $wwwroot . "files/goals/" . $fileName . "\" -vcodec " . $type . " -y " .
	"-r 3 -s sqcif " . $imgPath . $fileNoSuffix . "_" . $mask . "." . $type;

	 // echo "<p>command = [".$command."]</p>";

	$result = array ();
	$res = exec($command, $result);
	// echo "<p>res=[" + $res + "]</p>";
	return $result;
}

function generateAnimatedGif($fullSrcMask, $fullDestFileNoExt) {
	$command = "nice /usr/bin/gm convert -delay 20 " . $fullSrcMask . " " . $fullDestFileNoExt;

	// echo "<p>command = [" . $command . "]</p>";

	$result = array ();
	$res = exec($command, $result);

	// echo "<p>";
	foreach ($result as $line) {
		// echo "[" . $line . "]<br>";
	}
	// echo "</p>";
	return $result;
}

function cleanupDir($path, $player_id) {
	$command = "rm " . $path . $player_id . "_*";
		// echo "<p>command = [".$command."]</p>";
	$result = array ();
	$res = exec($command, $result);
}


?>
