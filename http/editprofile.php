<?php

// this allows players to edit their profile. the menu entry will appear if the player is logged in
// as defined in /menu.php, if nobody is logged in a link to the signup page (join.php) is displayed instead.
 
   header("Cache-Control: no-cache");
   header("Pragma: no-cache");

   $page = 'players';
   $subpage = 'editprofile';

   require('./variables.php');
   require('./variablesdb.php');
   require('./functions.php');
   require('./top.php');

   require_once('log/KLogger.php');
   $logEdit = new KLogger('/var/www/yoursite/http/log/editprofile/', KLogger::INFO);	

   
if (! empty($_GET['name'])) {
    $name = mysql_real_escape_string($_GET['name']);
} 

$back = "<p><a href='javascript:history.back()'>go back</a></p>";
$checked = "checked='checked'";

?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, $name), "") ?>

<?php

   if(empty($cookie_name)) {
     echo "<p class='text'>You are not logged in.</p>";
   }
	else {
		if (! empty($_POST['submit'])) {
         $submit = mysql_real_escape_string($_POST['submit']);
      }
      else {
         $submit = '';
      }

      if (! empty($_POST['passworddb'])) {
         $passworddb = mysql_real_escape_string($_POST['passworddb']);
      }
      else {
         $passworddb = '';
      }

      if (! empty($_POST['passwordrepeat'])) {
         $passwordrepeat = mysql_real_escape_string($_POST['passwordrepeat']);
      }
      else {
         $passwordrepeat = '';
      }
      
      $pwd = password_hash($passworddb, PASSWORD_DEFAULT);
      
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
		if ($submit == 'Submit') {
      
       $na = "n/a";
       $alias1 = mysql_real_escape_string($_POST['alias1']);
       $alias1 = trim(strip_tags($alias1));
       $platform1 = mysql_real_escape_string($_POST['platform1']);
       $game1 = mysql_real_escape_string($_POST['game1']);
       $msn1 = mysql_real_escape_string($_POST['msn1']);
       $msn1 = strip_tags($msn1);
       $msn1 = trim($msn1);
       $icq1 = mysql_real_escape_string($_POST['icq1']);
       $icq1 = strip_tags($icq1);
       $icq1 = trim($icq1);
       $aim1 = mysql_real_escape_string($_POST['aim1']);
       $aim1 = strip_tags($aim1);
       $aim1 = trim($aim1);
       $mail1 = mysql_real_escape_string($_POST['mail1']);
       $mail1 = strip_tags($mail1);
       $mail1 = trim($mail1);
       $forum1 = mysql_real_escape_string($_POST['forum1']);
       $forum1 = strip_tags($forum1);
       $forum1 = trim($forum1);
       $country1 = mysql_real_escape_string($_POST['country1']);
       if (empty($country1)) {
          $country1 = "No country";
       }
     $favteam11 = mysql_real_escape_string($_POST['favteam11']);
     $favteam21 = mysql_real_escape_string($_POST['favteam21']);

     $nationality1 = mysql_real_escape_string($_POST['nationality1']);

      if (empty($nationality1) || $nationality1 == "") {
          $nationality1 = $country1;
      }

      $message1 = mysql_real_escape_string($_POST['message1']);
      $message1 = strip_tags($message1);
      $message1 = trim($message1);
               
       if (isset($_POST["gamesMail1"])) {
        $gamesMail1 = "yes";
       } else {
        $gamesMail1 = "no";
       }

       if (isset($_POST["deductMail1"])) {
        $deductMail1 = "yes";
       } else {
        $deductMail1 = "no";
       }
       
       if (isset($_POST["newsletter1"])) {
        $newsletter1 = "yes";
       } else {
        $newsletter1 = "no";
       }
       
       $uploadSpeed1 = mysql_real_escape_string($_POST["uploadSpeed1"]);
       $downloadSpeed1 = mysql_real_escape_string($_POST["downloadSpeed1"]);
                 
        if (empty($msn1)) { $msn1 = $na; }

        if (empty($icq1)) { $icq1 = $na; }
        if (empty($aim1)) { $aim1 = $na; }
        if (empty($mail1)) { $mail1 = $na; }
               
       if (strcmp($cookie_name, $alias1) == 0) {
        $alias1 = "";
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
			$defaultversion1 = mysql_real_escape_string($_POST['defaultversion']);
						 
			$sql = "SELECT version from $versionstable";
			$result = mysql_query($sql);
			$count = 0;
			$versions1 = "";
			while ($row = mysql_fetch_array($result)) {
				$count++;
				$version = $row['version'];
				if (isset($_POST['version_'.$version])) {
				    $versions1 .= $version;
				}
			}

			if (empty ($passworddb)) {
        echo "<p>Please supply a password.</p>".$back;
      }
      else if ($passworddb != $passwordrepeat) {
        echo "<p>Password and repetition do not match.</p>".$back;
      }
   			else if ($passworddb == $name || $passworddb == $name) {
			  echo "<p>Please choose a better password.</p>".$back;
		 	}
			 else if ($versions1 == "") {
		 	  echo "<p>Please select at least one game.</p>".$back;
		   }
			else if (!stristr($versions1, $defaultversion1)) {
		 	   echo "<p>The default game you selected is not one of the games you have.</p>".$back;
		    }
        else if (strlen($serial5) > 0 && strlen($serial5) != 20) {
          echo "<p>The PES 5 serial you entered is not valid.</p>".$back;
        }
        else if (strlen($serial6) > 0 && strlen($serial6) != 20) {
          echo "<p>The PES 6 serial you entered is not valid.</p>".$back;
        }
		    else {
                 $sql="SELECT * FROM $playerstable WHERE name='$cookie_name' AND pwd = '$cookieSessionId'";
	               $result = mysql_query($sql);
	               $num = mysql_num_rows($result);
	
	               if ($num > 0) {
                  $row = mysql_fetch_array($result);
                  $player_id = $row['player_id'];
                  
                  $maildb = $row['mail'];
                  $numEmail = 0;
                  if ($mail1 != $maildb) {
                    $sqlEmail = "SELECT * FROM $playerstable WHERE player_id<>".$player_id." AND mail='".$mail1."'";
                    $resultEmail = mysql_query($sqlEmail);
                    $numEmail = mysql_num_rows($resultEmail);
                  }
                  
                  If ($numEmail == 0) {
                          $signup = $row['signup'];
                          $invalidEmail = $row['invalidEmail'];
                          $rejected = $row['rejected'];
                          $approved = $row['approved'];
                          $signupSent = $row['signupSent'];
                          
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
                                  echo '<p>Your picture is too big. '.$f1_size.'KB, Maximum: '.$maxsize_picture_upload.'KB</p>';
                               }
                               else {
                                  if (!in_array($ext,$valides)) {
                                     echo '<p>Invalid image extension \''.$ext.'\'. Valid extensions are: '.$valid_picture_extension1." ".$valid_picture_extension2." ".$valid_picture_extension3."</p>";
                                  }
                                  else {
                                     $picturename = $player_id.'.'.$ext;
                                     // will only work on unix sytems, add unlink() for windows!
                         $copywork = rename($f1_tmpname, "./pictures/$picturename");
                         chmod("./pictures/$picturename", 0644);
                                     if ($copywork) echo '<p>Picture '.$picturename.' saved.</p>';
                                  }
                               }
                            }
          
                            if (!empty($_FILES['picture']['size']) && !$copywork) {
                               // echo "<p>Upload failed.</p>";
                            }
                            else {
                             

                
                       $sql = "UPDATE $playerstable SET alias = '$alias1',  ".
                        "mail = '$mail1', icq = '$icq1', " .
                        "aim = '$aim1', msn = '$msn1', country = '$country1', nationality = '$nationality1', " .
                        "passworddb = '', pwd='$pwd', forum='$forum1', sendGamesMail = '$gamesMail1', " .
                        "uploadSpeed = '$uploadSpeed1', downloadSpeed = '$downloadSpeed1', " .
                        "sendDeductMail = '$deductMail1', " .
                        "sendNewsletter = '$newsletter1', " .
                        "message = '$message1', versions = '$versions1', ".
                        "defaultversion='$defaultversion1', favteam1='$favteam11', favteam2 = '$favteam21', " . 
                        "serial5='$serial5', hash5='$hash5', serial6='$serial6', hash6='$hash6', invalidEmail=0 WHERE name='$cookie_name'";
                         $result = mysql_query($sql);
                         
                         $logEdit->logInfo('Profile saved, name='.$cookie_name.' pwd='.$passworddb);
                         $logEdit->logInfo('sql='.$sql);
                         
                         echo "<p>Your changes have been saved.</p>";
                         echo "<p>You may need to log in again for changes to take effect.</p>";
                         
                         if (($mail1 != $maildb) && ($rejected == 0) && ($approved == 'no') && ($signupSent == 1) && !$inactiveAndBanned) {
                           $link = "http://www.yoursite/activate.php?id=".$signup;
                           $res = sendActivationLinkMail($mail1, $name, $link);
                           echo "<p>An account activation link has been sent to <b>".$mail1."</b>.</p>";
                         }
                      }
                    } else {
                      echo "<p>The email address you entered is already in use by another account. Please note that only one account per player is allowed.</p>".$back;
                    }
	               } // end if num > 0
	               else {
	                  echo "<p>We couldn't find you in the database. Please try again or contact the admin if the problem persists.</p>".$back;
	               }
	            } // end if submit
              
            }
            else {
           $sql="SELECT * FROM $playerstable WHERE name = '$cookie_name'";
					 $result=mysql_query($sql,$db);
           $row = mysql_fetch_array($result);
           $player_id = $row['player_id'];
           $mail = $row["mail"];
           $name = $cookie_name;
           $icq = $row["icq"];
           $aim = $row["aim"];
           $msn = $row["msn"];
           $country = $row["country"];
					 $nationality = $row["nationality"];
           $forum = $row["forum"];
           $alias = $row["alias"];
           $gamesMail = $row["sendGamesMail"];
           $deductMail = $row["sendDeductMail"];
           $newsletter = $row["sendNewsletter"];
           
           $uploadSpeed = $row["uploadSpeed"];
           $downloadSpeed = $row["downloadSpeed"];
					 $message = $row["message"];
					 $versions = $row["versions"];
					 $defaultversion = $row["defaultversion"];
           $approved = $row["approved"];
					 $favteam1 = $row["favteam1"];
 					 $favteam2 = $row["favteam2"];
 					 $serial5 = $row["serial5"];
 					 $serial6 = $row["serial6"];
 					 if ($approved == "no") {
              $nameDisplay = "<font color='#FF0000'>".$name."</font>";
				   } else {
              $nameDisplay = $name;
           } 
                ?>

                 <table class="layouttable">
				 	<tr><td width="70%">
				 	 <?= getBoxTop("Edit Profile", "", false, null); ?>
                     <form method="post" action="editprofile.php<?
                     	if ($name != null) {
                     		echo "?name=".$name;
                     	}
                     ?>" onsubmit="return validateProfile();" enctype="multipart/form-data">
                     <table class="formtable">
                     <tr>
                        <td>Name</td>
                        <td><b><?= $nameDisplay ?></b></td>
                     </tr>
                     
                        <tr>
		            <td>Alias</td>
		            <td><input class="width150" type="Text" name="alias1" maxlength="15" value="<? echo $alias ?>"></td>
		            <td>An alias, eg. a forum or messenger name</td>
		         </tr>     

                 <tr>
                        <td>Location*</td>
                        <td align="left"><select class="width150" name="country1">
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
        echo "<option value=\"$row_country\" $selected>$row_country</option>";
    }
    ?>

                        </select></td>
                     <td>Your Location</td>
                     </tr>

		 <tr>
            <td>Nationality</td>
            <td align="left"><select class="width150" name="nationality1">
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
        echo "<option  value=\"$row_country\" $selected>$row_country</option>";
    }
    ?>

            </select></td>
            <td>Your nationality (only if different from location)</td>
          </tr>
		  
                     <tr>
                        <td>Picture</td>
                        <td><input size="10" type="File" name="picture"></td>
                        <td>A picture of yourself (max. size: 500x500)</td>
                     </tr>

    				  <?= getCheckboxesForSupportedVersions($versions); ?>

			         <tr>
			            <td>Default Game</td>
			            <td><?= getSelectboxForAllVersions($defaultversion) ?></td>
			            <td>The game you play online the most often</td>
			         </tr>		

                     <tr>
                        <td>Email address*</td>
                        <td><input class="width150" type="Text" name="mail1" value="<? echo $mail ?>"></td>
                        <td><b>Must be valid!</b></td>
                     </tr>

                     <tr>
                        <td>PES 6 serial</td>
                        <td><input class="width150" maxlength="24" type="Text" name="serial6"  id="serial6" value="<? echo $serial6 ?>"><input class="width150" maxlength="24" type="hidden" name="serial5" id="serial5" value="<? echo $serial5 ?>"></td>
                        <td>For <?= $leaguename ?> Sixserver (no dashes)</td>
                     </tr>

                      <tr>
            <td>Group</td>
            <td align="left">
               <input class="width150" type="Text" name="forum1" maxlength="30" value="<?= $forum ?>">
            </td>
            <td>Displayed in Sixserver</td>
           </tr>

                     <tr>
                        <td>ICQ</td>
                        <td><input class="width150" type="Text" name="icq1" value="<? echo $icq ?>"></td>
                        <td></td>
                     </tr>

                     <tr>
                        <td>MSN</td>
                        <td><input class="width150" type="Text" name="msn1" value="<? echo $msn ?>"></td>
                        <td></td>
                     </tr>

                     <tr>
                        <td>AIM</td>
                        <td><input class="width150" type="Text" name="aim1" value="<? echo $aim ?>"></td>
                        <td></td>
                     </tr>
                     
                     <tr>
                        <td>Upload speed</td>
                        <td><input class="width150" type="Text" maxlength="5" name="uploadSpeed1" value="<? echo $uploadSpeed ?>"></td>
                        <td class="boxlink">Bandwidth in <a tabindex="500" target="_new" href="http://en.wikipedia.org/wiki/Kbps">kbps</a> (eg. 128, 256, 512)</td>
                     </tr>
                     
                     <tr>
                        <td>Download speed</td>
                        <td><input class="width150" type="Text" maxlength="5" name="downloadSpeed1" value="<? echo $downloadSpeed ?>"></td>
                        <td><a href="http://www.dslreports.com/stest" target="_new">No idea? Use this.</a></td>
                     </tr>

					  <tr>
						<td>Message</td>
						<td><input class="width150" type="Text" maxlength="40" name="message1" value="<? echo $message ?>"></td>
						<td>A short message that will appear on your profile page</td>
					 </tr>    
					  
            <tr>
              <td>Favorite team 1</td>
						<td>
							<select class="width150" name="favteam11">
							<?= getTeamsOptionsNone(false) ?>
							<?= getTeamsAllOptions($favteam1) ?>
							</select>
						</td>
						<td></td>
					 </tr>
					 
					  <tr>
			            <td>Favorite team 2</td>
						<td>
							<select class="width150" name="favteam21">
							<?= getTeamsOptionsNone(false) ?>
							<?= getTeamsAllOptions($favteam2) ?>
							</select>
						</td>
						<td></td>
					 </tr>
					 
                     <tr>
                     	<td colspan="2" style="padding-top:15px;">
						<input name="gamesMail1" type="checkbox" class="checkbox" 
						<? if ($gamesMail == 'yes') { echo $checked; } ?>/>&nbsp;&nbsp;Daily game summary
						by email</td>
						<td style="padding-top:15px;">If you have played games, highly recommended!</td>
             		</tr>
             		<tr>
                     	<td colspan="2">
						<input name="newsletter1" type="checkbox" class="checkbox" 
						<? 
						if ($newsletter == 'yes') { 
							echo $checked; 
						} 
						?>/>&nbsp;&nbsp;Occasional info mail</td>
						<td>Only once or twice a year</td>
</tr>

<tr>
    <td style="padding-top:15px;padding-bottom:10px" colspan="3"><b>Enter current or new password to save your changes.</b></td>
</tr>

<tr>
  <td>Password*</td>
  <td><input type="password" id="password" class="width150" name="passworddb" maxlength="10"></td>
  <td>(10 characters max.)</td>
</tr>

<tr>
  <td>Repeat password*</td>
  <td><input type="password" class="width150" name="passwordrepeat" maxlength="10"></td>
  <td></td>
</tr>


<tr>
					 	<td colspan="2" class="padding-button">
							<input type="hidden" name="hash5" id="hash5" size="32" value=""/>
							<input type="hidden" name="hash6" id="hash6" size="32" value=""/>
							<input type="hidden" name="name" id="name" size="32" value="<?= $name ?>"/>
					 		<input type="Submit" name="submit" value="Submit" class="width150">
					 	</td>
					 </form>

					 </tr>
                   </table>
				<?= getBoxBottom() ?>
					   				   
                   </td>
				   <td style="width:30%; vertical-align:top">
                    <?
                       $imgpathGif = './pictures/'.$player_id.'.gif';
					   $imgpathJpg = './pictures/'.$player_id.'.jpg';
					   $imgpath = "";
					   if (is_readable($imgpathGif) && is_readable($imgpathJpg)) { 
					   		$timeGif = filemtime($imgpathGif);	
					   		$timeJpg = filemtime($imgpathJpg);
							if ($timeGif > $timeJpg) {
								$imgpath = $imgpathGif;
							} else {
								$imgpath = $imgpathJpg;
							}
					   } else if (is_readable($imgpathGif)) { 
					   	$imgpath = $imgpathGif;
					   } else if (is_readable($imgpathJpg)) {
					   	$imgpath = $imgpathJpg;
					   }
					   
					   if (!empty($imgpath)) {
						?>    
						<?= getBoxTop("User Image", "", false, null); ?>
					      <img src="<? echo $imgpath ?>" />
						<?= getBoxBottom() ?>
					   <? } ?>
               </td>
            </tr></table>
      <?php
      }
   }
?>
<?= getOuterBoxBottom() ?>
<?
require('./bottom.php');
?>
