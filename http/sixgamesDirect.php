<?
	$logRep = new KLogger('/var/www/yoursite/http/log/sixgames-direct/', KLogger::INFO);	
?>
<?= getBoxTopImg("Edit Sixserver Teams", $boxheight, true, null, $box3Img, $box3Align); ?>
<? if (!isset($_POST['game_id0'])) { ?>
<p class="darkgrey-small">Please enter the teams used in these sixserver games. If you're unsure, leave the team field empty.</p>
<script language="javascript">
	<!--
		var teamsArray = new Array(<?= getTeamsAllJavascript() ?>);
	-->
</script>
<form method="post" name="editGamesDirect" action="<?= $_SERVER['PHP_SELF'] ?>">
<table class="layouttable">
<?
$teamsAsked = array();
$index = 0;
while (($row = mysql_fetch_array($result)) && ($index < 6)) {
  $matchId = $row['match_id'];
  $teamIdHome = $row['team_id_home'];
  $teamIdAway = $row['team_id_away'];

  // $logRep->logInfo('cookie_name='.$cookie_name.' matchId='.$matchId.' index='.$index);
  
  if (in_array($teamIdHome, $teamsAsked) && in_array($teamIdAway, $teamsAsked)) {
    $logRep->logInfo('skipping match='.$matchId.' teamIdHome='.$teamIdHome.' teamIdAway='.$teamIdAway.' - already asked');
  } else {
    if (!in_array($teamIdHome, $teamsAsked)) {
      $teamsAsked[] = $teamIdHome;
    }
    if (!in_array($teamIdAway, $teamsAsked)) {
      $teamsAsked[] = $teamIdAway;
    }
    
    $ladderTeamIdHome = $row['ladderTeamIdHome'];
    $ladderTeamIdAway = $row['ladderTeamIdAway'];
    $ladderTeamNameHome = $row['ladderTeamNameHome'];
    $ladderTeamNameAway = $row['ladderTeamNameAway'];
    $scoreHome = $row['score_home'];
    $scoreAway = $row['score_away'];
    $gamedate = formatShortDateAndTime($row['played_on']);
    $patchId = $row['patchId'];
    
    $sql2 = "SELECT weblm_players.name FROM weblm_players " .
            "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
            "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
            "WHERE six_matches_played.match_id=$matchId " .
            "AND six_matches_played.home=1";
            
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);
    $player_home = $row2['name'];

    $row2 = mysql_fetch_array($result2);
    if (!empty($row2)) {
      $player_home2 = $row2['name'];
    } else {
      $player_home2 = "";
    }

    $sql2 = "SELECT weblm_players.name FROM weblm_players " .
      "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
      "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
      "WHERE six_matches_played.match_id=$matchId " .
      "AND six_matches_played.home=0";
      
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);
    $player_away = $row2['name'];

    $row2 = mysql_fetch_array($result2);
    if (!empty($row2)) {
      $player_away2 = $row2['name'];
    } else {
      $player_away2 = "";
    }
    
    if (!empty($player_home2)) {
      $homedisplay = '<span><a href="'.$directory.'/profile.php?name='.$player_home.'">'.$player_home.'</a>'.
        '<span class="grey-small">&ndash;</span>'.
        '<a href="'.$directory.'/profile.php?name='.$player_home2.'">'.$player_home2.'</a></span>';
    } else {
      $homedisplay = '<span><a href="'.$directory.'/profile.php?name='.$player_home.'">'.$player_home.'</a></span>';
    }

    if (!empty($player_away2)) {
      $awaydisplay = '<span><a href="'.$directory.'/profile.php?name='.$player_away.'">'.$player_away.'</a>'.
        '<span class="grey-small">&ndash;</span>'.
        '<a href="'.$directory.'/profile.php?name='.$player_away2.'">'.$player_away2.'</a></span>';
    } else {
      $awaydisplay = '<span><a href="'.$directory.'/profile.php?name='.$player_away.'">'.$player_away.'</a></span>';
    }
    
    if (is_null($ladderTeamIdHome)) {
      $homeTeamDisplay = '<input type="text" align="right" class="width150" style="font-size:10px" id="homeTeam'.$index.'" name="homeTeam'.$index.'" '.
        'value="'.$ladderTeamNameHome.'" autocomplete="off">'.
        '<div id="homeTeamDiv'.$index.'" class="autocomplete" />'."\n".
        '<script type="text/javascript" language="javascript" charset="utf-8">'."\n".
        '// <![CDATA['."\n".
        '  new Autocompleter.Local(\'homeTeam'.$index.'\', \'homeTeamDiv'.$index.'\', teamsArray, { choices: 5, tokens: new Array(\',\',\'\n\'), fullSearch: true, partialSearch: true});'."\n".
        '//	]]>'."\n".
        '</script>'."\n";
    } else {
      $homeTeamDisplay = $ladderTeamNameHome.'<input type="hidden" id="homeTeam'.$index.'" name="homeTeam'.$index.'" '.
        'value="'.$ladderTeamNameHome.'">';
      $homeTeamAlign = ' align="right"';
    }
    
      if (is_null($ladderTeamIdAway)) {
      $awayTeamDisplay = '<input type="text" align="right" class="width150" style="font-size:10px" id="awayTeam'.$index.'" name="awayTeam'.$index.'" '.
        'value="'.$ladderTeamNameAway.'" autocomplete="off">'.
        '<div id="awayTeamDiv'.$index.'" class="autocomplete" />'."\n".
        '<script type="text/javascript" language="javascript" charset="utf-8">'."\n".
        '// <![CDATA['."\n".
        '  new Autocompleter.Local(\'awayTeam'.$index.'\', \'awayTeamDiv'.$index.'\', teamsArray, { choices: 5, tokens: new Array(\',\',\'\n\'), fullSearch: true, partialSearch: true});'."\n".
        '//	]]>'."\n".
        '</script>'."\n";
    } else {
      $awayTeamDisplay = $ladderTeamNameAway.'<input type="hidden" id="awayTeam'.$index.'" name="awayTeam'.$index.'" '.
        'value="'.$ladderTeamNameAway.'">';
      $awayTeamAlign = ' align="right"';
    }
    
    ?>
    <tr>
      <td class="shrink" style="cursor:help;" title="Sixserver match #<?= $matchId ?>"><?= $gamedate ?></td>
      <td class="shrink" align="right"><?= $homedisplay ?></td>
      <td class="shrink"<?= $homeTeamAlign ?>><?= $homeTeamDisplay ?></td>
      <td class="shrink" align="right"><b><?= $scoreHome ?></b></td>
      <td class="shrink">-</td>
      <td class="shrink"><b><?= $scoreAway ?></b></td>
      <td class="shrink"><?= $awayTeamDisplay ?></td>
      <td class="shrink">
        <?= $awaydisplay ?>
        <input type="hidden" name="game_id<?= $index ?>" value="<?= $matchId ?>" />
        <input type="hidden" name="teamIdHome<?= $index ?>" value="<?= $teamIdHome ?>" />
        <input type="hidden" name="teamIdAway<?= $index ?>" value="<?= $teamIdAway ?>" />
        <input type="hidden" name="patchId<?= $index ?>" value="<?= $patchId ?>" />
      </td>
      <td class="expand"></td>
    </tr>
    <?
    $index++;
  }
} // while
?>
</table>
<p>
  <input type="Submit" class="width100" name="submit" value="Save this!" />
</p>
</form>
<? 
} else { 
  $index = 0;
  while (isset($_POST['game_id'.$index])) {
    $matchId = mysql_real_escape_string($_POST['game_id'.$index]);
    $homeTeamName = mysql_real_escape_string($_POST['homeTeam'.$index]);
    $awayTeamName = mysql_real_escape_string($_POST['awayTeam'.$index]);
    $teamIdHome = mysql_real_escape_string($_POST['teamIdHome'.$index]);
    $teamIdAway = mysql_real_escape_string($_POST['teamIdAway'.$index]);
    $patchId = mysql_real_escape_string($_POST['patchId'.$index]);
    
    $logRep->logInfo('index='.$index.' matchId='.$matchId.' homeTeamName='.$homeTeamName.' awayTeamName='.$awayTeamName.' teamIdHome='.$teamIdHome.' teamIdAway='.$teamIdAway.' cookie_name='.$cookie_name);
    
    // get ladder team id for selected name
    $homeTeamIdLadder = 0;
    $result = mysql_query("SELECT id FROM ".$teamstable." WHERE name='".$homeTeamName."'");
		if (mysql_num_rows($result) != 1) {
       if ($homeTeamName <> '') {
          $logRep->logInfo('unknown team! patchId='.$patchId.' sixTeamId='.$teamIdHome.' teamName='.$homeTeamName.' cookie_name='.$cookie_name);
       }
    } else {
      $row = mysql_fetch_array($result);
      $homeTeamIdLadder = $row[0];
    }
    
    // get ladder team id for selected name
    $awayTeamIdLadder = 0;
    $result = mysql_query("SELECT id FROM ".$teamstable." WHERE name = '".$awayTeamName."'");
		if (mysql_num_rows($result) != 1) {
       if ($awayTeamName <> '') {
          $logRep->logInfo('unknown team! patchId='.$patchId.' sixTeamId='.$teamIdAway.' teamName='.$awayTeamName.' cookie_name='.$cookie_name);
       }
    } else {
      $row = mysql_fetch_array($result);
      $awayTeamIdLadder = $row[0];
    }
    
    // Insert teams
    if ($homeTeamIdLadder > 0) {
      InsertTeam($patchId, $teamIdHome, $homeTeamIdLadder, $logRep, $cookie_name);
    }
    if ($awayTeamIdLadder > 0) {
      InsertTeam($patchId, $teamIdAway, $awayTeamIdLadder, $logRep, $cookie_name);
    }
    
    // set game edited
    $sql = "UPDATE six_matches SET played_on=played_on, edited=1 WHERE id=".$matchId;
    mysql_query($sql);
    $logRep->logInfo('sql='.$sql);
    
    $index++;
  } // while: next game
  echo '<p>Updated <b>'.($index).'</b> Sixserver game(s).&nbsp;&nbsp;<a href="/">[Close]</a></p>';
} // else: form submitted

?>
<?= getBoxBottom() ?>
<?

function InsertTeam($patchId, $sixTeamId, $ladderTeamId, $logRep, $cookie_name) {
  
  $logRep->logInfo("InsertTeam: patchId=".$patchId." sixTeamId=".$sixTeamId." ladderTeamId=".$ladderTeamId);
  
  $exists = false;
  $sql = "SELECT * FROM six_teams WHERE patchId=".$patchId." AND sixTeamId=".$sixTeamId;
  $res = mysql_query($sql);
  
  $logRep->logInfo("InsertTeam: sql=".$sql);
  
  while ($row = mysql_fetch_array($res)) {
    $exists = true;
    $existingLadderTeamId = $row['ladderTeamId'];
    if ($existingLadderTeamId <> $ladderTeamId) {
      $logRep->logInfo('InsertTeam: ERROR: existingLadderTeamId='.$existingLadderTeamId.' ladderTeamId='.$ladderTeamId);
    } else {
      $logRep->logInfo('InsertTeam: Team already defined, existingLadderTeamId='.$existingLadderTeamId);
    }
  }

  $logRep->logInfo('InsertTeam: sql='.$sql.' exists='.$exists);
  if (!$exists) {
    $sql = "SELECT player_id FROM weblm_players WHERE name='$cookie_name'";
    $resPlayer = mysql_query($sql);
    $rowPlayer = mysql_fetch_array($resPlayer);
    $playerId = $rowPlayer[0];
    
    $sql = "INSERT INTO six_teams (patchId, sixTeamId, ladderTeamId, playerId) ".
      "VALUES ('".$patchId."','".$sixTeamId."','".$ladderTeamId."', '".$playerId."')";
    mysql_query($sql);
    $logRep->logInfo('InsertTeam: sql='.$sql.' affected='.mysql_affected_rows());
  }
  
}

?>