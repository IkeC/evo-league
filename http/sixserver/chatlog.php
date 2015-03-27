<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "index";
$subpage ="chatlog";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

if (!empty($_GET['f'])) {
	$filter = mysql_real_escape_string($_GET['f']);
} else if (!empty($_POST['f'])) {
	$filter = mysql_real_escape_string($_POST['f']);
} else {
	$filter = "";
}

if (!empty($_GET['ago'])) {
	$ago = mysql_real_escape_string($_GET['ago']);
} else if (!empty($_POST['ago'])) {
	$ago = mysql_real_escape_string($_POST['ago']);
} else {
	$ago = 1;
}
?>

<?= getOuterBoxTop($leaguename.getRaquo()." Chatlog", "") ?>

<?
$timespanAgo = 60*60*24*$ago;
$dateString = date('Y_n_j', time()-$timespanAgo);

echo "<p>/var/log/sixserver/sixserver.log.".$dateString."</p>";
EchoFile("/var/log/sixserver/sixserver.log.".$dateString, $filter);
echo "<p>/var/log/sixserver/sixserver.log</p>";
EchoFile("/var/log/sixserver/sixserver.log", $filter);

?>
<?= getOuterBoxBottom() ?>

<?
require('../bottom.php');

function EchoFile($logFile, $filter) {
  $handle = fopen ($logFile, "r");
  while (!feof($handle)) {
     $line = fgets($handle);
     if (stristr($line, "[CHAT]")) {
      if (strlen($filter) == 0 || stristr($line, $filter)) {
        $line = str_replace("[CHAT] ", "", $line);
        $line = str_replace("+0200 [MainService,", " [", $line);
        echo nl2br(htmlspecialchars($line));
      }
    }
  }	
  fclose($handle);
}
?>
