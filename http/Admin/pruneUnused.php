<?php


// running this will disable accounts for players that have not played a single game in the last 12 weeks.
// if mail sending is properly configured, the admin will receive a notification email.

// an equal file is in the /cron folder which can be used to run this file automatically every day 
// if you can set up a cronjob on your server.

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "pruneUnused";

require ('./../variables.php');
require ('./../variablesdb.php');
require_once ('../functions.php');
require_once ('./functions.php');
require ('./../top.php');

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Prune Unused", ""); ?>
<?
if (!$isAdminFull) {
  echo "<p>Access denied.</p>";
} else {

  $disableSpan = 60 * 60 * 24 * 7 * 15; // 15 weeks

  $playersquery = "select distinct winner from $gamestable ".
  "where (UNIX_TIMESTAMP() - date < $disableSpan) "."UNION ".
  "select distinct loser from $gamestable ".
  "where (UNIX_TIMESTAMP() - date < $disableSpan)";

  $result = mysql_query($playersquery);

  $played_array = array ();
  $adminMessage = "";

  while ($row = mysql_fetch_array($result)) {
    $played_array[] = $row[0];
  }

  $alloldplayersquery = "SELECT player_id, name, mail "."FROM $playerstable ".
  "WHERE approved='yes' AND (UNIX_TIMESTAMP() - activeDate > $disableSpan) order by joindate ASC";

  $playerresult = mysql_query($alloldplayersquery);

  while ($row = mysql_fetch_array($playerresult)) {

    $sendmail = 0;
    $mailSentResult = 0;

    $name = $row['name'];
    $userId = $row['player_id'];

    if (!in_array($name, $played_array)) {
      // has not played in 12 weeks
      echo "<p>$name has not played<br>";
      $sql = "UPDATE $playerstable "."SET approved = 'no' "."where name='$name'";

      $updateResult = mysql_query($sql);

      echo "update query: $sql | returned: [$updateResult]<br>";

      $toAddress = $row['mail'];

      if (isValidEmailAddress($toAddress)) {

        echo "-> valid: $toAddress<br>";

        $subject = "[$leaguename] $name account passivated";

        $head = "From:".$adminmail."\r\nReply-To:".$adminmail."";

        $message = "hello $name,\n"."\n"."since you have not played any $leaguename games in 12 weeks, \n"."your account has been passivated. this means you disappear from the players \n"."list, standings and statistics tables, but your profile, points and game \n"."information remain in the database. \n"."\n"."if you want your account reactivated sometime, just send a brief email with\n"."your username to ".$adminmail.".\n"."\n"."\n"."your ".$leaguename." administrator.\n";

        //$sendmail = @mail ($toAddress, $subject, $message, $head);

        echo "mail sent: $sendmail, log now...<br>";
        $mailSentResult = logSentMail($name, $toAddress, 'passivated');
        echo "logged.<br>";

        $date = time();
        $link = $directory."/info.php?#7.2";
        $reason = "automatically passivated on ".formatDate($date);

        $sql = "INSERT INTO $playerstatustable (userId, userName, type, active, "."date, expireDate, forumLink, reason) "."VALUES ('$userId', '$name', 'I', 'Y', "."'$date', '', '$link', '$reason')";
        $result = mysql_query($sql);
        echo "player status entry added: $result<br>";

      } // if valid address

      $msg = "[$name] "."passivated [$updateResult] "."address [$toAddress] "."mail sent [$sendmail] "."log [$mailSentResult]";

      echo $msg."</p>";

      $adminMessage .= $msg."\n";
    }
  }

  $adminSubject = "[$leaguename admin] accounts passivated";

  if (!empty ($adminMessage)) {
    // sendAdminMail($adminSubject, $adminMessage);
  }

  // expire

  $date = time();

  $sql = "SELECT * FROM $playerstatustable WHERE $date > expireDate AND expireDate > 0 AND active='Y'";
  $result = mysql_query($sql);
  $expireText = "";
  while ($row = mysql_fetch_array($result)) {
    $id = $row['id'];
    $userName = $row['userName'];
    $reason = $row['reason'];
    $sql = "UPDATE $playerstatustable SET active='N' where id='$id'";
    $result2 = mysql_query($sql);
    $type = $row['type'];
    $expireText .= "[".$userName."] expired [".$result2."]";
    if ($type == 'B') {
      // activate player
      $sql = "UPDATE $playerstable SET approved='yes' where name='$userName'";
      $result3 = mysql_query($sql);
      $expireText .= " approved [".$result3."]";
    }
    $expireText .= "\n"."reason [".$reason."]"."\n\n";
  }

  if (!empty ($expireText)) {
    echo "<textarea rows='10'>$expireText</textarea>";
    $adminSubject = "[$leaguename admin] bans expired";
    // sendAdminMail($adminSubject, $expireText);
  }

  // add entries for unapproved players if they don't already have one

  $sql = "SELECT name, player_id, joindate from $playerstable where approved = 'no' ORDER BY joindate asc";
  $result = mysql_query($sql);

  while ($row = mysql_fetch_array($result)) {
    $name = $row['name'];
    $userId = $row['player_id'];
    $date = time();
    $joindate = $row['joindate'];
    $reason = "inactive (joined ". formatDate($joindate). ")";
    $sql2 = "SELECT * from $playerstatustable where userName='$name' and type != 'W'";
    $result2 = mysql_query($sql2);
    if (mysql_num_rows($result2) == 0) {
      // player is inactive but has no entry, create one
      $sql3 = "INSERT INTO $playerstatustable (userId, userName, type, active, ".
        "date, expireDate, forumLink, reason) "."VALUES ('$userId', '$name', 'I', 'Y', ".
        "'$date', '', '', '$reason')";
      $result3 = mysql_query($sql3);
    }
  }
}
?>
<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>

