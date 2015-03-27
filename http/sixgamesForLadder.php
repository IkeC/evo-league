<?
	$logRep = new KLogger('/var/www/yoursite/http/log/report/', KLogger::INFO);	
?>
<?= getBoxTopImg("Edit Ladder Games", $boxheight, true, null, $box3Img, $box3Align); ?>
<? if (!isset($_POST['game_id0'])) { ?>
<p class="darkgrey-small">Please enter the teams used in these ladder games. If you're unsure, leave the team field empty.</p>
<script language="javascript">
	<!--
		var teamsArray = new Array(<?= getTeamsAllJavascript() ?>);
	-->
</script>
<form method="post" name="editGames" action="<?= $_SERVER['PHP_SELF'] ?>">
<table class="layouttable">
<?

$sql = "SELECT * FROM $gamestable ".
  "WHERE sixGameId IS NOT NULL ".
  "AND edited=0 ".
  "AND deleted=0 ".
  "AND (winner='$cookie_name' OR loser='$cookie_name' OR winner2='$cookie_name' OR loser2='$cookie_name') ".
  "AND (winnerteam=0 OR loserteam=0) ".
  "AND season=".$season." ".
  "ORDER BY game_id ASC";

$logRep->logInfo('sixgamesForLadder: sql='.$sql);
  
$res = mysql_query($sql);
$index = 0;
while ($row = mysql_fetch_array($res)) {
  $id = $row['game_id'];
  $gamedate = formatTime($row['date']);
  $winner = $row['winner'];
  $winner2 = $row['winner2'];
  $loser = $row['loser'];
  $loser2 = $row['loser2'];
  $winnerteam = $row['winnerteam'];
  $loserteam = $row['loserteam'];
  $winnerresult = $row['winnerresult'];
  $loserresult = $row['loserresult'];
  $winnerTeamName = getTeamNameForId($winnerteam);
  $loserTeamName = getTeamNameForId($loserteam);
  $winTeamAlign = '';
  if ($winnerteam == 0) {
    $winnerTeamDisplay = '<input type="text" align="right" class="width150" style="font-size:10px" id="winTeam'.$index.'" name="winTeam'.$index.'" '.
      'value="'.$winnerTeamName.'" autocomplete="off">'.
      '<div id="winTeamDiv'.$index.'" class="autocomplete" />'."\n".
      '<script type="text/javascript" language="javascript" charset="utf-8">'."\n".
      '// <![CDATA['."\n".
      '  new Autocompleter.Local(\'winTeam'.$index.'\', \'winTeamDiv'.$index.'\', teamsArray, { choices: 5, tokens: new Array(\',\',\'\n\'), fullSearch: true, partialSearch: true});'."\n".
      '//	]]>'."\n".
      '</script>'."\n";
  } else {
    $winnerTeamDisplay = $winnerTeamName.'<input type="hidden" id="winTeam'.$index.'" name="winTeam'.$index.'" '.
      'value="'.$winnerTeamName.'">';
    $winTeamAlign = ' align="right"';
  }
  if ($loserteam == 0) {
    $loserTeamDisplay = '<input type="text" align="right" class="width150" style="font-size:10px" id="loseTeam'.$index.'" name="loseTeam'.$index.'" '.
      'value="'.$loserTeamName.'" autocomplete="off">'.
      '<div id="loseTeamDiv'.$index.'" class="autocomplete" />'."\n".
      '<script type="text/javascript" language="javascript" charset="utf-8">'."\n".
      '// <![CDATA['."\n".
      '  new Autocompleter.Local(\'loseTeam'.$index.'\', \'loseTeamDiv'.$index.'\', teamsArray, { choices: 5, tokens: new Array(\',\',\'\n\'), fullSearch: true, partialSearch: true});'."\n".
      '//	]]>'."\n".
      '</script>'."\n";
  } else {
    $loserTeamDisplay = $loserTeamName.'<input type="hidden" id="loseTeam'.$index.'" name="loseTeam'.$index.'" '.
      'value="'.$loserTeamName.'">';
  }
  
  if (!empty($winner2)) {
    $winnerdisplay = '<span><a href="'.$directory.'/profile.php?name='.$winner.'">'.$winner.'</a>'.
      '<span class="grey-small">&ndash;</span>'.
      '<a href="'.$directory.'/profile.php?name='.$winner2.'">'.$winner2.'</a></span>';
  } else {
    $winnerdisplay = '<span><a href="'.$directory.'/profile.php?name='.$winner.'">'.$winner.'</a></span>';
  }
  if (!empty($loser2)) {
    $loserdisplay = '<span><a href="'.$directory.'/profile.php?name='.$loser.'">'.$loser.'</a>'.
      '<span class="grey-small">&ndash;</span>'.
      '<a href="'.$directory.'/profile.php?name='.$loser2.'">'.$loser2.'</a></span>';
  } else {
    $loserdisplay = '<span><a href="'.$directory.'/profile.php?name='.$loser.'">'.$loser.'</a></span>';
  }
  ?>
  <tr>
    <td class="shrink"><?= $gamedate ?></td>
    <td class="shrink"><?= $winnerdisplay ?></td>
    <td class="shrink"<?= $winTeamAlign ?>><?= $winnerTeamDisplay ?></td>
    <td class="shrink" align="right"><b><?= $winnerresult ?></b></td>
    <td class="shrink">-</td>
    <td class="shrink"><b><?= $loserresult ?></b></td>
    <td class="shrink"><?= $loserTeamDisplay ?></td>
    <td class="shrink">
      <?= $loserdisplay ?>
      <input type="hidden" name="game_id<?= $index ?>" value="<?= $id ?>" />
    </td>
    <td class="expand"></td>
  </tr>
  <?
  $index++;
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
    $game_id = mysql_real_escape_string($_POST['game_id'.$index]);
    $winTeamName = mysql_real_escape_string($_POST['winTeam'.$index]);
    $loseTeamName = mysql_real_escape_string($_POST['loseTeam'.$index]);
    
    $logRep->logInfo('index='.$index.' game_id='.$game_id.' winTeamName='.$winTeamName.' loseTeamName='.$loseTeamName);
    
    // get ladder team id for selected name
    $result = mysql_query("SELECT id FROM ".$teamstable." WHERE name = '".mysql_real_escape_string($winTeamName)."'");
		if (mysql_num_rows($result) != 1) {
       $winTeamId = 0;
    } else {
      $row = mysql_fetch_array($result);
      $winTeamId = $row[0];
    }        
    
    // get ladder team id for selected name
    $result = mysql_query("SELECT id FROM ".$teamstable." WHERE name = '".mysql_real_escape_string($loseTeamName)."'");
		if (mysql_num_rows($result) != 1) {
       $loseTeamId = 0;
    } else {
      $row = mysql_fetch_array($result);
      $loseTeamId = $row[0];
    }
    
    // update current ladder game
    $sql = "UPDATE $gamestable SET winnerteam='$winTeamId', loserteam='$loseTeamId', edited=1 WHERE game_id = $game_id";
    $result = mysql_query($sql);
		
    $logRep->logInfo('sql='.$sql.' affected='.mysql_affected_rows());
    
    UpdateLadderGamePoints($game_id, $logRep);
    
    // Insert teams
    if ($winTeamId > 0 || $loseTeamId > 0) {
      $sql = "SELECT sm.*, sp1.id AS patchIdHome, sp2.id AS patchIdAway, wg.winner, wg.winner2, wg.sixGameId ".
        "FROM weblm_games wg ".
        "LEFT JOIN six_matches sm ON sm.id=wg.sixGameId ".
        "LEFT JOIN six_patches sp1 ON sp1.hash=sm.hashHome ".
        "LEFT JOIN six_patches sp2 ON sp2.hash=sm.hashAway ".
        "WHERE wg.game_id=".$game_id;
      $row = mysql_fetch_array(mysql_query($sql));
      
      $logRep->logInfo('sql='.$sql.' affected='.mysql_affected_rows());
      
      $scoreHome = $row['score_home'];
      $scoreAway = $row['score_away'];
      
      if ($scoreHome < $scoreAway) {
        $sixTeamIdWinner = $row['team_id_away'];
        $sixTeamIdLoser = $row['team_id_home'];
        $patchIdWinner = $row['patchIdAway'];
        $patchIdLoser = $row['patchIdHome'];
      } elseif ($scoreHome > $scoreAway) {
        $sixTeamIdWinner = $row['team_id_home'];
        $sixTeamIdLoser = $row['team_id_away'];
        $patchIdWinner = $row['patchIdHome'];
        $patchIdLoser = $row['patchIdAway'];
      } else {
        // draw could have been flipped
        $winner = $row['winner'];
        $winner2 = $row['winner2'];
        $sixGameId = $row['sixGameId'];
        
        $winnerIsHome = false;
        
        $sql = "SELECT wp.name FROM weblm_players wp ".
          "LEFT JOIN six_profiles sp ON sp.user_id=wp.player_id ".
          "LEFT JOIN six_matches_played smp ON smp.profile_id=sp.id ".
          "WHERE smp.match_id=".$sixGameId." AND smp.home=1";
        $drawRes = mysql_query($sql);
        
        $logRep->logInfo('DrawFlip: sql='.$sql.' affected='.mysql_affected_rows());
        
        while ($drawRow = mysql_fetch_array($drawRes)) {
          $homeName = $drawRow['name'];
          if ($homeName == $winner || $homeName == $winner2) {
            $winnerIsHome = true;
          }
        }
        
        $logRep->logInfo('DrawFlip: winnerIsHome='.$winnerIsHome);
        
        if ($winnerIsHome) {
          $sixTeamIdWinner = $row['team_id_home'];
          $sixTeamIdLoser = $row['team_id_away'];
          $patchIdWinner = $row['patchIdHome'];
          $patchIdLoser = $row['patchIdAway'];
        } else {
          $sixTeamIdWinner = $row['team_id_away'];
          $sixTeamIdLoser = $row['team_id_home'];
          $patchIdWinner = $row['patchIdAway'];
          $patchIdLoser = $row['patchIdHome'];
        }
      }
      
      $logRep->logInfo('sixTeamIdWinner='.$sixTeamIdWinner.' sixTeamIdLoser='.$sixTeamIdLoser.' patchIdWinner='.$patchIdWinner.' patchIdLoser='.$patchIdLoser);
      
      if ($winTeamId > 0) {
        InsertTeamAndUpdate($patchIdWinner, $sixTeamIdWinner, $winTeamId, $logRep, $season, $cookie_name);
      }
      if ($loseTeamId > 0) {
        InsertTeamAndUpdate($patchIdLoser, $sixTeamIdLoser, $loseTeamId, $logRep, $season, $cookie_name);
      }
    } // if: insert teams
    $index++;
  } // while: next game
  echo '<p>Updated <b>'.($index).'</b> ladder game(s).&nbsp;&nbsp;<a href="/">[Close]</a></p>';
} // else: form submitted

?>
<?= getBoxBottom() ?>
<?

function UpdateLadderGamePoints($game_id, $logRep) {
  
  $logRep->logInfo('UpdateLadderGamePoints: game_id='.$game_id);
  
  $sql = "SELECT * FROM weblm_games WHERE game_id = '$game_id'";
  
  $logRep->logInfo('UpdateLadderGamePoints: sql='.$sql);
  
  $result = mysql_query($sql);
  $row = mysql_fetch_array($result);

  $draw = $row['isDraw'];
  $teamBonusOld = $row['teamBonus']; 
  $winpoints = $row['winpoints'];
  $winner = $row['winner'];
  $winner2 = $row['winner2'];
  $sixGameId = $row['sixGameId'];
  $winTeamId = $row['winnerteam'];
  $loseTeamId = $row['loserteam'];
  
  $teamBonusNewArray = getTeamBonus($winTeamId, $loseTeamId, $draw, $winpoints-$teamBonusOld);
  $teamBonusNew = $teamBonusNewArray['bonusWinner'];

  $logRep->logInfo('UpdateLadderGamePoints: teamBonusNewArray[msg]='.$teamBonusNewArray['msg'].' teamBonusOld='.$teamBonusOld.' teamBonusNew='.$teamBonusNew);

  if ($teamBonusOld != $teamBonusNew) {
    $teamBonusDiff = $teamBonusNew - $teamBonusOld;

    $sql = "UPDATE weblm_games SET winpoints=winpoints+$teamBonusDiff, ".
      "teamBonus='$teamBonusNew' WHERE game_id=$game_id";
    $result = mysql_query($sql);
    
    $logRep->logInfo('UpdateLadderGamePoints: sql='.$sql.' affected='.mysql_affected_rows());
        
    $pointsField = getPointsFieldForVersion('H'); // PES6 PC
        
    $sql = "SELECT $pointsField from weblm_players where name = '$winner'";
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    $pointsOld = $row[$pointsField];
    $pointsNew = $pointsOld + $teamBonusDiff;						
    if ($pointsNew < 0) {
      $pointsNew = 0;
    }
    
    $sql = "UPDATE weblm_players SET $pointsField = $pointsNew WHERE name = '$winner'";
    $result = mysql_query($sql);
    
    $logRep->logInfo('UpdateLadderGamePoints: sql='.$sql.' affected='.mysql_affected_rows());
    
    if (!empty($winner2)) {
      $sql = "SELECT $pointsField FROM weblm_players WHERE name = '$winner2'";
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      $pointsOld = $row[$pointsField];
      $pointsNew = $pointsOld + $teamBonusDiff;						
      if ($pointsNew < 0) {
        $pointsNew = 0;
      }
      
      $sql = "UPDATE weblm_players SET $pointsField=$pointsNew WHERE name='$winner2'";
      $result = mysql_query($sql);
      
      $logRep->logInfo('UpdateLadderGamePoints: sql='.$sql.' affected='.mysql_affected_rows());
    }
  }
}

?>