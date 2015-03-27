#!/usr/bin/php
<?php
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/functions.php');
require_once('/var/www/yoursite/http/classes.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/cron/', KLogger::INFO);

$log->logInfo('updateTeamladder: start');
updateTeamladders();
$log->logInfo('updateTeamladder: end');

?>