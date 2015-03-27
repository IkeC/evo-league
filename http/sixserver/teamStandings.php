<?php

$page = "index";
$subpage = "sixteamstandings";

require_once ('../variables.php');
require_once ('../variablesdb.php');
require_once ('../functions.php');
require_once ('./functions.php');
require_once ('../top.php');

$left = $subNavText.getRaquo().getSubNavigation($subpage, null);
$right = "our time is <b>".date("m/d h:i a")." ".$timezone."</b>";

?>
<?= getOuterBoxTop($left, $right) ?>
<?
$columnTitlesArray = array ('Pos', 'Player', 'Player', 'Points', 'W', 'D', 'L', 'Percentage');
	$boxTitle = "Sixserver Team Standings";
?>
<p>Sixserver team standings are calulated over all team games within the last 90 days. The team must have played at least 5 games to be included.</p>
<table width="900"><tr><td>
<?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
<?
  $standingsArray = GetSixTeamStandingsArray();
	$oldPoints = -1;
  $pos = 1;
  $counter = 1;
  foreach ($standingsArray as $key => $value) {
    $playerIds = explode("-",$key);
    
    $sql = "SELECT name, nationality FROM weblm_players WHERE player_id=".$playerIds[0];
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    $name1 = $row['name'];
    $nationality1 = $row['nationality'];
    
    $sql = "SELECT name, nationality FROM weblm_players WHERE player_id=".$playerIds[1];
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    $name2 = $row['name'];
    $nationality2 = $row['nationality'];
    
		$points = $value['pt'];
		
		if ($oldPoints != $points) {
			$pos = $counter;
			$oldPoints = $points;
		} else {
      $pos = "";
    }
		
		$wins = $value['w'];
    if (is_null($wins)) { $wins = 0;}
		$losses = $value['l'];
    if (is_null($losses)) { $losses = 0;}
		$draws = $value['d'];
    if (is_null($draws)) { $draws = 0;}
    
    $games = $wins + $losses + $draws;
		
		if ($games > 0) {
			$percentage = $wins / $games;
    }
?>
	<tr<?

			if (strcmp($cookie_name, $name1) == 0 || strcmp($cookie_name, $name2) == 0) {
				echo " class='row_active'";
			} else {
				echo " class='row'";
			}
?>>
	   <td width="5%" style="text-align:right;"><? echo $pos."." ?></td>
	   <td width="15%"><? echo "<img class='imgMargin' src='$directory/flags/$nationality1.bmp' title='$nationality1' align='absmiddle' border='1'><a href='$directory/profile.php?name=$name1'>$name1</a>"; ?></td>
     <td width="15%"><? echo "<img class='imgMargin' src='$directory/flags/$nationality2.bmp' title='$nationality2' align='absmiddle' border='1'><a href='$directory/profile.php?name=$name2'>$name2</a>" ?></td>
	   <td width="10%" style="text-align:right;"><?= $points ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$wins" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$draws" ?></td>
	   <td width="7%" style="text-align:right;"><?php echo "$losses" ?></td>
	   <td width="9%" style="text-align:right;"><?php printf("%.2f", $percentage*100); ?></td>
	</tr>
<?
      $counter++;
      if ($counter % 50 == 0) {
        flush();
      }
    }
		
?>
<?= getRankBoxBottom() ?>

</td></tr></table>

<?= getOuterBoxBottomLinks($linksBottomLeft, $linksBottomRight) ?>
<?php
		require ('../bottom.php');
?>

