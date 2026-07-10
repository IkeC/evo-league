#!/usr/bin/php
<?php
require('/var/www/html/variables.php');
require('/var/www/html/variablesdb.php');
require_once('/var/www/html/functions.php');
require_once('/var/www/html/classes.php');
require ('/var/www/html/log/KLogger.php');
$log = new KLogger('/var/www/html/log/cron/', KLogger::INFO);

$log->logInfo('updateTeamladder: start');
updateTeamladders();
$log->logInfo('updateTeamladder: end');

?>