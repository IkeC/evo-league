#!/usr/bin/php
<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/Admin/functions.php');
require('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/cron/', KLogger::INFO);

$backSpan = 60*60*24*14;
$sql = "SELECT * from $playerstable where approved='no' AND signupSent=0 AND rejected=0 and UNIX_TIMESTAMP() - joindate < $backSpan ".
       "ORDER BY joindate ASC limit 0, 20";  
$num = mysql_num_rows(mysql_query($sql));

if ($num > 1) {
  $adminSubject = "[".$leaguename." admin] ".$num." to approve";
  $adminMessage = "http://www.yoursite/Admin/approvePlayers.php";
  sendAdminMail($adminSubject, $adminMessage);
}

?>