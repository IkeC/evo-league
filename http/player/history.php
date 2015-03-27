<?php

// this shows the history of a player when clicking 'show history' in a players profile
// most data is pulled from the weblm_history table

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "players";
$subpage = "history";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../sixserver/functions.php');
require('../top.php');

if (! empty($_GET['name'])) {
    $name = mysql_real_escape_string($_GET['name']);
} 

$sortby = "name ASC";
$sql = "SELECT * FROM $playerstable WHERE name = '$name' ORDER BY $sortby";
$result = mysql_query($sql);
$num = mysql_num_rows($result);
   if ($num > 0) {
	$row = mysql_fetch_array($result);
    $player_id = $row["player_id"];
	$name = $row["name"];
    $alias = $row["alias"];
    $nationality = $row['nationality'];
    $country = $row['country'];
	$approved = $row["approved"];
	if ($approved == "no") {
        $nameDisplay = "<font color='#FF0000'>".$name."</font>";
    } else {
    	$nameDisplay = $name;
    }
}
?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, $name), "") ?>

<table>
<tr><td width="600">

<? 
if (empty($name)) {
	echo "<p>No player specified!</p>";
} else if ($num == 0) {
	echo "<p>The player <b>$name</b> could not be found in the database.</p>";
} else { ?>
<?= getBoxTop("Player Profile", "", false, null); ?>
 <table class="layouttable">
    <tr>
      <td><b><?= $nameDisplay ?></b></td>
	  <td style="text-align:right;vertical-align:bottom;">
	  </td>
	</tr>
    
    <tr>
       <td colspan="3">
         <table width="100%" style="margin-top:10px;">
         <tr>
           <td width="28%">Nationality</td>
           <td colspan="3"><?php echo "<img src='$directory/flags/$nationality.bmp' align='absmiddle' border='1'> $nationality" ?></td>
         </tr>
       <? if (!empty($country) && $country != $nationality) { ?>
         <tr>
           <td width="28%">Location</td>
           <td colspan="3"><?php echo "<img src='$directory/flags/$country.bmp' align='absmiddle' border='1'> $country" ?></td>
         </tr>
       <? } ?>
        </table>
      </td>
    </tr>
 </table>
<?= getBoxBottom() ?>
<? } // end no player ?>
</td>
<td width="600">&nbsp;</td>
</tr></table>

<table>
<tr>
<?
	$seasons_sql = "SELECT * FROM $historytable WHERE player_id = '$player_id' ".
		"ORDER BY season DESC";
	$seasons_result = mysql_query($seasons_sql);
	if (mysql_num_rows($seasons_result) > 0) {
    
    ?>
    <td valign="top" width="600">
    <?

    while ($seasons_row = mysql_fetch_array($seasons_result)) {	
      
      $db_season = $seasons_row['season'];
      $position = $seasons_row['position'];
      
      $points = $seasons_row['points'];
      $games_sql = "select sum(games) as gamesCount from $historytable where player_id = '$player_id' ".
        "AND season = '$db_season'";
      $games_result = mysql_query($games_sql);
      $games_row = mysql_fetch_array($games_result);
      $games = $games_row['gamesCount'];
      $aggregate = $seasons_row['aggregate'];
      $wins = $seasons_row['wins'];
      $losses = $seasons_row['losses'];
      $draws = $seasons_row['draws'];
      $goals_for = $seasons_row['goals_for'];
      $goals_against = $seasons_row['goals_against'];
      $percentage = sprintf("%.2f", $wins / $games * 100);
      $avg_goals_for = sprintf("%.2f", $goals_for / ($games + $aggregate));
      $avg_goals_against = sprintf("%.2f", $goals_against / ($games + $aggregate));
      $ladder = $seasons_row['ladder'];
      
      $sql = "SELECT begindate, enddate from $seasonstable where season = '$db_season'";
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $begindate = $row['begindate'];
      $enddate = $row['enddate'];
      if (strcmp($position, "0") == 0) {
        $position = "unknown";
      }  
      if (!empty($ladder)) {
        $ladder .= "&nbsp;";
      }
      $title = "Ladder Season ".$db_season."&nbsp;&nbsp;<span style='font-weight:normal;font-size:9px;'>(".$begindate." - ".$enddate.")</span>";
      
   ?>
   
  <?= getBoxTop($title, "", false, null); ?>
       <table class="layouttable">             
      <tr>
        <td style="width:30%;padding-bottom:10px;white-space:nowrap;">
        <?= $ladder ?>position
        </td>
          <td class="td_profile" style="padding-bottom:10px;">
          <b><?= $position ?></b>
        </td>
          <td style="width:5%;">
        </td>
          <td colspan="2" style="text-align:right;padding-bottom:10px;">
          <? echo getImgForRank($position, "top"); ?>
        </td>
        <td></td>   
     </tr>
     <tr>
          <td style="width:30%;">
        Points
        </td>
          <td class="td_profile">
          <?= $points ?>
        </td>
          <td style="width:5%;">
        </td>
          <td style="width:30%;">
        Percentage	  
        </td>     
        <td class="td_profile">
         <?= $percentage ?> 	
        </td>
          <td style="width:5%;"></td>   
     </tr>
     <tr>
          <td>
        Games
        </td>
          <td class="td_profile">
          <?= $games ?>
        </td>
          <td></td>
          <td class="boxlink">
        Draws
        </td>     
        <td class="td_profile">
         <?= $draws ?> 	
        </td>
          <td></td>   
     </tr>
      <tr>
          <td>
        Won
        </td>
          <td class="td_profile">
          <?= $wins ?>
        </td>
          <td></td>
          <td class="boxlink">
        Lost
        </td>     
        <td class="td_profile">
         <?= $losses ?> 	
        </td>
          <td></td>   
     </tr>
     <tr>
          <td>
        Goals For
        </td>
          <td class="td_profile">
          <?= $goals_for ?>
        </td>
          <td></td>
          <td class="boxlink">
        Goals Against
        </td>     
        <td class="td_profile">
         <?= $goals_against ?> 	
        </td>
          <td></td>   
     </tr> 
     <tr>
          <td>
        Avg. Scored
        </td>
          <td nowrap class="td_profile">
          <?= $avg_goals_for ?>
        </td>
          <td></td>
          <td nowrap class="boxlink">
        Avg. Conceeded
        </td>     
        <td class="td_profile">
         <?= $avg_goals_against ?> 	
        </td>
          <td></td>   
     </tr>
  </table>

  <?= getBoxBottom() ?>
  <?
    }
  ?>
  </td>
  <?
  }
  ?>

</td>

<td valign="top" width="600">

<? 
  $seasons_sql = "SELECT sh.*, sp.name FROM six_history sh ".
    "LEFT JOIN six_profiles sp ON sp.id=sh.profileId ".
    "WHERE sh.playerId='$player_id' ". 
    "ORDER BY sh.season DESC";
    
	$seasons_result = mysql_query($seasons_sql);
	
	while($seasons_row = mysql_fetch_array($seasons_result)) {	
		
    $profileName = $seasons_row['name'];
		$db_season = $seasons_row['season'];
		$position = $seasons_row['position'];
		$points = $seasons_row['points'];
		$games = $seasons_row['games'];
		$wins = $seasons_row['wins'];
		$losses = $seasons_row['losses'];
		$draws = $seasons_row['draws'];
    $dc = $seasons_row['DC'];
    $games = $games+$dc;
		$percentage = sprintf("%.2f", $wins / $games * 100);
		
		$sql = "SELECT begindate, enddate from six_seasons where season = '$db_season'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$begindate = $row['begindate'];
		$enddate = $row['enddate'];
		if (strcmp($position, "0") == 0) {
			$position = "unknown";
		}  

		$title = "Sixserver Season ".$db_season."&nbsp;&nbsp;<span style='font-weight:normal;font-size:9px;'>(".$begindate." - ".$enddate.")</span>";
		
 ?>
 
<?= getBoxTop($title, "", false, null); ?>
     <table class="layouttable">             
	  <tr>
	      <td style="width:30%;padding-bottom:10px;">Profile <b><?= $profileName ?></b></td>
	      <td class="td_profile" style="padding-bottom:10px;">
        </td>
	      <td style="width:5%;">
        </td>
	      <td colspan="2" style="text-align:right;padding-bottom:10px;">
		  	<? echo getSixImgForRank($position, "top"); ?>
		  </td>
	      <td></td>   
	 </tr>
	 <tr>
	    <td style="width:30%;">
			Position
		  </td>
	      <td class="td_profile">
		  	<b><?= $position ?></b>
		  </td>
	      <td style="width:5%;">
		  </td>
	      <td style="width:30%;">
			Points	  
		  </td>     
		  <td class="td_profile">
			 <b><?= $points ?></b>
		  </td>
	      <td style="width:5%;"></td>   
	 </tr>
	 <tr>
	      <td>
			Games
		  </td>
	      <td class="td_profile">
		  	<?= $games ?>
		  </td>
	      
	      <td></td>   
	      <td>
			Draws
		  </td>
	      <td nowrap class="td_profile">
		  	<?= $draws ?>
		  </td>
	 </tr>
	  <tr>
	   <td>
			Wins
		  </td>
	      <td class="td_profile">
		  	<?= $wins ?>
		  </td>
	      <td></td>
        <td>
			Disconnects
		  </td>     
		  <td class="td_profile">
			 <?= $dc ?>
		  </td>
	      
	 </tr>
 	 <tr>
	      <td>
			Losses
		  </td>
	      <td class="td_profile">
		  	<?= $losses ?>
		  </td>
	        <td></td>
	      <td nowrap class="boxlink">
        Percentage
		  </td>     
		  <td class="td_profile">
			 <?= $percentage ?>
		  </td>
	      <td></td>   
	 </tr> 
</table>

<?= getBoxBottom() ?>
<? } ?>

</td>
</tr>
</table>

<?= getOuterBoxBottom() ?>

<? require('../bottom.php'); ?>
