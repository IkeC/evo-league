<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "checkPointsCountUp";

require ('./../variables.php');
require ('./../variablesdb.php');
require ('./../functions.php');
require ('./../top.php');

?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Check Points", ""); ?>

<?

if (!$isAdminFull) {
  echo "<p>Access denied.</p>";
} else {

  $sql = "Select name, ra2pes5 from $playerstable WHERE pes5games > 0 order by ra2pes5 desc";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $name = $row['name'];
    $ra2pes5 = $row['ra2pes5'];

    $sql_games = "SELECT * from $gamestable where deleted = 'no' " .
    "and (winner = '$name' or winner2 = '$name' or loser = '$name' or loser2 = '$name') " .
    "and season = '$season' AND teamLadder = 0 ".
    "ORDER BY game_id asc";
    $result_games = mysql_query($sql_games);
    $points = 0;
    echo "<p><b>$name</b><br>";
    while ($rowg = mysql_fetch_array($result_games)) {
      $game_id = $rowg['game_id'];
      $winner = $rowg['winner'];
      $loser = $rowg['loser'];
      $winner2 = $rowg['winner2'];
      $loser2 = $rowg['loser2'];
      $winpts = $rowg['winpoints'];
      $losepts = $rowg['losepoints'];
      $losepts2 = $rowg['losepoints2'];

      if (($name == $winner) || ($name == $winner2)) {
        $points = $points+$winpts;
      } else if ($name == $loser) {
        $points = $points-$losepts;
      } else if ($name == $loser2) {
        $points = $points-$losepts2;
      } else {
        echo "<p>INVALID: $game_id</p>";
      }
      
      echo $game_id . ": ";
      if ($points < 0) {
        echo '</p><p><span style="color:red;"><b>' . $points . ' !!!!!!!!!!!!</b></span></p><p>';
      } else {
        echo $points. "&nbsp;&nbsp;";
      }
    }
    echo "</p>";
  }
}
?>

<?= getOuterBoxBottom() ?>
<?

require ('./../bottom.php');
?>

