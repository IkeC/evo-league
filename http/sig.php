<?php

// a page to generate a dynamic image showing a player's league data for a forum signature.
// the image can be referenced in the sig with sig.php?name=playername
// text is written on the sig_template.png image and delivered to the calling browser.
// may or may not work with your server config and forum

$page = "sig";
require('./variables.php');
require('./variablesdb.php');
require('./functions.php');
header("Content-type: image/png");
$name = mysql_real_escape_string($_GET['name']);
$sql = "SELECT * from $playerstable where name = '$name'";
//echo $sql;
$result = mysql_query($sql);
$row = mysql_fetch_array($result);

$version = $row["defaultversion"];
//echo $version;
$wins = $row[getWinsFieldForVersion($version)];
$losses = $row[getLossesFieldForVersion($version)];
$draws = $row['draws'];
$points = $row[getPointsFieldForVersion($version)];

$imageurl = "/var/www/yoursite/http/gfx/sig_template.png";

$font_line1 = 3;
$font_line1_right = 1;
$font_line2 = 2;

$offset_left = 13;
$offset_left_line1_right = 340;
$offset_line1_right = 10;
$offset_line1 = 8;
$offset_line2 = 26;

$string_line1 = $leaguename.".com statistics for ".$name;
$string_line1_right = "season ".$season;
$string_line2 = "rank: ".getPlayerPosition($name, $version)."  points: ".$points."  Wins: ". $wins ."  Draws: ".$draws."  Defeats: ".$losses; 

$name = mysql_real_escape_string($_GET['name']);
$im = imagecreatefrompng($imageurl);

$fontcolor_normal = imagecolorallocate($im, 0, 0, 0);
// $px = (imagesx($im) - 7.5 * strlen($string)) / 2;


imagestring($im, $font_line1, $offset_left, $offset_line1, $string_line1, $fontcolor_normal);
imagestring($im, $font_line1_right, $offset_left_line1_right, $offset_line1_right, $string_line1_right, $fontcolor_normal);
imagestring($im, $font_line2, $offset_left, $offset_line2, $string_line2, $fontcolor_normal);

imagepng($im);
imagedestroy($im);

?>
