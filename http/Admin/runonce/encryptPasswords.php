<?php
$page = "encrytPasswords";

require ('../../variables.php');
require ('../../variablesdb.php');
require_once ('../functions.php');
require_once ('./../../functions.php');
require ('../../top.php');

$log = new KLogger('/var/www/yoursite/http/log/encrypt/', KLogger::INFO);

$startId = $_GET['id'];

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Encrypt Passwords", ""); ?>
<?

$sql = "SELECT player_id, name FROM `weblm_players` where convert(pwd using 'utf8') like 'xxx%'"; 
$res = mysql_query($sql);
echo "<p>".$sql."</p>";
while ($row = mysql_fetch_array($res)) {
  $id = $row['player_id'];
  $name = $row['name'];
  
  $length = 10;

  $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
  $pwHash = password_hash($randomString, PASSWORD_DEFAULT);
  
  $msg = "<p>id=".$id." randomString=".$randomString." name=".$name."</p>";
  $log->LogInfo($msg);
  
  $sql = "UPDATE weblm_players set pwd='".$pwHash."' WHERE player_id=".$id;
  mysql_query($sql);
  $log->LogInfo($sql);
}

?>
<?= getOuterBoxBottom() ?>
<?


require ('../../bottom.php');
?>