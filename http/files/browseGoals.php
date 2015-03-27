<?php

// this page allows users to upload files

$page = "goals";
$subpage = "browseGoals";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('./functions.php');
require('../top.php');

$selected = 'selected="selected"';
$time = time();
$notFound = false;

?>

<?php

if (!empty($_GET['name'])) {
	$player = mysql_real_escape_string($_GET['name']);
} else if (!empty($_POST['name'])) {
	$player = mysql_real_escape_string($_POST['name']);
} 

if (!empty($player)) {
	$name = $player;
	$sql = "SELECT player_id from $playerstable where name = '$name'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$player_id = $row[0];
	} else {
		$notFound = true;
	}
}

if (!empty($_GET['animated'])) {
	$animated = true;
} else if (!empty($_POST['animated'])) {
	$animated = true;
} else {
	$animated = false;
}

$maxresults = 60;


if (!empty($_GET['type'])) {
	$type = mysql_real_escape_string($_GET['type']);
} else if (!empty($_POST['type'])) {
	$type = mysql_real_escape_string($_POST['type']);
} else {
	$type = "all";
}

if (!empty($_GET['goal'])) {
	$goal = mysql_real_escape_string($_GET['goal']);
}

if (!empty($_GET['rating'])) {
	$rating_vote = mysql_real_escape_string($_GET['rating']);
}
if (!empty($_GET['goal_id'])) {
	$rating_goal_id = mysql_real_escape_string($_GET['goal_id']);
}
if (!empty($_GET['voter'])) {
	$rating_voter = mysql_real_escape_string($_GET['voter']);
}

if (!empty($_GET['start'])) {
	$start = mysql_real_escape_string($_GET['start']);
} else {
	$start = 0;
}

if (!empty($_GET['order'])) {
	$order = mysql_real_escape_string($_GET['order']);
} else if (!empty($_POST['order'])) {
	$order = mysql_real_escape_string($_POST['order']);
} else {
	$order = "date";
}

$whereIn = "";

if (!empty($cookie_name) && $cookie_name != "null") {
	$isEvoPlayer = true;
	$userRatings = array();
	// get all ratings
	$sql = "SELECT player_id from $playerstable WHERE name = '$cookie_name'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	if (mysql_num_rows($result) > 0) {
		$player_id_user = $row[0];
		$sql = "SELECT goal_id, rating FROM $votestable WHERE player_id = '$player_id_user'";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			$user_rating = $row['rating']; 
			$user_goal_id = $row['goal_id'];	
			$userRatings[$user_goal_id] = $user_rating;
			$whereIn .= "'$user_goal_id', ";
		}
		if (strlen($whereIn) > 2) {
			$whereIn = substr($whereIn, 0, strlen($whereIn)-2); 
			$whereIn = "WHERE id NOT IN (".$whereIn.")";
		}
	}	  
}

if ($notFound) {
	echo "<p>The player <b>$player</b> could not be found in the database.</p>".$back;
} else {	
	$boxheight = 0;
	
	if (!empty($rating_vote)) {
		$sql = "SELECT name from $playerstable WHERE player_id = '$rating_voter'";
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_array($result);
			$name = $row[0];
			if (strcasecmp($name, $cookie_name) == 0) {
				// the logged in user send this request
				if (!array_key_exists($rating_goal_id, $userRatings)) {
					
					$sql = "INSERT INTO $votestable (goal_id, player_id, rating, ratedate) ".
						"VALUES ('$rating_goal_id', '$rating_voter', '$rating_vote', '$time')";
					$result = mysql_query($sql);
					if ($result == 1) {
						$sql = "SELECT AVG(rating) FROM $votestable where goal_id = '$rating_goal_id'";
						$result = mysql_query($sql);
						$row = mysql_fetch_array($result);
						$average = $row[0];
						if ($average > 0) {
							$sql = "UPDATE $goalstable SET votes = votes + 1, rating = '$average' ". 
								"WHERE id = '$rating_goal_id'";
							$result = mysql_query($sql);
							$userRatings[$rating_goal_id] = $rating_vote;
							$voted = 'You gave goal <a href="#'.$rating_goal_id.'"><b>#'.$rating_goal_id.'</b></a> a rating of <b>'.$rating_vote .'</b>. '. 
								"Thanks for your vote!"; 
							$voted_boxtitle = "Vote cast";
						}
					} else {
						$voted = "Your vote could not be added. Please notify an administrator."; 
						$voted_boxtitle = "Vote failed";
					}
				} else {
					$voted = "You already voted for this goal!"; 
					$voted_boxtitle = "Vote failed";
				}
			} else {
				$voted = "We couldn't verify your identity. Your vote did not count."; 
				$voted_boxtitle = "Vote failed";
			}
		} else {
			$voted = "We couldn't verify your identity. Your vote did not count."; 
			$voted_boxtitle = "Vote failed";
		}
	}
	
$sql = "SELECT COUNT(id) FROM $goalstable";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$goals = $row[0];

$sql = "SELECT COUNT(id) FROM $votestable";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$votes = $row[0];

$total = $goals. " videos - " .$votes. ' <a href="voters.php">votes</a>';
?>
	
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), $total); ?>
<table width="100%">
<tr><td>
	
	<? if (!empty($voted)) { ?>
	<table width="100%"><tr><td>
		<?= getBoxTop($voted_boxtitle, 0, true, null) ?>
			<p><?= $voted ?></p>
		<?= getBoxBottom() ?>
	</td></tr></table>
	<? } ?>
	
	<table width="100%">
	<tr>
	<td width="50%" valign="top">
	<?= getBoxTop("Show Videos", $boxheight, false, null) ?>
	<?
	$sql = "SELECT count(*) AS count, type ".
			"FROM $goalstable ".
			"GROUP BY type ".
			"ORDER BY type ASC";
	$result = mysql_query($sql);
	$countArray = array();
	while ($row = mysql_fetch_array($result)) {
		$countArray[$row['type']] = $row['count'];
	}
	?>
 	<form method="post" name="formQuickfind" action="browseGoals.php">
 	<table class="formtable" style="margin-bottom:-10px;">
	<tr>
	    <td width="80">Select type</td>
	    <td>
		    <select class="width150" size="1" name="type">		
				<option value="all" <? if (strcmp($type, "all") == 0) { echo $selected; }?>>All</option>
				<option value="unrated" <? if (strcmp($type, "unrated") == 0) { echo $selected; }?>>Unrated</option>
				<? if ($isEvoPlayer) { ?>
				<option value="unratedbyme" <? if (strcmp($type, "unratedbyme") == 0) { echo $selected; }?>>My unrated</option>
				<? } ?>
				<option value"">---------------------------</option>
				<option value="goals" <? if (strcmp($type, "goals") == 0) { echo $selected; }?>>Goals (<?= $countArray['A'] ?>)</option>
				<option value="compilations" <? if (strcmp($type, "compilations") == 0) { echo $selected; }?>>Compilations (<?= $countArray['C'] ?>)</option>
				<option value="misses" <? if (strcmp($type, "misses") == 0) { echo $selected; }?>>Game Scenes (<?= $countArray['M'] ?>)</option>
				<option value="other" <? if (strcmp($type, "other") == 0) { echo $selected; }?>>Other (<?= $countArray['O'] ?>)</option>
		    </select>
	    </td>    
		<td></td>
		<td width="20"><input type="checkbox" name="animated" <? if ($animated) { echo 'checked="checked"'; } ?>></td>
		<td>animate?</td>
    </tr>
    <tr>
	    <td width="80">Order by</td>
	    <td>
		    <select class="width150" size="1" name="order">		
				<option value="date" <? if (strcmp($order, "date") == 0) { echo $selected; }?>>Date</option>
				<option value="rating" <? if (strcmp($order, "rating") == 0) { echo $selected; }?>>Rating</option>
		    </select>
	    </td>
	    <td></td>
		<td class="padding-button" colspan="2"><input type="submit" class="width100" name="submit" value="Show Videos"></td>    
    </tr>
    </table></form>
	<?= getBoxBottom() ?>
	</td>
	<td width="50%" valign="top">
	<?= getBoxTop("Show Player Videos", $boxheight, false, null) ?>
 	<form method="post" name="formQuickfind" action="browseGoals.php">
 	<table class="formtable" style="margin-bottom:-10px;">
	<tr>
	    <td>Select player</td>
	    <td><select class="width150" size="1" name="name">		
    <?php
    $sql = "SELECT p.name, p.player_id, count(*) AS count ".
			"FROM $goalstable g ".
			"LEFT JOIN $playerstable p ON p.player_id = g.player_id ".
			"GROUP BY p.name ".
			"ORDER BY name ASC";

    $result = mysql_query($sql);
    $num = mysql_num_rows($result);
    $cur = 1;
    $playersArray = array();
    
    while ($num >= $cur) {
        $row = mysql_fetch_array($result);
        $row_name = $row["name"];
		$pl_id = $row['player_id'];
		$count = $row['count'];
		// $goaltype = $row['type'];
        $playersArray[$pl_id] = $row_name;
        
        if ($player == $row_name) {
        	$isSelected = $selected;
        } else if (empty($player) && $isEvoPlayer && $cookie_name == $row_name) {
        	$isSelected = $selected;
        } else {
        	$isSelected = ""; 
        }
    	// if ($goaltype == 'A') {
    		?><option <?= $isSelected ?> value="<?= $row_name ?>"><?= $row_name ?> (<?= $count ?>)</span></option><?
    	// }
        $cur++;
    } 

    ?>
	    </select></td>
	    <td></td>
	   	<td width="20"><input type="checkbox" name="animated" <? if ($animated) { echo 'checked="checked"'; } ?>></td>
		<td>animate?</td>
		    
    </tr>
    <tr>
	    <td width="80">Order by</td>
	    <td>
		    <select class="width150" size="1" name="order">		
				<option value="date" <? if (strcmp($order, "date") == 0) { echo $selected; }?>>Date</option>
				<option value="rating" <? if (strcmp($order, "rating") == 0) { echo $selected; }?>>Rating</option>
		    </select>
	    </td>
	    <td></td>
	    
		<td class="padding-button" colspan="2">
			<input type="hidden" name="type" value="player">
			<input type="submit" class="width100" name="submit" value="Show Videos">
		</td>    
    </tr>
    </table></form>
	<?= getBoxBottom() ?>
	</td></tr></table>
	</td></tr></table>
	<?= getOuterBoxBottom() ?>
	<?
		
		$limit = "LIMIT ".$start.", ".$maxresults;
		
		if (!empty($goal)) {
			$where = "WHERE id = '$goal'";
		} else if (strcmp($type, "all") == 0) {
			$where = "";
		} else if (strcmp($type, "player") == 0) {
			$where = "WHERE player_id = '$player_id'";
		} else if (strcmp($type, "goals") == 0) {
			$where = "WHERE type = 'A'";
		} else if (strcmp($type, "misses") == 0) {
			$where = "WHERE type = 'M'";
		} else if (strcmp($type, "compilations") == 0) {
			$where = "WHERE type = 'C'";
		} else if (strcmp($type, "unrated") == 0) {
			$where = "WHERE rating = '0'";
		} else if (strcmp($type, "other") == 0) {
			$where = "WHERE type = 'O'";
		} else if (strcmp($type, "unratedbyme") == 0) {
			$where = $whereIn;
		} 
		
		if (strcmp($order, "rating") == 0) {
			$orderSql = "ORDER BY rating DESC, votes DESC, uploaded DESC";
		} else {
			$orderSql = "ORDER BY uploaded DESC";
		}
		
		$sql = "SELECT * FROM $goalstable $where $orderSql $limit"; 
		
		$result = mysql_query($sql);
		$pageCount = mysql_num_rows($result);
		
		$sqlCount = "SELECT count(id) FROM $goalstable $where $orderSql";
		$resultCount = mysql_query($sqlCount);
		$rowCount = mysql_fetch_array($resultCount);
		$totalCount = $rowCount[0];
		
		$paging = "Result pages <span class='grey-small'>&raquo;&nbsp;&nbsp;";
		
		for ($i = 0; $i < $totalCount; $i = $i + $maxresults) {
			
			$queryString = "browseGoals.php?type=$type";
			if ($animated) {
				$queryString .= "&amp;animated=$animated";
			}
			if (!empty($player)) {
				$queryString .= "&amp;name=$player";
			}
			$queryString .= "&amp;order=$order&amp;start=$i";
			$pageName = ($i/$maxresults)+1;
			if ($i == $start) {
				$pageName = '<font class="menu-active"><b>'.$pageName.'</b></font>';
			}
			$pageLink = '<a href="'.$queryString.'">'.$pageName.'</a> | ';
			$paging .= $pageLink;			
		} 
		$paging = substr($paging, 0, strlen($paging)-3)."</span>";
?>
<? if ($pageCount > 0) { 
	if ($totalCount > 1) {
		$totalCountDisplay = $totalCount . " videos";
	} else {
		$totalCountDisplay = $totalCount . " video";
	}
?>
<?= getOuterBoxTop($paging, $totalCountDisplay); ?>
	<table width="100%"><tr><td>
	<table width="100%">
<?

		while ($row = mysql_fetch_array($result)) {
			$goal_id = $row['id'];
			$player_id = $row['player_id'];
			$uploaded = $row['uploaded']; 
			$extension = $row['extension'];
			$comment = $row['comment'];
			$goalType = $row['type'];
			$rating = $row['rating'];
			$votes = $row['votes'];
			$hasThumb = $row['hasThumb'];
			
			if ($hasThumb == '1') {
				if ($animated) {
					$img = $player_id."_".$uploaded."_anim.gif";
				} else {
					$img = $player_id."_".$uploaded."_thumb.png";
				}
				$imgUrl = 'files/goals/thumbnails/'.$img;
			} else {
				$imgUrl = 'gfx/thumb_missing.gif';
			}			
			
			$fileName = $player_id."_".$uploaded.".".$extension;
			
			if (file_exists($wwwroot.$imgUrl)) {
				$imgSrc = '<img height="96" width="128" src="/'.$imgUrl.'" border="2">';
			} else {
				$imgSrc = '<img height="96" width="128" src="/gfx/thumb_missing.gif" border="2">';
			}
			
			$imgLinked = '<a href="/files/goals/'.$fileName.'">'.$imgSrc.'</a>';
			$name = $playersArray[$player_id];
			
			$rating = getRating($rating, $votes, true);
			
			if ($isEvoPlayer) {
				if (array_key_exists($goal_id, $userRatings)) {
					$userRating = $userRatings[$goal_id];
					$rateList = "&nbsp;" . getRating($userRating, 0, false);			
				} else if (strcmp($player_id_user, $player_id) == 0) {
					$rateList = '&nbsp;&nbsp;<span style="color:#AAAAAA;">You cannot vote for yourself</span>';
				} else {
					$queryString = "browseGoals.php?type=$type";
					if ($animated) {
						$queryString .= "&amp;animated=$animated";
					}
					if (!empty($player)) {
						$queryString .= "&amp;name=$player";
					}
					$queryString .= "&amp;order=$order&amp;goal_id=$goal_id".
						"&amp;start=$start&amp;voter=$player_id_user&amp;rating=";
						
					$rateList = "<ul class='star-rating'>".
		   				"<li><a href='".$queryString."1' title='Rate this goal 1 out of 5' class='one-stars'>1</a></li>".
		   				"<li><a href='".$queryString."2' title='Rate this goal 2 out of 5' class='two-stars'>2</a></li>".
		   				"<li><a href='".$queryString."3' title='Rate this goal 3 out of 5' class='three-stars'>3</a></li>".
		   				"<li><a href='".$queryString."4' title='Rate this goal 4 out of 5' class='four-stars'>4</a></li>".
		   				"<li><a href='".$queryString."5' title='Rate this goal 5 out of 5' class='five-stars'>5</a></li>".
		   				"</ul>";
				}
			} else {
				$rateList = '&nbsp;&nbsp;<span style="color:#AAAAAA;">Log in to rate this goal</span>';
			}
			
			$fileLoc = $wwwroot."/files/goals/".$fileName;
			$fileSizeText = "";
			if (file_exists($fileLoc)) {
			 $fileSize = filesize($fileLoc)/(1024*1024);
			 $fileSizeText = '&nbsp;&nbsp;<span style="color:AAAAAA;">('.sprintf("%.2f", $fileSize)." MB)</span>";
			} 
			
			
			$text = '<p class="rating">&nbsp;&nbsp;<b><a href="/profile.php?name='.$name.'">'.$name."</a></b>".
				" - uploaded " .formatDate($uploaded) . 
				$fileSizeText. 
				"</p>".
				'<p class="rating">&nbsp;'.$rating."</p>".
				'<p class="rating">&nbsp;&nbsp;'.$comment."</p>".
				'<p class="rating">'.$rateList."</p>";
				
			if (($time - $uploaded) < 60 * 60 * 24 * 3) {
				$isNew = true;
			} else {
				$isNew = false;
			}
			?>

			<tr><td>
			<a name="<?= $goal_id ?>">
			<?
			$link = '<a style="text-decoration:none;" href="'.$directory.'/files/browseGoals.php?goal='.$goal_id.'">#'.$goal_id.'</a>';
			?>
			<?= getBoxTop($link." - ".$fileName, 30, $isNew, null) ?>
				<table width="100%">
					<tr>
						<td width="128" align="left" valign="top"><?= $imgLinked ?></td>
						<td align="left" valign="top"><?= $text ?></td>
						<td align="right" valign="top" nowrap width="10%">
							<table class="layouttable" height="96">
								<tr><td valign="top" align="right">
								<span class="grey-small"><?= getTextForGoalType($goalType) ?></span><br>
															<div class="forumcode" style="display:none;" id="fc<?= $goal_id ?>" border="1">[b]<?= $comment ?>[/b] - [size=9]Uploaded: <?= formatLongDate($uploaded) ?><br>
[url=<?= $directory ?>/files/browseGoals.php?goal=<?= $goal_id ?>&amp;animated=1]Rate this video here![/url][/size]<br/><br/>
[url=<?= $directory ?>/files/goals/<?= $fileName ?>][img]<?= $directory."/".$imgUrl ?>[/img][/url]<br/>
[size=9](click image to see video)[/size]</div>
								
								</td></tr>
								<tr><td valign="bottom" align="right"><span class="grey-small"><a href="javascript:toggleDiv('fc<?= $goal_id ?>')">forum code</a></span></td></tr>
							</table>
						</td>
					</tr>
				</table>
				<?= getBoxBottom() ?>
			</td></tr>
			<?
		}
	?>
	</table>
</td></tr></table>
<script language="Javascript 1.2"
type="text/javascript">
function toggleDiv(id)
{
	el = document.getElementById(id);
	if (el.style.display == 'none')
	{
		el.style.display = '';
		el = document.getElementById('more' + id);
	} else {
		el.style.display = 'none';
		el = document.getElementById('more' + id);
	}
}
</script>

<?= getOuterBoxBottomLinks($paging, $totalCountDisplay); ?>

	<?
} // end no files

?> 
<? } // not found ?>
<?
require('../bottom.php');

function getRating($rating, $votes, $isOverallRating) {
	$result = "";
	if ($votes != 1) {
		$s = "s";
	} else {
		$s = "";
	}
		
	if ($isOverallRating) {
		$title = 'style="cursor:help" title="Average rating of '.format2DigitsMax($rating).' from '.$votes.' vote'.$s.'"';
	} else {
		$title = 'style="cursor:help" title="Your rating"';
	}
	$star_empty = '<img src="/gfx/star_rating_empty.gif" '.$title.'>';
	$star_half = '<img src="/gfx/star_rating_half.gif" '.$title.'>';
	$star_full = '<img src="/gfx/star_rating_full.gif" '.$title.'>';
	
	if ($rating < 0.25) {
		 $result .= $star_empty;
	} else if ($rating < 0.75) {
		$result .= $star_half;
	} else {
		$result .= $star_full;
	}
	if ($rating < 1.25) {
		 $result .= $star_empty;
	} else if ($rating < 1.75) {
		$result .= $star_half;
	} else {
		$result .= $star_full;
	}
	if ($rating < 2.25) {
		 $result .= $star_empty;
	} else if ($rating < 2.75) {
		$result .= $star_half;
	} else {
		$result .= $star_full;
	}
	if ($rating < 3.25) {
		 $result .= $star_empty;
	} else if ($rating < 3.75) {
		$result .= $star_half;
	} else {
		$result .= $star_full;
	}
	if ($rating < 4.25) {
		 $result .= $star_empty;
	} else if ($rating < 4.75) {
		$result .= $star_half;
	} else {
		$result .= $star_full;
	}

	return $result;
}

function getTextForGoalType($type) {
	if ($type == 'A') return "Goal";
	if ($type == 'C') return "Compilation";
	if ($type == 'M') return "Game Scene";
	if ($type == 'O') return "Other";
}
?>
