<?php

// this will handle the login of admins and display menu links according to their 
// permissions (news admin, approve admin, season admin, full admin)

// full admin includes news/approve/season as well as additional features (deductPoints, pruneUnused) 

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "index";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./functions.php');
require('./../top.php');

$log = new KLogger('/var/www/yoursite/http/log/admin/', KLogger::INFO);

?>
<script language="javascript">
	<!--
		var teamsArray = new Array(<?= getTeamsAllJavascript() ?>);
	-->
</script>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Administration Index", "Season ".$season); ?>
<?
if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {
?>

<table class="layouttable">
<tr>
<td width="60%" valign="top">

<?php
		$resultMsg = "";
		
		if (isset($_POST['teamCategoryId'])) {
			$teamCategoryId = mysql_real_escape_string($_POST['teamCategoryId']);
			$teamCategoryValue = mysql_real_escape_string($_POST['teamCategoryValue']);
			
			$sql = "SELECT name, category FROM $teamstable where id = '$teamCategoryId'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) != 1) {
				$resultMsg = "<p>Selected team not found!</p>";
			} else {
				$row = mysql_fetch_array($result);
				$teamName = $row['name'];
				$oldCategory = $row['category'];
				$sql = "UPDATE $teamstable SET category='$teamCategoryValue' WHERE id = '$teamCategoryId'";
				$result = mysql_query($sql);
				$resultMsg .= "<p>Updating team category <b>$teamName</b> from <b>$oldCategory</b> to <b>$teamCategoryValue</b>... Result: <b>$result</b></p>";
			} 
			
		} else if (isset($_POST['stickTopicId'])) {
			$stickTopicId = mysql_real_escape_string($_POST['stickTopicId']);
      $stickTopicPrio = mysql_real_escape_string($_POST['stickTopicPrio']);
      if (!is_numeric($stickTopicId)) {
        $stickTopicPrio = 0;
      }
			if (!is_numeric($stickTopicId)) {
				$resultMsg .= "<p>Invalid topicId: $stickTopicId</p>";
			} else {
				$sql = "SELECT * FROM $topicstable where topic= '".$stickTopicId."'";
				$result = mysql_query($sql);
				
				if (mysql_num_rows($result) != 1) {
					// not found -> create new entry
					$sql2 = "INSERT INTO $topicstable (topic, active, prio) VALUES ('$stickTopicId', 'Y', '$stickTopicPrio')";
					$resulti = mysql_query($sql2);
					$resultMsg .= "<p>Creating sticky entry for topic <b>$stickTopicId</b>, prio <b>$stickTopicPrio</b>... <b>$resulti</b></p>";
				} else {
					$row = mysql_fetch_array($result);
					$active = $row['active'];
					if ($active == 'Y') {
						$sql2 = "UPDATE $topicstable SET active = 'N', prio = '$stickTopicPrio' where topic = '$stickTopicId'";
						$resulti = mysql_query($sql2);
						$resultMsg .= "<p>Deactivating sticky entry for topic <b>$stickTopicId</b>... <b>$resulti</b></p>";
					} else {
						$sql2 = "UPDATE $topicstable SET active = 'Y' where topic = '$stickTopicId'";
						$resulti = mysql_query($sql2);
						$resultMsg .= "<p>Activating sticky entry for topic <b>$stickTopicId</b>... <b>$resulti</b></p>";
					}
				}
			}	
		} else if (isset($_POST['changeCategoryGoalId'])) {
			$categoryGoalId = mysql_real_escape_string($_POST['changeCategoryGoalId']);
			$category = mysql_real_escape_string($_POST['changeCategory']);
			
			$sql = "SELECT type FROM $goalstable where id = '$categoryGoalId'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) != 1) {
				$resultMsg = "<p>Invalid goal ID <b>$categoryGoalId</b>!</p>";
			} else {
				$sql = "UPDATE $goalstable SET type='$category' WHERE id = '$categoryGoalId'";
				$result = mysql_query($sql);
				$resultMsg .= "<p>Updating goal <b>$categoryGoalId</b> to type <b>$category</b>... <b>$result</b></p>";
			} 
		} else if (isset($_POST['createGoalFile'])) {
			$filename = mysql_real_escape_string($_POST['createGoalFile']);
			$player = mysql_real_escape_string($_POST['createGoalPlayer']);
			$category = mysql_real_escape_string($_POST['createGoalCategory']);
			$comment = mysql_real_escape_string($_POST['createGoalComment']);
			$goalpath = '../files/goals/';
			if (empty($comment)) {
				$resultMsg .= "<p>Empty comment!</p>";
			} else if (empty($category) || strlen($category) != 1) {
				$resultMsg .= "<p>Invalid category <b>$category</b>!</p>";
			} else if (empty($filename) || !file_exists($goalpath.$filename)) {
				$resultMsg .= "<p>File <b>$filename</b> not found!</p>"; 
			} else {
				
				$sql = "Select player_id from $playerstable where name = '$player'";
				$result = mysql_query($sql);
				if (mysql_num_rows($result) != 1) {
					$resultMsg .= "<p>Player <b>$player</b> not found!</p>";
				} else {
					$extension = strtolower(substr($filename, strrpos($filename, ".") + 1));
					$uploaded = time();
					$row = mysql_fetch_array($result);
					$player_id = $row['player_id'];
					$filenameNew = $player_id."_".$uploaded.".".$extension;
					
					$renamed = rename($goalpath.$filename, $goalpath.$filenameNew);
					$resultMsg .= "<p>Renaming <b>$filename</b> to <b>$filenameNew</b> file... <b>$renamed</b></p>";					
					
					if ($renamed == 1) {
						$sqli = "INSERT INTO $goalstable (type, player_id, uploaded, extension, comment) ".
							"VALUES ('$category', '$player_id', '$uploaded', '$extension', '$comment')";
						$resulti = mysql_query($sqli);
						$resultMsg .= "<p>Creating goal table entry for filename <b>$filenameNew</b>... <b>$resulti</b></p>";					
					
					}
					$resultMsg .= '<p>Go to <a href="/files/convert.php"><b>video image conversion</b></a></p>';
				}
			}
		} else if (isset($_POST['resetGoalId'])) {
			$resetGoalId = mysql_real_escape_string($_POST['resetGoalId']);
			
			$sql = "UPDATE $goalstable set hasThumb = '' WHERE id = '$resetGoalId'";
			$result = mysql_query($sql);
			
			$resultMsg .= "<p>Resetting images flag for goal entry <b>#$resetGoalId</b> - Result: <b>$result</b></p>";
			$resultMsg .= '<p>Go to <a href="/files/convert.php"><b>video image conversion</b></a></p>';
		}
		else if (isset($_POST['deleteGoalId'])) {

			$deleteGoalId = mysql_real_escape_string($_POST['deleteGoalId']);

			$sql = "SELECT * FROM $goalstable where id = '$deleteGoalId'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) == 0) {
				$resultMsg .= "<p>Goal entry <b>#$deleteGoalId</b> does not exist!</p>";
			} else {
				
				$row = mysql_fetch_array($result);
				$player_id = $row['player_id'];
				$uploaded = $row['uploaded'];
				$extension = $row['extension'];
				
				$fileNameStripped = $player_id."_".$uploaded;
				$fileName = $fileNameStripped.".".$extension;
				$thumb = $wwwroot.'files/goals/thumbnails/'.$fileNameStripped."_thumb.png";
				if (file_exists($thumb)) {
					unlink($thumb);
					$resultMsg .= "<p>Deleted $thumb</p>";
				} else {
					$resultMsg .= "<p>Thumbnail not deleted - No file at <br>'$thumb'</p>";
				}
				$ani = $wwwroot.'files/goals/thumbnails/'.$fileNameStripped."_anim.gif";
				if (file_exists($ani)) {
					unlink($ani);
					$resultMsg .= "<p>Deleted $ani</p>";
				} else {
					$resultMsg .= "<p>Animation not deleted -  No file at<br> '$ani'</p>";
				}
				$file = $wwwroot.'files/goals/'.$fileName;
				if (file_exists($file)) {
					unlink($file);
					$resultMsg .= "<p>Deleted $file</p>";
				} else {
					$resultMsg .= "<p>Video file not deleted - No file at <br>'$file'</p>";
				}
				
				$sql = "DELETE FROM $goalstable where id = '$deleteGoalId'";
				$result = mysql_query($sql);
				$resultMsg .= "<p>Deleting entry <b>#$deleteGoalId</b> in goals table - Result: <b>$result</b></p>";
			}
			  
		} else if (isset($_POST['playercheck'])) {
      $playercheck = mysql_real_escape_string($_POST['playercheck']);
      $sql = "select * from $playerstable WHERE name ='$playercheck'";
      $result = mysql_query($sql);
      if (mysql_num_rows($result) > 0) {
        while($row = mysql_fetch_array($result)) {
          $resultMsg .= "id: ".$row['player_id']."<br>";
          $resultMsg .= "Name: ".$row['name']."<br>";
          $resultMsg .= "Approved: ".$row['approved']."<br>";
          $resultMsg .= "Mail: ".$row['mail']."<br>";
          $resultMsg .= "Serial6: ".$row['serial6']."<br>";
          $resultMsg .= "Hash6: ".$row['hash6']."<br>";
          $resultMsg .= "invalidEmail: ".$row['invalidEmail']."<br>";
          $resultMsg .= "signup: ".$row['signup']."<br>";
          $resultMsg .= "signupSent: ".$row['signupSent']."<br>";
          $resultMsg .= "rejected: ".$row['rejected']."<br>";
          $resultMsg .= "rejectReason: ".$row['rejectReason']."<br>";
        } 
      }
    } else if (isset($_POST['mailcheck'])) {
        $mailcheck = mysql_real_escape_string($_POST['mailcheck']);
          $sql = "select name, mail, msn from $playerstable ".
          "where mail like '%$mailcheck%' or msn like '%$mailcheck%' or name like '%$mailcheck%'";
        $result = mysql_query($sql);
        if (mysql_num_rows($result) > 0) {
          $resultMsg = "<table class='layouttable'>";
          while($row = mysql_fetch_array($result)) {
            $name = $row['name'];
            $mail = $row['mail'];
            $msn = $row['msn'];
            $resultMsg .= "<tr style='font-size:10px'><td><b>Name</b><br>".$name."</td><td><b>Email</b><br>".$mail.
              " </td><td><b>MSN</b><br>".$msn."</td></tr>";
          } // while
          $resultMsg .= "</table>";
        }
        else {
          $resultMsg = "<p>No matches found</p>";
        }
    	} 
    	else if (isset($_POST['access'])) {
    		$checkString = mysql_real_escape_string($_POST['access']);
        $checkString2 = mysql_real_escape_string($_POST['access2']);
    		$res = getLastOnline($checkString, $checkString2, 1000);	 
    		$resultMsg = $res;
    	}
      else if (isset($_POST['sixDeleteFinishedGameId'])) {
        $sixDeleteFinishedGameId = mysql_real_escape_string($_POST['sixDeleteFinishedGameId']);
        $resultMsg = DeleteFinishedSixserverGame($sixDeleteFinishedGameId);
      }
      else if (isset($_POST['sixDeleteUnfinishedGameId'])) {
        $sixDeleteUnfinishedGameId = mysql_real_escape_string($_POST['sixDeleteUnfinishedGameId']);
        $resultMsg = DeleteUnfinishedSixserverGame($sixDeleteUnfinishedGameId);
      }
      else if (isset($_POST['sixChangeGameId'])) {
        $sixChangeGameId = $_POST['sixChangeGameId'];
        $sixChangePlayerName = $_POST['sixChangePlayerName'];
        $sixChangeTeamTxt = $_POST['sixChangeTeamTxt'];
        $sql = "SELECT *, spHome.id as patchIdHome, spAway.id as patchIdAway
          FROM six_matches_played smp
          LEFT JOIN six_profiles sp ON smp.profile_id = sp.id
          LEFT JOIN weblm_players wp ON sp.user_id = wp.player_id
          LEFT JOIN six_matches sm ON sm.id = smp.match_id 
          LEFT JOIN six_patches spHome ON spHome.hash = sm.hashHome
          LEFT JOIN six_patches spAway ON spAway.hash = sm.hashAway
          WHERE smp.match_id = $sixChangeGameId
          AND wp.name = '$sixChangePlayerName'";
        $resultMsg = $sql."<br>";
        $result = mysql_query($sql);
        if ($row = mysql_fetch_array($result)) {
           $resultMsg .= "<p>patchIdHome:".$row['patchIdHome']." patchIdAway:".$row['patchIdAway'];
           $resultMsg .= "<p>team_id_home:".$row['team_id_home']." team_id_away:".$row['team_id_away'];
           if ($row['home'] == 1) {
              $sixTeamId = $row['team_id_home'];
              $patchId = $row['patchIdHome'];
           } else {
              $sixTeamId = $row['team_id_away'];
              $patchId = $row['patchIdAway'];
           }
           
           $sql = "SELECT ID FROM weblm_teams WHERE NAME='".$sixChangeTeamTxt."'";
           $result = mysql_query($sql);
           $resultMsg .= "<p>team sql:$sql";
           if ($row = mysql_fetch_array($result)) {
             $ladderTeamId = $row['ID'];
             
             $sql = "SELECT st.*, UNIX_TIMESTAMP(st.insertDate) as insertDateTS, wp.name FROM six_teams st ".
              "LEFT JOIN weblm_players wp on wp.player_id=st.playerId ".
              "WHERE patchId=$patchId AND sixTeamId=$sixTeamId";
             $res = mysql_query($sql);
             
             $resultMsg .= "<p>check sql:$sql";
             
             while ($row = mysql_fetch_array($res)) {
              $resultMsg .= "<p>Deleting: patchId=".$row['patchId']." sixTeamId=".$row['sixTeamId'].
                " ladderTeamId=".$row['ladderTeamId']." playerId=".$row['playerId'].
                " name=".$row['name']." insertDate=".formatLongDate($row['insertDateTS']);
             }
             
             $sql = "DELETE FROM six_teams WHERE patchId=$patchId AND sixTeamId=$sixTeamId";
             mysql_query($sql);
             $resultMsg .= "<p>delete sql:$sql";
             
             $sql = "INSERT INTO six_teams (patchId, sixTeamId, ladderTeamId, playerId) ".
                "VALUES ('".$patchId."','".$sixTeamId."','".$ladderTeamId."', '1')";
             mysql_query($sql);
             $resultMsg .= "<p>insert sql:$sql";
           }
        }
      }
      else if (isset($_POST['sixPatchId'])) {
        $sixPatchId = $_POST['sixPatchId'];
        $sixTeamId = $_POST['sixTeamId'];
        $sixLadderTeamTxt = $_POST['sixLadderTeamTxt'];
        
        $sql = "SELECT ID FROM weblm_teams WHERE NAME='".$sixLadderTeamTxt."'";
        $result = mysql_query($sql);
        $resultMsg .= "<p>patch id: $sixPatchId team sql:$sql";
        
        if ($row = mysql_fetch_array($result)) {
          $ladderTeamId = $row['ID'];

          $sql = "SELECT st.*, UNIX_TIMESTAMP(st.insertDate) as insertDateTS, wp.name FROM six_teams st ".
          "LEFT JOIN weblm_players wp on wp.player_id=st.playerId ".
          "WHERE patchId=$sixPatchId AND sixTeamId=$sixTeamId";
          $res = mysql_query($sql);

          $resultMsg .= "<p>check sql:$sql";

          while ($row = mysql_fetch_array($res)) {
            $resultMsg .= "<p>Deleting: patchId=".$row['patchId']." sixTeamId=".$row['sixTeamId'].
              " ladderTeamId=".$row['ladderTeamId']." playerId=".$row['playerId'].
              " name=".$row['name']." insertDate=".formatLongDate($row['insertDateTS']);
          }

          $sql = "DELETE FROM six_teams WHERE patchId=$sixPatchId AND sixTeamId=$sixTeamId";
          mysql_query($sql);
          $resultMsg .= "<p>delete sql:$sql";
          
          InsertTeamAndUpdate($sixPatchId, $sixTeamId, $ladderTeamId, $log, $season, 'Ike');
          $resultMsg .= "<p>InsertTeamAndUpdate done.</p>";
        }
      }
    	else if (isset($_POST['sixSourceProfile'])) {
        $sixSourceProfile = $_POST['sixSourceProfile'];
        $sixTargetProfile = $_POST['sixTargetProfile'];
        $sql = "SELECT name, id FROM six_profiles WHERE name='".$sixSourceProfile."'";
        if ($row = mysql_fetch_array(mysql_query($sql))) {
          $sixSourceProfile = $row[0];
          $profileId = $row['id'];
          $sql = "SELECT name FROM six_profiles WHERE name='".$sixTargetProfile."'";
          if ($row = mysql_fetch_array(mysql_query($sql))) {
            $resultMsg .= "<p>Target already exists: $sixTargetProfile</p>";
          } else {
            // user on server?
            
            $xml_query = "http://127.0.0.1:8192/stats";
            try {
              $xml = @simplexml_load_file($xml_query);
              if (!$xml) {
                $resultMsg .= "<p>Error opening $xml_query!</p>";
              } else {
                $users = array();
                foreach( $xml->lobbies->lobby as $lobby ) {
                  foreach( $lobby->user as $user) {
                    $users[] = (string) $user->attributes()->profile;
                  }
                }
                if (in_array(strtolower($sixSourceProfile), array_map('strtolower', $users))) {
                  $resultMsg .= "<p>Profile '$sixSourceProfile' appears to be online on the server right now. Not renaming.</p>";
                } else {
                  $sql = "UPDATE six_profiles SET name='$sixTargetProfile' WHERE id=$profileId";
                  $resultMsg .= "<p>SQL: $sql</p>";
                  mysql_query($sql);
                  $resultMsg .= "<p>Affected: ".mysql_affected_rows()."</p>";
                }
              }
            } catch (Exception $e) {
              $resultMsg .= "<p>Error parsing $xml_query: ".$e->getMessage()."</p>";
            }
          }
        } else {
          $resultMsg .= "<p>Source not found: $sixSourceProfile</p>";
        }
      }
      else if (isset($_POST['sixmaintenance'])) {
    		$sixmaintenance = mysql_real_escape_string($_POST['sixmaintenance']);
    		$sql = "UPDATE six_stats SET maintenance=".$sixmaintenance;
        mysql_query($sql);
    		$resultMsg = "Maintenance set to ".$sixmaintenance." - affected: ".mysql_affected_rows();
    	}
      else if (isset($_POST['sixdebug'])) {
    		$sixdebug = mysql_real_escape_string($_POST['sixdebug']);
    		$sql = "UPDATE six_stats SET DebugMode=".$sixdebug;
        mysql_query($sql);
    		$resultMsg = "DebugMode set to ".$sixdebug." - affected: ".mysql_affected_rows();
      }
  
      else if (isset($_POST['scriptPath'])) {
        $scriptPath = $_POST['scriptPath'];
        $resultMsg = "<p>Executing script $scriptPath...</p>";
        $output = shell_exec($scriptPath);
        $resultMsg .= "<p>Script executed.</p>";
        $resultMsg .= "<p>$output</p>";
      }
      else if (isset($_POST['sendActivationName'])) {
        $sendActivationName = Trim(mysql_real_escape_string($_POST['sendActivationName']));
        $sql = "SELECT player_id from weblm_players WHERE name='$sendActivationName'";
        $result = mysql_query($sql);
        if (mysql_num_rows($result) != 1) {
          $resultMsg .= "<p>Player <b>$sendActivationName</b> not found in database.</p>";
        }
        else {
          $row = mysql_fetch_array($result);
          $resultMsg .= sendActivation($row[0], $log);
        }
      }
      else if (isset($_POST['resetDC'])) {
        $resetDC = Trim(mysql_real_escape_string($_POST['resetDC']));
        $sql = "UPDATE six_matches_status SET updated=updated, dc=NULL where id=$resetDC";
        mysql_query($sql);
    		$resultMsg = "<p>Updated <b>".mysql_affected_rows()."</b> row(s)</p>";
      }
      else if (isset($_POST['invalidEmail'])) {
    		$invalidEmail = mysql_real_escape_string($_POST['invalidEmail']);
    		$sql = "UPDATE ".$playerstable." SET invalidEmail=1 WHERE mail='".$invalidEmail."'";
        mysql_query($sql);
    		$resultMsg = "<p>Updated <b>".mysql_affected_rows()."</b> row(s)</p>";
    	} 
    	else if (isset($_POST['signupAddress'])) {
    		$toAddress = mysql_real_escape_string($_POST['signupAddress']);
    		$time = time();
    		$sql = "insert into $signuptable (id, sid, expired, used) values ('', '$time', 'no', 'no')";
            $result = mysql_query($sql);
    		$signuplink = $directory."/join.php?sid=".$time;
    		$resSend = sendSignupMail($toAddress, $signuplink, $username); 
    		$resultMsg = "<p>Sign-up mail sent to <b>".$toAddress."</b></p>";
    		$resultMsg .= "<p>Log result: ".$resSend."</p>";
    	} 
    	else if (isset($_POST['toggleApproved'])) {
        $togglePlayer = mysql_real_escape_string($_POST['toggleApproved']);
        $sql = "SELECT approved, name, player_id from $playerstable where name = '$togglePlayer'";
        $result = mysql_query($sql);
        if (mysql_num_rows($result) != 1) {
          $resultMsg .= "<p>Player <b>$togglePlayer</b> not found in database.</p>";
        }
        else {
          $row = mysql_fetch_array($result);
          $approved = $row['approved'];
          $togglePlayer = $row['name'];
          $player_id = $row['player_id'];
				
          $activeDate = time();
          if ($approved == 'yes') {
            $newApproved = "no";
            $activeClause = "";
          }
          else {
            $newApproved = 'yes';
            $activeClause = ", activeDate = '$activeDate'";

            // disable all inactive or ban entries 
            $sql2 = "UPDATE $playerstatustable SET active = 'N' " .
                "WHERE userName = '$togglePlayer' " .
                "AND type != 'W'";
            $result2 = mysql_query($sql2);
          } 

          $sql = "UPDATE $playerstable SET approved = '$newApproved' $activeClause WHERE name = '$togglePlayer'";
          $result = mysql_query($sql);		
          
      if ($result == 1) {
          $resultMsg .= '<p><b><a href="/profile.php?name='.$togglePlayer.'">'.$togglePlayer.'</a></b> approve status set to <b>'.$newApproved.'</b></p>';
	            } 
	            else {
	                $resultMsg .= "<p>Error changing status for <b>$togglePlayer</b>!</p>";
	            }
            } 
        } else if (isset($_POST['oldName'])) {
            $oldName = mysql_real_escape_string($_POST['oldName']);
            $newName = mysql_real_escape_string($_POST['newName']);
            $sql = "SELECT name FROM $playerstable WHERE name = '$newName'";
            $result = mysql_query($sql);
            if (mysql_num_rows($result) > 0) {
              $row = mysql_fetch_array($result);
              $newName = $row['name'];
              $resultMsg .= '<p><b>Not renamed</b> - Player <b><a href="/profile.php?name='.$newName.'">'.$newName.'</a></b> already exists!</p>';   	
            } else {
              $sql = "UPDATE $gamestable set winner = '$newName' where winner = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $gamestable set winner2 = '$newName' where winner2 = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $gamestable set loser = '$newName' where loser = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $gamestable set loser2 = '$newName' where loser2 = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $playerstable set name = '$newName' where name = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $historytable set player_name = '$newName' where player_name = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $logtable set user = '$newName' where user = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $donationstable set name = '$newName' where name = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $playerstatustable set userName = '$newName' where userName = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $tournamenttable set firstPlace = '$newName' where firstPlace = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $tournamenttable set secondPlace = '$newName' where secondPlace = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $tournamenttable set thirdPlace = '$newName' where thirdPlace = '$oldName'";
              $result = mysql_query($sql);
              $sql = "UPDATE $leaguestable set player = '$newName' where player = '$oldName'";
              $result = mysql_query($sql);
              $sql = "Select username from phpbb3_users where username = '$oldName'";
              $result = mysql_query($sql);
              $num = mysql_num_rows($result);
              $resultMsg = "<p>Renamed <b>$oldName</b> to <b>$newName</b>.</p>";
              if ($num > 0) {
                $sql = "UPDATE phpbb_users set username = '$newName' where userName = '$oldName'";
                $result = mysql_query($sql);
                $resultMsg .= "<p>Forum name changed from <b>$oldName</b> to <b>$newName</b>.</p>";   
              }
              else {
                $resultMsg .= "<p>Forum name could not be found and was <b>NOT</b> changed!</p>";
              }
            }				
        } else if (isset($_POST['removeGameId'])) {
            $removeGameId = mysql_real_escape_string($_POST['removeGameId']);
            $deleteReason = mysql_real_escape_string($_POST['deleteReason']);
            if (isset($_POST['removeGameIdEnd']) && !empty($_POST['removeGameIdEnd'])) {
              $removeGameIdEnd = mysql_real_escape_string($_POST['removeGameIdEnd']);
              
              if ($removeGameId > $removeGameIdEnd) {
                while ($removeGameId >= $removeGameIdEnd) {
                  $resultMsg .= RemoveLadderGame($removeGameId, $deleteReason);
                  $removeGameId--;
                }
              }
            } else {
              $resultMsg = RemoveLadderGame($removeGameId, $deleteReason);
            }
        } 
        else if (isset($_POST['removeGamesForPlayer'])) {
          $removeGamesForPlayer = mysql_real_escape_string($_POST['removeGamesForPlayer']);
          // remove ladder games
          $sql = "SELECT game_id FROM weblm_games 
            WHERE winner2='' AND loser2='' AND winner='$removeGamesForPlayer' AND season=$season AND deleted='no' 
            ORDER BY game_id DESC";
          $result = mysql_query($sql);
          while ($row = mysql_fetch_array($result)) {
            $resultMsg .= "<p>Deleting Ladder Game ID #".$row[0];
            $resultMsg .= RemoveLadderGame($row[0], "Bulk delete");
          }
          // remove finished sixserver games
          
          $sql = "SELECT season FROM six_stats";
          $sixSeason = mysql_fetch_array(mysql_query($sql))[0];

          $sql = "SELECT six_matches.id, six_matches.score_home, six_matches.score_away, six_matches_played.home
            FROM six_matches 
            LEFT JOIN six_matches_played ON six_matches_played.match_id=six_matches.id 
            LEFT JOIN six_profiles ON six_matches_played.profile_id=six_profiles.id 
            LEFT JOIN weblm_players ON weblm_players.player_id=six_profiles.user_id 
            WHERE weblm_players.name='".$removeGamesForPlayer."'  
            AND six_matches.season=".$sixSeason." 
            ORDER BY six_matches.id DESC";
          // $resultMsg.= "<p>SQL: ".$sql;
          $result = mysql_query($sql);
          while ($row = mysql_fetch_array($result)) {
            // Delete game if player is winner or it's a draw
            $sixGameId = $row['id'];
            $home = $row['home'];
            $scoreHome = $row['score_home'];
            $scoreAway = $row['score_away'];
            if (($home == 1 && ($scoreHome > $scoreAway)) || ($home == 0 && ($scoreHome < $scoreAway)) || $scoreHome==$scoreAway) {
              $resultMsg .= "<p>Deleting finished Sixsever Game ID #".$sixGameId;
              $resultMsg .= DeleteFinishedSixserverGame($sixGameId);
            }
          }

          // remove unfinished sixserver games where this player is the winner
          $sqlProfiles = "SELECT six_profiles.id FROM six_profiles LEFT JOIN ".$playerstable." ON ".
          $playerstable.".player_id=six_profiles.user_id WHERE ".$playerstable.".name='".$removeGamesForPlayer."'";
          $inSelect = "IN (";
          $profiles = array();
          $resProfiles = mysql_query($sqlProfiles);
          while ($rowProfiles = mysql_fetch_array($resProfiles)) {
            $profiles[] = $rowProfiles['id'];
            $inSelect = $inSelect."'".$rowProfiles['id']."',";	
          }
           
          $inSelect = substr($inSelect,0,strlen($inSelect)-1);
          $inSelect .= ")";
          
          $sql = "SELECT sms.id
            FROM six_matches_status sms 
            WHERE (sms.profileHome ".$inSelect." OR sms.profileHome2 ".$inSelect." OR sms.profileHome3 ".$inSelect." OR sms.profileAway ".$inSelect." OR sms.profileAway2 ".$inSelect." OR sms.profileAway3 ".$inSelect.") AND season=".$sixSeason." ORDER BY sms.id DESC";
          
          $resultMsg.= "<p>SQL: ".$sql;
          $result = mysql_query($sql);

          while ($row = mysql_fetch_array($result)) {
            $sixGameId = $row[0];
            $resultMsg .= "<p>Deleting unfinished Sixserver Game ID #".$sixGameId;
            $resultMsg .= DeleteUnfinishedSixserverGame($sixGameId);
          }
        }
        else if (isset($_POST['restoreGameId'])) {
            $restoreGameId = mysql_real_escape_string($_POST['restoreGameId']);
            if (isset($_POST['restoreGameIdEnd']) && !empty($_POST['restoreGameIdEnd'])) {
              $restoreGameIdEnd = mysql_real_escape_string($_POST['restoreGameIdEnd']);
              if ($restoreGameId < $restoreGameIdEnd) {
                while ($restoreGameId <= $restoreGameIdEnd) {
                  $resultMsg .= RestoreLadderGame($restoreGameId);
                  $restoreGameId++;
                }
              }
            } else {
              $resultMsg .= RestoreLadderGame($restoreGameId);
            }
        
        } else if (isset($_POST['checkGame'])) {
            $checkGame = mysql_real_escape_string($_POST['checkGame']);
            $sql = "SELECT winner, loser, date from $gamestable WHERE game_id = $checkGame";
            $result = mysql_query($sql);
            $num = mysql_num_rows($result);
            if ($num != 1) {
                $resultMsg .= "<p>Found $num games: $sql</p>";
            } else {
                $gameRow = mysql_fetch_array($result);
                $winner = $gameRow['winner'];
                $loser = $gameRow['loser'];
                $gameDate = $gameRow['date'];
                $starttime = $gameDate - 60 * 60 * 2;
                $endtime = $gameDate + 60 * 60;

                $sql = "SELECT accesstime, user from $logtable " . "WHERE (user = '$winner' OR user = '$loser') " . "AND (accesstime BETWEEN $starttime and $endtime) " . "ORDER BY accesstime desc";

                $result = mysql_query($sql);
                $num = mysql_num_rows($result);
				
                $resultMsg .= getLastOnline($winner, $loser, 10);
                $resultMsg .= "<p>Game <b>#".$checkGame."</b>&nbsp;&nbsp;-&nbsp;&nbsp;played " . formatDate($gameDate) . "</p>";

                while ($row = mysql_fetch_array($result)) {
                    $user = $row['user'];
                    $accesstime = $row['accesstime'];
                    $resultMsg .= "<b>" . formatDate($accesstime) . "</b>&nbsp;&nbsp;-&nbsp;&nbsp;".$user."&nbsp;&nbsp;";
                    if ($accesstime == $gameDate) {
                        $resultMsg .= "&nbsp;&nbsp;<b>(report)</b>";
                    } 
                    $resultMsg .= "<br>";
                } // while					
            } 
         } else if (isset($_POST['mailLog']) && !empty($_POST['mailLogSelect'])) {
         	$logType = mysql_real_escape_string($_POST['mailLogSelect']);
         	$sql = "SELECT * from $mailtable WHERE mailType='".$logType."' order by id desc LIMIT 0, 30";
            $result = mysql_query($sql);
            $resultMsg = "<P>Showing mailType <B>".$logType."</b> (30 results max.)</p>";
            $resultMsg .= "<p>";
            while ($row = mysql_fetch_array($result)) {
            	$user = $row['user'];
            	$logTime = $row['logTime'];
            	$toAddress = $row['toAddress'];
            	$resultMsg .= "<b>".formatDate($logTime)."</b> - ".$user." - ".$toAddress."<br>";
            }
            $resultMsg .= "</p>";
    	 } else if (isset($_POST['gotsWinnerName'])) {
    	 	$gotsWinnerName = mysql_real_escape_string($_POST['gotsWinnerName']);
    	 	$gotsSeason = mysql_real_escape_string($_POST['gotsSeason']);
    	 	$sql = "SELECT player_id from $playerstable WHERE name = '$gotsWinnerName'";
    	 	$result = mysql_query($sql);
            $num = mysql_num_rows($result);
			if ($num != 1) {
	            $resultMsg = "<p>Invalid player!</p>";
			} else {
				$row = mysql_fetch_array($result);
				$playerId = $row['player_id'];
				$sql = "INSERT INTO $awardstable (type, playerId, profileImage, titleText) VALUES ".
					"('A', '$playerId', 'gots.gif', 'Goal of the Season winner - Season $gotsSeason')";
				$result = mysql_query($sql);
				$resultMsg = "Running query '".$sql."' - Result: ".$result;
			}
   }
?>
<?= getBoxTop("Game Admin", "", false, null); ?>
<table class="formtable">
<form name="form2" method="post" action="index.php">
<tr>
	<td width="150">Remove Game</td>
	<td>
		<input name="removeGameId" type="text" maxlength="6" class="width200">
	</td>
	<td></td>
</tr>
<tr>
	<td width="150">Game end (lower)</td>
	<td>
		<input name="removeGameIdEnd" type="text" maxlength="6" class="width200">
	</td>
	<td></td>
</tr>
<tr>
	<td>Reason (optional)</td>
	<td>
		<input name="deleteReason" type="text" maxlength="90" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="remove">
	</td>
</form>
</tr>
<form name="form21" method="post" action="index.php">
<tr>
	<td>Remove for player</td>
	<td>
		<input name="removeGamesForPlayer" type="text" maxlength="90" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="remove">
	</td>
</form>
</tr>
</form>
<tr>
<form name="form3" method="post" action="index.php">
	<td>Restore Game</td>
	<td>
		<input name="restoreGameId" type="text" maxlength="6" class="width200"> 
	</td>
	<td>
	</td> 
</tr>
<tr>
	<td width="150">Game end (greater)</td>
	<td>
		<input name="restoreGameIdEnd" type="text" maxlength="6" class="width200">
	</td>
	<td>
  		<input type="submit" class="width100" value="restore">
</td>
</tr>
</form>


<tr>
<form name="form5" method="post" action="index.php">
	<td>Check Game</td>
	<td>
		<input name="checkGame" type="text" maxlength="6" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="check"> 
	</td>
</form>
</tr>
<tr>
<form name="form51" method="post" action="editGame.php">
	<td>Edit Game</td>
	<td>
		<input name="editGame" type="text" maxlength="6" class="width200"></td>
	<td>
		<input type="submit" class="width100" value="edit">
		
	</td>
</form>
</tr>
</table>

<?= getBoxBottom() ?>

<?= getBoxTop("Sixserver Admin", "", false, null); ?>
<table class="formtable">
<form name="form66" method="post" action="editSixgame.php">
<tr>
	<td width="150">Game Id</td>
	<td>
		<input name="sixEditGameId" type="text" maxlength="10" class="width200">
	</td>
	<td>
    <input type="submit" class="width100" value="edit">
  </td>
</tr>
</form>
<form name="form66" method="post" action="index.php">
<tr>
	<td width="150">Finished Game Id</td>
	<td>
		<input name="sixDeleteFinishedGameId" type="text" maxlength="10" class="width200">
	</td>
	<td>
    <input type="submit" class="width100" value="delete">
  </td>
</tr>
</form>
<form name="form67" method="post" action="index.php">
<tr>
	<td width="150">Unfinished Game Id</td>
	<td>
		<input name="sixDeleteUnfinishedGameId" type="text" maxlength="10" class="width200">
	</td>
	<td>
    <input type="submit" class="width100" value="delete">
  </td>
</tr>
</form>

<form name="form66" method="post" action="index.php">
<tr>
	<td width="150">Game Id</td>
	<td>
		<input name="sixChangeGameId" type="text" maxlength="10" class="width200">
	</td>
	<td>
  </td>
</tr>
<tr>
	<td>Player</td>
	<td>
		<input name="sixChangePlayerName" type="text" maxlength="90" class="width200">
	</td>
	<td>
	</td>
</tr>
<tr>
	<td>New team</td>
	<td>
  <input class="width200" type="text" id="sixChangeTeamTxt" name="sixChangeTeamTxt" value="" autocomplete="off"><br />
				<div id="sixChangeTeamDiv" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
			    	new Autocompleter.Local('sixChangeTeamTxt', 'sixChangeTeamDiv', teamsArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    //	]]>
			    </script>
  </td><td>
		<input type="submit" class="width100" value="change">
	</td> 
</tr>
</form>
</table>

<table class="formtable">
<form name="form67" method="post" action="index.php">
<tr>
	<td width="150">Patch Id</td>
	<td>
		<input name="sixPatchId" type="text" maxlength="10" class="width200">
	</td>
	<td></td>
</tr>
<tr>
	<td>Sixteam Id</td>
	<td>
		<input name="sixTeamId" type="text" maxlength="10" class="width200">
	</td>
	<td>
	</td>
</tr>
<tr>
	<td>New team</td>
	<td>
  <input class="width200" type="text" id="sixLadderTeamTxt" name="sixLadderTeamTxt" value="" autocomplete="off"><br />
				<div id="sixLadderTeamDiv" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
			    	new Autocompleter.Local('sixLadderTeamTxt', 'sixLadderTeamDiv', teamsArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    //	]]>
			    </script>
  </td><td>
		<input type="submit" class="width100" value="insert">
	</td> 
</tr>
</form>

<form name="form68" method="post" action="index.php">
<tr>
	<td>Rename profile</td>
	<td>
		<input name="sixSourceProfile" type="text" maxlength="32" class="width200">
	</td>
  <td></td>
</tr>
<tr>
	<td></td>
  <td>
		<input name="sixTargetProfile" type="text" maxlength="32" class="width200">
	</td>
  <td>
		<input type="submit" class="width100" value="rename"> 
	</td>
</tr>
</form>

<form name="form69" method="post" action="index.php">
<tr>
	<td>Six maintenance</td>
	<td>
		<input name="sixmaintenance" type="text" maxlength="30" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="set"> 
	</td>
</tr>
</form>

<form name="form69" method="post" action="index.php">
<tr>
	<td>Six DebugMode</td>
	<td>
		<input name="sixdebug" type="text" maxlength="30" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="set"> 
	</td>
</tr>
</form>

<form name="form70" method="post" action="index.php">
<tr>
	<td>Call Script</td>
	<td>
    <select name="scriptPath" class="width200">
			<option value=""></option>
<?
  $files = glob('/opt/scripts/*nohup*.sh');
  foreach ($files as $fileName) {
    echo '<option>'.$fileName.'</option>';
  }
?>
      </select>

	</td>
	<td>
		<input type="submit" class="width100" value="execute"> 
	</td>
</tr>
</form>
</table>

<?= getBoxBottom() ?>

<?= getBoxTop("Player Admin", "", false, null); ?>
<table class="formtable">

<tr>
<form name="form79" method="post" action="index.php">
	<td width="150">Send activation</td>
	<td>
		<input name="sendActivationName" value="" type="text" maxlength="100" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="send">
	</td>
</form>
</tr>

<tr>
<form name="form799" method="post" action="index.php">
	<td width="150">Reset DC unfinished</td>
	<td>
		<input name="resetDC" value="" type="text" maxlength="100" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="send">
	</td>
</form>
</tr>

<tr>
<form name="form8" method="post" action="index.php">
	<td width="150">Invalid Email</td>
	<td>
		<input name="invalidEmail" value="" type="text" maxlength="100" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="send">
	</td>
</form>
</tr>

<tr>
<form name="form6" method="post" action="index.php">
	<td>Toggle Approved</td>
	<td>
		<input name="toggleApproved" type="text" maxlength="15" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="toggle"> 
	</td>
</form>
</tr>

<tr>
<form name="form99" method="post" action="index.php">
	<td>Check Player</td>
	<td>
		<input name="playercheck" type="text" maxlength="30" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="check"> 
	</td>
</form>
</tr>

<tr>
<form name="form9" method="post" action="index.php">
	<td>Check Email</td>
	<td>
		<input name="mailcheck" type="text" maxlength="30" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="check"> 
	</td>
</form>
</tr>

<form name="form9b" method="post" action="index.php">
<tr>
	<td>Access (IP or name)</td>
	<td>
		<input name="access" type="text" maxlength="30" class="width200">
	</td>
  </td></td>
</tr>
  <td>Name 2</td>
	<td>
		<input name="access2" type="text" maxlength="30" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="check"> 
	</td>
</tr>
</form>

<tr>
<form name="form7" method="post" action="index.php">
	<td width="150">Rename Player</td>
	<td>
		<input name="oldName" type="text" maxlength="15" class="width200">
	</td>
	<td></td>
</tr>
<tr>
	<td>New Name</td>
	<td>
		<input name="newName" type="text" maxlength="15" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="rename"> 
	</td>
</tr>
</form>
</table>

<?= getBoxBottom() ?>

<?= getBoxTop("Goal Admin", "", false, null); ?>
<table class="formtable">

<tr>
<form name="form_goal" method="post" action="index.php">
	<td width="150">Delete goal ID</td>
	<td>
		<input name="deleteGoalId" value ="" type="text" maxlength="100" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="delete">
	</td>
</form>

</tr>
<tr>

<form name="form_goal_reset" method="post" action="index.php">
<tr>
	<td width="150">Reset images</td>
	<td>
		<input name="resetGoalId" value ="" type="text" maxlength="100" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="reset">
	</td>
</tr>
</form>

<form name="form_goal_goaltype" method="post" action="index.php">
<tr>
	<td width="150">Change goal ID</td>
	<td>
		<input name="changeCategoryGoalId" value="" type="text" maxlength="100" class="width200">
	</td>
	</td>
</tr>
<tr>
	<td width="150">To Category</td>
	<td>
		<select name="changeCategory" class="width200">
			<option value="A">Goal</option>
			<option value="C">Compilation</option>
			<option value="M">Game Scene</option>
			<option value="O">Other</option>
		</select>
	</td>
	<td>
		<input type="submit" class="width100" value="change">
	</td>
</tr>
</form>

<form name="form_goal_create" method="post" action="index.php">
<tr>
	<td width="150">Create for file</td>
	<td>
		<input name="createGoalFile" value ="<?= mysql_real_escape_string($_POST['createGoalFile']); ?>" type="text" maxlength="100" class="width200">
	</td>
	<td>
	</td>
</tr>
<tr>
	<td width="150">Player name</td>
	<td>
		<input name="createGoalPlayer" value ="<?= mysql_real_escape_string($_POST['createGoalPlayer']); ?>" type="text" maxlength="100" class="width200">
	</td>
	<td>
	</td>
</tr>
<tr>
	<td width="150">Category</td>
	<td>
		<select name="createGoalCategory" value ="<?= mysql_real_escape_string($_POST['createGoalCategory']); ?>" class="width200">
			<option value="A">Goal</option>
			<option value="C">Compilation</option>
			<option value="M">Game Scene</option>
			<option value="O">Other</option>
		</select>
	</td>
	<td>
	</td>
</tr>
<tr>
	<td width="150">Comment</td>
	<td>
		<input name="createGoalComment" value ="<?= mysql_real_escape_string($_POST['createGoalComment']); ?>" type="text" maxlength="100" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="create">
	</td>
</tr>
</form>

<form name="form_gots" method="post" action="index.php">
<tr>
	<td width="150">GOTS Winner</td>
	<td>
		<input name="gotsWinnerName" value="" type="text" maxlength="100" class="width200">
	</td>
	</td>
</tr>
<tr>
	<td width="150">Season</td>
	<td>
		<input name="gotsSeason" value="" type="text" maxlength="100" class="width200">
	</td>
	<td>
		<input type="submit" class="width100" value="insert">
	</td>
</tr>
</form>
</table>
<?= getBoxBottom() ?>

<?= getBoxTop("Content Admin", "", false, null); ?>
<table class="formtable">

<tr>
<form name="form_sticky" method="post" action="index.php">
	<td width="150">Stick/unstick topicID</td>
	<td>
		<input name="stickTopicId" value="" type="text" maxlength="5" style="width:140px;">
    <input name="stickTopicPrio" value="" type="text" maxlength="5" class="width50">
	</td>
	<td>
		<input type="submit" class="width100" value="change">
	</td>
</form>
</tr>
</table>
<?= getBoxBottom() ?>

<?= getBoxTop("Teams Admin", "", false, null); ?>
<table class="formtable">

<tr>
<form name="form_categories" method="post" action="index.php">
	<td width="150">
	Team Category
	</td>
	<td>
		<select name="teamCategoryId" style="width:140px;">
		<?= getTeamsAllOptions('') ?>
		</select>
	</td>
	<td>
		<select name="teamCategoryValue" class="width50">
			<option>1</option>
			<option>2</option>
			<option>3</option>
			<option>4</option>
			<option>5</option>
		</select>
	</td>
	<td>
		<input type="submit" class="width100" value="change">
	</td>
</form>
</tr>
</form>
</table>
<?= getBoxBottom() ?>

</td><td width="40%" valign="top">

<? if (strlen($resultMsg) > 0)  { ?>
<?= getBoxTop("Result Info", "", true, null); ?>
<?= $resultMsg ?>
<?= getBoxBottom() ?>
<? } ?>
</td></tr></table>
<?php
    
} 

?>
<?= getOuterBoxBottom() ?>
<?
require('./../bottom.php');
?>