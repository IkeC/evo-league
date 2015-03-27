<?php
require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$page = "leagues";
$subpage = "leagues";
?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> All Leagues", "") ?>
<table width="70%"><tr><td>
<? $columnsArray = array('Id', '', 'Name', 'Winner', 'Year', 'Status'); ?>
<?= getRankBoxTop("Leagues", $columnsArray); ?>

<?
$sql = "SELECT wlm.*, wa.profileImage, wp.name FROM weblm_leagues_meta wlm ".
  "LEFT JOIN weblm_awards wa ON wa.leagueId=wlm.leagueId ".
  "LEFT JOIN weblm_players wp ON wp.player_id=wa.playerId ".
  "ORDER BY wlm.leagueId DESC";
$result = mysql_query($sql);
while ($row = mysql_fetch_array($result)) {
  $leagueLink = '<a href="/leagues/league.php?id='.$row['leagueId'].'">'.$row['leagueName'].'</a>';
  if ($row['isActive'] <> 1) {
    $status = '<span class="grey-small">finished</span>';
  } else {
    $status = '<span class="darkgrey-small">ongoing</span>';
  }
  $year = '<span class="darkgrey-small">'.$row['year'].'</span>';
  $leagueImage = '';
  if (!is_null($row['profileImage'])) {
    $leagueImage = '<img border="0" src="/gfx/awards/'.$row['profileImage'].'" />';
  }
  $winner = '';
  if (!is_null($row['name'])) {
    $winner = '<a href="/profile.php?name='.$row['name'].'">'.$row['name'].'</a>';
  }
  
  echo '<tr class="row" height="33">';
  echo '<td width="30" align="right">'.$row['leagueId'].'</td>';
  echo '<td width="25" align="center">'.$leagueImage.'</td>';
  echo '<td>'.$leagueLink.'</td>';
  echo '<td>'.$winner.'</td>';
  echo '<td width="50">'.$year.'</td>';
  echo '<td width="100">'.$status.'</td>';
  echo '</tr>';
}

?>
<?= getRankBoxBottom("Games", $columnsArray); ?>
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?
require('../bottom.php');
?>

