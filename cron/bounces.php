#!/usr/bin/php
<?php
// read mails from internal bounce mailbox and set invalidEmail=1 for these email addresses
require('/var/www/yoursite/http/variables.php');
require('/var/www/yoursite/http/variablesdb.php');
require_once('/var/www/yoursite/http/functions.php');
require ('/var/www/yoursite/http/log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/bounces/', KLogger::INFO);	

$mailbox = imap_open('{localhost:993/ssl/novalidate-cert}', 'bounce', 'bounce01$');
$mailbox_info = imap_check($mailbox);
for($i = 1; $i <= $mailbox_info->Nmsgs; $i++) {
  $msg = imap_fetch_overview($mailbox, $i);
  $rcpt = $msg[0]->to;
  if(substr($rcpt, 0, 6) == 'bounce') {
    $target = substr($rcpt, 7); // exclude 'bounce='
    $target = substr($target, 0, -9); // exclude '@yoursite'
    $target = str_replace('=', '@', $target); // revert '=' to '@'
    if ($msg[0]->answered == 0) {
		$sql = "UPDATE $playerstable SET invalidEmail=1 WHERE invalidEmail=0 AND mail='".$target."'";
      	mysql_query($sql);
      	$affected = mysql_affected_rows();
		$uid = imap_uid($mailbox,$i);
		$status = imap_setflag_full($mailbox, $uid, '\\Answered \\Seen', ST_UID);
		$log->logInfo('sql=['.$sql.'] affected=['.$affected.'] status=['.$status.']');
    }
  }
}
imap_close($mailbox); // close the mailbox 
?>
