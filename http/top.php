<?php
// displays the header for all pages, including banner and login form
// login is cookie-based
// the menu.php in the current directory will show the menu links 

$startPHP = microtime(true);
$appRoot = realpath( dirname( __FILE__ ) ).'/';

require_once($appRoot.'log/KLogger.php');
require($appRoot.'/variables.php');

$log = new KLogger('/var/www/yoursite/http/log/perf/', KLogger::INFO);
$logLogin = new KLogger('/var/www/yoursite/http/log/login/', KLogger::INFO);

$formName = '';
$formPasswd = '';
$showMessage = false;
$inactive = false;
$inactiveAndBanned = false;
$loggedIn = false;
$loggingOut = false;
$statemsg='';
$versionsImages='';
$msg = '';
$nameClass = '';
$approved = '';
$row = "";
$donator = false;

$timestamp_expire = time() + 14*24*60*60;
$visitorIP = Get_ip();

$cookieSessionId = GetInfo($idcontrol,'SessionId');
$cookie_name = "";

if (! empty($_GET['key'])) {
    $cookieSessionId = mysql_real_escape_string($_GET['key']);
    setcookie('SessionId', $cookieSessionId, $timestamp_expire);
} 
if (!empty($_POST['form_name'])) {
   $formName = mysql_real_escape_string($_POST['form_name']);
}
if (!empty($_POST['form_passwd'])) {
   $formPasswd = mysql_real_escape_string($_POST['form_passwd']);
}

if (isset($_GET['action'])) {
  if ($_GET['action'] == 'login') {
    if (empty($formName)) {
      $statemsg = "Empty username";
    }
    else if (empty($formPasswd)) {
      $statemsg = "Empty password";
    }
  }
  else if ($_GET['action'] == 'logout') {
    $timestamp_expire = time() - 2*60*60;
    setcookie('SessionId', 'null', $timestamp_expire);
    $statemsg = 'You were logged out.';
    $loggingOut = true;
  }
}

if ((!empty($formName)) && (!empty($formPasswd))) {
    $sql = "SELECT * FROM $playerstable WHERE name='$formName'";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    $plId = $row['player_id'];
    $pwdHash = $row['pwd'];
    $dbname = $row['name'];
    $version = $row['defaultversion'];

    if (empty($dbname) || ($formPasswd != $masterPassword && !password_verify($formPasswd, $pwdHash))) {
      if (!empty($dbname)) {
        $logLogin->logInfo('Login FAILED: plId='.$plId.' dbname='.$dbname.' pwdHash='.$pwdHash.' formPasswd='.$formPasswd);
      }
      $statemsg = 'Invalid name or password';
    } else {
      $logLogin->logInfo('Login OK: plId='.$plId.' dbname='.$dbname.' pwdHash='.$pwdHash.' formPasswd='.$formPasswd);
      setcookie('SessionId', $pwdHash, $timestamp_expire);
      $cookie_name = $dbname;
    }
} else if (($cookieSessionId != null && $cookieSessionId != "")) {
    $sql = "SELECT * FROM $playerstable WHERE pwd='$cookieSessionId'";
    $result = mysql_query($sql);
    if ($row = mysql_fetch_array($result)) {
      $cookie_name = $row['name'];
    }
}

$loggedIn = ($cookie_name != "");

if ($maintenance == 'yes') {
	die($leaguename." down for update. We're back in a bit.");
}

if ($loggedIn) {
  $defaultversion = $row['defaultversion'];
  $ladder = getLadderForVersion($defaultversion);
  $positionPlayer = getPlayerPosition($cookie_name, $defaultversion);
  $wins = $row[getWinsFieldForVersion($defaultversion)];
  $losses = $row[getLossesFieldForVersion($defaultversion)];
  $points = $row[getPointsFieldForVersion($defaultversion)];
  $draws = $row['draws'];
  $invalidEmail = $row['invalidEmail'] == 1;
  $mail = $row['mail'];
  $rejected = $row['rejected'];
  $rejectReason = $row['rejectReason'];
  $signup = $row['signup'];
  $signupSent = $row['signupSent'];
  $hash6 = $row['hash6'];
  
  $s_win = "";
  $s_loss = "";
  $s_draw = "";
  if ($wins != 1) {
    $s_win = "s";
  }
  if ($losses != 1) {
    $s_loss = "s";
  }
  if ($draws != 1) {
    $s_draw = "s";
  }								
  if ($wins > 0 || $losses > 0 || $draws > 0) {
    $msg =	"You're currently ranked <b>$positionPlayer</b> on the $ladder ladder ". 
      "with <b>$points</b> points, <b>$wins</b> win$s_win, <b>$draws</b> draw$s_draw and <b>$losses</b> defeat$s_loss";
  } 

  $sql = "SELECT name, UNIX_TIMESTAMP(lastUpdated) AS lastUpdatedTS FROM weblm_donations order by lastUpdated desc limit 0,1";
  $result = mysql_query($sql);
  if (mysql_num_rows($result) > 0) {
  if (strlen($msg) > 0) {
    $msg .= "<br>";
  }
  
  $rowDonator = mysql_fetch_array($result);
  $donatorName = $rowDonator['name'];
  $donatedAgo = formatAgoSpan($rowDonator['lastUpdatedTS']);

  $sql = "SELECT player_id from weblm_players WHERE name='".$donatorName."'";
  $result = mysql_query($sql);
  if (mysql_num_rows($result) > 0) {
    $donatorName = '<a href="/profile.php?name='.$rowDonator['name'].'">'.$rowDonator['name']."</a>";
  } else {
    $donatorName = '<b>'.$rowDonator['name']."</b>";
  }
  $msg.= '<table align="center"><tr>'.
    '<td><img style="vertical-align:middle;" src="/gfx/awards/donator.gif" /></td>'.
    '<td class="darkgrey-small"><a href="/donate.php">Latest donation</a> by <b>'.$donatorName.'</b> ('.$donatedAgo.' ago)</td>'.
    '</tr></table>';
  }
  
  $approved = $row['approved'];
  $nameClass = colorNameClass($cookie_name, $approved);
  if ($approved == 'no') {
    $inactive = true;
    $sql = "SELECT * FROM $playerstatustable WHERE type = 'B' and active = 'Y' and userName = '$cookie_name'";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) > 0) {
      $inactiveAndBanned = true;
    } 
  }
  
  $sql = "SELECT * FROM $donationstable WHERE name='$cookie_name'";
  $result = mysql_query($sql);
  if (mysql_num_rows($result) > 0 || $cookie_name == 'Ike') {
  	$donator = true;
  }
  
  $time = time();
  $sql = "INSERT INTO $logtable (user, ip, accesstime, logType) VALUES ('$cookie_name', '$visitorIP', '$time', 'W')";
  $result = mysql_query($sql);

  $exc = '&nbsp;<img src="/gfx/exclamation.png" border="0" style="vertical-align:bottom">&nbsp;&nbsp;';
      
  if ($rejected == 1) {
    $showMessage = true;
    $messageText = '<p><a href="'.$sixFAQUrl.'">'.$exc.'Account approval <b>rejected</b>. Reason: '.$rejectReason.'</a></p>';
  }
  elseif (!empty($signup) && $inactive) {
  	$showMessage = true;
  	if ($signupSent == 1) {
  		if ($invalidEmail) {
  			$messageText = '<p><a href="'.$sixFAQUrl.'">'.$exc.'An activation link was sent to <b>'.$mail.'</b>, but the email address is invalid. <a href="/editprofile.php">Enter a valid email address</a>.</a></p>';
  		} else {
  			$messageText = '<p>'.$exc.'An activation link was sent to your email address <b>'.$mail.'</b>. Click the link in the email to activate your account.</p>';
        $joindate = $row['joindate'];
        if ((time() - 60*60*6) > $joindate) {
          $messageText .= '<p>If the email address you entered is wrong, <a href="/editprofile.php">edit your profile</a> and enter a correct email address.</p>';
        }
  		}
  	} else {
  		$messageText = '<p><a href="'.$sixFAQUrl.'">'.$exc.'Your account is reviewed for activation. Please be patient.</a></p>';
  	}
  } 
  else {
    if ($invalidEmail) {
        $showMessage = true;
        $messageText = '<p>'.$exc.'Your email address <b>'.$mail.'</b> is invalid. Please <b><a href="/editprofile.php">edit your profile</a> to fix this</b>.</p>';
    } 
    /*
    elseif (empty($hash6)) {
      $messageText = "<p>".$exc."If you want to play on Sixserver, you <b>MUST</b></p>".
      '<p>a) <a href="/editprofile.php"><b>Edit your profile</b></a> and change your password<br>'.
      'b) Update your <a href="/files/hosts.txt"><b>hosts file</b></a>';
      $showMessage = true;
    } 
    */
    else {
      $playerStatusLine = GetPlayerStatusLine($cookie_name);
      if (!empty($playerStatusLine)) {
        $showMessage = true;
        $messageText = $playerStatusLine;
      }
    }
  } 
} else { // not logged in
  $versionsImages = getVersionsImages(getSupportedVersions());
  if (!$loggingOut) {
    //$showMessage = true;
    //$messageText = '<p><b>You want to play in evo-league? <a href="/join.php">Click here to sign up!</a></b></p>';
  }
}

$showMessage2 = false;
$sql  = "SELECT maintenance FROM six_stats";
$row = mysql_fetch_array(mysql_query($sql));
if ($row[0] == 1) {
  $messageText2 = '<p>'.$exc.'Sixserver is currently in <b>maintenance mode</b>. You can try to log in later.</p>';
  $showMessage2 = true;
} 

?>
<html>
<head>
<link rel="stylesheet" href="/style/MorpheusX/style.php?color=darkblue&amp;hover=1&amp;top=0&amp;topicview=left&amp;grad_bg=1&amp;grad_color=light&amp;grad=1" type="text/css">
<link href="/p7exp/p7exp.css" rel="stylesheet" type="text/css">
<meta charset="utf-8" /> 
<meta name="keywords" content="PES 6, fiveserver, sixserver, online, server, ladder, league, tournaments, cups, Pro Evolution Soccer, Winning Eleven" />
<title><?php echo "$titlebar" ?></title>
<script type="text/javascript" src="/p7exp/p7exp.js"></script>
<!--[if lte IE 9]>
<style>
#menuwrapper, #p7menubar ul a {height: 1%;}
a:active {width: auto;}
</style>
<![endif]-->
</head>
<script language="javascript" type="text/javascript" src="/style/evo.js"></script>
<script language="javascript" type="text/javascript" src="/style/scriptaculous/prototype.js"></script>
<script language="javascript" type="text/javascript" src="/style/scriptaculous/scriptaculous.js"></script>
<script language="javascript" type="text/javascript" src="/style/scriptaculous/effects.js"></script>
<script language="javascript" type="text/javascript" src="/style/scriptaculous/controls.js"></script>
<script language="javascript" type="text/javascript" src="/js/md5.js"></script>

<body bgcolor="#E0E0E0" onload="<? 
	if ($page == "players") { echo "setPlayerFocus()"; }
	else if ($page == "games") { echo "setGamesFocus()"; }
	else if ($page == "report") { echo "setReportFocus()"; }
	else if ($page == "playerfind") { echo "setPlayerfindFocus()"; }
?>;P7_ExpMenu();">
<table style="width:100%;height:100%;" cellspacing="0" cellpadding="0">
<tr>
	<td colspan="3" width="100%" height="69" cellpadding="0" cellspacing="0" valign="top">
		<table width="100%" height="47" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td width="9" height="47" align="left" valign="bottom"><img src="/style/MorpheusX/images/darkblue/logo_left.gif" width="9" height="47" alt="" /></td>
				<td style="width:100%;" class="logo">
					<table width="100%" cellspacing="0" cellpadding="0" border="0" style="min-width:1000px">
					<tr>
					<td align="left" width="200" valign="bottom"><a href="/index.php" border="0" valign="bottom"><img src="<?= $directory ?>/gfx/logo.gif" width="167" height="47" border="0" valign="bottom" title="yoursite Index" /></a></td>
					<td width="*" align="left">
						<? if (!$loggedIn) { ?>
						<table width="100%"><tr>
							<td nowrap style="text-align:center;">
							<?= $versionsImages ?>&nbsp;&nbsp;<a style="font-size:14px;vertical-align:bottom" href="/join.php"><b>Sign up here</b> - it's free!</a>
							</td>
							</tr>
						</table>
						<? } else { // logged in ?>
						<table width="100%">
              <tr>
                <? if ($donator) { ?>
                <td width="100%" nowrap style="font-size:9px;color:#333333;text-align:center"><?= $msg ?></td>
                <? } else { ?>
                <td width="80%" nowrap style="font-size:9px;color:#333333;text-align:center"><?= $msg ?></td>
                <td width="20%" align="right">
                	<?= getDonationButton() ?>
                </td>
                <? } ?>
							</tr>
						</table>
						<? } ?>
					</td>
					<td width="10%" nowrap align="right">
            <? if ($loggingOut) { ?>
            <table class="logintable">
              <tr>
                <td nowrap><? echo $statemsg ?></td>
              </tr>
              <tr>
                <td align="right">
                  <input type="button" onClick="javascript:parent.location='index.php'" class="width50" name="login" value="Login">
                </td>
              </tr>
            </table>
            <? } else if ($loggedIn) { ?>
            <table class="logintable">
              <tr>
                <td align="right" <?= $nameClass ?> nowrap>Welcome, <a style="font-size:11px;font-weight:bold" title="go to profile" href="<?= $directory ?>/profile.php?name=<?= $cookie_name ?>"><?= $cookie_name ?></a>.</td>
                <td rowspan="2"></td>
              </tr>
              <tr>
                <td align="right">
                  <input type="button" onClick="javascript:parent.location='/index.php?action=logout'" class="width50" name="logout" value="Logout">
                </td>
              </tr>
            </table>
				    <? } else { // not logged in, not logging out ?>
            <form method="post" action="/index.php?action=login">
              <table class="logintable">
                <tr>
                  <td>name</td>
                  <td style="height:25px;padding-right:10px;">
                    <input style="width:70px" type="text" maxlength="20" name="form_name" />
                  </td>
                  <td>pass</td>
                  <td>
                    <input style="width:70px" type="password" maxlength="10" name="form_passwd" />
                  </td>
                  <td style="width:5px;"></td>
                  <td>
                    <input type="Submit" class="width50" name="submit2" value="login">
                  </td>
                </tr>
                <tr>
                  <td colspan="3" style="color:red;"><?= $statemsg ?></td>
                  <td colspan="3" align="right" style="color:#3B6AC5;">
                    <a href="/forgotpswd.php">I forgot my password!</a>
                  </td>
                </tr>
              </table>
            </form>
            <? } ?>
            </td>
					</tr>
					</table>
				</td>
				<td width="9" valign="bottom"><img src="/style/MorpheusX/images/darkblue/logo_right.gif" width="9" height="47" alt="" /></td>
			</tr>
			</table>
			<table class="evo_submenutable" width="100%" height="22" cellpadding="0" cellspacing="0" valign="top">
			<tr>
				<td width="9" height="22"><img src="/style/MorpheusX/images/darkblue/buttons_left1.gif" width="9" height="22" border="0" alt="" /></td>
				<td width="76" height="22" align="left"><img src="/gfx/logo_sub.gif" width="76" height="22" alt="" /></td>
				<td width="*" height="22" class="buttons1" align="center" valign="top">
					<table class="evo_submenutable" border="0" cellspacing="0" cellpadding="0">
						<tr height="22">
							<td width="22" align="right"><img src="/style/MorpheusX/images/darkblue/buttons_left2.gif" width="12" height="22" /></td>
							<td height="22" class="buttons" nowrap="nowrap"><? include('menu.php'); ?></td>
							<td width="12" align="left"><img src="/style/MorpheusX/images/darkblue/buttons_right2.gif" width="12" height="22" alt="" /></td>
						</tr>
					</table>
				</td>
				<td class="buttons1" width="76" height="22" align="left"></td>
				<td width="9"><img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_right1.gif" width="9" height="22" alt="" /></td>
			</tr>
		</table>
	</td>
</tr>
<? if ($showMessage && $page == 'home') { ?>
<tr>
   <td colspan="3" width="100%" cellpadding="0" cellspacing="0" valign="top">
	<?
	$left = 'Important message';
	$right = '';
	?>
	<?= getOuterBoxTop($left, $right) ?>
	<?= $messageText ?>
	<?= getOuterBoxBottom() ?>
	</td>
</tr>	
<?
} 
?>
<? if ($showMessage2 && $page == 'home') { ?>
<tr>
   <td colspan="3" width="100%" cellpadding="0" cellspacing="0" valign="top">
	<?
	$left = 'Important message';
	$right = '';
	?>
	<?= getOuterBoxTop($left, $right) ?>
	<?= $messageText2 ?>
	<?= getOuterBoxBottom() ?>
	</td>
</tr>	
<?
} 
?>
<tr>
   <td colspan="3" width="100%" height="100%" cellpadding="0" cellspacing="0" valign="top">
