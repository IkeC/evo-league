#!/usr/bin/php
<?php
require('/var/www/html/variables.php');
require('/var/www/html/variablesdb.php');
require_once('/var/www/html/Admin/functions.php');
require ('/var/www/html/log/KLogger.php');
$log = new KLogger('/var/www/html/log/cron/', KLogger::INFO);

// PES

$log->logInfo('newLadderSeason: start');

$msg = StartLadderSeason();
$log->logInfo('StartLadderSeason: '.$msg);
echo $msg;

$log->logInfo('newLadderSeason: end');

?>