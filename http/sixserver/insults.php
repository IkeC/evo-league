<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "index";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$pid = "";
if (!empty ($_GET['pid'])) {
	$pid = mysql_real_escape_string($_GET['pid']);
}

$d = 7;
if (!empty ($_GET['d'])) {
	$d = mysql_real_escape_string($_GET['d']);
}

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Insults", ""); ?>
<?
if (!empty($pid)) {
  
  $sql = "SELECT name FROM weblm_players WHERE player_id=$pid";
  $result = mysql_query($sql);
  $row = mysql_fetch_array($result);
  $username = $row['name'];
  
  $sql = "SELECT name FROM six_profiles WHERE user_id=$pid";
  $result = mysql_query($sql);
  $grep = "";
  while ($row = mysql_fetch_array($result)) {
    $name = $row['name'];
    if (!empty($grep)) {
      $grep = $grep."\|(".$name.")";
    } else {
      $grep = "(".$name.")";
    }
  }  
  if (!empty($grep)) {
    echo "<p><b>$username</b> offending words (~ last ".$d." days)</p>";
    $cmd = 'find /var/log/sixserver/sixserver.log* -type f -printf \'%T@ %p\n\' | sort -k 1n | sed \'s/^[^ ]* //\' | tail -n '.($d+1).'|xargs cat|grep -i "'.$badWordsGrep.'"|grep -i "'.$grep.'"';
    $subject = shell_exec($cmd);
    foreach(preg_split("/((\r?\n)|(\r\n?))/", $subject) as $line){
      $displayLine = substr($line, 0, 25).substr($line, strpos($line, "[CHAT]"));
      echo $displayLine."<br>";
    }
  }
}
?>
<?= getOuterBoxBottom() ?>
<?
	require('../bottom.php');
?>