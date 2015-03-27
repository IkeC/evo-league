<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "access";

require ('./../variables.php');
require ('./../variablesdb.php');
require_once ('../functions.php');
require_once ('./functions.php');
require ('./../top.php');

$msgs = "";

$days = 5;
if (!empty($_POST['days'])) {
  $days = mysql_real_escape_string($_POST['days']);
} 
elseif (!empty($_GET['days'])) {
  $days = mysql_real_escape_string($_GET['days']);
}

if (!empty($_POST['user'])) {
  $userName = mysql_real_escape_string($_POST['user']);
	$sql = "SELECT player_id as userId from $playerstable where name = '$userName'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) != 1) {
		echo '<p>Invalid Username: '.$userName."</p>";
	}
	else {
    $row = mysql_fetch_array($result);
    $userId = $row['userId'];	
    ?>
    <?= getOuterBoxTop("","") ?>
    <?
		$strictness = "";
    if ($_POST['submit'] == 'Lenient') {
      $strictness = "lenient";
    } elseif ($_POST['submit'] == 'Strict') {
      $strictness = "strict";
    }
    $type = 'B';
    $expireDays = 0;
    $expireDate = "";
    $reason = "Multi-Account";
    $date = time();
    $sql = "INSERT INTO $playerstatustable (userId, userName, type, active, date, expireDate, forumLink, reason, strictness) ".
        "VALUES ('$userId', '$userName', '$type', 'Y', '$date', '$expireDate', '$forumLink', '$reason', '$strictness')";
    mysql_query($sql);
    $sql = "UPDATE $playerstable SET approved = 'no' WHERE player_id = '$userId'";
    $result = mysql_query($sql);
    ?>
    <p>Banned player (<?= $strictness ?>): <b><a href="#<?= $userId ?>"><?= $userName ?></a></b></p>
    <?
   
    ?>
    <?= getOuterBoxBottom() ?>
    <?
  }
}

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Access", ""); ?>
<?
if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {
?>

<style type="text/css">
  .specialtable td {
    border-bottom: 1px solid black;
    margin: 0;
    padding: 0;
    border-collapse: collapse; 
  }
</style>

<table class="layouttable" width="600">
<?
$fishy = GetFishyAccessRows($days);
$first = true;
foreach ($fishy as $fishyRow) {
  $sql = "SELECT player_id, joindate, approved, rejectReason, serial6, versions, nationality FROM $playerstable WHERE name = '".$fishyRow['user']."'";
  $row = mysql_fetch_array(mysql_query($sql));
  $style = colorNameClass("", $row['approved']);
  if ($fishyRow['fishyFlag'] <> 1) {
    if (!$first) {
      echo "<tr><td colspan='8'>&nbsp;</td></tr>";
    } else {
      $first = false;
    }
  }
  echo '<tr>';
  
  echo "<td>".$fishyRow['logType']."</td><td><a name=".$row['player_id']." />".$fishyRow['ip']."</td>".
    "<td><img src='$directory/flags/".$row['nationality'].".bmp' width='18' height='15' border='1'></td>".
    "<td $style><a target='_new' href='/profile.php?name=".$fishyRow['user']."'>".$fishyRow['user']."</a></td>";
  echo '<td class="darkgrey-small">';
  $prevBans = 0;
  $sql = "SELECT * FROM $playerstatustable WHERE type='B' AND userId=".$row['player_id'];
  $resultstatus = mysql_query($sql);
  while ($rowstatus = mysql_fetch_array($resultstatus)) {
    if ($rowstatus['active'] == 'Y') {
      echo '<img src="/gfx/status_ban.gif" style="vertical-align:middle;">&nbsp;'.$rowstatus['reason'];
      if (!empty($rowstatus['strictness'])) {
        echo "&nbsp;(".$rowstatus['strictness'].")";
      }
    } else {
      $prevBans++;
    }
  }
  
  echo "</td>";
  ?>
    <td>
      <form name="form1" method="post" action="<?= $_SERVER['PHP_SELF'] ?>" />
        <input type="hidden" name="user" value="<?= $fishyRow['user'] ?>" />
        <input type="hidden" name="days" value="<?= $days ?>" />
        <input type="submit" name="submit" value="Lenient">
        <input type="submit" name="submit" value="Strict">
      </form>
    </td>
<?  
  echo "<td>Acc: ".formatLongDate($fishyRow['accesstime'])."</td>".
	  "<td>#".$row['player_id']."</td>";
  echo '<td>Join: '.formatLongDate($row['joindate']).'</td>'.
	  
	  '<td>'.$row['rejectReason'].'</td>'.
    '<td>'.$row['serial6'].'</td>'.
    '<td>Bans: '.$prevBans.'</td>'.
    '<td>'.getVersionsImages($row['versions']).'</td>'.
    '</tr>';
}

?>
</table>
<? 
} 
?>
<?= getOuterBoxBottom() ?>
<?


require ('./../bottom.php');
?>
