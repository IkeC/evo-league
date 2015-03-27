<?php

// displays the game reporting page if the user is logged in.
// the actual point calculation and database update is done in /functions.php#ReportGame

$page = "report";
$subpage = "";
$back = "<p><a href='javascript:history.back()'>Go back</a></p>";

require_once('variables.php');
require_once('variablesdb.php');
require_once('functions.php');
require('top.php');

$maxlength = 255-16-strlen($cookie_name);

$teamChecked = 'checked="checked"';

function getSelectTeamsAll($type) {
	require_once('functions.php');
	return '<select class="width150" name="'.$type.'team">'.getTeamsOptionsNone(true).getTeamsAllOptions(null).'</select>';
}

function getSelectPlayersAll($type) {
	require_once('functions.php');
	return '<select class="width150" name="'.$type.'">'.
		'<option value="">[select]</option>'.
		getPlayersOptionsRecentPlayers().
		'<option value="">[all players]</option>'.
		getPlayersOptionsApproved().'</select>';
}

?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Report Game", "") ?>

<?
if (empty($cookie_name)) {
   echo $membersonly;
} else {
	
	$host = "B";
	if (isset($_POST['version'])) {
		$version = mysql_real_escape_string($_POST['version']);
	} else {
		$version = "";
	}
	$error = '';
	
	$losername = "";
	$winnername2 = "";
	$losername2 = "";
	$winnerteam = "";
	$loserteam = "";
	$winnerteamId = "";
	$loserteamId = "";
	$winnerresult = "";
	$loserresult = "";
	$ratingOldWinner2 = "";
	$ratingOldLoser2 = "";
	$pointsOldWinner2 = "";
	$pointsOldLoser2 = "";
	$teamBonusWinnerDisplay = "";
	$ra2newwinner2 = "";
	$ra2newloser2 = "";
	$ra2newloserremL2 = "";
	$comment = "";
	
	if (isset($_POST['drawOverride'])) {
		$drawOverride = true;
	} else {
		$drawOverride = false;
	}
	
	if (!empty($version)) {

		 $is1on1 = isset($_POST['1on1']);		 
		 $teamChecked = $is1on1 ? 'checked="checked"' : "";
		 
		 if (!empty($_POST['winnernameTxt'])) {
			$winnername = mysql_real_escape_string($_POST['winnernameTxt']);
			$result = mysql_query("Select name from ".$playerstable." where name = '".$winnername."' and approved = 'yes'");
			if (mysql_num_rows($result) != 1) {
				 $error .= "<p>Player <b>".$winnername."</b> not found. Please check your input.</p>";
			} else {
				$row = mysql_fetch_array($result);
				$winnername = $row[0];
			}        

		 } else {
		   	 $error .= "<p>Please select the winner!</p>";
		 } 
		 
		 if (!empty($_POST['losernameTxt'])) {
			$losername = mysql_real_escape_string($_POST['losernameTxt']);
			$result = mysql_query("Select name from ".$playerstable." where name = '".$losername."' and approved = 'yes'");
			if (mysql_num_rows($result) != 1) {
				 $error .= "<p>Player <b>".$losername."</b> not found. Please check your input.</p>";
			} else {
				$row = mysql_fetch_array($result);
				$losername = $row[0];
			}        

		 } else {
		   	 $error .= "<p>Please select your opponent(s)!</p>";
		 } 
		
	    if (!empty($_POST['winnername2Txt'])) {
	        $winnername2 = mysql_real_escape_string($_POST['winnername2Txt']);
	        $result = mysql_query("Select name from ".$playerstable." where name = '".$winnername2."' and approved = 'yes'");
			if (mysql_num_rows($result) != 1) {
				$error .= "<p>Player <b>".$winnername2."</b> not found. Please check your input.</p>";
			} else {
				$row = mysql_fetch_array($result);
				$winnername2 = $row[0];
			}    
	    } 
		
	    if (!empty($_POST['losername2Txt'])) {
	        $losername2 = mysql_real_escape_string($_POST['losername2Txt']);
	        $result = mysql_query("Select name from ".$playerstable." where name = '".$losername2."' and approved = 'yes'");
			if (mysql_num_rows($result) != 1) {
				 $error .= "<p>Player <b>".$losername2."</b> not found. Please check your input.</p>";
			} else {
				$row = mysql_fetch_array($result);
				$losername2 = $row[0];
			}     
	    } 
	    	
	    if (!empty($_POST['winnerteamTxt'])) {
	        $winnerteam = mysql_real_escape_string($_POST['winnerteamTxt']);
	        $result = mysql_query("Select id from ".$teamstable." where name = '".mysql_real_escape_string($winnerteam)."'");
          if (mysql_num_rows($result) != 1) {
             $error .= "<p>Team <b>".$winnerteam."</b> not found. Please check your input.</p>";
          } else {
            $row = mysql_fetch_array($result);
            $winnerteamId = $row[0];
          }
	    } elseif ($cookie_name == 'Ike') {
        $winnerteamId = 0;
      } else {
	        $error .= "<p>Please select your team!</p>";
	    } 
	    
	    if (!empty($_POST['loserteamTxt'])) {
	        $loserteam = mysql_real_escape_string($_POST['loserteamTxt']);
	        $result = mysql_query("Select id from ".$teamstable." where name = '".mysql_real_escape_string($loserteam)."'");
        if (mysql_num_rows($result) != 1) {
           $error .= "<p>Team <b>".$loserteam."</b> not found. Please check your input.</p>";
        } else {
          $row = mysql_fetch_array($result);
          $loserteamId = $row[0];
        }
	    } elseif ($cookie_name == 'Ike') {
        $loserteamId = 0;
	    } else {
	        $error .= "<p>Please select the opposing team!</p>";
	    } 
	
		 if (is_numeric($_POST['winnerresult'])) {
		 	$winnerresult = mysql_real_escape_string($_POST['winnerresult']);
		 } else {
		     $error .= '<p>Winner goals required.</p>';
		 } 		
		 
		 if (is_numeric($_POST['loserresult'])) {
		 	$loserresult = mysql_real_escape_string($_POST['loserresult']);
		 } else {
		     $error .= '<p>Loser goals required.</p>';
		 } 
		 
		if ($winnerresult < $loserresult) {
		    $error .= '<p>The loser cannot have more goals than the winner.</p>';
		}
	   
		if (! empty($_POST['comment'])) {
	        $comment = mysql_real_escape_string($_POST['comment']);
	    } else {
	        $error .= '<p>Please enter a comment for the game.</p>';
	    } 
	    
		if (stristr(getUnsupportedVersions(), $version)) {
		 	$ladder = getLadderForVersion($version);
		 	$error = 
				'<p><b>The '.$ladder.' ladder is discontinued.</b> You cannot play or report games for this ladder anymore.</p>'.
		 		'<p>If you already own a new version of the game to play online, <a href="'.
		 		$directory.'/editprofile.php">edit your profile</a> and update your games list so you can select this game when reporting.</p>';
		}
		 
	    if (empty($error)) {
          $date = time();
	        $dateday = date("d/m/Y");
	        $fairness = mysql_real_escape_string($_POST['fairness']);
	        $sql = "SELECT * FROM $playerstable WHERE name = '$winnername' and approved = 'yes'";
	        $result = mysql_query($sql);
	        $row = mysql_fetch_array($result);
	        $name = $row["name"];
          
	        $re = mysql_num_rows($result);
	        if ($re == 0) { 
	        	$error.= "<p>We couldn't find you in the database. You might be blocked or set inactive.</p>".$back; 
	        } else {
	             if (($winnername == $losername) 
	             	|| ((strlen($winnername2) > 0) && (($winnername2 == $losername) || ($winnername2 == $losername2)))
	             	|| ((strlen($losername2) > 0) && (($losername2 == $winnername) || ($losername2 == $winnername2)))
	             	) {
	                 $error.= "<p>You can't play against yourself.</p>";
	             } else if (strlen($winnername2) > 0 && ($winnername == $winnername2)) {
	                 $error.= "<p>You can't be twice in the same team! Leave the 'Winner 2' box empty 
					or put the correct name of your teammate.</p>";
	             }  else if (strlen($losername2) > 0 && ($losername == $losername2)) {
	                 $error.= "<p>You can't be twice in the same team! Leave the 'Loser 2' box empty 
					or put the correct names of the two losing players.</p>";
	             }  
	             else {
	                 $sql = "SELECT game_id FROM $gamestable ".
					 	"WHERE ((winner = '$winnername' and loser = '$losername') OR (winner = '$losername' and loser = '$winnername')) and deleted='no' and dateday = '$dateday'";
	                 $result = mysql_query($sql);
	                 $gamesplayedplayer = mysql_num_rows($result); 
	                 if ($gamesplayedplayer >= $gamesmaxdayplayer) {
	                 	$error.= "<p>You can't play more than <b>$gamesmaxdayplayer</b> games a day against the same player.</p>";
	                 } else {
	                     $sql = "SELECT game_id FROM $gamestable ".
                          "WHERE (winner = '$winnername' or loser = '$winnername') and dateday = '$dateday' and deleted='no'";
	                     $result = mysql_query($sql);
	                     $gamesplayedday = mysql_num_rows($result);
	                     if ($gamesplayedday >= $gamesmaxday) {
	                     	$error.= "<p>You can't play more than <b>$gamesmaxday</b> games a day.</p>";
	                     } else {
	                         $sql = "SELECT game_id FROM $gamestable ".
							 	"WHERE (winner = '$losername' or loser = '$losername') and dateday = '$dateday' and deleted='no'";
	                         $result = mysql_query($sql);
	                         $losergamesplayedday = mysql_num_rows($result);
	
	                         if ($losergamesplayedday >= $gamesmaxday) {
	                         	$error.= "<p>Your opponent already played <b>$gamesmaxday</b> games today.</p>";
	                         } else {
	                             if ($is1on1) {
		                             // no more than $maxGamesAgainstSamePlayer games in a row against the same player
		                            /* 
	                             	$sql = "SELECT winner, loser FROM $gamestable ".
		                             	"WHERE (winner = '$winnername' OR loser = '$winnername') ".
		                             	"and teamLadder = 0 and deleted = 'no' ORDER BY game_id DESC LIMIT 0, $maxGamesAgainstSamePlayer";
		                             $result = mysql_query($sql);
		                             
		                             if (mysql_num_rows($result) == $maxGamesAgainstSamePlayer) {
			                             $winnerOk = false;
			                             while ($row = mysql_fetch_array($result)) {
										 	if (($row['winner'] == $winnername && $row['loser'] != $losername) || ($row['loser'] == $winnername && $row['winner'] != $losername)) {
										 		$winnerOk = true;
										 		break;
										 	}  
			                             }
			                             if (!$winnerOk) {
			                             	$error = "You appear to have played <b>$maxGamesAgainstSamePlayer</b> games in a row against <b>$losername.</b> ".
			                             		"You must play against other players before you can play games against $losername again.";
			                             }
		                             }
		                             if (empty($error)) {
			                             $sql = "SELECT winner, loser FROM $gamestable ".
			                             	"WHERE (winner = '$losername' OR loser = '$losername') ".
			                             	"and teamLadder = 0 and deleted = 'no' ORDER BY game_id DESC LIMIT 0, $maxGamesAgainstSamePlayer";
			                             $result = mysql_query($sql);
			                             if (mysql_num_rows($result) == $maxGamesAgainstSamePlayer) {
				                             $loserOk = false;
				                             while ($row = mysql_fetch_array($result)) {
                                      if (($row['winner'] == $losername && $row['loser'] != $winnername) || ($row['loser'] == $losername && $row['winner'] != $winnername)) {
                                        $loserOk = true;
                                        break;
                                      }  
				                             }
				                             if (!$loserOk) {
				                             	$error = "Your opponent <b>$losername</b> has played his last <b>$maxGamesAgainstSamePlayer</b> games exclusively against you. ".
				                             		"He must play against other players before he can play against you again.";
				                             }
			                             }
		                             }
                                 */
	                             }
	                             if (($winnerresult == $loserresult) && !$drawOverride) {
		                             // check for double reports		                        
		                             $sql = "SELECT * FROM $gamestable ".
		                             "where ((winner = '$losername' or loser = '$losername' or winner2 = '$losername' or loser2 = '$losername') ".
		                             "AND (winner = '$winnername' or loser = '$winnername' or winner2 = '$winnername' or loser2 = '$winnername')) ".
		                             "and winnerresult = '$winnerresult' and loserresult = '$loserresult' ".
		                             "and ((winnerteam = '$winnerteamId' and loserteam = '$loserteamId') or (winnerteam = '$loserteamId' and loserteam = '$winnerteamId')) ". 
		                             "and dateday = '$dateday' ".
		                             "order by game_id desc limit 0,1";
		                           
		                              $result = mysql_query($sql);
			                             if (mysql_num_rows($result) > 0) {
				                             $row = mysql_fetch_array($result);
				                             $gameid = $row['game_id'];
			                             	 $timeago = formatAgoSpan($row['date']);
			                             	 $error.= "<p>A similar draw with the same teams was reported ".$timeago.' ago. Please check <b><a href="/games.php?gameId='.$gameid.
			                             	 	'" target="_blank">Game #'.$gameid.'</a></b> to check if your report is correct, then submit again.</p>';
			                             	 $drawOverride = true;
			                             }
			                             
	                             }
	                             
	                             if (empty($error)) {
		                             $replayname = '';
		                             if ($approvegames != "yes") { 
		                                 $rec = 'no';
		                                 $gameid = 'null';
		                                 $report = ReportGame($winnername, $winnername2, $losername, $losername2, $date, $comment, 
										 	$gameid, $winnerresult, $loserresult, $host, 
										 	$fairness, $version, $winnerteamId, $loserteamId, NULL);
		                                 if ($report == 3) {
		                                 	
		                                     $gamesleft = $gamesmaxday - $gamesplayedday-1;
		                                     $gamesplayerleft = $gamesmaxdayplayer - $gamesplayedplayer-1;
											 
											 updateTeamladders();
											 
											 if (strlen($comment) < 11) {
											 	echo "<p><b>Your game comment is very short.</b> Please try to make game reports more enjoyable to read and supply a better description of the game next time.</p>";
											 }
		                                     echo "<p>You can play <b>";
		                                     echo $gamesleft;
		
		                                     echo "</b> more game(s) today and <b>";
		
		                                     if ($gamesplayerleft > $gamesleft) {
		                                         echo $gamesleft;
		                                     } else {
		                                         echo $gamesplayerleft;
		                                     } 
		                                     echo "</b> more game(s) against this player.";
                                         echo $back;
		                                 } 
		                             }
		                         } 
	                        } 
	                     } 
	                 } 
	             } 
	        }
	    } 
	} 
	
	if (empty($version) || !empty($error)) {
	 
    ?>
	<script language="javascript">
	<!--
		var playersArray = new Array(<?= getPlayersActiveJavascript(); ?>);
		var teamsArray = new Array(<?= getTeamsAllJavascript() ?>);
		var MaximumCharacters = "<?= $maxlength ?>";
		function toggleTeammates(checked) {
			el1 = document.getElementById('teammatesRow');
			if (checked) {
				el1.style.display = 'none';
			} else {
				el1.style.display = '';
			}
		}
	-->
	</script>
	<script language="javascript" type="text/javascript" src="<?= $directory ?>/style/charsLeft.js"></script>
  <table class="layouttable">
	  <tr><td width="700">
		<?= getBoxTop("Report Game", "", false, null) ?>
		<p><b>PES6 games are now <a href="http://www.yoursite/forum/viewtopic.php?t=5529">automatically reported</a> to the ladder.</b></p> 
    <p>If there are any issues with PES6 games, post in the forums.</p>
    
    <form method="post" action="report.php" name="formReport" enctype="multipart/form-data">
	    <table class="formtable reporttable" style="padding-top:10px;">
		<tr>
	    	<td width="120">Game</td>
	    	<td width="200"><?= getGamesSelectboxForPlayer($cookie_name) ?></td>
	    	<td width="50"></td>
	    	<td width="200"><input name="1on1" <?= $teamChecked ?> type="checkbox" onClick="toggleTeammates(this.checked)">&nbsp;1on1 game</input></td>
	    </tr>
	    <tr>
		    <td>Players</td>
		    <?php if ($cookie_name == 'Ike') { ?>
		    <td>
				<input tabindex="1" class="width150" type="text" id="winnernameTxt" name="winnernameTxt" value="<?= $winnername ?>" autocomplete="off"><br>
			    <div id="winnernameDiv" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
					new Autocompleter.Local('winnernameTxt', 'winnernameDiv', playersArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    // ]]>
			    </script>
		    </td>
		    
		    <?php } else { ?>
		    <td><b><? echo $cookie_name ?></b><input type="hidden" name="winnernameTxt" id="winnernameTxt" value="<? echo $cookie_name ?>"></td>
		    <?php } ?>
		    <td>vs.</td>
		    <td>
				<input tabindex="1" class="width150" type="text" id="losernameTxt" name="losernameTxt" value="<?= $losername ?>" autocomplete="off"><br>
			    <div id="losernameDiv" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
					new Autocompleter.Local('losernameTxt', 'losernameDiv', playersArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    // ]]>
			    </script>
		    </td>
	    </tr>
	  
	    <tr id="teammatesRow" <? if (empty($teamChecked)) { echo 'style="display:"'; } else { echo 'style="display:none"';} ?>>
		    <td>Teammate(s) <?= getHelpHint("For 2on1 or 1on2 games, leave one side empty") ?></td>
		    <td>
		    	<input class="width150" type="text" id="winnername2Txt" name="winnername2Txt" value="<?= $winnername2 ?>" autocomplete="off"><br>
		    	<div id="winnername2Div" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
			    	new Autocompleter.Local('winnername2Txt', 'winnername2Div', playersArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    //	]]>
			    </script>
			<td></td>
			<td>
				<input class="width150" type="text" id="losername2Txt" name="losername2Txt" value="<?= $losername2 ?>" autocomplete="off"><br>
				<div id="losername2Div" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
			    	new Autocompleter.Local('losername2Txt', 'losername2Div', playersArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    // ]]>
			    </script>
			</td>
	    </tr>
	    <tr>
		    <td>Goals</td>
		    <td><input tabindex="5" type="text" name="winnerresult" class="width20" value="<?= $winnerresult ?>" maxlength="2"></td>
		    <td></td>
	        <td><input tabindex="10" type="text" name="loserresult" class="width20" value="<?= $loserresult ?>" maxlength="2"></td>
	    </tr>
		<tr>
			<td>Teams</td>
			<td>
				<input tabindex="15" class="width150" type="text" id="winnerteamTxt" name="winnerteamTxt" value="<?= $winnerteam ?>" autocomplete="off"><br />
				<div id="winnerteamDiv" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
			    	new Autocompleter.Local('winnerteamTxt', 'winnerteamDiv', teamsArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    //	]]>
			    </script>
			</td>
			<td></td>
			<td>
				<input tabindex="20" class="width150" type="text" id="loserteamTxt" name="loserteamTxt" value="<?= $loserteam ?>" autocomplete="off"><br />
				<div id="loserteamDiv" class="autocomplete"></div>
			    <script type="text/javascript" language="javascript" charset="utf-8">
			    // <![CDATA[
			    	new Autocompleter.Local('loserteamTxt', 'loserteamDiv', teamsArray, { choices: 10, tokens: new Array(',','\n'), fullSearch: true, partialSearch: true});
			    //	]]>
			    </script>
			</td>
		</tr>
	    <tr>
		    <td>Comment</td>
		    <td colspan="2"><textarea tabindex="25" onBlur="InputLengthCheck();"
		onKeyUp="InputLengthCheck();" name="comment" rows="4" class="width250"><?= $comment ?></textarea></td>
		    <td valign="bottom"><input type="text" disabled="disabled" name="charsLeft" value="<?= $maxlength ?>" style="width:28px;color:grey;text-align:center;"></td>
	    </tr>
		<tr>
			<td >Opponent Fairness</td>
			<td>unfair&nbsp;
			<input type="radio" name="fairness" value="1">
			<input type="radio" name="fairness" value="2">
			<input type="radio" name="fairness" value="3">
			<input type="radio" name="fairness" value="4">
			<input type="radio" name="fairness" value="5" checked>
			&nbsp;fair
			</td>
			<td></td>
			<td></td>
		</tr>
	    <tr>
	    	<td colspan="4" class="padding-button"><input tabindex="25" type="Submit" class="width150" name="submit" value="Report game"></td>
	    </tr>
	</table>
	<input type="hidden" name="losername" value="<?= $losername ?>">
	<? if ($drawOverride) { ?>
		<input type="hidden" name="drawOverride" value="true">
	<? } ?>
    </form>
	<?= getBoxBottom() ?>
	</td>	
	<td valign="top">
<? 
if (!empty($error)) { 
	echo getBoxTop("Info", "", true, null);
	echo $error;
	echo getBoxBottom();
} 
?>		
	</td></tr></table>
<?
 	}
} // membersonly
?>
<?= getOuterBoxBottom() ?>
<?
require('bottom.php');
?>