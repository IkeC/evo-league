<?php
require ('../variables.php');
require ('../variablesdb.php');
require ('./graph.php');

if (!empty($_GET['name'])) {
	$name = mysql_real_escape_string($_GET['name']);
}
else {
	$name = "";
}

if (!empty($_GET['height'])) {
	$height = mysql_real_escape_string($_GET['height']);
} else {
	$height = 150;
}

if (!empty($_GET['width'])) {
	$width = mysql_real_escape_string($_GET['width']);
} else {
	$width = 390;
}

if (!empty($_GET['days'])) {
	$days = mysql_real_escape_string($_GET['days']);
	$timeSpan = 60*60*24 * $days;
	$sql = "SELECT * FROM $gamestable WHERE (winner = '$name' OR winner2 = '$name' OR loser = '$name' OR loser2 = '$name')  ".
		"AND (UNIX_TIMESTAMP() - date < $timeSpan) ORDER by game_id ASC";
}
else {
	$showSeason = mysql_real_escape_string($_GET['season']);
	$sql = "SELECT * FROM $gamestable WHERE (winner = '$name' OR winner2 = '$name' OR loser = '$name' OR loser2 = '$name')  ".
		"AND season = $showSeason ORDER by game_id ASC";
	
}

$result = mysql_query($sql);
$num = mysql_num_rows($result);

$cur = 0;
$pointer = 0;
$streak = 0;

while ($num >= $cur) {
	$row = mysql_fetch_array($result);
	$date = $row["dateday"];
	$date_arr[$pointer] = $date;

	if ($date_arr[$pointer] == $date_arr[$pointer -1]) {
		$winner = $row["winner"];
		$winner2 = $row["winner2"];
		$isDraw = $row["isDraw"];
		if ($isDraw == 0) {
			if ((strcmp(strtolower($winner), strtolower($name)) == 0) || (strcmp(strtolower($winner2), strtolower($name)) == 0)) {
				$streak++;
			}
			else {
				$streak--;
			}
		}
	}

	if ($date_arr[$pointer] != $date_arr[$pointer -1] || $num < $cur+1) {			
		$out[$pointer] = array($streak, $graph_black, 0);
		$pointer ++;
		$streak = 0;
	}

	$cur ++;
}
$imageurl = "grid.png";
$img = getGraph($out, $height, $width, $imageurl);
// Image Header and CACHE (1 hour)
Header("Content-type: image/png");
$exp = GMDate("D, d M Y H:i:s", time() + 60*60*2);
Header("Expires: $exp GMT");
// Output Image data
imagepng($img);
?>
