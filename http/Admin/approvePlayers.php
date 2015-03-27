<?php

// this displays an admin table where you can check the details of a signed up player
// against info of other players 

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "approvePlayers";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./functions.php');
require('./../top.php');

$ids = $_POST['ids'];
$rejectIds = $_POST['rejectIds'];
$rejectReasonsText = $_POST['rejectReasonText'];
$rejectReasonsSelect = $_POST['rejectReasonSelect'];

$approveDefault = "<p>Select players to approve.</p>";
$approveMsg = $approveDefault;

$logToday = "log_".date("Y-m-d").".txt";
$logYesterday = "log_".date("Y-m-d", time() - 60 * 60 * 24).".txt";

$boxTitle = 'Approve Players - <a target="_new" href="/log/join/'.$logToday.'">Today\'s log</a> - <a target="_new" href="/log/'.$logYesterday.'">Yesterday\'s log</a>';

?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Approve Players", "") ?>
<?

if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {

if (sizeof($rejectReasonsSelect) > 0) {
  $approveMsg = "";

  for ($i = 0; $i < sizeof($rejectReasonsSelect); $i++) {
    
    $id = $ids[$i];
    $rejectId = $rejectIds[$i];
    $rejectReasonText = $rejectReasonsText[$i];
    $rejectReasonSelect = $rejectReasonsSelect[$i];

    if (!empty($id)) {
      $approveMsg .= "<p>";
      $sql = "SELECT mail, name, signup from $playerstable where player_id = '$id'";
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $toAddress = $row['mail'];
      $name = $row['name'];
      $link = "http://www.yoursite/activate.php?id=".$row['signup'];
      $res = sendActivationLinkMail($toAddress, $name, $link);
      $approveMsg .= "Activation mail: <b>".$name."</b> - Address: ".$toAddress." - Result: ".$res."<br>";
      $update_sql = "UPDATE $playerstable SET signupSent=1 WHERE player_id = '".$id."'";
      $update_result = mysql_query($update_sql);
      $approveMsg .= "</p>";
    } elseif (!empty($rejectId)) {
      if (!empty($rejectReasonSelect)) {
        $reason = $rejectReasonSelect;
        if (!empty($rejectReasonText)) {
          $reason .= " (".$rejectReasonText.")";
        }
      } else {
       $reason = $rejectReasonText;
      }
      $approveMsg .= "<p>";
      $sql = "UPDATE $playerstable SET rejected=1, rejectReason='".$reason."' WHERE player_id = '$rejectId'";
      $result = mysql_query($sql);
      $approveMsg .= "Rejecting: <b>".$rejectId."</b> - Reason: ".$reason." - Result: ".$result."<br>";
      $approveMsg .= "</p>";
    }
  }
}

$sql="SELECT * FROM $admintable WHERE name = '$username' AND password = '$password'";
$result = mysql_query($sql,$db);
$number = mysql_num_rows($result);

$columnTitlesArray = array('ID', 'User', 'Pass', 'Email', 'Players', 'Access', '','Reject');

If ($approveMessage != $approveDefault) {
	$new = true;
} else {
	$new = false;
}
?>
<form method="post" action="approvePlayers.php">
<?= getBoxTop($boxTitle, "", $new, null); ?>

<table class="layouttable">
<tr width="100%">
<td width="90%"><?= $approveMsg ?></td>
<td width="10%" align="right" valign="middle">
	<input type="Submit" name="submit" value="Submit" class="width200">
</td>
</tr>
</table>
<?= getBoxBottom() ?>

<?= getRankBoxTop("Unapproved Players", $columnTitlesArray); ?>

<?

$backSpan = 60*60*24*14;
$sql = "SELECT * from $playerstable where approved='no' AND signupSent=0 AND rejected=0 and UNIX_TIMESTAMP() - joindate < $backSpan ".
       "ORDER BY joindate ASC limit 0, 20";
       
$result = mysql_query($sql);

// iterate over players
$count = 0;
while($row = mysql_fetch_array($result)) {
	
	$player_id = $row['player_id'];
  
  $sql = "SELECT userId FROM $playerstatustable WHERE userId='".$player_id."' AND type='B' AND active='Y'";
  $status = mysql_num_rows(mysql_query($sql));
  
  if ($status == 0) {
    
    $name = $row['name'];
    $nameLink = '<a href="'.$directory.'/profile.php?name='.$name.'">'.$name.'</a>';
    $country = $row['country'];
    $join = $row['joindate'];
    $joindate = date('m/d h:i', $join);
    $ip = $row['ip'];
    $pwd = $row['pwd'];
    $mail = $row['mail'];
    $msn = $row['msn'];
    $defaultVersion = $row['defaultversion'];
    $versionImg = getImgForVersion($defaultVersion);
    echo "<tr class='row_approve'>";
    
    echo "<td><p>$player_id</p><p>$versionImg</p></td>";
    echo "<td><b>$nameLink</b> ($country)<br>$mail<br>$joindate<br>$ip";
    echo "</td>";
    $count++;

    // pwd
    echo "<td>";
    $pwd_sql = "SELECT name,pwd from $playerstable where pwd like '%$pwd%' and name != '$name' limit 0,5";
    $pwd_result = mysql_query($pwd_sql);
    if (mysql_num_rows($pwd_result) > 0) {
      echo "<b>$pwd</b><br>password matches player(s):<br>";	    
    }
    while($pwd_row = mysql_fetch_array($pwd_result)) {
      $pwd_name = $pwd_row['name'];
      $nameLink = '<a href="'.$directory.'/profile.php?name='.$pwd_name.'">'.$pwd_name.'</a>';
      $pwd_passworddb = $pwd_row['pwd'];
      echo "$nameLink&nbsp;($pwd_passworddb)<br />";
    }
    echo "</td>";

    // mail	
    echo "<td>";
    $mail_sql = "SELECT name from $playerstable where mail like '%$mail%' and name != '$name'";
    $mail_result = mysql_query($mail_sql);
    if (mysql_num_rows($mail_result) > 0) {
      echo "<b>$mail</b><br>matches player(s):<br>";	    
    }
    while($mail_row = mysql_fetch_array($mail_result)) {
      $mail_name = $mail_row['name'];
      $nameLink = '<a href="'.$directory.'/profile.php?name='.$mail_name.'">'.$mail_name.'</a>';
      echo "$nameLink<br />";
    }
    echo "</td>";

    $lastDot = strripos($ip, ".");
    if ($lastDot > 1) {
      $ip_start = substr($ip, 0, $lastDot);
    } else {
      $ip_start = $ip;
    }
    echo "<!--ip$ip_start-->";
    // players ip match
    echo "<td>";
    if (!empty($ip_start)) {
      $ip_player_sql = "SELECT name, ip from $playerstable where ip like '$ip_start%' ".
        "and ip != '' and name != '$name'";
      $ip_player_result = mysql_query($ip_player_sql);
      while($ip_player_row = mysql_fetch_array($ip_player_result)) {
        $ip_player_name = $ip_player_row['name'];
        $ip_player_ip = $ip_player_row['ip'];
        $nameLink = '<a href="'.$directory.'/profile.php?name='.$ip_player_name.'">'.$ip_player_name.'</a>';
        if ($ip_player_ip == $ip) {
          $ipDisplay = "<font color=red>$ip_player_ip</font>";
        } else {
          $ipDisplay = $ip_player_ip;
        }
        echo "$nameLink&nbsp;($ipDisplay)<br />";
      }
    }
    echo "</td>";

    // access ip match
    echo "<td>";
    if (!empty($ip_start)) {
      $ip_log_sql = "SELECT user, ip, accesstime from $logtable where ip like '$ip_start%' " .
        "and ip != '' and user != '$name' group by ip order by accesstime desc";
      $ip_log_result = mysql_query($ip_log_sql);
      while($ip_log_row = mysql_fetch_array($ip_log_result)) {
        $ip_log_name = $ip_log_row['user'];
        $nameLink = '<a href="'.$directory.'/profile.php?name='.$ip_log_name.'">'.$ip_log_name.'</a>';
        $accesstime = $ip_log_row['accesstime'];
        $ip_log_ip = $ip_log_row['ip'];
        if ($ip_log_ip == $ip) {
          $ipDisplay = "<font color=red>$ip_log_ip</font>";
        } else {
          $ipDisplay = $ip_log_ip;
        }
        echo "$nameLink&nbsp;($ipDisplay) ".formatTimeDiff($join, $accesstime)." signup<br>";
      }
    }

    echo "</td>";
    echo "<td><input style='width:12px;' type='checkbox' checked name='ids[".$pos."]' value='$player_id'></td>";
    echo "<td>";
    echo "<input style='width:12px;' type='checkbox' name='rejectIds[".$pos."]' value='$player_id'>";
    echo "<br>";
    echo "<select class='width200' style='margin: 2px 0 2px 0;' name='rejectReasonSelect[".$pos."]'>";
    echo "<option></option>";
    echo "<option selected='selected'>Similarities to existing account</option>";
    echo "</select>";
    echo "<br>";
    echo "<input class='width200' type='text' name='rejectReasonText[".$pos."]'>";
    echo "</td>";
      
    echo "</tr>";
    
    $pos++;
	}
}
?>
<?= getRankBoxBottom() ?>

 </form> 
<?
} 
?>
<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>