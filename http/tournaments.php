<?php

// the tournaments page. new tournaments can be added/stated/finished by adding rows or editing fields
// in the weblm_tournaments page. if called without the 'cup' GET parameter the overview will show, if a
// valid parameter is supplied (eg. http://www.yoursite/tournaments.php?cup=evocup1) the 
// tournament detail page is shown. Sample data from evo-league is in the db, don't forget to delete.

// note the different states for tournaments:

// planned: 
// weblm_tournaments.startDate undefined 
// weblm_tournaments.forumLink will be shown on the front page and is supposed to link to a sign-up thread   
// cup name not linked
// fill the trophyImage and extension fields to show the cup image

// running: 
// .endDate undefined, .startDate defined, .getParam defined
// forumLink is supposed to link to a thread where people report results
// Cup name is a link with the .getParam as GET-parameter to link to the cup page
// One of the defined users in .uploaders should have uploaded a cup image by now!

// finished:
// .endDate defined 
// Be sure to fill the .firstPlace, .secondPlace and .thirdPlace fields now
// for the user in .firstPlace the image defined in .profileImage will show in his profile

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = 'tournaments';
$subpage = "";
require('./variables.php');
require('./variablesdb.php');
require('./functions.php');
require('./top.php');

?> 

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Tournaments", "") ?>

<?
if (empty($_GET['cup'])) { ?>
<table width="100%">
	<tr>
	<td width="50%" valign="top">
	<?= getBoxTop("Tournaments", "", false, null); ?>
		<table class="layouttable" width="100%">
		<tr><td colspan="2" style="padding-bottom:15px;">
		<p>These are the present and past tournaments played on <?= $leaguename ?>.<br><b>Click the Cup name</b> 
			to see the schedule and standings of a Cup.</p>
		</td></tr>
		
	<?
    $sql = "SELECT * from $tournamenttable order by id desc";
    $result = mysql_query($sql);

    while ($row = mysql_fetch_array($result)) {
        $cupName = $row['cupName'];
        $getParam = $row['getParam'];
        $startDate = $row['startDate'];
        $endDate = $row['endDate'];
        $forumLink = $row['forumLink'];
        $trophyImage = $row['trophyImage'];

        $firstPlaceDisplay = getPlaceDisplay($row['firstPlace'], true);
        $secondPlaceDisplay = getPlaceDisplay($row['secondPlace'], false);
        if (!empty($row['thirdPlace'])) {
        	$thirdPlaceDisplay = getPlaceDisplay($row['thirdPlace'], false);
        }

        $updateDate = $row['updateDate'];

        $timespan = getTimespan($startDate, $endDate);
        $planned = empty($startDate);
        $running = !empty($startDate) && empty($endDate);
        $finished = !empty($startDate) && !empty($endDate);

        if (!$planned) {
            $nameHtml = '<a href="/tournaments.php?cup=' . $getParam . '">' . $cupName . '</a>';
        } else {
            $nameHtml = $cupName;
        } 

        if (!empty($trophyImage) && file_exists("./Cup/" . $trophyImage)) {
            $trophyHtml = '<img  src="./Cup/' . $trophyImage . '" />';
        } else {
            $trophyHtml = "";
        } 

        if ($planned) {
            $lineHtml = '<b><a href="/' . $forumLink . '">Sign up for this tournament!</a></b>';
        } else if ($running) {
            $lineHtml = '<span class="size11">Last schedule update: ' . formatAgoSpan($updateDate) . ' ago</span>';
        } else if ($finished) {
            $lineHtml = 'Winner: ' . $firstPlaceDisplay . '</td></tr><tr><td colspan="2"><span class="size11">2nd: ' . $secondPlaceDisplay;
            if (!empty($thirdPlaceDisplay)) {
             $lineHtml .= '&nbsp;&nbsp;3rd: '. $thirdPlaceDisplay;
            }
            $lineHtml .= '</span>';
        } 

        ?>
		<tr>
			<td align="center" style="vertical-align:bottom;width:50px;padding-top:3px;padding-bottom:10px;"><?php echo $trophyHtml ?></td>
			<td valign="top" style="width:550px;">
				<table style="width:450px;">
				<tr>
					<td valign="middle" style="width:250px;">
						<b class="boxlink"><?= $nameHtml ?></b>
					</td>
					<td align="right" valign="middle" style="width:300px;font-size:11px;"><?php echo $timespan ?></td>
				</tr>
				<tr>
					<td colspan="2" class="blacklink">
						<?php echo $lineHtml ?>					
					</td>
				</tr>
				</table>
			</td>
		</tr>
<?php
    } // while
?>
	</table>
</td>
</tr>
<?= getBoxBottom() ?>
</td>
<td width="50%" valign="top">
	<?= getBoxTop("Mini Tournaments", "", false, null); ?>
	<table class="layouttable" width="100%">
		<tr><td colspan="2" style="padding-bottom:15px;">
		<p>Move the mouse over an image to get info 
		on a tournament, click it to go to the tournament forum thread, or visit the <a href="/forum/viewforum.php?f=35">Mini Tournaments forum</a> to see them all. 
		</td></tr>
	</table>
	<table>
<?
	$sql = "SELECT * from $minitournamenttable ORDER BY ID ASC";
	$result = mysql_query($sql);
	$linksArray = array();
	while ($row = mysql_fetch_array($result)) {
		$playerId = $row['PLAYER_ID'];
		$link = $row['LINK'];
		if (!array_key_exists($playerId, $linksArray)) {
			$linksArray[$playerId] = array($link);
		} else {
			$linkArray = $linksArray[$playerId];
			$linkArray[] = $link;
			$linksArray[$playerId] = $linkArray;
		}		
	}
	
	$playersArray = array();
	$keys = array_keys($linksArray);
	foreach ($keys as $key) {
		$count = count($linksArray[$key]);
		$playersArray[] = array($count, $key); 
	}
	arsort($playersArray);
	$oldCount = 0;
	$pos = 1;
	$cur = 0;
	foreach ($playersArray as $player) { 
		$cur++;
		$count = $player[0];
		$playerId = $player[1];
		
		if ($count != $oldCount) {
            $pos = $cur.".";
	        $oldCount = $count;
        }
        else {
            $pos = ".";
        }	
        $sql = "SELECT name FROM $playerstable WHERE player_id = '$playerId'";
	    $result = mysql_query($sql);
	    $row = mysql_fetch_array($result);
	    $playerDisplay = getPlayerLink($row['name']);
		$linkArray = $linksArray[$playerId];
		$imgLine = "";
		$img = '<img src="/gfx/awards/'.$gfx_mini_tournament_prefix.'1.gif" border="0">';
		foreach($linkArray as $link) {
			$topicId = substr($link, strrpos($link, "=") + 1);
			
			$sql = "SELECT topic_title from $forumtopicstable where topic_id = '$topicId'";
			$result = mysql_query($sql);
			$title = "";
			if (mysql_num_rows($result) > 0) {
				$row = mysql_fetch_array($result);
				$title = $row['topic_title'];
			}
			$sql = "SELECT * from $minitournamenttable ORDER BY ID ASC";
			
			$imgLine .= '<a style="cursor:help;padding-right:5px;" href="'.$link.'" title="'.$title.'" border="0">'.$img.'</a>';
		}
		
?>
	<tr>
		<td align="right"><?= $pos ?></td>
		<td><?= $playerDisplay ?></td>
		<td align="left"><?= $imgLine ?></td>
	</tr>
<?
	}
	
?>	
</table>
<?= getBoxBottom(); ?>
</td>
</tr></table>
<?php
} else {  ?>
<table>
	<tr><td>
	<?
    $getParam = mysql_real_escape_string($_GET['cup']);
    $sql = "select * from $tournamenttable where getParam='$getParam'";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
	$tournamentId = $row['id'];
    $cupName = $row['cupName'];
    $forumLink = $row['forumLink'];
    $imageFilename = $row['imageFilename'];
    $updateDate = $row['updateDate'];
    $extension = $row['extension'];
    $getParam = $row['getParam'];
    $startDate = $row['startDate'];
    $endDate = $row['endDate'];
    $trophyImage = $row['trophyImage'];
    $firstPlace = $row['firstPlace'];
    $secondPlace = $row['secondPlace'];
    $thirdPlace = $row['thirdPlace'];

    $timespan = getTimespan($startDate, $endDate);

	$linksArray[] = array($forumLink, 'show_game_reports', 'show game reports'); 
	$filename = $imageFilename . "." . $extension;
	$hasImg = false;
	if (file_exists("./Cup/".$filename)) {
		 $hasImg = true;
	?>

<?= getBoxTop($cupName." ".$timespan, "", false, $linksArray); ?>
<table class="layouttable">
<tr>
	<td style="padding-bottom:15px;">
 		Image updated <?= formatDate($updateDate); ?> (<?= formatAgoSpan($updateDate) ?> ago)
	</td>
</tr>
<tr>
 	<td colspan="2" ><img src="./Cup/<?= $filename ?>" style="margin-bottom:20px;" border="1"/></td>
</tr>
</table>
<?= getBoxBottom() ?>
<?
	} // file exists

	$sql = "SELECT * FROM $tournamentimagestable WHERE tid = '$tournamentId' order by slot asc";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$slotId = $row['slot'];
		$description = $row['description'];
		$user = $row['user'];
		$updateDate = $row['updateDate'];
		if (!empty($updateDate)) {
			$updated = formatDate($updateDate); 
		} else {	
			$updated = "";
		} 
		$ext = $row['ext'];
		$filename = $imageFilename."_".$slotId.".".$ext;
		// echo "<p>".$filename."</p>";
		if (file_exists("./Cup/".$filename)) {
			$hasImg = true;
?>
<?= getBoxTop($description, "", false, null); ?>
<table class="layouttable">
<a name="<?= $slotId ?>"></a>
<tr>
	<td style="padding-bottom:15px;">
 		Image updated <?= $updated ?> by <?= $user ?> (<?= formatAgoSpan($updateDate) ?> ago)
	</td>
</tr>
<tr>
 	<td colspan="2"><img src="./Cup/<?= $filename ?>" style="margin-bottom:20px;" border="1"/></td>
</tr>
</table>
</td></tr>
<?= getBoxBottom() ?>
<?
		 } // file exists
	} // while
?>
</td></tr></table>
 <?php 
	if (!$hasImg) {
		echo doParagraph("Sorry, no further info yet!".$back);
	}
}
?> 
<?= getOuterBoxBottom() ?>
<?php

function getTimespan($startDate, $endDate)
{
    if (empty($startDate)) {
        $timespan = "(planned)";
    } else if (empty($endDate)) {
        $timespan = "(started $startDate)";
    } else {
        $timespan = "($startDate - $endDate)";
    } 
    return $timespan;
} 

function linkNameBold($name) {
    return '<a style="font-weight:bold;" href="/profile.php?name=' . $name . '">' . $name . '</a>';
} 

function linkName($name) {
    return '<a style="font-size:11px;" href="/profile.php?name=' . $name . '">' . $name . '</a>';
} 

function getPlaceDisplay($name, $bold) {
	$placeDisplay = "";
	$dash = '<span class="grey-small">&ndash;</span>';
	if (stristr($name, '~')) {
    	$nameArray = explode('~', $name);
    	
    	foreach ($nameArray as $placeName) {
    		if ($bold) {
    	 		$placeDisplay .= linkNameBold($placeName).$dash;
    		} else {
    	 		$placeDisplay .= linkName($placeName).$dash;
    		}
    	}
    	$placeDisplay = substr($placeDisplay, 0, strlen($placeDisplay)-strlen($dash));
    } else {
    	if ($bold) {
    		$placeDisplay = linkNameBold($name);
    	} else {
    		$placeDisplay = linkName($name);
    	}
    }
    return $placeDisplay;
}
require('./bottom.php');
?>