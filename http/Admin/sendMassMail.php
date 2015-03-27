<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "sendMassMail";

require ('./../variables.php');
require ('./../variablesdb.php');
require_once ('../functions.php');
require_once ('./functions.php');
require ('./../top.php');

$msgs = "";
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Send Mass Mail", ""); ?>
<?


$posted = mysql_real_escape_string($_POST['posted']);
$addresses = "";

if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {
	if (!empty ($posted)) {

		$mailText = stripslashes(mysql_real_escape_string($_POST['mailText']));
		$plainText = stripslashes(mysql_real_escape_string($_POST['plainText']));
		$mailSubject = mysql_real_escape_string($_POST['mailSubject']);
		$mailType = mysql_real_escape_string($_POST['mailType']);
		$limit1 = mysql_real_escape_string($_POST['limit1']);
		$limit2 = mysql_real_escape_string($_POST['limit2']);

		if (empty ($mailText) || empty ($mailSubject) || empty ($mailType) || empty ($limit1) || empty ($limit2) || empty($plainText)) {
			$msgs .= "<p>Form incomplete</p>";
		} else {

			if (mysql_real_escape_string($_POST['target']) == 'test') {
				
				$address = "admin@".$leaguename;
				
				$userMailText = str_replace('%playername%', "Ike", $mailText);
				$unsubscribeUrl = "http://www.".$leaguename."/unsubscribe.php?id=" . md5("Ike");
				$userMailText = str_replace('%unsubscribeLink%', $unsubscribeUrl, $userMailText);
				
				$msgs .= "Send [" . $address . "] -> ";
				$result = sendHtmlMail($address, $mailSubject, $userMailText, $plainText);
				$msgs .= "Result [" . $result . "]<br>";
				logSentMail("", $address, $mailType);
			} else {
				if (mysql_real_escape_string($_POST['target']) == 'all') {
					$sql = "SELECT mail, name, player_id, msn from $playerstable  " .
					"where player_id <= " . $limit2 . " and player_id >= " . $limit1 . " " .
					"and sendNewsletter = 'yes' order by player_id asc";
					$result3 = mysql_query($sql);
					$msgs .= "<p>".mysql_num_rows($result3)." matched your query</p>";
					while ($row = mysql_fetch_array($result3)) {
						$name = $row['name'];
						$mail = $row['mail'];
						$invalidEmail = $row['invalidEmail'] == 1;
						if (!isValidEmailAddress($mail) || $invalidEmail) {
							$msgs .= "Invalid mail address for [$player_id] $name: $mail<br>";
						} else {
							$player_id = $row['player_id'];
							$msgs .= "Player: [$player_id] $name ->";
							$sql2 = "SELECT userId from $playerstatustable " .
							"where userId = '$player_id' and type='B' and active='Y'";
							$result2 = mysql_query($sql2);
							if (mysql_num_rows($result2) > 0) {
								$msgs .= "Player " . $name . " is banned!<br>";
							} else {
								$unsubscribeUrl = "http://www.yoursite/unsubscribe.php?id=" . md5($name);
								$userMailText = str_replace('%playername%', $name, $mailText);
								$userMailText = str_replace('%unsubscribeLink%', $unsubscribeUrl, $userMailText);
								
								$plainText = str_replace('%playername%', $name, $plainText);
								$plainText = str_replace('%unsubscribeLink%', $unsubscribeUrl, $plainText);
								
								$msgs .= "Send [" . $mail . "] -> ";
								$result = sendHtmlMail($mail, $mailSubject, $userMailText, $plainText);
								$msgs .= "Result [" . $result . "]<br>";
								logSentMail($name, $mail, $mailType);
							}
						}
					}
				}
			}
		}
	}
	if (!empty ($msgs)) {
		echo '<div style="border:1px solid #888;"><p><b>Message</b></p><p>' . $msgs . '</p></div>';
	}
	//	if (!empty ($mailText)) {
	//		echo '<div style="border:1px solid #888;"><p><b>Body</b></p><p>' . $mailText . '</p></div>';
	//	}
?>
	<form name="sendMail" method="post">
		<p>Target <select name="target">
			<option value="test">test addresses</option>
			<option selected="selected" value="all">all</option>
			</select>
			
			<input type="hidden" name="posted" value="true" />
		</p>
		<p>
			Subject <input type="text" name="mailSubject" class="width400" value="<?= $mailSubject?>" />
		</p>
		<p>
			Type <input type="text" name="mailType" maxlength="20" class="width400" value="<?= $mailType ?>" />
		</p>
		<p>
			Lower ID (included) <input type="text" name="limit1" maxlength="20" class="width100" value="<?= $limit1 ?>" />
		</p>
		<p>
			Upper ID (included) <input type="text" name="limit2" maxlength="20" class="width100" value="<?= $limit2 ?>" />
		</p>
		<p>
			HTML-Body		
			<textarea name="mailText" rows="15" class="width400"><?= $mailText ?></textarea>
		</p>
		<p>
			Plain Body		
			<textarea name="plainText" rows="15" class="width400"><?= $plainText ?></textarea>
		</p>
		<p>
			<input type="submit" class="width200" name="Send mail" />
		</p>
	</form>
<?
}
?>
<?= getOuterBoxBottom() ?>
<?


require ('./../bottom.php');
?>
