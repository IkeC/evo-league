<?php

// this page will pop up if you click 'forgot password?' under the login fields. if the entered name or
// email address matches a user in the weblm_players table and mail sending is properly configured,
// the user will get a mail with his account details.
 
	require('./variables.php');
	require('./variablesdb.php');
	require('./functions.php');
  
  require('top.php');
?>

<?= getOuterBoxTop("Password Recovery",""); ?>

<?php

$retry = "<p><a href='forgotpswd.php'>Try again</a></p>";
$error = "";
if($allowpswdmail == 'yes')
	{
if(! empty($_GET['submit']))
	{
	$submit = mysql_real_escape_string($_GET['submit']);
	}
	else
		{
		$submit = '0';
		}
if($submit)
	{
	if(! empty($_POST['editname']))
		{
		$editname = mysql_real_escape_string($_POST['editname']);
		}
		else
		{
		$error .= '<p>Invalid username.</p>';
		}
	
	$query = "SELECT * FROM $playerstable WHERE name='$editname'";
	$queryresult = mysql_query($query);
	if (mysql_num_rows($queryresult) < 1) {
	    $error .= '<p>Player <b>'.$editname.'</b> not found</p>';
	}
	$row = mysql_fetch_array($queryresult);
	
	if (strlen($error) > 0) {
	    echo $error.$retry;
	} else {
	$message =  "You have asked to get your account password for ".$leaguename.".

The password is encryted in our database and cannot be sent to you. Please click the following link to enter your profile and set a new password.

".$directory."/editprofile.php?key=".$row['pwd']."

Please do not reply to this email. If you have questions or problems, post in our forums: http://www.".$leaguename."/forum/

Your ".$leaguename." administrator.\n";
	$subject = "[".$leaguename."] Password reset request";
	$head = "From:".$noReplyMail."\r\nReply-To:".$noReplyMail."";
	$sendmail = @mail ($row['mail'],$subject,$message,$head,$mailconfig);
	if($sendmail) {
		echo '<p>An email with password reset instructions has been sent.</p>';
		}
		else {
      echo '<p>Unable to send email. <br>Please contact your league administrator at '.$adminmail.' for more help.</p>';
    }
   }
	}
	else
		{
?>
<p>We can send you a password reset email to the email address set in your account profile. Please enter your username below.</p>

<form method="post" action="forgotpswd.php?submit=1">
<table class="formtable">
  <tr>
    <td>Your Username</td>
    <td><input type="text" value="<? echo $cookie_name ?>" name="editname" /></td>
    <td><input type="submit" name="Submit" value="Send password" /></td>
  </tr>
</table>
</form>

<p>If you don't remember your username, or the email address in your profile might be wrong, please post in our <a href="/forum/">forums</a>.</p>

<?php
		}
	}
	else
		{
		echo "<p>You can't access this page.</p>";
		}
		
?>
<?= getOuterBoxBottom() ?>

<?php
require('./bottom.php');
?>
