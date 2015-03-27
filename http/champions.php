<?php

// this page will show past award winners for all seasons

   header("Cache-Control: no-cache");
   header("Pragma: no-cache");
   $page = "games";
   $subpage = 'champions';
   require('./variables.php');
   require('./variablesdb.php');
   require('./functions.php');
   require('./top.php');
?> 

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table class="layouttable">
<tr><td width="50%" valign="top">

<?

$overall = array();

for ($cur = $season-1; $cur > 0; $cur--) {

    $season_sql = "SELECT begindate, enddate, ladders from $seasonstable where season = $cur";
    $season_row = mysql_fetch_array(mysql_query($season_sql));
	$ladders = explode(" ", $season_row['ladders']);
	
	
	foreach ($ladders as $combinedLadders) {
		
		$combinedLaddersArray = explode("/", $combinedLadders);
		$ladder = $combinedLaddersArray[0];
		
		$sql = "SELECT * FROM $historytable where season = $cur and ladder = '$combinedLadders' and position between 1 and 6 order by position asc";
	    $result = mysql_query($sql);
	    $boxtitle = 
	    	"Season ".$cur."&nbsp;&nbsp;<span style='font-weight:normal;font-size:10px;'>(".$season_row['begindate']." - ".$season_row['enddate'].")</span>";
	   		
	    echo getBoxTop($boxtitle, "", false, null);
	?>
	    <table class="layouttable">
	    	<tr>
	        	<td style="padding-bottom:10px;">Ladder - <b><?= $combinedLadders ?></b></td>
	        	<td style="padding-bottom:10px;" align="right">
	        	<? foreach ($combinedLaddersArray as $combinedLadder) {
	        		// echo "getVersionsForLadder($combinedLadder):".getVersionsForLadder($combinedLadder);
	        		
	        		echo getVersionsImages(getVersionsForLadder($combinedLadder));
	        		} 
	        	?>
	        	&nbsp;</td>
	        </tr>
	    <tr><td width="50%">
	        <table class="layouttable">
	        	
	<?
		$count = 0;
		$rows = mysql_num_rows($result);
	    while ($row = mysql_fetch_array($result)) {
	        $count++;
	        $position = $row['position'];
	        $name = $row['player_name'];
	        $points = $row['points'];
	        $player_sql = "SELECT approved from $playerstable where name = '$name'";
	        $player_row = mysql_fetch_array(mysql_query($player_sql));
	        $nameClass = colorNameClass($name, $player_row['approved']);
	        $imgRank = getImgForRank($position, "");
	        $cup_points = 7 - $position;
	        if (array_key_exists($name, $overall)) {
	            $overall_player = $overall[$name];
	            $overall_player['points'] += $cup_points;
	            $overall_player['seasonpos'] = addSeasonAndPos($overall_player['seasonpos'], $cur, $position);
	            $overall[$name] = $overall_player;
	        }
	        else {
	            $overall_player = array();
	            $overall_player['points'] = $cup_points;
	            $overall_player['seasonpos'] = addSeasonAndPos(array(), $cur, $position);
	            $overall[$name] = $overall_player;
	        }
	
	        echo "<tr style='height:19px;'>".
	        	"<td align='center'>".$imgRank ."</td>".
				"<td width='80%' $nameClass><a href='$directory/profile.php?name=$name' title='view profile'>$name</a>".
	            "</td><td align='right' nowrap><b>".$points."</b>&nbsp;pts</td></tr>";
			
	        
		        if ($count == 3) {
					echo '</table></td><td width="50%"><table class="layouttable">';
		        }
	        
	
	    }
	?>
	        </td></tr></table>
	    </td></tr></table>
	<?
	    echo getBoxBottom();
	} // for each ladder
}
?>


</td>
<td width="50%" align="center" valign="top">

<?= getBoxTop("Hall of Fame", "", false, null); ?>
<table><tr><td>
<table>
<?
    arsort($overall);
    $cur = 1;
    $pos = 1;
    $oldpoints = "";
    foreach ($overall as $currentname => $player_array) {
        $player_sql = "SELECT approved from $playerstable where name = '$currentname'";
        $player_row = mysql_fetch_array(mysql_query($player_sql));
        $nameClass = colorNameClass($name, $player_row['approved']);
        $points = $player_array['points'];
        $seasonpos = $player_array['seasonpos'];
        if ($points != $oldpoints) {
            $pos = $cur;
        }
        else {
            $pos = "";
        }
        $oldpoints = $points;
        $cur++;
		$points = $player_array['points'];
		if ($points == 1) {
		    $pts = "pt";
		}
		else {
			$pts = "pts";
		}
    	echo '<tr>'.
		'<td align="right">'.$pos.'.</td>'.
		"<td $nameClass><a href='$directory/profile.php?name=$currentname' title='view profile'>$currentname</a></td>".
		"<td style='padding-left:10px;padding-right:20px;'><b>".$points."</b>&nbsp;".$pts."</td>".
		'<td>';
		foreach($seasonpos as $position => $seasonArray) {
			echo getImgForPosAndSeasonArray($position, $seasonArray, "bottom");
		}
		echo '</td>'.
		'</tr>';
	}
?>
</table>
</td></tr></table>
<?= getBoxBottom() ?>	
</td>
</tr></table>
<?= getOuterBoxBottom() ?>
<?php
require('./bottom.php');


?>
