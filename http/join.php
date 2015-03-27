<?php

// the signup page. if it is called normally (eg. http://www.yoursite/join.php), the description text
// on how to sign up is displayed. if it is called with a valid sid that exists in the weblm_signup table 
// (eg.http://www.yoursite/join.php?sid=1234567890), the signup form is displayed. valid sid's can be
// generated using the 'generate signup link' button on the admin control panel.

// requiring to send an email to sign up can be toggled with the $signupEmailRequired-switch in variables.php
$page = "join";
$subpage = "";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('top.php');

require_once('log/KLogger.php');

$logJoin = new KLogger('/var/www/yoursite/http/log/join/', KLogger::INFO);	

?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Join League", "") ?>

<?php

$na = "n/a";
$checked = "checked='checked'";
$back = "<p><a href='javascript:history.back()'>go back</a></p>";
$alias = "";
$uploadSpeed = "";
$downloadSpeed = "";
$message = "";
$ip = Get_ip();
$blacklist = false;
$sid = mysql_real_escape_string($_GET['sid']);

// if (date('w') <> 0) {
if ($false) {
  echo "<p>Site signups only open on Sundays. <a href=\"/forum/viewtopic.php?t=6776\">Here's why.</a></p>";
  echo '<p>You can <a href="http://www.yoursite/forum/ucp.php?mode=register">sign up for the forums</a> while you wait, but a forum account only enables you to post on the forums.</p>';
  echo "<p>While you wait until you can sign up for a player account, we highly recommend reading these topics <b>before</b> you sign up:</p>";
  echo '<p>- <b><a href="/forum/viewtopic.php?f=3&t=5340" target="_new">How to play PES 6 online on Sixserver</a></b><br>';
  echo '- <b><a href="/forum/viewtopic.php?f=3&t=5438" target="_new">Sixserver FAQ</a></b></p>';
  
} else {
   $xml_string = file_get_contents("http://www.stopforumspam.com/api?ip=" . urlencode($ip));
   if (!$xml_string) {
      $logJoin->logInfo('Could not read http://www.stopforumspam.com/api?ip='.urlencode($ip));
   } else {
     try {
       $xml = new SimpleXMLElement($xml_string);
       if ($xml->appears == "yes") {
        $blacklist = true; 
        $logJoin->logInfo('Blacklisted=['.$ip.']');
       }
     } catch (Exception $e) {   
        $logJoin->logInfo('Could not parse xml_string:'.$xml_string);
        $logJoin->logInfo('Exception: '. $e->getMessage());
     }
   }
  if ($blacklist) {
   echo "<p>You are blacklisted.</p>";
  }
  else if ($cookie_name != '' && $cookie_name != 'Ike') {
    echo "<p>You already have an account, <b>".$cookie_name."</b>.</p>";
  } else { ?>
    <table class="layouttable"><tr><td width="800">
    <?= getBoxTop("Sign up for ".$leaguename, "", false, null); ?>
    <?
    if (! empty($_GET['submit'])) {
      $submit = mysql_real_escape_string($_GET['submit']);
    }
    else {
      $submit = 0;
    }

    if ($submit == 1) {
      $name = mysql_real_escape_string($_POST['name']);
      $name = strip_tags($name);
      $name = trim($name);
      $alias = mysql_real_escape_string($_POST['alias']);
      $alias = trim(strip_tags($alias));
      if ($alias == '12') {
        die();
      }
      
      $passworddb = $_POST['passworddb'];
      $passworddb = strip_tags($passworddb);
      $passworddb = trim($passworddb);
      $passwordrepeat = $_POST['passwordrepeat'];
      $passwordrepeat = strip_tags($passwordrepeat);
      $passwordrepeat = trim($passwordrepeat);
      $msn = mysql_real_escape_string($_POST['msn']);
      $msn = strip_tags($msn);
      $msn = trim($msn);
      $icq = mysql_real_escape_string($_POST['icq']);
      $icq = strip_tags($icq);
      $icq = trim($icq);
      $aim = mysql_real_escape_string($_POST['aim']);
      $aim = strip_tags($aim);
      $aim = trim($aim);
      
      $mail = mysql_real_escape_string($_POST['mail']);
      $mail = strip_tags($mail);
      $mail = trim($mail);
      $mail2 = mysql_real_escape_string($_POST['mail2']);
      $mail2 = strip_tags($mail2);
      $mail2 = trim($mail2);
      
      $country = mysql_real_escape_string($_POST['country']);
      if (empty($country)) {
        $country = "No country";
      }
      $nationality = mysql_real_escape_string($_POST['nationality']);
      if ($signupEmailRequired) $sid = mysql_real_escape_string($_POST['sid']);
      $defaultversion = mysql_real_escape_string($_POST['defaultversion']);
      
      if (empty($nationality) || $nationality == "") {
        $nationality = $country;
      }
      $message = mysql_real_escape_string($_POST['message']);
      $message = strip_tags($message);
      $message = trim($message);
      if ($message == 'girl') {
        die();
      }    
      $forum = mysql_real_escape_string($_POST['forum']);
      $favteam1 = mysql_real_escape_string($_POST['favteam1']);
      $favteam2 = mysql_real_escape_string($_POST['favteam2']);
      
      if (! empty($_POST['serial5'])) {
        $serial5 = mysql_real_escape_string($_POST['serial5']);
        $serial5 = strtoupper(str_replace("-","", $serial5));
      }
      else {
        $serial5 = '';
      }
      
      if (! empty($_POST['serial6'])) {
        $serial6 = mysql_real_escape_string($_POST['serial6']);
        $serial6 = strtoupper(str_replace("-","", $serial6));
      }
      else {
        $serial6 = '';
      }
      
      if (empty($msn)) { $msn = $na; }
      if (empty($icq)) { $icq = $na; }
      if (empty($aim)) { $aim = $na; }
      if (empty($mail)) { $mail = $na; }
      
      
      if (isset($_POST["gamesMail"])) {
        $gamesMail = "yes";
      } else {
        $gamesMail = "no";
      }

      $deductMail = "no";
      
      if (isset($_POST["newsletter"])) {
        $newsletter = "yes";
      } else {
        $newsletter = "no";
      }
      
      $uploadSpeed = mysql_real_escape_string($_POST["uploadSpeed"]);
      $downloadSpeed = mysql_real_escape_string($_POST["downloadSpeed"]);
      
      $createdmsg = "<p>Your account has been created but it is not active yet.</p>";
      $createdmsg .= "<p><b>After we have checked your account</b>, an email with an activation link will be sent to <b>".$mail."</b>.</p>";
      $createdmsg .= '<p>Please read the <b><a href="'.$sixFAQUrl.'">Sixserver FAQ</a></b> while you wait.</p>';
      
      $nocountrymsg = "<p class='boxlink' style='font-weight:bold'>You didn't specify your location, please post <a href='http://www.".$leaguename."/forum/viewtopic.php?t=7'>here</a> and let us know so we can add the flag. Other players may not want to play you if they don't know where you are.</p>";
      
      $sql = "SELECT version from $versionstable";
      $result = mysql_query($sql);
      $count = 0;
      $versions = "";
      while ($row = mysql_fetch_array($result)) {
        $count++;
        $version = $row['version'];
        if (isset($_POST['version_'.$version])) {
          $versions .= $version;
        }
      }

      $num_rows = 0;
      if ($signupEmailRequired) {
        $sql = "SELECT sid from $signuptable where sid='$sid' and expired='no' and used='no'";
        $result = mysql_query($sql);
        $num_rows = mysql_num_rows($result);
      }
      
      if ($num_rows != 1 && $signupEmailRequired) {
        echo "<p>The signup link used is invalid or has expired.</p><p>You will need to request a new one from an administrator.</p>";
      } else if ($name == "") {
        echo "<p>Please enter a nickname.</p>".$back;
      } else if (preg_match('/[^0-9A-Za-z]/', $name)) {
        echo "<p>Please use only standard alphanumeric characters (A-Z, 0-9) in your nickname.</p>".$back;
      } else if ($passworddb == "") {
        echo "<p>Please supply a password.</p>".$back;
      } else if (strstr($passworddb,'1234')) {
        echo "<p>Please choose a better password.</p>".$back;
      } else if (!isValidEmailAddress($mail)) {
        echo "<p>Please supply a valid email address.</p>".$back;        
      } else if ($mail != $mail2) {
        echo "<p>Email address and repetition do not match.</p>".$back;
      } else if ($passworddb != $passwordrepeat) {
        echo "<p>Password and repetition do not match.</p>".$back;
      } else if ($passworddb == $name || $passworddb == $name) {
        echo "<p>Please choose a better password.</p>".$back;
      } else if ($versions == "") {
        echo "<p>Please select at least one game.</p>".$back;
      } else if (strlen($serial5) > 0 && strlen($serial5) != 20) {
        echo "<p>The PES 5 serial you entered is not valid.</p>".$back;
      } else if (strlen($serial6) > 0 && strlen($serial6) != 20) {
        echo "<p>The PES 6 serial you entered is not valid.</p>".$back;
      } else if (stristr($versions, 'H') && strlen($serial6) == 0) {
        echo "<p>You must enter your PES 6 serial number if you want to play PES 6.</p>".$back;
      }
      
      else if (!stristr($versions, $defaultversion)) {
        echo "<p>The default game you selected is not one of the games you have.</p>".$back;
      }
      else {
        $length = strlen($name);
        if ($length > $maxnamelength) {
          echo "<p>Your name is too long. The maximum is $maxnamelength characters.</p>".$back;
        }
        else {
          $pwdHash = password_hash($passworddb, PASSWORD_DEFAULT);
          $similarAccounts = CheckSimilarAccounts($ip, $name, $pwdHash, $mail);
          // $similarAccounts = "";
          if (strlen($similarAccounts) > 0 && $cookie_name != 'Ike') {
            echo '<p>It appears you already have at least one account here. <b>You may not sign up for more than one account per IP.</b></p>';
            echo '<p>If you have more than one player in your home, create another in-game profile.</p>';
            echo '<p>Please do not try to sign up again.</p>';
            echo '<p>If you need any help with your account, or you believe this is an error, ';
            echo 'please <b><a href="http://www.'.$leaguename.'/forum/viewtopic.php?t=5346">post in the forums</a></b>.</p>';
          }	else {
            $sql="SELECT name FROM $playerstable WHERE name = '$name'";
            $result=mysql_query($sql,$db);
            $samenick = mysql_num_rows($result);
            if ($samenick > 0) {
              echo "<p>The name '$name' is already taken. Please choose another.</p>".$back;
            }
            else {
              if ($approve == 'yes') {
                $approved = 'no';
              }
              else {
                $approved = 'yes';
              }
              if (strcmp($name, $alias) == 0) {
                $alias = "";
              }
              
              $hash5 = mysql_real_escape_string($_POST["hash5"]);
              if (!empty($serial5)) {
                $result = array();
                $res5 = exec("/opt/sixserver/sixserver-env/bin/python2.6 /opt/sixserver/lib/fiveserver/gethash.py ".$hash5, $result);
                $hash5 = $result[0];
              }
              
              $hash6 = mysql_real_escape_string($_POST["hash6"]);
              if (!empty($serial6)) {
                $result = array();
                $res6 = exec("/opt/sixserver/sixserver-env/bin/python2.6 /opt/sixserver/lib/fiveserver/gethash.py ".$hash6, $result);
                $hash6 = $result[0];
              }

              if (!empty($_FILES['picture']['size'])) {
                $f1_size = $_FILES['picture']['size'];
                $f1_name = $_FILES['picture']['name'];
                $f1_tmpname = $_FILES['picture']['tmp_name'];
                
                $ext = strtolower(substr($f1_name,strrpos($f1_name, ".")+1));
                
                $valides = array($valid_picture_extension1,$valid_picture_extension2,$valid_picture_extension3);
                $w = 0;
                $h = 0;
                list($w, $h) = getimagesize($f1_tmpname);
                if ($w > 500 || $h > 500) {
                  echo '<p>Your picture is too big ('.$w.'x'.$h.'). Maximum size: 500x500</p>'.$back;
                }
                elseif ($f1_size > $maxsize_picture_upload) {
                  echo '<p>Your picture is too big. '.$f1_size.'KB, Maximum: '.$maxsize_picture_upload.'KB</p>'.$back;
                }
                else {
                  if (!in_array($ext,$valides)) {
                    echo '<p>Invalid image extension \''.$ext.'\'. Valid extensions are: '.$valid_picture_extension1." ".$valid_picture_extension2." ".$valid_picture_extension3."</p>".$back;
                  }
                  else {
                    $joindate = time();
                    $activeDate = $joindate;
                    $signup = md5($joindate.$name);
                    
                    $message = mysql_real_escape_string($message);
                    $alias = mysql_real_escape_string($alias);
                    
                    $sql = "INSERT INTO $playerstable (name, alias, pwd, mail, icq, aim, msn, " .
                      "country, nationality, approved, ip, joindate, activeDate, forum, " .
                      "sendGamesMail, sendDeductMail, sendNewsletter, uploadSpeed, downloadSpeed, message, versions, ".
                      "defaultversion, favteam1, favteam2, serial5, hash5, serial6, hash6, signup) " .
                      "VALUES ('$name','$alias', '$pwdHash','$mail','$icq','$aim', '$msn', " .
                      "'$country', '$nationality', '$approved', '$ip', '$joindate', '$activeDate', '$forum', " .
                      "'$gamesMail', '$deductMail', '$newsletter', '$uploadSpeed', '$downloadSpeed', '$message', ".
                      "'$versions', '$defaultversion', '$favteam1', '$favteam2', '$serial5', '$hash5', '$serial6', '$hash6', '$signup')";
                    $result = mysql_query($sql);
                    
                    $logJoin->logInfo('sql: '.$sql);
                    $logJoin->logInfo('result: '.$result);
                    
                    $sql = "SELECT player_id from $playerstable where name = '$name'";
                    $result = mysql_query($sql);
                    $row = mysql_fetch_array($result);
                    $player_id = $row['player_id'];
                    
                    $picturename = $player_id.'.'.$ext;
                    $copywork = rename($f1_tmpname, "./pictures/$picturename");
                    chmod("./pictures/$picturename", 0644);
                    
                    echo $createdmsg;
                    if ($country == "No country") {
                      echo $nocountrymsg;
                    }
                    
                    if (startsWith($ip, "41.") || startsWith($ip, "197.") || startsWith($ip, "105.")) {
                      $logJoin->logInfo('Not sending activation email for IP='.$ip);
                    } else {
                      sendActivation($player_id, $logJoin);
                    }
                  }
                }
              }
              else { // no picture
                $joindate = time();
                $activeDate = $joindate;
                $signup = md5($joindate.$name);
                
                $message = mysql_real_escape_string($message);
                $alias = mysql_real_escape_string($alias);
                
                $sql = "INSERT INTO $playerstable (name, alias, pwd, mail, icq, aim, msn, " .
                "country, nationality, approved, ip, joindate, activeDate, forum, " .
                "sendGamesMail, sendDeductMail, sendNewsletter, uploadSpeed, downloadSpeed, message, " .
                "versions, defaultversion, favteam1, favteam2, serial5, hash5, serial6, hash6, signup) " .
                "VALUES ('$name','$alias', '$pwdHash', '$mail','$icq','$aim', '$msn', " .
                "'$country', '$nationality', '$approved', '$ip', '$joindate', '$activeDate', '$forum', " .
                "'$gamesMail', '$deductMail', '$newsletter', '$uploadSpeed', '$downloadSpeed', '$message', '$versions', " .
                "'$defaultversion', '$favteam1', '$favteam2','$serial5', '$hash5', '$serial6', '$hash6', '$signup')";
                $result = mysql_query($sql);
                
                $logJoin->logInfo('sql: '.$sql);
                $logJoin->logInfo('result: '.$result);
                
                $sql = "SELECT player_id from $playerstable where name = '$name'";
                $result = mysql_query($sql);
                $row = mysql_fetch_array($result);
                $player_id = $row['player_id'];
                
                echo $createdmsg;
                if ($country == "No country") {
                  echo $nocountrymsg;
                }
                
                if (startsWith($ip, "41.") || startsWith($ip, "197.") || startsWith($ip, "105.")) {
                  $logJoin->logInfo('Not sending activation email for IP='.$ip);
                } else {
                  sendActivation($player_id, $logJoin);
                }
              }
            }
          }
        }
      }    
    }
    else {

      if (!isset($_GET['sid']) && $signupEmailRequired) {
        ?>
        <table class="layouttable" width="600"><tr><td>
        <p style="margin-bottom:10px">To get a quick overview, check out our <a href="/faq.php">FAQ</a>. We currently support these games: <?= getVersionsImagesNoSpace(getSupportedVersions()) ?></p>
        <p style="margin-bottom:10px">To be allowed to sign up for the league, please send an email <b>in English language</b> to <b><?= $admin_signup ?><?= $mailDomain ?></b> and tell us briefly  
        why you want to join - no lengthy explanations needed.</p>
        <p style="margin-bottom:10px">Only players that fully agree to the rules will be allowed to sign up
        - this especially means accepting defeat, being polite to the other players and being able to cope with the difficulties of 
        playing online like network lag. Your main reason to play here should be to have a good time playing the game online and meet new players, not necessarily 
        being the best player of the league. <? echo "<img align='middle' src='$directory/smileys/wink.gif' />" ?> </p>
        <p>After we received your email, we usually send you a sign-up link where you can enter your account details within 24 hours. Please be patient when you do not get an instant reply. If you don't hear from us for a few days, send another email or post in our <a href="http://www.yoursite/forum/">forum</a>.</p>
        <p><b>Important: </b>Some people were wondering why they did not receive an answer, and we have heard of some players finding our mail in their spam folder. If your email was readable, you should get an answer within 72 hours. If you didn't get an email from us, check your spam folder, and if it's not there, send your sign-up request again.</p>   
      </td></tr></table>
        <?
      } else {
      $num_rows = 0;
          if ($signupEmailRequired) {
            $sid = mysql_real_escape_string($_GET['sid']);
            $sql = "SELECT sid from $signuptable where sid='$sid' and expired='no' and used='no'";
            $result = mysql_query($sql);
        $num_rows = mysql_num_rows($result);
          }
          
      if ($signupEmailRequired && $num_rows != 1) {
        echo "<p>The signup link used is invalid or has expired.</p><p>You will need to request a new one from an administrator.</p>";
      } else {		      	
        
      ?>
          <form method="post" action="join.php?submit=1" onsubmit="return validateProfile();" enctype="multipart/form-data">

          <table class="formtable">
          <tr>
              <td>Nickname*</td>
              <td><input class="width150" type="Text" id="name" name="name" maxlength="15"></td>
              <td>Alphanumeric characters only (A-Z, 0-9)</td>
          </tr>
          <tr>
            <td>Email address*</td>
            <td><input class="width150" type="Text" name="mail" value=""></td>
            <td><b>Must be valid!</b></td>
          </tr>
          
          <tr>
            <td>Repeat Email address*</td>
            <td><input class="width150" type="Text" name="mail2" value=""></td>
            <td><b>Must be valid!</b></td>
          </tr>

          <tr>
              <td>Password*</td>
              <td><input type="password" id="password" class="width150" name="passworddb" maxlength="10"></td>
              <td>Alphanumeric characters only (A-Z, 0-9)<br>10 characters max.</td>
          </tr>
          
          <tr>
              <td>Repeat password*</td>
              <td><input type="password" class="width150" name="passwordrepeat" maxlength="10"></td>
              <td></td>
          </tr>

          <tr>
              <td>Location*</td>
              <td align="left"><select class="width150" name="country">
    <option></option>
    <option value="No country">No country</option>
    <?php
      $sql = "SELECT country FROM $countriestable ORDER BY COUNTRY ASC";
      $result = mysql_query($sql);
      while ($row = mysql_fetch_array($result)) {
        $row_country = $row['country'];	
        if ($row_country == $country) {
          $selected = "selected='selected'";
        } else {
          $selected = "";
        }
          echo '<option '.$selected.' value="'.$row_country.'">'.$row_country.'</option>';
      }
      ?>
              </select></td>
              <td>Where you are now</td>
         </tr>
         
         <tr>
              <td>Nationality</td>
              <td align="left"><select class="width150" name="nationality">
    <option></option>
    <option value="No country">No country</option>
    <?php
      $sql = "SELECT country FROM $countriestable ORDER BY COUNTRY ASC";
      $result = mysql_query($sql);
      while ($row = mysql_fetch_array($result)) {
        $row_country = $row['country'];	
        if ($row_country == $nationality) {
          $selected = "selected='selected'";
        } else {
          $selected = "";
        }
          echo '<option '.$selected.' value="'.$row_country.'">'.$row_country.'</option>';
      }
      ?>

              </select></td>
              <td>Where you are from</td>
          </tr>

          <?= getCheckboxesForSupportedVersions("H"); ?>

          <tr>
              <td>Default Game</td>
              <td><?= getSelectboxForSupportedVersions('') ?></td>
              <td>The game you play online the most often</td>
          </tr>		
          
          <!--
          <tr>
              <td>PES 5 serial</td>
              <td><input class="width150" maxlength="24" type="hidden" name="serial5" id="serial5" value=""></td>
              <td></td>
          </tr>
          -->
          
          <tr>
              <td>PES 6 serial</td>
              <td><input class="width150" maxlength="24" type="Text" name="serial6" id="serial6" value="">
              <input type="hidden" name="serial5" id="serial5" value=""></td>
              <td><b>Required for Sixserver</b> (no dashes)</td>
          </tr>

          <tr>
              <td style="padding-top:15px;padding-bottom:10px" colspan="3"><b>Optional information</b></td>
          </tr>

          <tr>
              <td>Group</td>
              <td><input class="width150" type="Text" name="forum" maxlength="30" value=""></td>
              <td>Displayed in Sixserver</td>
          </tr>

          <tr>
              <td>Picture</td>
              <td><input size="10" type="File" name="picture"></td>
              <td>A picture of yourself (max. size: 500x500)</td>
          </tr>

          <tr>
              <td>Alias</td>
              <td><input class="width150" type="Text" name="alias" maxlength="15" value="<?= $alias ?>"></td>
              <td>An alias, eg. a messenger or profile name (optional)</td>
          </tr>

          <tr>
            <td>Message</td>
            <td><input type="Text" class="width150" maxlength="40" name="message" value="<? echo $message ?>"></td>
            <td>A short message for your profile page</td>
          </tr>         

          <tr>
              <td>MSN Messenger</td>
              <td><input class="width150" type="Text" name="msn" value=""></td>
              <td></td>
          </tr>

          
        <tr>
        <td>Favorite team 1</td>
        <td>
          <select class="width150" name="favteam1">
          <?= getTeamsOptionsNone(false) ?>
          <?= getTeamsAllOptions(null) ?>
          </select>
        </td>
        <td></td>
      </tr>

      <tr>
        <td>Favorite team 2</td>
        <td>
          <select class="width150" name="favteam2">
          <?= getTeamsOptionsNone(false) ?>
          <?= getTeamsAllOptions(null) ?>
          </select>
        </td>
        <td></td>
      </tr>

          <tr>
            <td colspan="2" style="padding-top:15px;">
        <input name="gamesMail" type="checkbox" class="checkbox" 
        <? echo $checked; ?>/>&nbsp;&nbsp;Receive a daily game summary
        by email</td>
        <td style="padding-top:15px;">Only if you have played games, highly recommended!</td>
      </tr>
      <tr>
            <td colspan="2" class="padding-bottom:15px;">
        <input name="newsletter" type="checkbox" class="checkbox" 
        <? echo $checked; ?>/>&nbsp;&nbsp;Occasional <?= $leaguename ?> info</td>
        <td class="padding-bottom:15px;">Once or twice a year</td>
      </tr>
      <tr>
        <td class="padding-button">
                <input name="sid" type="hidden" value="<?= $sid ?>" />
                <input type="hidden" name="hash5" id="hash5" size="32" value=""/>
          <input type="hidden" name="hash6" id="hash6" size="32" value=""/>
          <input class="width150" type="Submit" name="submit" value="Join league">
        </td>
      </tr>             		
        </table>
        </form>

          <? 
      }
        }
    }
    ?>
    <?= getBoxBottom() ?>
    </td><td></td>
    </tr></table>
    <?
  }
} // only Sundays
?>
<?= getOuterBoxBottom() ?>
<?php
require('bottom.php');

function startsWith($haystack, $needle) {
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}
?>
