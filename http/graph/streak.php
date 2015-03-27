<?php
require ('../variables.php');
require ('../variablesdb.php');
require ('./graph.php');
require('./../functions.php');
	
	$winDaysGet = mysql_real_escape_string($_GET['winDays']);
	if (!empty($winDaysGet)) {
		$winStreakArray = unserialize(stripcslashes($winDaysGet));
		$winDays = array();
		foreach ($winStreakArray as $winItem => $item) { 
					
			$winStart = $item[1];
			$winEnd = $item[2];
			$streak = $item[0];
			while ($winStart < $winEnd) {
				$winDays[formatDateDay($winStart)] = 0;
				$winStart = $winStart + 60*60*24;
			}
			$winDays[formatDateDay($winEnd)] = $streak;
		}
	} else {
		$winDays = array();
	}

	$loseDaysGet = mysql_real_escape_string($_GET['loseDays']);
	if (!empty($loseDaysGet)) {
		$loseStreakArray = unserialize(stripcslashes($loseDaysGet));
		$loseDays = array();
		foreach ($loseStreakArray as $loseItem => $item) { 
			$loseStart = $item[1];
			$loseEnd = $item[2];
			$streak = $item[0];
			while ($loseStart < $loseEnd) {
				$loseDays[formatDateDay($loseStart)] = 0;
				$loseStart = $loseStart + 60*60*24;
			}
			$loseDays[formatDateDay($loseEnd)] = $streak;
		}
	} else {
		$loseDays = array();
	}

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
	
	$sql = "SELECT * FROM $gamestable WHERE (winner = '$name' OR loser = '$name')  ".
		" ORDER by game_id ASC";
	
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	
	$cur = 0;
	$pointer = 0;
	$streak = 0;
	
	while ($num >= $cur) {
		$row = mysql_fetch_array($result);
		$dateday = $row["dateday"];
		
		$date_arr[$pointer] = $dateday;
	
		if ($date_arr[$pointer] == $date_arr[$pointer -1]) {
			$winner = $row["winner"];
			if (strcmp(strtolower($winner), strtolower($name)) == 0) {
				$streak++;
			}
			else {
				$streak--;
			}
		}
	
		if ($date_arr[$pointer] != $date_arr[$pointer -1] || $num < $cur+1) {			
			$fullStreak = 0;
			if (array_key_exists($dateday, $winDays)) {
				$fullStreak = $winDays[$dateday]; 
				$color = $graph_green;	
			} else if (array_key_exists($dateday, $loseDays)) {
				$fullStreak = $loseDays[$dateday];
				$color = $graph_red;	
			} else {
				$color = $graph_grey;
			}
			
			$out[$pointer] = array($streak, $color, $fullStreak);
			$pointer ++;
			$streak = 0;
		}
		
		$cur ++;
	}
	
	$imageurl = "grid.png";
	$img = getGraph($out, $height, $width, $imageurl);
	Header("Content-type: image/png");
	$exp = GMDate("D, d M Y H:i:s", time() + 60*60*2);
	Header("Expires: $exp GMT");
	// Output Image data
	imagepng($img);

?>
