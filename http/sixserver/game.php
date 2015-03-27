<?php
$page = "index";
$subpage = "sixgames";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');


if(!empty($_POST['id'])) {
	$gameId = mysql_real_escape_string($_POST['id']);
} elseif (!empty($_GET['id'])) {
  $gameId = mysql_real_escape_string($_GET['id']);
} else {
  $gameId = "";
}

$log = new KLogger('/var/www/yoursite/http/log/general/', KLogger::INFO);	

$left = $subNavText.getRaquo().getSubNavigation($subpage, null);
?>

<?= getOuterBoxTop($left, $right) ?>

<?

  $sql = "SELECT six_matches.id, six_matches.season, six_matches.lobbyName, six_matches.roomName, sp1.name AS patchHome, sp2.name AS patchAway, ".
    "UNIX_TIMESTAMP(six_matches.played_on) as played_on, six_matches.score_home, six_matches.score_away, ".
    "six_matches.score_home_reg, six_matches.score_away_reg, ".
    "st1.ladderTeamId as ladderTeamHome, st2.ladderTeamId as ladderTeamAway ". 
    "FROM six_matches ".
    "LEFT JOIN six_patches sp1 ON six_matches.hashHome=sp1.hash ".
    "LEFT JOIN six_patches sp2 ON six_matches.hashAway=sp2.hash ".
    "LEFT JOIN six_teams st1 ON (st1.sixTeamId=six_matches.team_id_home AND st1.patchId=sp1.id) ".
    "LEFT JOIN six_teams st2 ON (st2.sixTeamId=six_matches.team_id_away AND st2.patchId=sp2.id) ".
    "WHERE six_matches.id=".$gameId;
    
    $log->logInfo('game sql='.$sql);
    
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0) {
      echo "<p>Game not found.</p>";
    } else {
      $row = mysql_fetch_array($result);
      $id = $row['id'];
      $sixSeason = $row['season'];
      $gamedate = formatLongDate($row['played_on']);
      $score_home = $row['score_home'];
      $score_home_reg = $row['score_home_reg'];
      $score_away = $row['score_away'];
      $score_away_reg = $row['score_away_reg'];
      $ladderTeamHome = $row['ladderTeamHome'];
      $ladderTeamAway = $row['ladderTeamAway'];
      $patchHome = $row['patchHome'];
      $patchAway = $row['patchAway'];
      if ($patchHome == "") $patchHome = "Unknown";
      if ($patchAway == "") $patchAway = "Unknown";
      
      $lobbyName = $row['lobbyName'];
      if ($lobbyName == "") $lobbyName = "Unknown";
      $roomName = $row['roomName'];
      
      $columnTitlesArray = array ('Id', 'Season', 'Lobby', 'Room', 'Start', 'End', 'Duration');
      $boxTitle = "Game Info";
      $sql = "SELECT *, UNIX_TIMESTAMP(matchStart) as matchStartTS FROM six_matches_info WHERE matchId=$gameId AND type='F'";
      $resInfo = mysql_query($sql);
      $rowInfo = mysql_fetch_array($resInfo);
      if ($rowInfo) {
        if ($rowInfo['matchStart'] == 0) {
          $startDate = "Unknown";
        } else {
          $startDate = formatLongDate($rowInfo['matchStartTS']);
        }
        $duration = $rowInfo['duration'];
        if ($duration == 0) {
          $duration = "Unknown";
        } else {
          $mins = floor($duration/60);
          if ($mins < 10) {
            $mins = "0".$mins;
          }
          $secs = $duration % 60;
          if ($secs < 10) {
            $secs = "0".$secs;
          }
          $duration = $mins.":".$secs." min.";
        }
      } else {
        $startDate = "Unknown";
        $duration = "Unknown";
      }
      ?>
      
      <?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
      
      <tr class="row">
        <td style="white-space: nowrap" align="center" class="black-small"><?= $gameId ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $sixSeason ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $lobbyName ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $roomName ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $startDate ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $gamedate ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $duration ?></td>
      </tr>
      
      <?= getRankBoxBottom() ?>
      
      <?
      $columnTitlesArray = array ('Patch', 'Rating', 'Rat &plusmn;', 'Points', 'Pt &plusmn;', 'Profile', 'User','Team','', 'Result', '','Team', 'User', 'Profile', 'Pt &plusmn;', 'Points', 'Rat &plusmn;', 'Rating', 'Patch');
      $boxTitle = "Team Info";
      ?>
      <br>
      <?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>

      <?  
          
          $sql2 = "SELECT six_matches_played.points, six_matches_played.pointsDiff, six_matches_played.rating, six_matches_played.ratingDiff, ".
            "weblm_players.name, weblm_players.nationality, six_profiles.name AS profileName FROM weblm_players " .
            "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
            "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
            "WHERE six_matches_played.match_id=$id " .
            "AND six_matches_played.home=1";
          
          $result2 = mysql_query($sql2);
          $row2 = mysql_fetch_array($result2);
          $player_home = $row2['name'];
          $nationalityHome = $row2['nationality'];
          $profileNameHome = $row2['profileName'];
          $pointsHome = $row2['points'];
          $pointsDiffHome = $row2['pointsDiff'];
          $ratingHome = $row2['rating'];
          $ratingDiffHome = $row2['ratingDiff'];
          
          $player_home2 = "";
          $nationalityHome2 = "";
          $profileNameHome2 = "";
          $pointsHome2 = "";
          $pointsDiffHome2 = "";
          $ratingHome2 = "";
          $ratingDiffHome2 = "";

          $player_home3 = "";
          $nationalityHome3 = "";
          $profileNameHome3 = "";
          $pointsHome3 = "";
          $pointsDiffHome3 = "";
          $ratingHome3 = "";
          $ratingDiffHome3 = "";

          $row2 = mysql_fetch_array($result2);
          if (!empty($row2)) {
            $player_home2 = $row2['name'];
            $nationalityHome2 = $row2['nationality'];
            $profileNameHome2 = $row2['profileName'];
            $pointsHome2 = $row2['points'];
            $pointsDiffHome2 = $row2['pointsDiff'];
            $ratingHome2 = $row2['rating'];
            $ratingDiffHome2 = $row2['ratingDiff'];
            $row2 = mysql_fetch_array($result2);
            if (!empty($row2)) {
              $player_home3 = $row2['name'];
              $nationalityHome3 = $row2['nationality'];
              $profileNameHome3 = $row2['profileName'];
              $pointsHome3 = $row2['points'];
              $pointsDiffHome3 = $row2['pointsDiff'];
              $ratingHome3 = $row2['rating'];
              $ratingDiffHome3 = $row2['ratingDiff'];
            } 
          } 
          
          $sql2 = "SELECT six_matches_played.points, six_matches_played.pointsDiff, six_matches_played.rating, six_matches_played.ratingDiff, ".
            "weblm_players.name, weblm_players.nationality, six_profiles.name AS profileName FROM weblm_players " .
            "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
            "LEFT JOIN six_matches_played ON six_matches_played.profile_id = six_profiles.id " .
            "WHERE six_matches_played.match_id=$id " .
            "AND six_matches_played.home=0";
          $result2 = mysql_query($sql2);
          $row2 = mysql_fetch_array($result2);
          $player_away = $row2['name'];
          $nationalityAway = $row2['nationality'];
          $profileNameAway = $row2['profileName'];
          $pointsAway = $row2['points'];
          $pointsDiffAway = $row2['pointsDiff'];
          $ratingAway = $row2['rating'];
          $ratingDiffAway = $row2['ratingDiff'];
          
          $player_away2 = "";
          $nationalityAway2 = "";
          $profileNameAway2 = "";
          $pointsAway2 = "";
          $pointsDiffAway2 = "";
          $ratingAway2 = "";
          $ratingDiffAway2 = "";

          $player_away3 = "";
          $nationalityAway3 = "";
          $profileNameAway3 = "";
          $pointsAway3 = "";
          $pointsDiffAway3 = "";
          $ratingAway3 = "";
          $ratingDiffAway3 = "";

          $row2 = mysql_fetch_array($result2);
          if (!empty($row2)) {
            $player_away2 = $row2['name'];
            $nationalityAway2 = $row2['nationality'];
            $profileNameAway2 = $row2['profileName'];
            $pointsAway2 = $row2['points'];
            $pointsDiffAway2 = $row2['pointsDiff'];
            $ratingAway2 = $row2['rating'];
            $ratingDiffAway2 = $row2['ratingDiff'];
            $row2 = mysql_fetch_array($result2);
            if (!empty($row2)) {
              $player_away3 = $row2['name'];
              $nationalityAway3 = $row2['nationality'];
              $profileNameAway3 = $row2['profileName'];
              $pointsAway3 = $row2['points'];
              $pointsDiffAway3 = $row2['pointsDiff'];
              $ratingAway3 = $row2['rating'];
              $ratingDiffAway3 = $row2['ratingDiff'];
            } 
          } 
          
          $rowheight = 19;
          $idtooltip = "style='cursor:help;' title='ID #$id'";
          
          if ($score_home < $score_away || ($score_home == $score_away && $pointsDiffAway > $pointsDiffHome)) {
            $scoreLeft = $score_away;
            $scoreRight = $score_home;
            $scoreRegLeft = $score_away_reg;
            $scoreRegRight = $score_home_reg;
            $nationalityLeft = $nationalityAway;
            $nationalityRight = $nationalityHome;
            $ladderTeamLeft = $ladderTeamAway;
            $ladderTeamRight = $ladderTeamHome;
            $profileNameLeft = $profileNameAway;
            $profileNameRight = $profileNameHome;
            $playerLeft = $player_away;
            $playerRight = $player_home;
            $pointsLeft = $pointsAway;
            $pointsRight = $pointsHome;
            $pointsDiffLeft = $pointsDiffAway;
            $pointsDiffRight = $pointsDiffHome;
            $ratingLeft = $ratingAway;
            $ratingRight = $ratingHome;
            $ratingDiffLeft = $ratingDiffAway;
            $ratingDiffRight = $ratingDiffHome;
            
            $nationalityLeft2 = $nationalityAway2;
            $nationalityRight2 = $nationalityHome2;
            $profileNameLeft2 = $profileNameAway2;
            $profileNameRight2 = $profileNameHome2;
            $playerLeft2 = $player_away2;
            $playerRight2 = $player_home2;
            $pointsLeft2 = $pointsAway2;
            $pointsRight2 = $pointsHome2;
            $pointsDiffLeft2 = $pointsDiffAway2;
            $pointsDiffRight2 = $pointsDiffHome2;
            $ratingLeft2 = $ratingAway2;
            $ratingRight2 = $ratingHome2;
            $ratingDiffLeft2 = $ratingDiffAway2;
            $ratingDiffRight2 = $ratingDiffHome2;

            $nationalityLeft3 = $nationalityAway3;
            $nationalityRight3 = $nationalityHome3;
            $profileNameLeft3 = $profileNameAway3;
            $profileNameRight3 = $profileNameHome3;
            $playerLeft3 = $player_away3;
            $playerRight3 = $player_home3;
            $pointsLeft3 = $pointsAway3;
            $pointsRight3 = $pointsHome3;
            $pointsDiffLeft3 = $pointsDiffAway3;
            $pointsDiffRight3 = $pointsDiffHome3;
            $ratingLeft3 = $ratingAway3;
            $ratingRight3 = $ratingHome3;
            $ratingDiffLeft3 = $ratingDiffAway3;
            $ratingDiffRight3 = $ratingDiffHome3;
          } else {
            $scoreLeft = $score_home;
            $scoreRight = $score_away;
            $scoreRegLeft = $score_home_reg;
            $scoreRegRight = $score_away_reg;
            $nationalityLeft = $nationalityHome;
            $nationalityRight = $nationalityAway;
            $ladderTeamLeft = $ladderTeamHome;
            $ladderTeamRight = $ladderTeamAway;
            $profileNameLeft = $profileNameHome;
            $profileNameRight = $profileNameAway;
            $playerLeft = $player_home;
            $playerRight = $player_away;
            $pointsLeft = $pointsHome;
            $pointsRight = $pointsAway;
            $pointsDiffLeft = $pointsDiffHome;
            $pointsDiffRight = $pointsDiffAway;
            $ratingLeft = $ratingHome;
            $ratingRight = $ratingAway;
            $ratingDiffLeft = $ratingDiffHome;
            $ratingDiffRight = $ratingDiffAway;

            $nationalityLeft2 = $nationalityHome2;
            $nationalityRight2 = $nationalityAway2;
            $profileNameLeft2 = $profileNameHome2;
            $profileNameRight2 = $profileNameAway2;
            $playerLeft2 = $player_home2;
            $playerRight2 = $player_away2;
            $pointsLeft2 = $pointsHome2;
            $pointsRight2 = $pointsAway2;
            $pointsDiffLeft2 = $pointsDiffHome2;
            $pointsDiffRight2 = $pointsDiffAway2;
            $ratingLeft2 = $ratingHome2;
            $ratingRight2 = $ratingAway2;
            $ratingDiffLeft2 = $ratingDiffHome2;
            $ratingDiffRight2 = $ratingDiffAway2;
            
            $nationalityLeft3 = $nationalityHome3;
            $nationalityRight3 = $nationalityAway3;
            $profileNameLeft3 = $profileNameHome3;
            $profileNameRight3 = $profileNameAway3;
            $playerLeft3 = $player_home3;
            $playerRight3 = $player_away3;
            $pointsLeft3 = $pointsHome3;
            $pointsRight3 = $pointsAway3;
            $pointsDiffLeft3 = $pointsDiffHome3;
            $pointsDiffRight3 = $pointsDiffAway3;
            $ratingLeft3 = $ratingHome3;
            $ratingRight3 = $ratingAway3;
            $ratingDiffLeft3 = $ratingDiffHome3;
            $ratingDiffRight3 = $ratingDiffAway3;
          }
          
          if ($id < 129140) {
            $ratingLeft = "?";
            $ratingLeft2 = "?";
            $ratingLeft3 = "?";
            $pointsLeft = "?";
            $pointsLeft2 = "?";
            $pointsLeft3 = "?";
            $ratingRight = "?";
            $ratingRight2 = "?";
            $ratingRight3 = "?";
            $pointsRight = "?";
            $pointsRight2 = "?";
            $pointsRight3 = "?";
          }

          
          $rowspan = "";
          $topmargin = 'style="margin-top:2px"';
          $rowclass = "row";
          
          $regScoreInfo = "";
          if (($scoreLeft > $scoreRegLeft) || ($scoreRight > $scoreRegRight)) {
            $regScoreInfo = ' style="cursor:help;" title="'.$scoreRegLeft.'-'.$scoreRegRight.' after 90 minutes"';
          }
          
          if (is_null($ladderTeamLeft)) {
            $teamNameLeft = "Unknown";
            $flagLeft = '<img title="'.$nationalityLeft.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft.'.bmp" width="18" height="15" border="1">';
            if ($playerLeft2 != "") {
              $flagLeft .= '<div '.$topmargin.'><img title="'.$nationalityLeft2.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft2.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
            if ($playerLeft3 != "") {
              $flagLeft .= '<div '.$topmargin.'><img title="'.$nationalityLeft3.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft3.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
          } else {
            $flagLeft = getImgForTeam($ladderTeamLeft);
            $teamNameLeft = getTeamNameForIdAdditionalInfo($ladderTeamLeft);
          }
          
          if (is_null($ladderTeamRight)) {
            $teamNameRight = "Unknown";
            $flagRight = '<img title="'.$nationalityRight.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight.'.bmp" width="18" height="15" border="1">';
            if ($playerRight2 != "") {
              $flagRight .= '<div '.$topmargin.'><img title="'.$nationalityRight2.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight2.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
            if ($playerRight3 != "") {
              $flagRight .= '<div '.$topmargin.'><img title="'.$nationalityRight3.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight3.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
          } else {
            $flagRight = getImgForTeam($ladderTeamRight);
            $teamNameRight = getTeamNameForIdAdditionalInfo($ladderTeamRight);
          }
          ?>
        <tr class="<?= $rowclass ?>">
          <td style="white-space: nowrap" class="black-small" align="center"><?= $patchHome ?></td>
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $ratingLeft ?>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingLeft2 ?></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingLeft3 ?></div>
            <? } ?>
          </td>
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $ratingDiffLeft ?>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingDiffLeft2 ?></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingDiffLeft3 ?></div>
            <? } ?>
          </td>
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $pointsLeft ?>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsLeft2 ?></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsLeft3 ?></div>
            <? } ?>
          </td>
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $pointsDiffLeft ?>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsDiffLeft2 ?></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsDiffLeft3 ?></div>
            <? } ?>
          </td>
          <td align="right" class="black-small">
            <?= $profileNameLeft ?>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><?= $profileNameLeft2 ?></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><?= $profileNameLeft3 ?></div>
            <? } ?>
          </td>
          <td align="right">
            <a href="<?= $directory ?>/profile.php?name=<?= $playerLeft ?>"><?= $playerLeft ?></a>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><a href="<?= $directory ?>/profile.php?name=<?= $playerLeft2 ?>"><?= $playerLeft2 ?></a></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><a href="<?= $directory ?>/profile.php?name=<?= $playerLeft3 ?>"><?= $playerLeft3 ?></a></div>
            <? } ?>
          </td>
          <td align="right" style="white-space: nowrap" class="black-small"><?= $teamNameLeft ?></td>
          <td align="center"><?= $flagLeft ?></td>
          <td align="center" <?= $regScoreInfo; ?>><?= $scoreLeft ?>&nbsp;-&nbsp;<?= $scoreRight ?></td>
          <td align="center"><?= $flagRight ?></td>
          <td align="left" style="white-space: nowrap" class="black-small"><?= $teamNameRight ?></td>
          <td>
            <a href="<?= $directory ?>/profile.php?name=<?= $playerRight ?>"><?= $playerRight ?></a>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><a href="<?= $directory ?>/profile.php?name=<?= $playerRight2 ?>"><?= $playerRight2 ?></a></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><a href="<?= $directory ?>/profile.php?name=<?= $playerRight3 ?>"><?= $playerRight3 ?></a></div>
            <? } ?>
          </td>
          <td class="black-small">
            <?= $profileNameRight ?>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><?= $profileNameRight2 ?></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><?= $profileNameRight3 ?></div>
            <? } ?>
          </td>
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $pointsDiffRight ?>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsDiffRight2 ?></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsDiffRight3 ?></div>
            <? } ?>
          </td>          
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $pointsRight ?>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsRight2 ?></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsRight3 ?></div>
            <? } ?>
          </td>         
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $ratingDiffRight ?>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingDiffRight2 ?></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingDiffRight3 ?></div>
            <? } ?>
          </td>          
          <td style="white-space: nowrap" align="center" class="darkgrey-small">
            <?= $ratingRight ?>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingRight2 ?></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><?= $ratingRight3 ?></div>
            <? } ?>
          </td>
          <td style="white-space: nowrap" class="black-small" align="center"><?= $patchAway ?></td>
        </tr>

  <?= getRankBoxBottom() ?>
  <? 
    if ($rowInfo) {
    
      $columnTitlesArray = array ('Match Time', 'Extra Time', 'Penalties', 'Injuries', 'Pauses', 'Subs', 'Time Limit', 'Condition', 'Time Of Day', 'Season', 'Weather');
      $boxTitle = "Game Settings";
      ?>
      <br>
      <?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
      <tr class="row">
        <td style="white-space: nowrap" align="center" class="black-small"><?= GetMatchTime($rowInfo['matchTime']) ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= GetYesNoRev($rowInfo['matchTypeEx']) ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= GetYesNo($rowInfo['matchTypePk']) ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= GetYesNo($rowInfo['injuries']) ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $rowInfo['numberOfPauses'] ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $rowInfo['maxNoOfSubstitutions'] ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $rowInfo['timeLimit'] ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $rowInfo['conditionSetting'] ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $rowInfo['timeSetting'] ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $rowInfo['season'] ?></td>
        <td style="white-space: nowrap" align="center" class="black-small"><?= $rowInfo['weather'] ?></td>
      </tr>
    <?= getRankBoxBottom() ?>
  <?    
    } 
  ?>
  
 
    <?
    }
  ?>
  <?= getOuterBoxBottomLinks('<a href="#" onClick="history.go(-1); return false;">&laquo;&nbsp;Go back</a>', null) ?>

<?php
		require ('../bottom.php');
    
    
function GetMatchTime($matchTime) {
  $ret = "Unknown";
  if ($matchTime == 0) {
    $ret = "5 minutes";
  } elseif ($matchTime == 1) {
    $ret = "10 minutes";
  } elseif ($matchTime == 2) {
    $ret = "15 minutes";
  } elseif ($matchTime == 3) {
    $ret = "20 minutes";
  } elseif ($matchTime == 4) {
    $ret = "25 minutes";
  } elseif ($matchTime == 5) {
    $ret = "30 minutes";
  }
  return $ret;
}

function GetYesNo($var) {
  $ret = "";
  if ($var == 0) {
    $ret = "Yes";
  } elseif ($var == 1) {
    $ret = "No";
  } else {
    $ret = "?";
  }
  return $ret;
}

function GetYesNoRev($var) {
  $ret = "";
  if ($var == 0) {
    $ret = "No";
  } elseif ($var == 1) {
    $ret = "Yes";
  } else {
    $ret = "?";
  }
  return $ret;
}
?>

