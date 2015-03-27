<?
	$logRep = new KLogger('/var/www/yoursite/http/log/report/', KLogger::INFO);	
?>
<?= getBoxTopImg("Patch info", $boxheight, true, null, $box3Img, $box3Align); ?>
<? if (!isset($_POST['submitSave']) && !isset($_POST['submitCancel'])) { ?>
<p class="red-small" style="font-color:red">What is the name of the patch you used in this game?</p>
<script language="javascript">
	<!--
		var teamsArray = new Array(<?= getTeamsAllJavascript() ?>);
    var patchesArray = new Array(<?= getPatchesAllJavascript() ?>);
	-->
</script>
<form method="post" name="editPatch" action="<?= $_SERVER['PHP_SELF'] ?>">
<table class="layouttable">
<?
  $sql = "SELECT id, UNIX_TIMESTAMP(played_on) as played_on, score_home, score_away FROM six_matches WHERE id=".$spMatchId;
  $result = mysql_query($sql);
  if ($row = mysql_fetch_array($result)) {
    $id = $row['id'];
    $gamedate = formatDate($row['played_on']);
    $score_home = $row['score_home'];
    $score_away = $row['score_away'];
    
    $sql2 = "SELECT weblm_players.name, weblm_players.nationality, six_profiles.name AS profileName FROM weblm_players " .
    "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
    "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
    "WHERE six_matches_played.match_id=$id " .
    "AND six_matches_played.home=1";
    
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);
    $player_home = $row2['name'];
    $nationalityHome = $row2['nationality'];
    $profileNameHome = $row2['profileName'];
    
    $row2 = mysql_fetch_array($result2);
    if (!empty($row2)) {
      $player_home2 = $row2['name'];
      $nationalityHome2 = $row2['nationality'];
      $profileNameHome2 = $row2['profileName'];
    } else {
      $player_home2 = "";
      $nationalityHome2 = "";
      $profileNameHome2 = "";
    }
    
    $sql2 = "SELECT weblm_players.name, weblm_players.nationality, six_profiles.name AS profileName FROM weblm_players " .
    "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
    "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
    "WHERE six_matches_played.match_id=$id " .
    "AND six_matches_played.home=0";
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);
    $player_away = $row2['name'];
    $nationalityAway = $row2['nationality'];
    $profileNameAway = $row2['profileName'];
    
    $row2 = mysql_fetch_array($result2);
    if (!empty($row2)) {
      $player_away2 = $row2['name'];
      $nationalityAway2 = $row2['nationality'];
      $profileNameAway2 = $row2['profileName'];
    } else {
      $player_away2 = "";
      $nationalityAway2 = "";
      $profileNameAway2 = "";
    }
    
    $rowheight = 19;
    $idtooltip = "style='cursor:help;' title='ID #$id'";
    
    if ($score_home < $score_away) {
      $scoreLeft = $score_away;
      $scoreRight = $score_home;
      $nationalityLeft = $nationalityAway;
      $nationalityRight = $nationalityHome;
      $profileNameLeft = $profileNameAway;
      $profileNameRight = $profileNameHome;
      $playerLeft = $player_away;
      $playerRight = $player_home;
      $nationalityLeft2 = $nationalityAway2;
      $nationalityRight2 = $nationalityHome2;
      $profileNameLeft2 = $profileNameAway2;
      $profileNameRight2 = $profileNameHome2;
      $playerLeft2 = $player_away2;
      $playerRight2 = $player_home2;
    } else {
      $scoreLeft = $score_home;
      $scoreRight = $score_away;
      $nationalityLeft = $nationalityHome;
      $nationalityRight = $nationalityAway;
      $profileNameLeft = $profileNameHome;
      $profileNameRight = $profileNameAway;
      $playerLeft = $player_home;
      $playerRight = $player_away;
      $nationalityLeft2 = $nationalityHome2;
      $nationalityRight2 = $nationalityAway2;
      $profileNameLeft2 = $profileNameHome2;
      $profileNameRight2 = $profileNameAway2;
      $playerLeft2 = $player_home2;
      $playerRight2 = $player_away2;
    }

    if (empty($playerLeft2)) {
      $winnerdisplay = '<span style="cursor:help" title="server profile" class="grey-small">('.$profileNameLeft.')</span>&nbsp<span><a href="'.$directory.'/profile.php?name='.$playerLeft.'">'.$playerLeft.'</a></span>';
    } else {
      $winnerdisplay = '<span><a href="'.$directory.'/profile.php?name='.$playerLeft.'" title="server profile: '.$profileNameLeft.'">'.$playerLeft.'</a></span>'.
        '<span class="grey-small">&ndash;</span>'.
        '<span><a href="'.$directory.'/profile.php?name='.$playerLeft2.'" title="server profile: '.$profileNameLeft2.'">'.$playerLeft2.'</a></span>';
    }
    if (empty($playerRight2)) {
      $loserdisplay = '<span><a href="'.$directory.'/profile.php?name='.$playerRight.'">'.$playerRight.'</a></span>'.
        '&nbsp;<span style="cursor:help" title="server profile" class="grey-small">('.$profileNameRight.')</span>';
    } else {
      $loserdisplay = '<span><a href="'.$directory.'/profile.php?name='.$playerRight.'" title="server profile: '.$profileNameRight.'">'.$playerRight.'</a></span>'.
        '<span class="grey-small">&ndash;</span>'.
        '<span><a href="'.$directory.'/profile.php?name='.$playerRight2.'" title="server profile: '.$profileNameRight2.'">'.$playerRight2.'</a></span>';
    }
    ?>
    <table class="gamesbox">
      <tr style='height:<?= $rowheight ?>px;'>
        <td nowrap style="padding-right: 5px;"><?= getImgForVersion('H') ?></td>
        <td <?= $idtooltip ?> nowrap style="padding-right: 15px;"><span class="darkgrey-small"><?= $gamedate ?></span></td>
				<td nowrap align="right"><?= $winnerdisplay ?></td>
				<td><img src="<?= $directory ?>/flags/<?= $nationalityLeft ?>.bmp" width='18' height='15' border='1'></td>
				<td align="right" class="rightalign_gamesbox"><b><?= $scoreLeft ?></b></td>
				<td>-</td>
				<td><b><?= $scoreRight ?></b></td>
				<td><img src="<?= $directory ?>/flags/<?= $nationalityRight ?>.bmp" width='18' height='15' border='1'></td>
				<td nowrap><?= $loserdisplay ?></td>
			</tr>
    </table>
    <? } ?>

<p>
<table class="gamesbox">
  <tr>
    <td>Patch name</td>
    <td>
    <input type="text" class="width250" name="patchName" id="patchName" maxlength="64">
    <div id="patchNameDiv" class="autocomplete" />
    <script type="text/javascript" language="javascript" charset="utf-8">
    <!--
      new Autocompleter.Local('patchName', 'patchNameDiv', patchesArray, { choices: 5, tokens: new Array(';','\n'), fullSearch: true, partialSearch: true});
    -->
    </script>
    </td>
    <td></td>
  </tr>
  <tr>
    <td>Patch homepage</td>
    <td><input type="text" class="width250" name="patchHomepage" maxlength="255"></td>
    <td><span class="darkgrey-small">(optional)</span></td>
  </tr>
</table>
</p>  
<p class="black-small">If you're not sure, click the "I don't know" button.</p>
<p>  
  <input type="hidden" name="patchHash" value="<?= $spHash ?>" />
  <input type="Submit" class="width100" name="submitSave" value="Save this!" />&nbsp;
  <input type="Submit" class="width100" name="submitCancel" value="I don't know!" />
</p>
</form>
<? 
} else { 
  $patchHash = mysql_real_escape_string($_POST['patchHash']);
  $patchName = mysql_real_escape_string($_POST['patchName']);
  $patchHomepage = mysql_real_escape_string($_POST['patchHomepage']);
  if (!empty($_POST['submitSave'])) {
    if ($patchName <> '') {
      $sql = "INSERT INTO six_patches (hash, name, homepage, autoReport) ".
        "VALUES ('$patchHash','$patchName','$patchHomepage',0)";
      $logRep->logInfo('sixpatch: user='.$cookie_name.' sql='.$sql);
      mysql_query($sql);
      
      $subject = "[$leaguename] new patch: $patchName";
      $message = "User: ".$cookie_name."\r\n".
        "Patch name: ".$patchName."\r\n".
        "Patch homepage: ".$patchHomepage;
      $head = "From:".$adminmail."\r\nReply-To:".$adminmail."";
      $toAddress = $adminmail;
      @ mail($toAddress, $subject, $message, $head, $mailconfig);
    }
  } 
  elseif (!empty($_POST['submitCancel'])) {
    $sql = "SELECT player_id FROM weblm_players WHERE name='".$cookie_name."'";
    $row = mysql_fetch_array(mysql_query($sql));
    $userId = $row[0];
    
    $sql = "INSERT INTO six_patches_unknown (hash, userId) VALUES ('$patchHash','$userId')";
    $logRep->logInfo('unknown: user='.$cookie_name.' sql='.$sql);
    mysql_query($sql);

    // $subject = "[$leaguename] patch unknown: $cookie_name";
    // $message = "Patch hash: ".$patchHash;
    // $head = "From:".$adminmail."\r\nReply-To:".$adminmail."";
    // $toAddress = $adminmail;
    // @ mail($toAddress, $subject, $message, $head, $mailconfig);
  }
  echo '<p>Thanks for your help.&nbsp;&nbsp;<a href="/">[Close]</a></p>';
} // else: form submitted

?>
<?= getBoxBottom() ?>
<?

?>