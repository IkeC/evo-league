<?php
$page = "index";
$subpage = "sixgames";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');


if(!empty($_POST['selectname'])) {
	$selectname = mysql_real_escape_string($_POST['selectname']);
} elseif (!empty($_GET['p'])) {
  $selectname = mysql_real_escape_string($_GET['p']);
} else {
  $selectname = "all";
}

if(!empty($_POST['selectname2'])) {
	$selectname2 = mysql_real_escape_string($_POST['selectname2']);
} elseif (!empty($_GET['p2'])) {
  $selectname2 = mysql_real_escape_string($_GET['p2']);
} else {
  $selectname2 = "all";
}

if(!empty($_POST['patch'])) {
	$patch = mysql_real_escape_string($_POST['patch']);
} elseif (!empty($_GET['patch'])) {
  $patch = mysql_real_escape_string($_GET['patch']);
} else {
  $patch = "all";
}

if(!empty($_POST['lobby'])) {
	$lobby = mysql_real_escape_string($_POST['lobby']);
} elseif (!empty($_GET['lobby'])) {
  $lobby = mysql_real_escape_string($_GET['lobby']);
} else {
  $lobby = "all";
}

if(!empty($_GET['s'])) {
  $start = mysql_real_escape_string($_GET['s']);
} else {
  $start = 0;
}

if (!empty($_GET['t'])) {
	$type = mysql_real_escape_string($_GET['t']);
} elseif (!empty($_POST['t'])) {
	$type = mysql_real_escape_string($_POST['t']);
} else {
	$type = "finished";
}

if (!empty($_GET['id'])) {
	$id = mysql_real_escape_string($_GET['id']);
} elseif (!empty($_POST['id'])) {
	$id = mysql_real_escape_string($_POST['id']);
} else {
	$id = "";
}

 $log = new KLogger('/var/www/yoursite/http/log/general/', KLogger::INFO);	

$maxDisplayedPages = 25;
$gamesPerPage = 30;

$left = $subNavText.getRaquo().getSubNavigation($subpage, null);
$sql = "SELECT COUNT(*) FROM six_matches WHERE Date(played_on) = CURDATE()";
$row = mysql_fetch_array(mysql_query($sql));
$numToday = $row[0];
if ($numToday == 1) {
  $right = $numToday. " game today";
} else {
  $right = $numToday. " games today";
}
?>

<?= getOuterBoxTop($left, $right) ?>
<table width="100%">
  <tr>
    <td style="text-align:left;vertical-align:middle;">
    <form method="post" name="formGames" action="<?= $_SERVER['PHP_SELF'] ?>">
    <span style="vertical-align:middle">Player&nbsp;</span>
    <select class="width150" name="selectname">
    <option <? if ($selectname == 'all') {
   		echo ' selected="selected"';
   }?> value="all">(all players)</option>
   <?
   if (!empty($cookie_name)) {
    echo "<option";
    if ($cookie_name == $selectname) {
      echo ' selected="selected"';
    }
    echo ">".$cookie_name."</option>";
   }
   echo '<option value="all"></option>';
   $sql = "SELECT name FROM ".$playerstable." WHERE serial6<>'' ORDER BY name ASC";
   $result = mysql_query($sql);
   $num = mysql_num_rows($result);
   while ($row = mysql_fetch_array($result)) {
      $name = $row["name"];
      echo "<option";
      if ($selectname != "all" && $name != $cookie_name && $name == $selectname) {
				echo ' selected="selected"';
      }
      echo ">".$name."</option>";
	 } ?>
   </select>
   <? /* 
   <span style="vertical-align:middle">&nbsp;with&nbsp;</span>
   <select class="width150" name="selectname2">
    <option <? if ($selectname2 == 'all') {
   		echo ' selected="selected"';
   }?> value="all"></option>
   <?
   echo '<option value="all"></option>';
   mysql_data_seek($result,0);
   while ($row = mysql_fetch_array($result)) {
      $name = $row["name"];
      echo "<option";
      if ($selectname2 != "all" && $name == $selectname2) {
				echo ' selected="selected"';
      }
      echo ">".$name."</option>";
	 } ?>
   </select>
   */ ?>
   <span style="vertical-align:middle">&nbsp;&nbsp;Patch&nbsp;</span>
    <select class="width200" name="patch">
    <option <? if ($patch == 'all') {
   		echo ' value="all" selected="selected"';
   }?> value="all">(all patches)</option>
   <?
   $sql = "SELECT name FROM six_patches GROUP BY name ORDER BY name ASC";
   
   $result = mysql_query($sql);
   $num = mysql_num_rows($result);
   while ($row = mysql_fetch_array($result)) {
      $name = $row["name"];
      echo "<option";
      if ($patch == $name) {
				echo ' selected="selected"';
      } 
      echo ">".$name."</option>";
	 } ?>
   </select>
   <span style="vertical-align:middle">&nbsp;&nbsp;Lobby&nbsp;</span>
    <select class="width200" name="lobby">
    <option <? if ($lobby == 'all') {
   		echo ' value="all" selected="selected"';
   }?> value="all">(all lobbies)</option>
   <?
   $sql = "SELECT lobbyName FROM six_matches WHERE lobbyName<>'' GROUP BY lobbyName ORDER BY lobbyName ASC";
   $result = mysql_query($sql);
   $num = mysql_num_rows($result);
   while ($row = mysql_fetch_array($result)) {
      $lobbyName = $row["lobbyName"];
      echo "<option";
      if ($lobby == $lobbyName) {
				echo ' selected="selected"';
      } 
      echo ">".$lobbyName."</option>";
	 } ?>
   </select>
   <span style="vertical-align:middle">&nbsp;&nbsp;Type&nbsp;</span>
   <select class="width100" name="t">
    <option <? if ($type == 'finished') { echo ' selected="selected"';}?> value="finished">Finished</option>
    <option <? if ($type == 'unfinished') { echo ' selected="selected"';}?> value="unfinished">Unfinished</option>
   </select>
   <span style="vertical-align:middle">&nbsp;&nbsp;ID&nbsp;</span>
   <input type="text" class="width50" name="id" maxlength="8" />
   &nbsp;&nbsp;<input type="Submit" class="width100" name="submit" value="Show" />
</td></tr>
</table>
<?= getOuterBoxBottom() ?>
<?

$manBlue = '<img src="/gfx/man-blue.png" style="cursor:help;" title="Hosted first half">';
$manRed = '<img src="/gfx/man-red.png" style="cursor:help;" title="Hosted second half">';


if ($type == "finished") {
  if ($selectname == "all") {
    $sqlfilter = "WHERE 1=1 ";
  } else {
    $sqlfilter = "LEFT JOIN six_matches_played ON six_matches_played.match_id=six_matches.id ".
      "LEFT JOIN six_profiles ON six_matches_played.profile_id=six_profiles.id ".
      "LEFT JOIN ".$playerstable." ON ".$playerstable.".player_id=six_profiles.user_id ".
      "WHERE ".$playerstable.".name='".$selectname."' ";
  }
  
  if ($patch <> "all") {
    $sqlfilter .= "AND (sp1.name='".$patch."' OR sp2.name='".$patch."') ";
  }
  if ($lobby <> "all") {
    $sqlfilter .= "AND six_matches.lobbyName='".$lobby."' ";
  }
  if ($id <> "") {
    $sqlfilter .= "AND six_matches.id=".$id." ";
  }
  $sqlCount = "SELECT COUNT(*) FROM six_matches ".
    "LEFT JOIN six_patches sp1 ON six_matches.hashHome=sp1.hash ".
    "LEFT JOIN six_patches sp2 ON six_matches.hashAway=sp2.hash ".$sqlfilter;
    $result = mysql_query($sqlCount);
  
  $row = mysql_fetch_array($result);
  $numTotal = $row[0];
  
  $sql = "SELECT six_matches.id, six_matches.season, six_matches.lobbyName, six_matches.roomName, sp1.name AS patchHome, sp2.name AS patchAway, ".
    "UNIX_TIMESTAMP(six_matches.played_on) as played_on, six_matches.score_home, six_matches.score_away, ".
    "six_matches.score_home_reg, six_matches.score_away_reg, ".
    "st1.ladderTeamId as ladderTeamHome, st2.ladderTeamId as ladderTeamAway ". 
    "FROM six_matches ".
    "LEFT JOIN six_patches sp1 ON six_matches.hashHome=sp1.hash ".
    "LEFT JOIN six_patches sp2 ON six_matches.hashAway=sp2.hash ".
    "LEFT JOIN six_teams st1 ON (st1.sixTeamId=six_matches.team_id_home AND st1.patchId=sp1.id) ".
    "LEFT JOIN six_teams st2 ON (st2.sixTeamId=six_matches.team_id_away AND st2.patchId=sp2.id) ".
    $sqlfilter." ORDER BY six_matches.id DESC LIMIT ".$start.", ".$gamesPerPage;
  
  $result = mysql_query($sql);
} 
else { // unfinished
  if ($selectname == "all") {
    $sqlfilter = "";
  } else {
  	$sqlProfiles = "SELECT six_profiles.id FROM six_profiles LEFT JOIN ".$playerstable." ON ".
    	$playerstable.".player_id=six_profiles.user_id WHERE ".$playerstable.".name='".$selectname."'";
  	$inSelect = "IN (";
  	$resProfiles = mysql_query($sqlProfiles);
  	while ($rowProfiles = mysql_fetch_array($resProfiles)) {
  		$inSelect = $inSelect."'".$rowProfiles['id']."',";	
	}
 
	$inSelect = substr($inSelect,0,strlen($inSelect)-1);
  $inSelect .= ")";
	
  $sqlfilter = "AND (". 
      "sms.profileHome ".$inSelect." OR sms.profileHome2 ".$inSelect." OR sms.profileHome3 ".$inSelect.
      " OR sms.profileAway ".$inSelect." OR sms.profileAway2 ".$inSelect." OR sms.profileAway3 ".$inSelect.
      ") ";
  }
  
  if ($patch <> "all") {
    $sqlfilter .= "AND (sp1.name='".$patch."' OR sp2.name='".$patch."') ";
  }
  if ($lobby <> "all") {
    $sqlfilter .= "AND sms.lobbyName='".$lobby."' ";
  }
  if ($id <> "") {
    $sqlfilter .= "AND sms.id=".$id." ";
  }

  $sqlCount = "SELECT COUNT(*) FROM six_matches_status sms ".
   "LEFT JOIN six_patches sp1 ON sp1.hash=sms.hashHome ".
   "LEFT JOIN six_patches sp2 ON sp2.hash=sms.hashAway ".
   "WHERE updated < date_sub(now(), INTERVAL 5 MINUTE) ".$sqlfilter;
  
  $result = mysql_query($sqlCount);
  $row = mysql_fetch_array($result);
  $numTotal = $row[0];
  $end = $start + $gamesPerPage;
  
  $sql = "SELECT sms.id, sms.dc, sms.season, sms.minutes, UNIX_TIMESTAMP(sms.updated) as updatedOn, sms.profileHome, sms.profileHome2, sms.profileHome3, sms.profileAway, ". 
   "sms.profileAway2, sms.profileAway3, sms.scoreHome, sms.scoreAway, sms.scoreHomeReg, sms.scoreAwayReg, sms.lobbyName, sp1.name AS patchHome, sp2.name AS patchAway, ".
   "UNIX_TIMESTAMP(sms.homeExit) AS homeExit, UNIX_TIMESTAMP(sms.awayExit) AS awayExit, UNIX_TIMESTAMP(sms.homeCancel) AS homeCancel, UNIX_TIMESTAMP(sms.awayCancel) AS awayCancel, ".
   "st1.ladderTeamId as ladderTeamHome, st2.ladderTeamId as ladderTeamAway ". 
   "FROM six_matches_status sms ".
   "LEFT JOIN six_patches sp1 ON sp1.hash=sms.hashHome ".
   "LEFT JOIN six_patches sp2 ON sp2.hash=sms.hashAway ".
   "LEFT JOIN six_teams st1 ON (st1.sixTeamId=sms.teamHome AND st1.patchId=sp1.id) ".
   "LEFT JOIN six_teams st2 ON (st2.sixTeamId=sms.teamAway AND st2.patchId=sp2.id) ".
   "WHERE sms.updated < date_sub(now(), INTERVAL 15 MINUTE) ".$sqlfilter." ORDER BY sms.id DESC LIMIT ".$start.", ".$gamesPerPage;
  
  $result = mysql_query($sql);
}

$log->logInfo('games sql='.$sql);

$navString = "";
$pages = ceil($numTotal/$gamesPerPage);
$currentPage = floor($start / $gamesPerPage) + 1;
$startPage = floor(($currentPage-1) / $maxDisplayedPages) * $maxDisplayedPages + 1;

if (($startPage + $maxDisplayedPages) > $pages) {
  $endPage = $pages;
} else {
  $endPage = $startPage + $maxDisplayedPages-1;
}

$getParams = '&amp;p='.$selectname.'&amp;t='.$type.'&amp;patch='.urlencode($patch).'&amp;lobby='.urlencode($lobby);

if ($startPage > 1) {
  $navString .= '<a href="'.$_SERVER['PHP_SELF'].'?s='.(($startPage-$maxDisplayedPages-1)*$gamesPerPage).$getParams.'">&lt;&lt;</a>&nbsp;|&nbsp;';
}

for ($pageNo = $startPage; $pageNo <= $endPage; $pageNo++) {
  if ($pageNo == $currentPage) {
    $pageDisplay = '<font class="menu-active">'.$pageNo.'</font>';
  } else {
    $pageDisplay = $pageNo;
  }
  $startForLink = ($pageNo-1)*$gamesPerPage;
  $navString .= '<a href="'.$_SERVER['PHP_SELF'].'?s='.$startForLink.$getParams.'">'.$pageDisplay.'</a>';
  if ($pageNo < $endPage) {
    $navString .= "&nbsp;|&nbsp;";
  }
}

if ($endPage < $pages) {
  $navString .= '&nbsp;|&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?s='.(($startPage+$maxDisplayedPages-1)*$gamesPerPage).$getParams.'">&gt;&gt;</a>';
}


$left = 'Result pages <span class="grey-small">&raquo;&nbsp;&nbsp;'.$navString;
If ($numTotal == 1) {
  $right = $numTotal." game";
} else {
  $right = $numTotal." games";
}

?>
<?= getOuterBoxTop($left, $right) ?>
<table width="100%"><tr><td>
<?

if ($type == "finished") {
  $columnTitlesArray = array ('Id', '<span style="cursor:help;" title="Season">S</span>', 'Date', 'Pt &plusmn;', 'Profile', 'User','','', 'Result', '','','User', 'Profile', 'Pt &plusmn;', 'Patch', 'Lobby', '');
  $boxTitle = "Sixserver Finished Games";
  ?>
  <?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>

      <?
      while ($row = mysql_fetch_array($result)) {
        
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
          if ($patchHome <> $patchAway) {
            $patchDisplay = "Mixed";
          } else if ($patchHome == "") {
            $patchDisplay = "Unknown";
          } else {
            $patchDisplay = $patchHome;
          }
          
          $lobbyName = $row['lobbyName'];
          if ($lobbyName == "") $lobbyName = "Unknown";
          $roomName = $row['roomName'];
          
          $sql2 = "SELECT six_matches_played.pointsDiff, ".
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
          $pointsDiffHome = $row2['pointsDiff'];
          
          $player_home2 = "";
          $nationalityHome2 = "";
          $profileNameHome2 = "";
          $pointsDiffHome2 = "";

          $player_home3 = "";
          $nationalityHome3 = "";
          $profileNameHome3 = "";
          $pointsDiffHome3 = "";

          $row2 = mysql_fetch_array($result2);
          if (!empty($row2)) {
            $player_home2 = $row2['name'];
            $nationalityHome2 = $row2['nationality'];
            $profileNameHome2 = $row2['profileName'];
            $pointsDiffHome2 = $row2['pointsDiff'];
            $row2 = mysql_fetch_array($result2);
            if (!empty($row2)) {
              $player_home3 = $row2['name'];
              $nationalityHome3 = $row2['nationality'];
              $profileNameHome3 = $row2['profileName'];
              $pointsDiffHome3 = $row2['pointsDiff'];
            } 
          } 
          
          $sql2 = "SELECT six_matches_played.pointsDiff, ".
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
          $pointsDiffAway = $row2['pointsDiff'];
          
          $player_away2 = "";
          $nationalityAway2 = "";
          $profileNameAway2 = "";
          $pointsDiffAway2 = "";

          $player_away3 = "";
          $nationalityAway3 = "";
          $profileNameAway3 = "";
          $pointsDiffAway3 = "";

          $row2 = mysql_fetch_array($result2);
          if (!empty($row2)) {
            $player_away2 = $row2['name'];
            $nationalityAway2 = $row2['nationality'];
            $profileNameAway2 = $row2['profileName'];
            $pointsDiffAway2 = $row2['pointsDiff'];
            $row2 = mysql_fetch_array($result2);
            if (!empty($row2)) {
              $player_away3 = $row2['name'];
              $nationalityAway3 = $row2['nationality'];
              $profileNameAway3 = $row2['profileName'];
              $pointsDiffAway3 = $row2['pointsDiff'];
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
            $pointsDiffLeft = $pointsDiffAway;
            $pointsDiffRight = $pointsDiffHome;

            $nationalityLeft2 = $nationalityAway2;
            $nationalityRight2 = $nationalityHome2;
            $profileNameLeft2 = $profileNameAway2;
            $profileNameRight2 = $profileNameHome2;
            $playerLeft2 = $player_away2;
            $playerRight2 = $player_home2;
            $pointsDiffLeft2 = $pointsDiffAway2;
            $pointsDiffRight2 = $pointsDiffHome2;

            $nationalityLeft3 = $nationalityAway3;
            $nationalityRight3 = $nationalityHome3;
            $profileNameLeft3 = $profileNameAway3;
            $profileNameRight3 = $profileNameHome3;
            $playerLeft3 = $player_away3;
            $playerRight3 = $player_home3;
            $pointsDiffLeft3 = $pointsDiffAway3;
            $pointsDiffRight3 = $pointsDiffHome3;
            $hostLeft = $manRed;
            $hostRight = $manBlue;
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
            $pointsDiffLeft = $pointsDiffHome;
            $pointsDiffRight = $pointsDiffAway;

            $nationalityLeft2 = $nationalityHome2;
            $nationalityRight2 = $nationalityAway2;
            $profileNameLeft2 = $profileNameHome2;
            $profileNameRight2 = $profileNameAway2;
            $playerLeft2 = $player_home2;
            $playerRight2 = $player_away2;
            $pointsDiffLeft2 = $pointsDiffHome2;
            $pointsDiffRight2 = $pointsDiffAway2;

            $nationalityLeft3 = $nationalityHome3;
            $nationalityRight3 = $nationalityAway3;
            $profileNameLeft3 = $profileNameHome3;
            $profileNameRight3 = $profileNameAway3;
            $playerLeft3 = $player_home3;
            $playerRight3 = $player_away3;
            $pointsDiffLeft3 = $pointsDiffHome3;
            $pointsDiffRight3 = $pointsDiffAway3;
            $hostLeft = $manBlue;
            $hostRight = $manRed;
          }
                    
          $rowspan = "";
          $topmargin = 'style="margin-top:2px"';
          
          if ($selectname == 'all' && ($playerLeft == $cookie_name || $playerRight == $cookie_name || (!empty($playerLeft2) && $playerLeft2 == $cookie_name) || (!empty($playerRight2) && $playerRight2 == $cookie_name))) {
            $rowclass = "row_active";
          } else {
            $rowclass = "row";
          }
          
          $regScoreInfo = "";
          if (($scoreLeft > $scoreRegLeft) || ($scoreRight > $scoreRegRight)) {
            $regScoreInfo = ' style="cursor:help;" title="'.$scoreRegLeft.'-'.$scoreRegRight.' after 90 minutes"';
          }
          
          if (is_null($ladderTeamLeft)) {
            $flagLeft = '<img title="'.$nationalityLeft.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft.'.bmp" width="18" height="15" border="1">';
            if ($playerLeft2 != "") {
              $flagLeft .= '<div '.$topmargin.'><img title="'.$nationalityLeft2.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft2.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
            if ($playerLeft3 != "") {
              $flagLeft .= '<div '.$topmargin.'><img title="'.$nationalityLeft3.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft3.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
          } else {
            $flagLeft = getImgForTeam($ladderTeamLeft);
          }
          
          if (is_null($ladderTeamRight)) {
            $flagRight = '<img title="'.$nationalityRight.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight.'.bmp" width="18" height="15" border="1">';
            if ($playerRight2 != "") {
              $flagRight .= '<div '.$topmargin.'><img title="'.$nationalityRight2.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight2.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
            if ($playerRight3 != "") {
              $flagRight .= '<div '.$topmargin.'><img title="'.$nationalityRight3.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight3.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
          } else {
            $flagRight = getImgForTeam($ladderTeamRight);
          }
          
          
          
          ?>
        <tr class="<?= $rowclass ?>">
          <td align="center" class="black-small"><?= $id ?></td>
          <td align="center" class="black-small"><?= $sixSeason ?></td>
          <td style="white-space: nowrap" class="black-small"><?= $gamedate ?></td>
          <td style="white-space: nowrap" align="center" class="grey-small">
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
          <td align="center"><?= $hostLeft ?></td>
          <td align="center"><?= $flagLeft ?></td>
          <td align="center" <?= $regScoreInfo; ?>><?= $scoreLeft ?>&nbsp;-&nbsp;<?= $scoreRight ?></td>
          <td align="center"><?= $flagRight ?></td>
          <td align="center"><?= $hostRight ?></td>
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
          
          <td style="white-space: nowrap" align="center" class="grey-small">
            <?= $pointsDiffRight ?>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsDiffRight2 ?></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><?= $pointsDiffRight3 ?></div>
            <? } ?>
          </td>
          <td style="white-space: nowrap" class="darkgrey-small"><?= $patchDisplay ?></td>
          <td style="white-space: nowrap" class="darkgrey-small"  title="Room: <?= $roomName ?>"><?= $lobbyName ?></td>
          <td style="white-space: nowrap" class="darkgrey-small"><a href="game.php?id=<?= $id ?>">Details &raquo;</a></td>
        </tr>
        <?
  }
  ?>
  <?= getRankBoxBottom() ?>
  <?
}
else { // unfinished

  $columnTitlesArray = array ('Id', 'Season', 'Updated', 'Min', 'Patch', 'Profile', 'User','','', 'Result', '','','User', 'Profile', 'Patch', 'Lobby');
  $boxTitle = "Sixserver Unfinished Games";
  ?>
  <?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>

      <?
      while ($row = mysql_fetch_array($result)) {
        
          $id = $row['id'];
          $dc = $row['dc'];
          $sixSeason = $row['season'];
          $updated = formatLongDate($row['updatedOn']);
          $minutes = $row['minutes'];
          $score_home = $row['scoreHome'];
          $score_home_reg = $row['scoreHomeReg'];
          $score_away = $row['scoreAway'];
          $score_away_reg = $row['scoreAwayReg'];
          $ladderTeamHome = $row['ladderTeamHome'];
          $ladderTeamAway = $row['ladderTeamAway'];
          $profileIdHome = $row['profileHome'];
          $profileIdHome2 = $row['profileHome2'];
          $profileIdHome3 = $row['profileHome3'];
          $profileIdAway = $row['profileAway'];
          $profileIdAway2 = $row['profileAway2'];
          $profileIdAway3 = $row['profileAway3'];
          $patchHome = $row['patchHome'];
          $patchAway = $row['patchAway'];
          $exitHome = $row['homeExit'];
          $exitAway = $row['awayExit'];
          $cancelHome = $row['homeCancel'];
          $cancelAway = $row['awayCancel'];
          if ($patchHome == "") $patchHome = "Unknown";
          if ($patchAway == "") $patchAway = "Unknown";
          
          $lobbyName = $row['lobbyName'];
          if ($lobbyName == "") $lobbyName = "Unknown";

          $sqlStart = "SELECT weblm_players.name, weblm_players.nationality, six_profiles.name AS profileName FROM weblm_players " .
	          "LEFT JOIN six_profiles ON six_profiles.user_id = weblm_players.player_id " .
	          "WHERE six_profiles.id=";
          // home
          $result2 = mysql_query($sqlStart.$profileIdHome);
          $row2 = mysql_fetch_array($result2);
          $player_home = $row2['name'];
          $nationalityHome = $row2['nationality'];
          $profileNameHome = $row2['profileName'];
          
          $player_home2 = "";
          $nationalityHome2 = "";
          $profileNameHome2 = "";

          $player_home3 = "";
          $nationalityHome3 = "";
          $profileNameHome3 = "";

          if ($profileIdHome2 > 0) {
	          $result2 = mysql_query($sqlStart.$profileIdHome2);
	          $row2 = mysql_fetch_array($result2);
	          $player_home2 = $row2['name'];
            $nationalityHome2 = $row2['nationality'];
            $profileNameHome2 = $row2['profileName'];          	
          } 

          if ($profileIdHome3 > 0) {
	          $result2 = mysql_query($sqlStart.$profileIdHome3);
	          $row2 = mysql_fetch_array($result2);
	          $player_home3 = $row2['name'];
            $nationalityHome3 = $row2['nationality'];
            $profileNameHome3 = $row2['profileName'];          	
          } 
          
          // away
          $result2 = mysql_query($sqlStart.$profileIdAway);
          $row2 = mysql_fetch_array($result2);
          $player_away = $row2['name'];
          $nationalityAway = $row2['nationality'];
          $profileNameAway = $row2['profileName'];

          $player_away2 = "";
          $nationalityAway2 = "";
          $profileNameAway2 = "";
          
          $player_away3 = "";
          $nationalityAway3 = "";
          $profileNameAway3 = "";            
          
          if ($profileIdAway2 > 0) {
	          $result2 = mysql_query($sqlStart.$profileIdAway2);
	          $row2 = mysql_fetch_array($result2);
	          $player_away2 = $row2['name'];
            $nationalityAway2 = $row2['nationality'];
            $profileNameAway2 = $row2['profileName'];          	
          } 
          if ($profileIdAway3 > 0) {
	          $result2 = mysql_query($sqlStart.$profileIdAway3);
	          $row2 = mysql_fetch_array($result2);
	          $player_away3 = $row2['name'];
            $nationalityAway3 = $row2['nationality'];
            $profileNameAway3 = $row2['profileName'];          	
          } 

          $rowheight = 19;
          $idtooltip = "style='cursor:help;' title='ID #$id'";
          
          $tdStylePlayerLeft = "";
          $tdStylePlayerRight = "";
          
          $homeDC = false;
          $awayDC = false;
          if ($dc == 1) {
            $homeDC = true;
          } elseif ($dc == 2) {
            $awayDC = true;
          }


          if ($score_home < $score_away) {
            $scoreLeft = $score_away;
            $scoreRight = $score_home;
            $scoreRegLeft = $score_away_reg;
            $scoreRegRight = $score_home_reg;
            $nationalityLeft = $nationalityAway;
            $nationalityRight = $nationalityHome;
            $profileNameLeft = $profileNameAway;
            $profileNameRight = $profileNameHome;
            $playerLeft = $player_away;
            $playerRight = $player_home;
            $ladderTeamLeft = $ladderTeamAway;
            $ladderTeamRight = $ladderTeamHome;
            $exitLeft = $exitAway;
            $exitRight = $exitHome;
            $cancelLeft = $cancelAway;
            $cancelRight = $cancelHome;

            $nationalityLeft2 = $nationalityAway2;
            $nationalityRight2 = $nationalityHome2;
            $profileNameLeft2 = $profileNameAway2;
            $profileNameRight2 = $profileNameHome2;
            $playerLeft2 = $player_away2;
            $playerRight2 = $player_home2;

            $nationalityLeft3 = $nationalityAway3;
            $nationalityRight3 = $nationalityHome3;
            $profileNameLeft3 = $profileNameAway3;
            $profileNameRight3 = $profileNameHome3;
            $playerLeft3 = $player_away3;
            $playerRight3 = $player_home3;
                        
            $hostLeft = $manRed;
            $hostRight = $manBlue;
            
            $DCLeft = $awayDC;
            $DCRight = $homeDC;
            
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
            $exitLeft = $exitHome;
            $exitRight = $exitAway;
            $cancelLeft = $cancelHome;
            $cancelRight = $cancelAway;

            $nationalityLeft2 = $nationalityHome2;
            $nationalityRight2 = $nationalityAway2;
            $profileNameLeft2 = $profileNameHome2;
            $profileNameRight2 = $profileNameAway2;
            $playerLeft2 = $player_home2;
            $playerRight2 = $player_away2;

            $nationalityLeft3 = $nationalityHome3;
            $nationalityRight3 = $nationalityAway3;
            $profileNameLeft3 = $profileNameHome3;
            $profileNameRight3 = $profileNameAway3;
            $playerLeft3 = $player_home3;
            $playerRight3 = $player_away3;   

            $hostLeft = $manBlue;
            $hostRight = $manRed;
            
            $DCLeft = $homeDC;
            $DCRight = $awayDC;
          }
          
          $rowspan = "";
          $topmargin = 'style="margin-top:2px"';
          
          if ($selectname == 'all' && ($playerLeft == $cookie_name || $playerRight == $cookie_name || (!empty($playerLeft2) && $playerLeft2 == $cookie_name) || (!empty($playerRight2) && $playerRight2 == $cookie_name))) {
            $rowclass = "row_active";
          } elseif (empty($playerLeft2) && empty($playerRight2) && ($lobbyName <> 'Training') && IsFishySixserverGame($scoreLeft, $scoreRight, $minutes) && ($DCRight || ($scoreLeft == $scoreRight && $DCLeft))) {
            $rowclass = "row_alert";
          } else {
            $rowclass = "row";
          }
          
          $regScoreInfo = "";
          if (($scoreLeft > $scoreRegLeft) || ($scoreRight > $scoreRegRight)) {
            $regScoreInfo = ' style="cursor:help;" title="'.$scoreRegLeft.'-'.$scoreRegRight.' after 90 minutes"';
          }
          if (!is_null($exitLeft)) {
            $playerLeftDisplay = '<span title="Disconnected at '.formatLongDate($exitLeft).'">'.$playerLeft.'</span>';
          } else {
            $playerLeftDisplay = $playerLeft;
          }
          
          if ($playerLeft2 != '') {
            if (!is_null($exitLeft)) {
              $playerLeft2Display = '<span style="cursor:help;" title="Disconnected at '.formatDate($exitLeft).'">'.$playerLeft2.'</span>';
            } else {
              $playerLeft2Display = $playerLeft2;
            }
            $playerLeft2Display = '<a href="'.$directory.'/profile.php?name='.$playerLeft2.'">'.$playerLeft2Display.'</a>';
          }
          if ($playerLeft3 != '') {
            if (!is_null($exitLeft)) {
              $playerLeft3Display = '<span style="cursor:help;" title="Disconnected at '.formatDate($exitLeft).'">'.$playerLeft3.'</span>';
            } else {
              $playerLeft3Display = $playerLeft3;
            }
            $playerLeft3Display = '<a href="'.$directory.'/profile.php?name='.$playerLeft3.'">'.$playerLeft3Display.'</a>';
          }
          
          if (!is_null($exitRight)) {
            $playerRightDisplay = '<span style="cursor:help;" title="Disconnected at '.formatDate($exitRight).'">'.$playerRight.'</span>';
          } else {
            $playerRightDisplay = $playerRight;
          }
          
          if ($playerRight2 != '') {
            if (!is_null($exitRight)) {
              $playerRight2Display = '<span style="cursor:help;" title="Disconnected at '.formatDate($exitRight).'">'.$playerRight2.'</span>';
            } else {
              $playerRight2Display = $playerRight2;
            }
            $playerRight2Display = '<a href="'.$directory.'/profile.php?name='.$playerRight2.'">'.$playerRight2Display.'</a>';
          }
          if ($playerRight3 != '') {
            if (!is_null($exitRight)) {
              $playerRight3Display = '<span style="cursor:help;" title="Disconnected at '.formatDate($exitRight).'">'.$playerRight3.'</span>';
            } else {
              $playerRight3Display = $playerRight3;
            }
            $playerRight3Display = '<a href="'.$directory.'/profile.php?name='.$playerRight3.'">'.$playerRight3Display.'</a>';
          }

          $styleLeft = '';
          $styleRight = '';
          if ($DCLeft) {
            $styleLeft = 'style="color:red"';
          } elseif ($DCRight) {
            $styleRight = 'style="color:red"';
          }
          
          $playerLeftDisplay = '<a '.$styleLeft.' href="'.$directory.'/profile.php?name='.$playerLeft.'">'.$playerLeftDisplay.'</a>';
          $playerRightDisplay = '<a '.$styleRight.' href="'.$directory.'/profile.php?name='.$playerRight.'">'.$playerRightDisplay.'</a>';
          
          if (is_null($ladderTeamLeft)) {
            $flagLeft = '<img title="'.$nationalityLeft.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft.'.bmp" width="18" height="15" border="1">';
            if ($playerLeft2 != "") {
              $flagLeft .= '<div '.$topmargin.'><img title="'.$nationalityLeft2.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft2.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
            if ($playerLeft3 != "") {
              $flagLeft .= '<div '.$topmargin.'><img title="'.$nationalityLeft3.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft3.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
          } else {
            $flagLeft = getImgForTeam($ladderTeamLeft);
          }
          
          if (is_null($ladderTeamRight)) {
            $flagRight = '<img title="'.$nationalityRight.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight.'.bmp" width="18" height="15" border="1">';
            if ($playerRight2 != "") {
              $flagRight .= '<div '.$topmargin.'><img title="'.$nationalityRight2.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight2.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
            if ($playerRight3 != "") {
              $flagRight .= '<div '.$topmargin.'><img title="'.$nationalityRight3.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight3.'.bmp" width="18" height="15" border="1" style="margin-top:2px"></div>';
            }
          } else {
            $flagRight = getImgForTeam($ladderTeamRight);
          }
          ?>
        <tr class="<?= $rowclass ?>">
          <td align="center" class="black-small"><?= $id ?></td>
          <td align="center" class="black-small"><?= $sixSeason ?></td>
          <td style="white-space: nowrap" class="black-small"><?= $updated ?></td>
          <td class="black-small"><?= $minutes ?></td>
          <td style="white-space: nowrap" class="darkgrey-small" align="right"><?= $patchHome ?></td>
          <td align="right" class="black-small">
            <?= $profileNameLeft ?>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><?= $profileNameLeft2 ?></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><?= $profileNameLeft3 ?></div>
            <? } ?>
          </td>
          <td align="right" <?= $tdStylePlayerLeft ?>>
            <?= $playerLeftDisplay ?>
            <? if ($playerLeft2 != "") { ?>
            <div <?= $topmargin ?>><?= $playerLeft2Display ?></div>
            <? } ?>
            <? if ($playerLeft3 != "") { ?>
            <div <?= $topmargin ?>><?= $playerLeft3Display ?></div>
            <? } ?>
          </td>
          <td align="center"><?= $hostLeft ?></td>
          <td align="center"><?= $flagLeft ?></td>
          <td align="center" <?= $regScoreInfo; ?>><?= $scoreLeft ?>&nbsp;-&nbsp;<?= $scoreRight ?></td>
          <td align="center"><?= $flagRight ?></td>
          <td align="center"><?= $hostRight ?></td>
          <td <?= $tdStylePlayerRight ?>>
           <?= $playerRightDisplay ?>
            <? if ($playerRight2 != "") { ?>
            <div <?= $topmargin ?>><?= $playerRight2Display ?></div>
            <? } ?>
            <? if ($playerRight3 != "") { ?>
            <div <?= $topmargin ?>><?= $playerRight3Display ?></div>
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
            <td style="white-space: nowrap" class="darkgrey-small"><?= $patchAway ?></td>
            <td style="white-space: nowrap" class="darkgrey-small"><?= $lobbyName ?></td>
        </tr>
        <?
  }
  ?>
  <?= getRankBoxBottom() ?>
  <?
} // end unfinished
?>
</td></tr></table>
<?= getOuterBoxBottomLinks($left, $right) ?>
<?php
		require ('../bottom.php');

?>

