<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "index";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$type = "";
if (!empty ($_GET['type'])) {
	$type = mysql_real_escape_string($_GET['type']); // pt2
}

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Sixserver Points Distribution", ""); ?>
<p>This is the current Sixserver points distribution grid. Draws are counted as half a win.</p>
<?

  $i = 0;
  echo '<table style="border-collapse:collapse;">';
  while ($i <= 200) {
    echo "<tr>";
    $j = 0;
    while ($j <= 200) {
      if ($type == "pt2") {
        $pt = getSixserverPoints2($j,0,$i);
      } else {
        $pt = getSixserverPoints($j,0,$i);
      }
      echo "<td style=\"border:1px solid #AAAAAA; padding: 3x 3px 3px 3px;white-space:nowrap;border-collapse:collapse;\">Wins: $j<br>Losses: $i<br>Points: <b>".$pt."</b></td>";
      $j = $j+1;
    }
    echo "</tr>";
    $i = $i+1;
  }
  echo "</table>";
?>

<?= getOuterBoxBottom() ?>
<?
	require('../bottom.php');
?>