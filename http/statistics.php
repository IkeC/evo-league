<?php

// the statistics page showing all kinds of fancy statistics that can be selected through a drop down menu

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "games";
$subpage = "statistics";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('top.php');
?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Statistics", "") ?>

<table width="70%"><tr><td>
<table class="formtable" style="margin-bottom:10px;"><tr><td nowrap>
<form method="post" action="<?php echo"$directory"?>/statistics.php">
<select name="stat" class="width200">

<?php

$selected = " selected='selected'";
$separator = "---------------------------------------";
$offense = false;
$defense = false;
$forum = false;
$active = false;
$inactivity = false;
$nationalities = false;
$forum = false;
$gamesPerMonth = false;
$favTeams = false;
$gameTeams = false;
$fairness = false;
$gamesPerVersion = false;

if(! empty($_POST['stat'])) {
	$stat = mysql_real_escape_string($_POST['stat']);
} else {
	$stat = "Ladder Games (per Month)";
}
?>


<? $tmp_name = "Ladder Games (per Month)" ?>
<option<? if ($stat == $tmp_name) { echo $selected; $gamesPerMonth = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name = "Ladder Games (per Version)" ?>
<option<? if ($stat == $tmp_name) { echo $selected; $gamesPerVersion = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name = "Ladder Games (per Player)" ?>
<option<? if ($stat == $tmp_name) { echo $selected; } ?>><? echo $tmp_name ?></option>

<? $tmp_name = "Best Offense" ?>
<option<? if ($stat == $tmp_name) { echo $selected; $offense = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name = "Best Defense" ?>
<option<? if ($stat == $tmp_name) { echo $selected; $defense = true;} ?>><? echo $tmp_name ?></option>

<option><?= $separator ?></option>

<? $tmp_name="Total Wins"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; } ?>><? echo $tmp_name ?></option>

<? $tmp_name="Total Losses"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; } ?>><? echo $tmp_name ?></option>

<? $tmp_name="Best Streak (current)"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; } ?>><? echo $tmp_name ?></option>

<? $tmp_name="Worst Streak (current)"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; } ?>><? echo $tmp_name ?></option>

<option><?= $separator ?></option>

<? $tmp_name = "Fairness" ?>
<option<? if ($stat == $tmp_name) { echo $selected; $fairness = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name="ELOrating"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; } ?>><? echo $tmp_name ?></option>

<? $tmp_name ="Activity Rating" ?>
<option<? if ($stat == $tmp_name) { echo $selected; $active = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name ="Inactivity Points" ?>
<option<? if ($stat == $tmp_name) { echo $selected; $inactivity = true;} ?>><? echo $tmp_name ?></option>

<option><?= $separator ?></option>

<? $tmp_name = "Favorite Teams (Profile)"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; $favTeams = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name = "Favorite Teams (Games)"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; $gameTeams = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name = "Nationalities"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; $nationalities = true;} ?>><? echo $tmp_name ?></option>

<? $tmp_name = "Forums"; ?>
<option<? if ($stat == $tmp_name) { echo $selected; $forum = true;} ?>><? echo $tmp_name ?></option>


</select>&nbsp;&nbsp;<input class="width150" type="Submit" name="submit" value="show" /></form>
</td></tr></table>

<?

$pos = strpos($stat, ' ');
$col3_name = substr($stat, $pos);

if ($stat == "Ladder Games (per Player)") {
   $col3_name = "Games";
   $sortby = "totalgames DESC";
}
else if ($stat == "Total Wins") {
   $sortby = "Totalwins DESC";
}
else if ($stat == "Total Losses") {
   $sortby = "Totallosses DESC";
}
else if ($stat == "Best Streak (current)") {
   $sortby = "Streakwins DESC";
}
else if ($stat == "Worst Streak (current)") {
   $sortby = "Streaklosses DESC";
}
else if ($stat == "ELOrating") {
	$sortby = "rating DESC, totalgames DESC";
	$col3_name = "Rating";
} else {
   $stat = "Fairness";
   $fairness = true;
}

if ($offense || $defense) {
	$sql = "SELECT name, totalgames, nationality FROM $playerstable " .
	 		"WHERE approved='yes' and totalgames > 50";
	$col2_name = "Player";
	$col3_name = "Goals&nbsp;average&nbsp;&nbsp;<span class='grey-small'><b>(Games)</b></span>";
}
else if ($forum) {
   	$sql = "select count(*) as counter, forum " .
   			"from $playerstable " .
   			"WHERE approved = 'yes' " .
   			"group by forum " .
   			"order by counter desc";
	$col2_name = "Forum";
	$col3_name = "Users";
} 
else if ($active) {
	$sql = "select name, nationality " .
			"from $playerstable " .
			"WHERE approved = 'yes' " .
			"ORDER BY player_id DESC ";
	$col2_name = "Player";
	$col3_name = "Activity (%)";
}
else if ($nationalities) {
	$sql ="SELECT nationality, count(*) as counter " .
			"FROM $playerstable " .
			"WHERE approved = 'yes' " .
			"group by nationality " .
			"order by counter desc";
	$col2_name = "Nationality";
	$col3_name = "Players";
}
else if($inactivity) {
	$sql = "SELECT name, deductedPoints, nationality " .
		    "FROM  $playerstable " .
			"WHERE deductedPoints >0 AND approved =  'yes' " .
			"ORDER BY deductedPoints DESC";
	$col2_name = "Player";
	$col3_name = "Points deducted";
}
else if($gamesPerVersion) {
	$sql = "SELECT version, color, name FROM $versionstable ORDER BY ID ASC"; 
	$col2_name = "Version";
	$col3_name = "Games";
}
else if($gamesPerMonth) {
	$sql = "SELECT 0";
  $col1_name = "Month";
  $col2_name = "Games";
	$col3_name = "Total";
}
else if ($favTeams) {
	$sql = "SELECT favteam1 AS team, count( favteam1 ) AS cnt ".
		"FROM $playerstable ".
		"WHERE favteam1 > 0 ".
		"GROUP BY favteam1 ".
		"UNION ".
		"SELECT favteam2 AS team, count( favteam2 ) AS cnt ".
		"FROM $playerstable ".
		"WHERE favteam2 > 0 ".
		"GROUP BY favteam2 ".
		"ORDER BY cnt DESC";
	$col2_name = "Team";
	$col3_name = "Times used";
}
else if ($gameTeams) {
	$sql = "SELECT winnerteam AS team, count( winnerteam ) AS cnt ".
		"FROM $gamestable ".
		"WHERE winnerteam >0 AND deleted = 'no' ".
		"GROUP BY winnerteam ".
		"UNION ".
		"SELECT loserteam AS team, count( loserteam ) AS cnt ".
		"FROM $gamestable ".
		"WHERE loserteam >0 AND deleted = 'no' ".
		"GROUP BY loserteam ".
		"ORDER BY cnt DESC";
	$col2_name = "Team";
	$col3_name = "Times used";
}

// keep fairness always as last!
else if ($fairness) {
	$sql = "select loser, avg(fairness) as avg, count(*) as votes from $gamestable ".
		"where deleted = 'no' and fairness != '' and isDraw=0 group by loser order by avg desc, votes desc";
	$col2_name = "Player";
	$col3_name = "Fairness&nbsp;&nbsp;<span class='grey-small'><b>(Votes)</b></span>";
}
else {
		$approved = "";
		if ($stat == "ELOrating") {
			$limit = "";
		} else {
			$limit = "LIMIT 0, 1000";
		}	
	
	$sql = "SELECT * FROM $playerstable " .
			"WHERE totalgames > 0 " .
			"$approved " .
			"ORDER BY $sortby $limit";
	$col2_name = "Player";
}

$result = mysql_query($sql);
$num = mysql_num_rows($result);
$cur = 1;

$columnsArray = array('Position', $col2_name, $col3_name);
?>

<?= getRankBoxTop("", $columnsArray); ?>

<?
if ($offense) {
	$entriesArray = array(); 
	
	while ($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$totalgames = $row['totalgames'];
		
		// goals as winner
		$games_sql = "SELECT sum(winnerresult) as goals_for ".
			"FROM $gamestable " .
			"WHERE winner = '$name' AND deleted = 'no'";
	
		$games_result = mysql_query($games_sql);
		$games_row = mysql_fetch_array($games_result);
		$goals_for = $games_row['goals_for'];
		
		// goals as loser
		$games_sql = "SELECT sum(loserresult) as goals_for ".
			"FROM $gamestable " .
			"WHERE loser = '$name' AND deleted = 'no'";
	
		$games_result = mysql_query($games_sql);
		$games_row = mysql_fetch_array($games_result);
		$goals_for += $games_row['goals_for'];
		
		
		// aggregate games
		$agg_sql = "SELECT count(*) as aggregate ".
			"FROM $gamestable " .
			"WHERE (loser = '$name' or winner = '$name') AND deleted = 'no' AND host = 'A'";
		$agg_result = mysql_query($agg_sql);
		$agg_row = mysql_fetch_array($agg_result);
		$aggregateCount = $agg_row['aggregate'];
		
		$avg_goals_for = sprintf("%.2f", $goals_for / ($totalgames + $aggregateCount));
		$entriesArray[$name] = array($avg_goals_for, $row['nationality'], $totalgames); 
	}
	
	arsort($entriesArray);
	
	foreach ($entriesArray as $name => $valArray) {
		$nameClass = colorNameClass($name, 'yes');
    	$namedisplay = "<img src='$directory/flags/".$valArray[1].".bmp' align='absmiddle' border='1'>" .
			"&nbsp;<span $nameClass><a href='$directory/profile.php?name=".$name."'>".$name."</a></span>";
		$avgDisplay = $valArray[0]." <span class='grey-small'>(".$valArray[2].")</span>";
    	$highlight = strcmp($cookie_name, $name) == 0;
		printRow($cur, $namedisplay, $avgDisplay, $highlight);
		$cur++;
	}
}
else if ($defense) {
	$entriesArray = array(); 
	
	while ($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$totalgames = $row['totalgames'];

		// goals against as winner
		$games_sql = "SELECT sum(loserresult) as goals_against ".
			"FROM $gamestable " .
			"WHERE winner = '$name' AND deleted = 'no'";
	
		$games_result = mysql_query($games_sql);
		$games_row = mysql_fetch_array($games_result);
		$goals_against = $games_row['goals_against'];
		
		// goals as loser
		$games_sql = "SELECT sum(winnerresult) as goals_against ".
			"FROM $gamestable " .
			"WHERE loser = '$name' AND deleted = 'no'";
	
		$games_result = mysql_query($games_sql);
		$games_row = mysql_fetch_array($games_result);
		$goals_against += $games_row['goals_against'];
		
		
		// aggregate games
		$agg_sql = "SELECT count(*) as aggregate ".
			"FROM $gamestable " .
			"WHERE (loser = '$name' or winner = '$name') AND deleted = 'no' AND host = 'A'";
		$agg_result = mysql_query($agg_sql);
		$agg_row = mysql_fetch_array($agg_result);
		$aggregateCount = $agg_row['aggregate'];

		$avg_goals_against = sprintf("%.2f", $goals_against / ($totalgames + $aggregateCount));
		
		$entriesArray[$name] = array($avg_goals_against, $row['nationality'], $totalgames); 
	}
	
	asort($entriesArray);
	
	foreach ($entriesArray as $name => $valArray) {
			$nameClass = colorNameClass($name, 'yes');
	    	$namedisplay = "<img src='$directory/flags/".$valArray[1].".bmp' align='absmiddle' border='1'>" .
				"&nbsp;<span $nameClass><a href='$directory/profile.php?name=".$name."'>".$name."</a></span>";
				
			$avgDisplay = $valArray[0]." <span class='grey-small'>(".$valArray[2].")</span>";
			
	    	$highlight = strcmp($cookie_name, $name) == 0;
			printRow($cur, $namedisplay, $avgDisplay, $highlight);
			$cur++;
	}

}
else if ($active) {
	$act = array();
	while ($num >= $cur) {
		$row = mysql_fetch_array($result);
		$name = $row["name"];
		$nationality = $row["nationality"];
		$activity = getActivityForPlayer($name);
		if ($activity > 0) {
			$percent = sprintf("%.2f", $activity);
			$act[$name] = array($percent, $nationality);
		}
		$cur++;
	}
	$cur = 1;
    
    arsort($act);
   	
   	foreach ($act as $name=>$player) {
      $nameClass = colorNameClass($name, 'yes');
	  $col2 = "<img src='$directory/flags/".$player[1].".bmp' align='absmiddle' border='1'>" .
			"&nbsp;<span $nameClass><a href='$directory/profile.php?name=".$name."'>".$name."</a></span>";
      
      $highlight = strcmp($cookie_name, $name) == 0;
      
      printRow($cur, $col2, $player[0], $highlight);  
      $cur++;
   	}
}	
else if ($forum) {
	while ($num >= $cur) {
		$row = mysql_fetch_array($result);
		$counter = $row["counter"];
		$forum = $row["forum"];
		printRow($cur, $forum, $counter, false);
		$cur++;
	}
} 
else if ($nationalities) {
	while ($num >= $cur) {
		$row = mysql_fetch_array($result);
		$nationality = $row['nationality'];
		$counter = $row['counter'];
		$col2 = "<img src='$directory/flags/$nationality.bmp' align='absmiddle' border='1'>&nbsp;&nbsp;$nationality";
		printRow($cur, $col2, $counter, false);
		$cur++;
	}	
}
else if ($inactivity) {
	while ($num >= $cur) {
		$row = mysql_fetch_array($result);
		$name = $row['name'];
		$points = $row['deductedPoints'];
      	$nationality = $row['nationality'];
		
		$nameClass = colorNameClass($name, 'yes');
	  	
		$col2 = "<img src='$directory/flags/$nationality.bmp' align='absmiddle' border='1'>" .
			"&nbsp;<span $nameClass><a href='$directory/profile.php?name=".$name."'>".$name."</a></span>";
      
    	$highlight = strcmp($cookie_name, $name) == 0;
	    printRow($cur, $col2, $points, $highlight);  
		$cur++;
	}	
}
else if ($gamesPerVersion) {
	while ($row = mysql_fetch_array($result)) {
		$color = $row['color'];
		$version = $row['version'];
		$gameName = $row['name'];
		$sql2 = "SELECT count(*) as cnt FROM $gamestable WHERE deleted = 'no' AND version = '$version'";
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_array($result2);
		$count = $row2['cnt'];
		$displayWidth = ceil($count/100);
		// echo "count:$count displayWidth:$displayWidth gameName: $gameName<br>";
		$col2 = getImgForVersion($version)."&nbsp;&nbsp;".$gameName;
		$col3 = "<img border='1' style='cursor:help;vertical-align:middle;background-color:#$color' ".
			"src='gfx/1px_transparent.gif' title='$gameName games' alt='$gameName games' height='4' width='$displayWidth' />&nbsp;&nbsp;".$count;
		
		printRow($cur, $col2, $col3, false); 
		$cur++;
	}
}
else if ($gamesPerMonth) {
	
	$startDate = 1099288800; // 01.11.2004
               
  $baseDate = new DateTime();
  $baseDate->setTime(0,0,0);
  
  $cur = 1;
  while ($startDate < $baseDate->getTimestamp()) {
    $baseDateOrig = clone $baseDate;
    $baseDate->modify('first day of this month');
    $firstDay = $baseDate->getTimestamp();
    $baseDate->modify('first day of next month');
    $lastDay = $baseDate->getTimestamp();
    $sql = "SELECT count(date) AS counter, v.grouping, v.color, v.id " .
      "FROM $gamestable g ".
      "LEFT JOIN $versionstable v ON v.version = g.version ".
      "WHERE g.date < ".$lastDay." AND g.date > ".$firstDay." AND deleted = 'no' ".
      "GROUP BY v.grouping " .
      "ORDER BY v.grouping ASC";

    $result = mysql_query($sql);
    $totalcounter = 0;
    $img = "";
    while ($row = mysql_fetch_array($result)) {
      $grouping = $row['grouping'];
      $color = $row['color'];
      $versionId = $row['id'];
      $counter = $row['counter'];
      $totalcounter = $counter+$totalcounter;
      $width = ceil($counter/8);
      $img .= "<img border='1' style='cursor:help;vertical-align:middle;background-color:#$color' ".
        "src='gfx/1px_transparent.gif' title='$counter $grouping game(s)' alt='$counter $grouping game(s)' height='4' width='$width' />".
        "<img src='gfx/1px_transparent.gif' />";
    }
    $baseDate = $baseDateOrig;
    printRow($baseDate->format("M Y"), $img, $totalcounter, false);
    $baseDate->modify('-1 month');
    $cur++;
  }
}
else if ($favTeams) {
		$teamsArray = array();
		while ($row = mysql_fetch_array($result)) {
			$team = $row["team"];
			$cnt = $row["cnt"];
			if (in_array($team, array_keys($teamsArray))) {
				$teamsArray[$team] = array($teamsArray[$team][0] + $cnt, $team);
			} else {
				$teamsArray[$team] = array($cnt, $team);
			}
		}	
		arsort($teamsArray);
		$cur = 1;
		foreach ($teamsArray as $teamArray) {
			$teamName = getTeamNameForId($teamArray[1]);
			$teamImg = getImgForTeam($teamArray[1]);
			$teamDisplay = $teamImg . "&nbsp;&nbsp;" . $teamName;
			$cnt = $teamArray[0];
			if ($cnt == $oldcnt) {
				$curDisplay = ".";
			} else {
				$curDisplay = $cur;
				$oldcnt = $cnt;
			}
			
			printRow($curDisplay, $teamDisplay, $cnt, false);
			$cur++;
	  }
}

else if ($gameTeams) {
		$teamsArray = array();
		while ($row = mysql_fetch_array($result)) {
			$team = $row["team"];
			$cnt = $row["cnt"];
			if (in_array($team, array_keys($teamsArray))) {
				$teamsArray[$team] = array($teamsArray[$team][0] + $cnt, $team);
			} else {
				$teamsArray[$team] = array($cnt, $team);
			}
		}	
		arsort($teamsArray);
		$cur = 1;
		foreach ($teamsArray as $teamArray) {
			$teamName = getTeamNameForId($teamArray[1]);
			$teamImg = getImgForTeam($teamArray[1]);
			$teamDisplay = $teamImg . "&nbsp;&nbsp;" . $teamName;
			$cnt = $teamArray[0];
			if ($cnt == $oldcnt) {
				$curDisplay = ".";
			} else {
				$curDisplay = $cur;
				$oldcnt = $cnt;
			}
			
			printRow($curDisplay, $teamDisplay, $cnt, false);
			$cur++;
	  }
}

else if ($fairness) {
    while ($row = mysql_fetch_array($result)) {
		$loser = $row["loser"];
		$votes = $row["votes"];
		if ($votes > 2) {
	 		$player_sql = "SELECT nationality from $playerstable where name = '$loser'";
			$player_result = mysql_query($player_sql);
			$player_row = mysql_fetch_array($player_result);
			$nationality = $player_row['nationality'];
			$nameClass = colorNameClass($loser, 'yes');
	    	$namedisplay = "<img src='$directory/flags/".$nationality.".bmp' align='absmiddle' border='1'>" .
	     		"&nbsp;<span $nameClass><a href='$directory/profile.php?name=".$loser."'>".$loser."</a></span>";
			$avg = formatFairness($row["avg"]);
			if ($old_avg != $avg) {
				$curdisplay = $cur;
			}
			else {
				$curdisplay = ".";
			}			
			$old_avg = $avg;
			$avgDisplay = $avg." <span class='grey-small'>(".$votes.")</span>";
	    	$highlight = strcmp($cookie_name, $loser) == 0;
			printRow($curdisplay, $namedisplay, $avgDisplay, $highlight);
			$cur++;
		}
	}
}	

else {
	while ($num >= $cur) {
		$row = mysql_fetch_array($result);

		$name = $row["name"];
		$approved = $row["approved"];
		
		$nameClass = colorNameClass($name, $approved);
		
		$nationality = $row["nationality"];
		$totalwins = $row["totalwins"];
		$totallosses = $row["totallosses"];
		$totaldraws = $row["totaldraws"];
		$streakwins = $row["streakwins"];
		$streaklosses = $row["streaklosses"];
		$rating = $row ["rating"];
		$totalgames = $row["totalgames"];
		$totalwins = $row["totalwins"];
		$totallosses = $row["totallosses"];
		$streakwins = $row["streakwins"];
		$streaklosses = $row["streaklosses"];
		
		if ($totalgames <= 0) {
			$totalpercentage = 0.000;
		} else {
			$totalpercentage = $totalwins / $totalgames;
		}
		
		$col1 = $cur;
		
		$col2 = "<img src='$directory/flags/".$nationality.".bmp' align='absmiddle' border='1'>" .
			"&nbsp;&nbsp;<span $nameClass><a href='$directory/profile.php?name=".$name."'>".$name."</a></span>";
			
		if ($stat == "Ladder Games (per Player)") {
		   $col3 = $totalgames;
		}
		else if ($stat == "Total Wins") {
		   $col3 = $totalwins;
		}
		else if ($stat == "Total Losses") {
		   $col3 = $totallosses;
		}
		else if ($stat == "Best Streak (current)") {
		   $col3 = $streakwins;
		}
		else if ($stat == "Worst Streak (current)") {
		   $col3 = $streaklosses;
		}
		else if ($stat == "ELOrating") {
		   $col3 = $rating;
		}
		
		$highlight = strcmp($cookie_name, $name) == 0;
		
		printRow($col1, $col2, $col3, $highlight);
		$cur++;
	}
}
	
function printRow($col1, $col2, $col3, $highlight) {
	if ($highlight) {
		echo "<tr class='row_active'>";
	} else {
		echo "<tr class='row'>";
	}
   ?>
		<td width="10%" style="text-align:right;"><? echo $col1 ?></td>
		<td width="70%" ><? echo $col2 ?></td>
		<td width="20%" nowrap><? echo $col3 ?></td>
	</tr>
	<? 
} // end function
?>
<?= getRankBoxBottom() ?>
</td>
</tr></table>

<?= getOuterBoxBottom() ?>

<?php
require('bottom.php');
?>

