#!/usr/bin/php
<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/Admin/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/cron/', KLogger::INFO);

// PES

$log->logInfo('newLadderSeason: start');

$msg = StartLadderSeason();
$log->logInfo('StartLadderSeason: '.$msg);
echo $msg;

$log->logInfo('newLadderSeason: end');

?>