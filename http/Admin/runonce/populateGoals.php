<?php
$page = "populateGoals";

require ('../../variables.php');
require ('../../variablesdb.php');
require_once ('../functions.php');
require_once ('./../../functions.php');
require_once ('./../../files/functions.php');
require ('../../top.php');
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Populate Goals", ""); ?>
<?


$goalsDir = "./../../files/goals/";
$thumbsDir = "./../../files/goals/thumbnails/";

$filesList = getAllFiles($goalsDir);

$filesListRev = array ();

foreach ($filesList as $fileArray) {
	if (!is_dir($fileArray[0])) {
		$filesListRev[] = array (
			$fileArray[1],
			$fileArray[0]
		);
	}
}

$fullList = $filesListRev;

asort($fullList);
$i = 0;

foreach ($fullList as $fileArray) {
	// DEBUG
	$fileName = $fileArray[1];
	$modified = $fileArray[0];
	echo "$modified - $fileName<br>";

	// select forum post

	$sqlf = "SELECT post_id, post_text from phpbb_posts_text where post_text like '%$fileName%'";
	$resultf = mysql_query($sqlf);
	$num = mysql_num_rows($resultf);
	while ($rowf = mysql_fetch_array($resultf)) {
		$postId = $rowf[0];
		$postText = $rowf[1];
		$startIndex = strpos($postText, $fileName);
		$endIndex = strpos($postText, "[", $startIndex);
		if ($startIndex < $endIndex) {
			$startIndex = $startIndex + strlen($fileName) + 1;
			$endIndex = $endIndex - $startIndex - 4;
			$text = trim(substr($postText, $startIndex, $endIndex));
			if (strpos($text, "/") > 0 || empty($text)) {
				continue;
			}
			break;
		} 
	}
	if (strpos($text, "//") > 0 || empty($text) || $num == 0) {
		$text = substr($fileName, 0, strlen($fileName)-4);
	}
	echo "<P>text: <b>[$text]</b></p>";
	$indexUnderscore = strpos($fileName, "_");
	$indexDot = strpos($fileName, ".");
	$player_id = substr($fileName, 0, $indexUnderscore);
	$modified = substr($fileName, $indexUnderscore+1, $indexDot-$indexUnderscore-1);
	$extension = substr($fileName, $indexDot+1);
	$text = addslashes($text);
	$sqli = "INSERT INTO $goalstable (player_id, uploaded, extension, comment) ".
		"values ('$player_id', '$modified', '$extension', '$text')";
	$resi = mysql_query($sqli);
	echo "<p>inserted [$sqli] - Result: <b>$resi</b></p>";
}
?>
<?= getOuterBoxBottom() ?>
<?


require ('../../bottom.php');
?>