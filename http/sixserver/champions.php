<?php

// this page will show past award winners for all seasons

   header("Cache-Control: no-cache");
   header("Pragma: no-cache");
   $page = "index";
   $subpage = 'sixchampions';
   require('../variables.php');
   require('../variablesdb.php');
   require('../functions.php');
   require('./functions.php');
   
   require('../top.php');
?> 

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table class="layouttable">
<tr><td width="50%" valign="top">

<?

$sql = "SELECT season FROM six_stats";
$row = mysql_fetch_array(mysql_query($sql));
$sixSeason = $row[0];
$overall = array();

for ($cur = $sixSeason-1; $cur > 0; $cur--) {

    $season_sql = "SELECT begindate, enddate FROM six_seasons WHERE season=$cur";
    $season_row = mysql_fetch_array(mysql_query($season_sql));
	
	$sql = "SELECT sh.*, wp.name, wp.approved FROM six_history sh ".
    "LEFT JOIN weblm_players wp ON wp.player_id=sh.playerId ".
    "WHERE sh.season=$cur AND sh.position BETWEEN 1 AND 6 ORDER BY sh.position ASC";
	    $result = mysql_query($sql);
            
	    $boxtitle = "Sixserver Season ".$cur."&nbsp;&nbsp;<span style='font-weight:normal;font-size:10px;'>(".$season_row['begindate']." - ".$season_row['enddate'].")</span>";
	   		
	    echo getBoxTop($boxtitle, "", false, null);
	?>
	    <table class="layouttable">
	    <tr><td width="50%">
	        <table class="layouttable">
	        	
	<?
		$count = 0;
		$rows = mysql_num_rows($result);
	    while ($row = mysql_fetch_array($result)) {
	        $count++;
	        $position = $row['position'];
	        $name = $row['name'];
	        $points = $row['points'];
	        $nameClass = colorNameClass($name, $row['approved']);
	        $imgRank = getSixImgForRank($position, "");
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
			echo getSixImgForPosAndSeasonArray($position, $seasonArray, "bottom");
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
require('../bottom.php');


?>
