<?php

// shows the table of played games
// how many rows are shown on each page is defined in weblm_vars.numgamespage

$page = "games";
$subpage = "games";

$selected = ' selected="selected"';
$msgRes = "";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('top.php');

if(!empty($_GET['gameId'])) {
	$gameId = trim(mysql_real_escape_string($_GET['gameId']));
}
else if(!empty($_POST['gameId'])) {
	$gameId = trim(mysql_real_escape_string($_POST['gameId']));
}

if(!empty($_GET['startplayed'])) {
	$startplayed = mysql_real_escape_string($_GET['startplayed']);
}
else {
   $startplayed = '0';
}

if(!empty($_GET['finishplayed'])) {
	$finishplayed = mysql_real_escape_string($_GET['finishplayed']);
}
else {
   $finishplayed = $numgamespage;
}

if(!empty($_POST['selectname'])) {
	$selectname = mysql_real_escape_string($_POST['selectname']);
}
else {
   $selectname = 'all';
}
if (!empty($_GET['player'])) {
   $selectname = mysql_real_escape_string($_GET['player']);
}

if(!empty($_POST['opponent'])) {
	$opponent = mysql_real_escape_string($_POST['opponent']);
}
else {
   $opponent = 'all';
}
if (!empty($_GET['opponent'])) {
   $opponent = mysql_real_escape_string($_GET['opponent']);
}

if(!empty($_POST['type'])) {
	$type = mysql_real_escape_string($_POST['type']);
}
else {
   $type = 'against';
}
if (!empty($_GET['type'])) {
   $type = mysql_real_escape_string($_GET['type']);
}
if (!empty($_GET['gameType'])) {
   $gameType = mysql_real_escape_string($_GET['gameType']);
} else if (!empty($_POST['gameType'])) {
   $gameType = mysql_real_escape_string($_POST['gameType']);
} else {
	$gameType = 0;
}


$dateday = date("d/m/Y");
$sql = "SELECT count(*) from $gamestable WHERE dateday = '$dateday' AND deleted = 'no'";
$result = mysql_query($sql);
if (mysql_num_rows($result) > 0) {
	$gamesTodayRow = mysql_fetch_array($result);
	$gamesToday = $gamesTodayRow[0]." games today";
} else {
	$gamesToday = "";
}

if (!empty($gameId)) {
	$sql = "Select count(*) from $gamestable where game_id > $gameId";
	$result = mysql_query($sql);
	if (@mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$count = $row[0];
		$pageGameId = floor($count/$numgamespage);
		$startplayed = $numgamespage*$pageGameId;
		$type = "against";
		$selectname = "all";
		$opponent = "all";
	}
}

?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), $gamesToday) ?>
<table width="100%"><tr><td style="text-align:left;">
<form method="post" name="formGames" action="<?php echo"$directory"?>/games.php?startplayed=0&finishplayed=<?php echo"$numgamespage"?>">
   <select class="width150" name="gameType">
   		<option value="0"<? if (strcmp($gameType, "0") == 0) echo $selected; ?>>All games</option>
   		<option value="1"<? if (strcmp($gameType, "1") == 0) echo $selected; ?>>Single games</option>
   		<option value="2"<? if (strcmp($gameType, "2") == 0) echo $selected; ?>>Team games</option>
   </select>
<span style="vertical-align:middle">&nbsp;&nbsp;of&nbsp;&nbsp;</span>
<select class="width150" name="selectname">
   <?php
   $sortby = "name ASC";
   $sql="SELECT name FROM $playerstable ORDER BY $sortby";

   $result = mysql_query($sql,$db);
   $num = mysql_num_rows($result);
   $cur = 1;
   ?>
   <option <? if ($opponent == 'all') {
   		echo ' selected="selected"';
   }?> value="all">[any player]</option>
   <?
   while ($num >= $cur) {
      $row = mysql_fetch_array($result);
      $name = $row["name"];
      ?>
      <option<?
      if ($selectname != "all") {
         if ($name == $selectname) {
            echo ' selected="selected"';
         }
      }
      else {
         if ($name == $cookie_name && empty($_POST['selectname'])) {
            echo ' selected="selected"';
         }
      }
      ?>
      >
      <?php echo "$name" ?></option>
      <?php
      $cur++;
   }
   ?>
   </select>
   <select class="width100" name="type">
   		<option <? if (strcmp($type, "against") == 0) echo $selected; ?>>against</option>
   		<option <? if (strcmp($type, "with") == 0) echo $selected; ?>>with</option>
   </select>
   <select class="width150" name="opponent">
   <option <? if ($opponent == 'all') {
   		echo $selected;
   }?> value="all">[any player]</option>
   <?php

   mysql_data_seek($result, 0); 
   $cur = 1;
   while ($num >= $cur) {
      $row = mysql_fetch_array($result);
      $name = $row["name"];
      ?>
      <option<?
      if ($opponent != "all") {
         if ($name == $opponent) {
            echo $selected;
         }
      }
      ?>
      >
      <?php echo "$name" ?></option>
      <?php
      $cur++;
   }
   ?>
   </select>
   <span style="vertical-align:middle">&nbsp;&nbsp;or by ID&nbsp;&nbsp;</span><input type="text" class="width50" name="gameId" maxlength="6" />
   &nbsp;&nbsp;<input type="Submit" class="width100" name="submit2" value="show games" /></form>
</td></tr>
</table>
<?= getOuterBoxBottom() ?>

<?php

$sortby = "game_id DESC";
$not_deleted = "deleted = 'no'";
$deleted = "deleted = 'yes'";
$draws = "isDraw > 0 AND deleted ='no'";
$sqlNoDraw = "isDraw = 0";
$whereclause = "";

if (strcmp($type, "against") == 0) {
	if ($selectname != "all") {
	   if ($opponent == "all") {
	   	$whereclause = "WHERE (winner = '$selectname' or winner2 = '$selectname' or loser = '$selectname' or loser2 = '$selectname') and $not_deleted";
	   } else {
	   	$whereclause = "WHERE (((winner = '$selectname' OR winner2 = '$selectname') AND (loser = '$opponent' OR loser2 = '$opponent')) " .
	   			"OR ((winner = '$opponent' OR winner2 = '$opponent') AND (loser = '$selectname' OR loser2 = '$selectname'))) and $not_deleted";
	   }
	} else {
	   if ($opponent != "all") {
	   	 $whereclause = "WHERE (winner = '$opponent' OR winner2 = '$opponent' OR loser = '$opponent' OR loser2 = '$opponent') and $not_deleted";
	   } else {
	   	 $whereclause = "WHERE $not_deleted";
	   }
	}
} else { // type = 'with'
	if ($selectname != "all") {
	   if ($opponent == "all") {
	   		$whereclause = "WHERE (".
	   		"((winner = '$selectname' OR winner2 = '$selectname') AND winner2 != '') ".
	   		" OR ((loser = '$selectname' OR loser2 = '$selectname') AND loser2 != '') ".
	   		") AND $not_deleted";
	   } else {
	   		$whereclause = "WHERE (". 
			"(winner = '$selectname' AND winner2 = '$opponent') ".
	   		"OR (winner = '$opponent' AND winner2 = '$selectname') ".
	   		"OR (loser = '$selectname' AND loser2 = '$opponent') ".
	   		"OR (loser = '$opponent' AND loser2 = '$selectname') ".
	   		") AND $not_deleted";
	   }
	} else {
	   if ($opponent != "all") {
	   	 $whereclause = "WHERE (".
	   		"((winner = '$opponent' OR winner2 = '$opponent') AND winner2 != '') ".
	   		" OR ((loser = '$opponent' OR loser2 = '$opponent') AND loser2 != '') ".
			") AND $not_deleted";
	   } else {
	   	 $whereclause = "WHERE (winner2 != '' OR loser2 != '') AND $not_deleted";
	   }
	}
}

if (empty($whereclause)) {
	if ($gameType == 1) {
		$whereclause .= " WHERE winner2 = '' AND loser2 = ''";
	} else if ($gameType == 2) {
		$whereclause .= " WHERE (winner2 != '' OR loser2 != ''";
	}
} else {
	if ($gameType == 1) {
		$whereclause .= " AND winner2 = '' AND loser2 = ''";
	} else if ($gameType == 2) {
		$whereclause .= " AND (winner2 != '' OR loser2 != '')";
	}
}

$sql = "SELECT count(*) as gamescount FROM $gamestable $whereclause ORDER BY $sortby";
$result = mysql_query($sql);

$resarray = mysql_fetch_array($result);
$yo = $resarray['gamescount'];
$sql = str_replace($not_deleted, $deleted, $sql);
$result2 = mysql_query($sql);
$resarray2 = mysql_fetch_array($result2);
$yo_deleted = $resarray2['gamescount'];

$sql = str_replace($deleted, $draws, $sql);

$result3 = mysql_query($sql);
$resarray3 = mysql_fetch_array($result3);
$yo_draws = $resarray3['gamescount'];

$navRight = "";
if ($yo == 0 && $yo_deleted == 0) {
  $msgRes.= "No games found";
  $navRight = $yo." results";
} 
else {
	$yo > 1 ? $s = "s" : $s = "";
	
	$msgRes.= "<b>".$yo."</b> game$s";
	$navRight = ($yo+$yo_deleted)." result$s";
	
	if ($selectname != "all" || $opponent != "all") {
	  
	   
	   if ($yo_deleted > 0) {
	   	$msgRes.= "&nbsp;&nbsp;|&nbsp;&nbsp;<b>".$yo_deleted."</b> deleted";
	   }
	   if ($yo_draws > 0) {
	   	$yo_draws > 1 ? $s = "s" : $s = "";
	   	$msgRes.= "&nbsp;&nbsp;|&nbsp;&nbsp;<b>".$yo_draws."</b> draw$s";
	   }
	   if (strcmp($type, "against") == 0) {
		   if ($opponent == "all") {
			   	$sql = "SELECT count(*) as cnt FROM $gamestable " .
			   			"WHERE (winner = '$selectname' OR winner2 = '$selectname') AND $sqlNoDraw AND deleted='no'";
			   	$msgRes.= getPlayerLine($sql, $selectname, $yo);
		   } else if ($selectname == "all") { 
			   	$sql = "SELECT count(*) as cnt FROM $gamestable " .
			   			"WHERE (winner = '$opponent' OR winner2 = '$opponent') AND $sqlNoDraw AND deleted='no'";
			   	$msgRes.= getPlayerLine($sql, $opponent, $yo);
		   } else {
			   	$sql = "SELECT count(*) as cnt FROM $gamestable " .
			   			"WHERE ((winner = '$selectname' OR winner2 = '$selectname') AND $sqlNoDraw AND (loser = '$opponent' OR loser2 = '$opponent')) AND deleted='no'";
			   	$msgRes.= getPlayerLine($sql, $selectname, $yo);
		   	
			   	$sql = "SELECT count(*) as cnt FROM $gamestable " .
			   			"WHERE ((winner = '$opponent' OR winner2 = '$opponent') AND $sqlNoDraw AND (loser = '$selectname' OR loser2 = '$selectname')) AND deleted='no'";
			   	$msgRes.= getPlayerLine($sql, $opponent, $yo);
		   }
	   } else { // type with
	   	    if ($opponent == "all") {
			   	$sql = "SELECT count(*) as cnt FROM $gamestable " .
			   			"WHERE (".
			   			"((winner = '$selectname' OR winner2 = '$selectname') AND $sqlNoDraw AND winner2 != '') ".
			   			") AND deleted='no'";
			   	$msgRes.= getPlayerLine($sql, $selectname, $yo);
		   } else if ($selectname == "all") { 
			   	$sql = "SELECT count(*) as cnt FROM $gamestable " .
			   			"WHERE (".
			   			"((winner = '$opponent' OR winner2 = '$opponent') AND AND $sqlNoDraw winner2 != '') ".
						") AND deleted='no'";
			   	$msgRes.= getPlayerLine($sql, $opponent, $yo);
		   } else {
			   	$sql = "SELECT count(*) as cnt FROM $gamestable " .
		   			"WHERE (".
		   			"(winner = '$selectname' AND winner2 = '$opponent') OR ".
		   			"(winner = '$opponent' AND winner2 = '$selectname') ".
					") AND $sqlNoDraw AND deleted='no'";
				$displayname = $selectname.'<span class="grey-small">&ndash;</span>'.$opponent;
			   	$msgRes.= getPlayerLine($sql, $displayname, $yo);
		   	}	 
	   }
	} 
	
	if(!empty($pageGameId)) {
		$number = $startplayed;
	} else if(!empty($_GET['number'])) {
	   $number = mysql_real_escape_string($_GET['number']);
	}
	else {
	   $number = 0;
	}
	
	if(!empty($pageGameId)) {
		$link = $pageGameId;
	} else if(!empty($_GET['link'])) {
		$link = mysql_real_escape_string($_GET['link']);
	}
	else {
	   $link = 1;
	}
	
	$totalgames = $yo + $yo_deleted;
	$finishnumber = $numgamespage;
	$startnext = $startplayed + $numgamespage;

	$startprevious = $startplayed - $numgamespage;
	$lastpage = $totalgames - $numgamespage;
	$prevnumber = $number - $numgamespage;
	
	$prevlink = $link - 1;
	$nextnumber = $number + $numgamespage;
	$nextlink = $link + 1; 
	
	$compteur = 1;
	$compteur2 = 0;
	$compteurgfx = 1;
	
	$maxlink = 0;
	$navLine = "";
	while ($compteur2 < $totalgames)
		{
		$compteur2 = $compteur2 + $numgamespage;
		$maxlink++;
		}
	$maxnumber = ($maxlink - 1) * $numgamespage;
	$msgRes .= "<tr><td>";
	
	
	$navLine.= "Result pages <span class='grey-small'>&raquo;&nbsp;&nbsp;";
	if ($startprevious >= $numgamespage) {
		$navLine.= " <a href='$directory/games.php?startplayed=0&amp;finishplayed=$finishnumber&amp;number=0&amp;link=1&amp;player=$selectname&amp;opponent=$opponent&amp;type=$type&amp;gameType=$gameType'><<</a> |";
	}
	if ($startprevious >= 0) {
		$navLine.=  " <a href='$directory/games.php?startplayed=$startprevious&amp;finishplayed=$finishnumber&amp;number=$prevnumber&amp;link=$prevlink&amp;player=$selectname&amp;opponent=$opponent&amp;type=$type&amp;gameType=$gameType'><</a> |";
	}
	while (($number < $totalgames) && ($compteur < $maxgameslinkpage)) {
		$navLine.= " <a href='$directory/games.php?startplayed=$number&amp;finishplayed=$finishnumber&amp;number=$number&amp;link=$link&amp;player=$selectname&amp;opponent=$opponent&amp;type=$type&amp;gameType=$gameType'>";
	if($compteurgfx == 1)
		{
		$navLine.= "<font class='menu-active'>".$link."</font>";
		}
		else
			{
			$navLine.= $link;
			}
	$navLine.= "</a> ";
	$number = $number + $numgamespage;
	if($startplayed < $lastpage) { $navLine.= "| ";}
	$link++;
	$compteur++;
	$compteurgfx++;
	}
	if ($startplayed < $lastpage) {
		$navLine.= "<a href='$directory/games.php?startplayed=$startnext&amp;finishplayed=$finishnumber&amp;number=$nextnumber&amp;link=$nextlink&amp;player=$selectname&amp;opponent=$opponent&amp;type=$type&amp;gameType=$gameType'>></a>";
	}
	if ($startplayed < ($totalgames - ($numgamespage*2))) {
		$navLine.= " | <a href='$directory/games.php?startplayed=$lastpage&amp;finishplayed=$finishnumber&amp;number=$maxnumber&amp;link=$maxlink&amp;player=$selectname&amp;opponent=$opponent&amp;type=$type&amp;gameType=$gameType'>>></a>";
	}
	
	$whereclause = str_replace("and ".$not_deleted, "", $whereclause);
	$whereclause = str_replace("WHERE ".$not_deleted, "", $whereclause);
	
	if (substr($whereclause,1,3) == 'AND') {
		$whereclause = "WHERE".substr($whereclause,4);
	}

	$sql="SELECT * FROM $gamestable $whereclause ORDER BY $sortby LIMIT $startplayed, $finishplayed";

	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	$cur = 1;
}
	?>

	<?= getOuterBoxTop($navLine, $navRight) ?>
	<table width="100%" style="padding-bottom:5px">
	<tr><td><p><?= $msgRes ?></p></td></tr></table>
<? if ($yo > 0 || $yo_deleted > 0) { ?>
	<? $columnsArray = array('Id', 'Season', 'Game', 'Date', 'Pt+', 'Winner', 'WT', 'Result', 'LT', 'Loser', 'Pt-', 'Comment'); ?>
	<?= getRankBoxTop("Games", $columnsArray); ?>
	<?
	
	while ($num >= $cur) {
    $row = mysql_fetch_array($result);
    $gameid = $row["game_id"];
    $winner = $row["winner"];
    $winner2 = $row["winner2"];
    $loser = $row["loser"];
    $loser2 = $row["loser2"];
    $winnerresult = $row["winnerresult"];
    $loserresult = $row["loserresult"];
    $winpoints = $row["winpoints"];
    $losepoints = $row["losepoints"];
    $losepoints2 = $row["losepoints2"];
    $date = $row["date"];
    $comment = $row['comment'];
    $comment = escapeComment($comment); 	
    $sixGameId = $row['sixGameId'];
  if (strpos($comment, 'Unfinished Sixserver Game') !== FALSE) {
    $comment = str_replace('#'.$sixGameId,  '<a href="/sixserver/games.php?id='.$sixGameId.'&t=unfinished">#'.$sixGameId.'</a>', $comment);
  } elseif (strpos($comment, 'Sixserver Game') !== FALSE) {
    $comment = str_replace('#'.$sixGameId,  '<a href="/sixserver/games.php?id='.$sixGameId.'&t=finished">#'.$sixGameId.'</a>', $comment);
  }
	$deleted = $row["deleted"];
	$host = $row["host"];
	$gameseason = $row["season"];
	$version = $row["version"];
	$winnerteam = $row["winnerteam"];
	$loserteam = $row["loserteam"];
	$deleteReason = $row['deleteReason'];
	$teamBonus = $row['teamBonus'];
	$blacklink = "class='blacklink'";
	
	if ($deleted == 'yes' && !empty($deleteReason)) {
		$deletedBy = $row['deletedBy'];
		if (stristr($deleteReason, "http://")) {
			$link = substr($deleteReason, strpos($deleteReason, "http://"));
			if (strpos($link, " ") > 0) {
				$link = substr($link, 0, strpos($link, " "));
			}
			$newlink = '<a href="'.$link.'">(click for details)</a>';
			$deleteReason = str_replace($link, $newlink, $deleteReason);
		}
		
		$tooltipDeleted = $deleteReason."&nbsp;&nbsp;<span class='grey-small'>(".$deletedBy.")</span>";
		$deleteLine = "<p style='font-size:9px'>".getImgForDeletedGame()."&nbsp;".$tooltipDeleted."</p>";
		$comment = $comment.$deleteLine;
	}
	
	if ($host == "W") {
		$loseopts = "title='client'";    
		$loseClass = "class='link-client'";
		$winopts = "title='host'";
		$winClass = "";
		$wintd = $blacklink;
		$losetd = "";
	}
	else if ($host == "L") {
		$loseopts = "title='host'";
		$loseClass = "";
		$winopts = "title='client'";    
		$winClass = "class='link-client'";
		$wintd = "";
		$losetd = $blacklink;
	}
	else {
		$loseopts = "";
		$winopts = "";
		$loseClass = "";
		$winClass = "";
		$wintd = $blacklink;
		$losetd = $blacklink;
	}
	
	if ($teamBonus > 0) {
		$winpointsDisplay = '<span style="cursor:help;color:#1eac22" title="+'.$teamBonus.' for team">'.$winpoints.'</span>';
	} else {
		$winpointsDisplay = $winpoints;
	}
	
	$winnerlink = "<span $winClass><a $winopts href='profile.php?name=".$winner."'>".$winner."</a></span>";
	if (!empty($winner2)) {
		$winnerlink .= "<br><span $winClass><a $winopts href='profile.php?name=".$winner2."'>".$winner2."</a></span>";
		$winpointsDisplay .= "<br>".$winpointsDisplay;
	}
	$loserlink = "<span $loseClass><a $loseopts href='profile.php?name=".$loser."'>".$loser."</a></span>";
	if (!empty($loser2)) {
		$loserlink .= "<br><span $loseClass><a $loseopts href='profile.php?name=".$loser2."'>".$loser2."</a></span>";
		$losepoints .= "<br>".$losepoints2;		
	}
  
  if ($winnerteam > 0) {
    $flagLeft = getImgForTeam($winnerteam);
  } else {
    $sql2 = "SELECT nationality FROM $playerstable where name='$winner'";
    $row2 = mysql_fetch_array(mysql_query($sql2));
    $nationalityLeft = $row2['nationality'];
    $flagLeft = '<img title="'.$nationalityLeft.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft.'.bmp" width="18" height="15" border="1">';
  }
  
  if ($loserteam > 0) {
    $flagRight = getImgForTeam($loserteam);
  } else {
    $sql2 = "SELECT nationality FROM $playerstable where name='$loser'";
    $row2 = mysql_fetch_array(mysql_query($sql2));
    $nationalityRight = $row2['nationality'];
    $flagRight = '<img title="'.$nationalityRight.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight.'.bmp" width="18" height="15" border="1">';
  }
  
	?>
	<tr<? 
	if ($deleted == "yes") {
		echo " class='row_deleted'";
	}
	else if (!empty($gameId) && strcmp($gameId, $gameid) == 0) {
		 echo " class='row_active'";
	} else {
		echo " class='row'";
	}
	?>>
	   <td width="4%" style="text-align:center;"><?= $gameid ?></td>
	   <td width="4%" style="text-align:center;" title="season"><?= $gameseason ?></td>
	   <td width="4%" style="text-align:center;" nowrap><?= getImgForVersion($version)  ?></td>
	   <td width="10%" nowrap><?= formatDate($date) ?></td>
	   <td width="5%" class="grey-small" style="text-align:center;"><?= $winpointsDisplay ?></td>
	   <td width="10%" style="text-align:right;" <?= $wintd ?>><?= $winnerlink ?></td>
	   <td wdith="4%" style="text-align:center;"><?= $flagLeft; ?></td>
	   <td width="5% "style="text-align:center;" nowrap><?= $winnerresult." - ".$loserresult ?></td>
	   <td wdith="4%" style="text-align:center;"><?= $flagRight; ?></td>	   
	   <td width="10%" <?= $losetd ?>><?= $loserlink ?></td>
	   <td width="5%" class="grey-small" style="text-align:center;"><?= $losepoints ?></td>
	   <td width="35%"><?= $comment ?></td>
	</tr>
	<?php
	$cur++;
	}
	echo getRankBoxBottom();
} // end else nogames
?>

<?= getOuterBoxBottom() ?>
<?php
require('bottom.php');

function getPlayerLine($sql, $name, $total) {
	$result = mysql_query($sql);
   	// echo $sql;
   	$row = mysql_fetch_array($result);
   	$cnt = $row['cnt'];
   	if ($total > 0) {
   		$perc = $cnt / $total;
   	} else {
   		$perc = 0;
   	}
   	$percentage = sprintf("%.2f", $perc * 100);
   	return "&nbsp;&nbsp;|&nbsp;&nbsp;".$name." won <b>".$cnt."</b> (".$percentage."%)";
}
?>
