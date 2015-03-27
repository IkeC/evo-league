<?php
$page = "encodePassword";

require ('../../variables.php');
require ('../../variablesdb.php');
require_once ('../functions.php');
require_once ('./../../functions.php');
require ('../../top.php');

$log = new KLogger('/var/www/yoursite/http/log/encrypt/', KLogger::INFO);

$startId = $_GET['id'];
if (empty($startId)){ 
  die();
}
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Encrypt Passwords", ""); ?>
<?

$sql = "SELECT player_id from weblm_players where player_id=$startId order by player_id desc";
$res = mysql_query($sql);
echo "<p>".$sql."</p>";
while ($row = mysql_fetch_array($res)) {
  $id = $row['player_id'];
  
  // get old password
  $sqlOld = "SELECT passworddb,name from weblm_players_old where player_id=".$id;
  $resOld = mysql_query($sqlOld);
  while ($rowOld = mysql_fetch_array($resOld)) {
    $pwdOld = $rowOld['passworddb'];
    $pwHash = password_hash($pwdOld, PASSWORD_DEFAULT);
    $name = $rowOld['name'];
    // $verify = password_verify($pwdOld, $pwHash);
    $msg = "<p>id=".$id." pwdOld=".$pwdOld." name=".$name."</p>";
    // echo $msg;
    $log->LogInfo($msg);
    
    $sql = "UPDATE weblm_players set pwd='".$pwHash."', hash6='' WHERE player_id=".$id;
    // echo "<p>".$sql."</p>";
    mysql_query($sql);
    // $log->LogInfo($sql);
    
    /*
    $sqlNew = "SELECT pwd FROM weblm_players where player_id=".$id;
    $resNew = mysql_query($sqlNew);
    while ($rowNew = mysql_fetch_array($resNew)) {
      $hashNew = $rowNew['pwd'];
      $verify = password_verify($pwdOld, $hashNew);
      $msg = "<p>pwdOld=".$pwdOld." name=".$name." verify=".$verify." (reloaded)</p>";
      $log->LogInfo($msg);
      echo $msg;
    }
    */
  }
}

?>
<?= getOuterBoxBottom() ?>
<?


require ('../../bottom.php');
?>