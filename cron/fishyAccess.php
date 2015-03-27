#!/usr/bin/php
<?php
// count fishy access and send an email
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/Admin/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/cron/', KLogger::INFO);

$log->logInfo('fishyAccess: start');

$days = 5;
$fishy = GetFishyAccessRows($days);
$fishyIP = array();

foreach ($fishy as $fishyRow) {
  $sql = "SELECT approved, rejectReason FROM $playerstable WHERE name = '".$fishyRow['user']."'";
  $row = mysql_fetch_array(mysql_query($sql));
  if ($row['approved'] == 'yes' && empty($row['rejectReason']) && !empty($fishyRow['ip'])) {
    if (!array_key_exists($fishyRow['ip'], $fishyIP)) {
      $fishyIP[$fishyRow['ip']] = 1;
    } else {
      $fishyIP[$fishyRow['ip']] = $fishyIP[$fishyRow['ip']] + 1;
    }
  }
}

foreach ($fishyIP as $check) {
  if ($check > 1) {
    $adminSubject = "[".$leaguename." admin] ".sizeof($fishy)." fishy access";
    $adminMessage = "http://yoursite/Admin/access.php?days=".$days;
    sendAdminMail($adminSubject, $adminMessage);
    break;
  }
}

$log->logInfo('fishyAccess: end');

?>