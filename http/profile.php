<?

// shows a players profile, this page will usually appear if you click a players name somewhere
// the name is taken from the url, eg. http://www.yoursite/profile.php?name=IkeC

$page = "players";
$subpage = "profile";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('./sixserver/functions.php');
require('top.php');

$sortby = "name ASC";

if (! empty($_GET['name'])) {
    $name = mysql_real_escape_string($_GET['name']);
	$sql = "SELECT * FROM $playerstable WHERE name = '$name' ORDER BY $sortby";
} else if (! empty($_GET['id'])) {
    $id = mysql_real_escape_string($_GET['id']);
	$sql = "SELECT * FROM $playerstable WHERE player_id = '$id' ORDER BY $sortby";
} else if ($loggedIn) {
  $sql = "SELECT * FROM $playerstable WHERE name = '$cookie_name' ORDER BY $sortby";
}

$result = mysql_query($sql);
$num = @mysql_num_rows($result);
if ($num > 0) {
    $row = mysql_fetch_array($result);
		$player_id = $row['player_id'];
    $serial6 = $row['serial6'];
		$name = $row["name"];
    $alias = $row["alias"];
    $approved = $row["approved"];
    $version = $row["defaultversion"];
    $forum = $row["forum"];
	$joindate = $row['joindate'];
	$joindate = formatDate($joindate);    
    if ($approved == "no") {
        $nameDisplay = "<font color='#FF0000'>".$name."</font>";
    } else {
    	$nameDisplay = $name;
    }
    
    $nameDisplay = '<span style="cursor:help;" alt="ID #'.$player_id.' - joined '.$joindate.'" title="ID #'.$player_id.' - joined '.$joindate.'">'.$nameDisplay."</span>";
    
    $msn = $row["msn"];
    $mail = $row["mail"];
    
    if ($mail == "n/a" || empty($mail)) {
        $mailaddress = "";
        $mailpic = "";
    } else {
        $mailaddress = "<a href='mailto:$mail'><font color='$color1'>$mail</font></a>";
        $mailpic = "<img border='1' src='gfx/mail.gif' align='absmiddle'></a>";
    }
    $icq = $row["icq"];
    if ($icq == "n/a" || empty($icq)) {
        $icqnumber = "";
        $icqpic = "";
    } else {
        $icqnumber = "<a href='http://web.icq.com/whitepages/add_me?uin=$icq&action=add'><font color='$color1'>$icq</font></a>";
        $icqpic = "<img border='1' src='gfx/icq.gif' align='absmiddle'></a>";
    }
    $aim = $row["aim"];
    if ($aim == "n/a" || empty($aim)) {
        $aimname = "";
        $aimpic = "";
    } else {
        $aimname = "<a href='aim:AddBuddy?ScreenName=$aim'><font color='$color1'>$aim</font></a>";
        $aimpic = "<img border='1' src='gfx/aim.gif' align='absmiddle'></a>";
    }
    $msn = $row["msn"];
    if ($msn == "n/a" || empty($msn)) {
        $msnname = "";
        $msnpic = "";
    } else {
        $msnname = "$msn";
        $msnpic = "<img border='1' src='gfx/msn.gif' align='absmiddle'></a>";
    }
    $uploadSpeed = $row["uploadSpeed"];
    $downloadSpeed = $row["downloadSpeed"];
    
    $deductedPoints = $row["deductedPoints"];
    
    $country = $row["country"];
	$nationality = $row["nationality"];
	
	$message = $row["message"];
	
    $rating = $row["rating"];
    $wins = $row[getWinsFieldForVersion('A')] + $row[getWinsFieldForVersion('D')];
    $losses = $row[getLossesFieldForVersion('A')] + $row[getLossesFieldForVersion('D')];
    $points = $row[getPointsFieldForVersion($version)];
    $games = $row[getGamesFieldForVersion('A')] + $row[getGamesFieldForVersion('D')];
    $draws = $row['draws'];
    
    $teamWinsSql = "SELECT count(*) as count from $gamestable where season = $season ".
    	"AND deleted = 'no' AND teamLadder = 1 AND (winner = '$name' or winner2 = '$name') and isDraw = 0";
    $teamWnsRow = mysql_fetch_array(mysql_query($teamWinsSql));
    $teamWins = $teamWnsRow['count'];
    $wins += $teamWins;
    
    $teamLossesSql = "SELECT count(*) as count from $gamestable where season = $season ".
    	"AND deleted = 'no' AND teamLadder = 1 AND (loser = '$name' or loser2 = '$name') and isDraw = 0";
    $teamLossesRow = mysql_fetch_array(mysql_query($teamLossesSql));
    $teamLosses = $teamLossesRow['count'];
    $losses += $teamLosses;
	
    $teamDrawsSql = "SELECT count(*) as count from $gamestable where season = $season ".
    	"AND deleted = 'no' AND teamLadder = 1 AND (winner = '$name' or winner2 = '$name' or loser = '$name' or loser2 = '$name') and isDraw = 1";
    $teamDrawsRow = mysql_fetch_array(mysql_query($teamDrawsSql));
    $teamDraws = $teamDrawsRow['count'];
    $draws += $teamDraws;
	
	$teamGames = $teamWins + $teamLosses + $teamDraws;
	$games += $teamGames;
		
    if ($games <= 0) {
        $percentage = 0.000;
    } else {
        $percentage = $wins / $games;
    }

    $totalwins = $row["totalwins"];
    $totallosses = $row["totallosses"];
    $totaldraws = $row["totaldraws"];
    $totalgames = $row["totalgames"];
    
    if ($totalgames <= 0) {
        $totalpercentage = 0.000;
    } else {
        $totalpercentage = $totalwins / $totalgames;
    }
    $streakwins = $row["streakwins"];
    $streaklosses = $row["streaklosses"];
    if ($streakwins >= 1) {
        $streak = "+$streakwins";
    } else if ($streaklosses >= 1) {
        $streak = "-$streaklosses";
    } else {
        $streak = 0;
    }
    $versions = $row['versions'];
    $positionPlayer = getPlayerPosition($name, $version);
	$favteam1 = $row['favteam1'];
	$favteam2 = $row['favteam2'];
	
	
  $statusLinks[] = array('/cards.php?id='.$player_id, 'show_warnings_bans', 'show warnings and bans');
    ?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, $name), "") ?>

<table class="layouttable">
<tr><td width="50%" valign="top">
<?= getBoxTop("Player Profile", "", false, $statusLinks); ?>

<table class="layouttable" style="padding: 0 0 0 0;">
  <tr>
    <td colspan="3" nowrap><b><?= $nameDisplay ?></b></td>
	</tr>
<?
if (!empty($alias)) {
?>
	<tr>
	  <td colspan="2">
	  <?= "<span class='size11'>also known as&nbsp;&nbsp;<b>$alias</b><span>" ?>
	  </td>
	  <td width="10%"></td>
    </tr>
<? } ?>
	  <?
	  if (!empty($message)) {
	  	echo "<tr><td colspan='3'><span class='size11'>says&nbsp;&nbsp;<b>$message</b></span></td></tr>";
	  }
	  ?>
    <tr>
       <td colspan="3">
       <table style="margin-top:10px;">
		 <tr>
         	<td>Games</td>
	  		<td><?= getVersionsImages($versions) ?></td>
   		</tr>
	   <? if ($favteam1 != '0' || $favteam2 != '0') { ?>
		   <tr><td></td></tr>
		   <tr>
	         <td nowrap>Favorite team(s)</td>
	         <td colspan="3">
			 	<? if ($favteam1 != '0') {
			 	       echo getImgForTeam($favteam1);
			 	   }
				   if ($favteam2 != '0') {
			 	       echo "&nbsp;".getImgForTeam($favteam2);
			 	   }
				 ?>
			 </td>
	       </tr>
		<? } ?>       
	   <tr><td></td></tr>	   
	   <tr>
         <td>Nationality</td>
         <td colspan="3"><?php echo "<img src='$directory/flags/$nationality.bmp' align='absmiddle' border='1'> $nationality" ?></td>
       </tr>
	   <? if (!empty($country) && $country != $nationality) { ?>
		
	   <tr>
         <td>Location</td>
         <td colspan="3"><?php echo "<img src='$directory/flags/$country.bmp' align='absmiddle' border='1'> $country" ?></td>
       </tr>
	   <? } ?> 
		
       <? if (!empty($forum)) { ?>
       <tr>
          <td>Group</td>
          <td height="25" colspan="3"><?= $forum ?></td>
       </tr>
       <? } ?>

    <? if (!empty($icqnumber)) { ?>
       <tr>
          <td>ICQ</td>
          <td height="25" colspan="3"><?
           if ($cookie_name == 'null') {
            echo '<span style="color:#AAAAAA;">Hidden - log in to see</span>'; 
          } else {
          	echo "$icqpic $icqnumber";
          }
          ?></td>
       </tr>
       <? } ?>
       <? if (!empty($msnname)) { ?>
       <tr>
          <td>MSN</td>
          <td height="25" colspan="3"><?
          if ($cookie_name == 'null') {
            echo '<span style="color:#AAAAAA;">Hidden - log in to see</span>'; 
          } else {
          	echo "$msnpic $msnname";
          }
          ?></td>
       </tr>
       <? } ?>
       <? if (!empty($aimname)) { ?>
       <tr>
          <td>AIM</td>
          <td height="25" colspan="3"><?php echo "$aimpic $aimname" ?></td>
       </tr>
       <? } ?>
	  
       </table>
	</td>
  </tr>
</table>
<?= getBoxBottom() ?>

<?
  $statusLine = getPlayerStatusLine($name);
  if (!empty($statusLine)) {
?>
<?= getBoxTop("", "", false, null); ?>
<table class="layouttable">
	<tr>
		<td><?= $statusLine ?></td>
	</tr>
</table>
<?= getBoxBottom() ?>
<?	  
  }
?>

<? 
if (strlen($serial6)>0) { 
	$sql = "SELECT name, id AS profileId, disconnects, points, rating FROM six_profiles WHERE six_profiles.user_id=".$player_id.
    " ORDER BY points DESC";
	$result = mysql_query($sql);
	while ($rowProfiles = mysql_fetch_array($result)) {
    $sixName = $rowProfiles['name'];
    $sixProfileId = $rowProfiles['profileId'];
    $sixName = '<span title="#'.$sixProfileId.'">'.$sixName.'</span>';
    $pointsSix = $rowProfiles['points'];
    $ratingSix = $rowProfiles['rating'];
    
    $sixWins = GetSixserverWins($sixProfileId);
    $sixLosses = GetSixserverLosses($sixProfileId);
    $sixDraws = GetSixserverDraws($sixProfileId);
    $sixDC = $rowProfiles['disconnects'];
    $sixGames = $sixWins + $sixLosses + $sixDraws + $sixDC;
    if ($sixGames <= 0) {
        $sixPercentage = 0.000;
    } else {
        $sixPercentage = $sixWins / $sixGames;
    }
    if ($sixGames <= 0) {
        $DCPercentage = 0.000;
    } else {
        $DCPercentage = $sixDC / $sixGames * 100;
    }
    $DCDisplay = formatDC($DCPercentage, $sixGames);

    $sixWinsTeam = GetSixserverWinsTeam($sixProfileId);
    $sixLossesTeam = GetSixserverLossesTeam($sixProfileId);
    $sixDrawsTeam = GetSixserverDrawsTeam($sixProfileId);
    $sixGamesTeam = $sixWinsTeam + $sixLossesTeam + $sixDrawsTeam;
    if ($sixGamesTeam <= 0) {
        $sixPercentageTeam = 0.000;
    } else {
        $sixPercentageTeam = $sixWinsTeam / $sixGamesTeam;
    }
    
    $sixWinsTotal = $sixWins + $sixWinsTeam + GetSixserverWinsHistory($sixProfileId);
    $sixLossesTotal = $sixLosses + $sixLossesTeam + GetSixserverLossesHistory($sixProfileId);
    $sixDrawsTotal = $sixDraws + $sixDrawsTeam + GetSixserverDrawsHistory($sixProfileId);
    $sixDCTotal = $sixDC + GetSixserverDCHistory($sixProfileId);
    $sixGamesTotal = $sixWinsTotal + $sixLossesTotal + $sixDrawsTotal + $sixDCTotal;
    if ($sixGamesTotal <= 0) {
        $sixPercentageTotal = 0.000;
    } else {
        $sixPercentageTotal = $sixWinsTotal / $sixGamesTotal;
    }
    if ($sixGamesTotal <= 0) {
        $DCPercentageTotal = 0.000;
    } else {
        $DCPercentageTotal = $sixDCTotal / $sixGamesTotal * 100;
    }
    $DCDisplayTotal = formatDC($DCPercentageTotal, $sixGamesTotal);

    
?>
<?= getBoxTop("Sixserver Profile: ".$sixName."", false, null, ''); ?>
<table class="layouttable">

    <tr>
      <td style="padding-bottom:10px;"><b>Points</b></td>
      <td style="padding-bottom:10px;" class="td_profile">
        <?= $pointsSix ?>
      </td>
    <td></td>
      <td style="padding-bottom:10px;"><b>Rating</b></td>
      <td style="padding-bottom:10px;" class="td_profile">
        <?= $ratingSix ?>
      </td>
    </tr>   
             
   <tr>
      <td style="width:30%">
	    <? echo "<a href='/sixserver/games.php?p=$name' title=\"show $name's games\">"; ?><b>Games this season<b></a>
	  </td>
      <td class="td_profile">
	  	<span style="cursor:help;" title="Single player games"><?= $sixGames ?></span> / <span style="cursor:help;" title="Team games"><?= $sixGamesTeam ?></span>
	  </td>
      <td style="width:5%;"></td>
      <td style="width:30%">
  	    <? echo "<a href='$directory/sixserver/games.php?p=$name' title=\"show $name's games\">"; ?><b>Games total<b></a>
	  </td>     
	  <td class="td_profile">
		 <?php echo $sixGamesTotal ?> 	
	  </td>
      <td style="width:5%"></td>   
	 </tr>
	 
	 <tr>
    <td>
      Won	  
	  </td>
      <td class="td_profile">
	  	<span style="cursor:help;" title="Single player wins"><?= $sixWins ?></span> / <span style="cursor:help;" title="Team wins"><?= $sixWinsTeam ?></span>
	  </td>
      <td></td>
      <td>
	  	Won
	  </td>     
	  <td class="td_profile">
		 <?php echo $sixWinsTotal ?> 	
	  </td>
      <td></td>   
	 </tr>

	 <tr>
      <td>Lost</td>
      <td class="td_profile">
		  <span style="cursor:help;" title="Single player losses"><?= $sixLosses ?></span> / <span style="cursor:help;" title="Team losses"><?= $sixLossesTeam ?></span>
	  </td>
      <td></td>
      <td>Lost</td>
      <td class="td_profile">
	  	  <? echo $sixLossesTotal ?>
	  </td>
      <td></td>     
    </tr> 
    
    <tr>
      <td>Draw</td>
      <td class="td_profile">
        	<span style="cursor:help;" title="Single player draws"><?= $sixDraws; ?></span> / <span style="cursor:help;" title="Team draws"><?= $sixDrawsTeam ?></span>
	  </td>
      <td></td>
      <td>Draw</td>
      <td class="td_profile">
	  	  <? echo $sixDrawsTotal ?>
	  </td>
      <td></td>     
    </tr> 
	 
    <tr>
      <td>Win Percentage</td>
      <td class="td_profile">
      <span style="cursor:help;" title="Single player"><? printf("%.1f", $sixPercentage*100); ?></span> / <span style="cursor:help;" title="Team player"><? printf("%.1f", $sixPercentageTeam*100); ?></span>
		  
	  </td>
    <td></td>
      <td>Win Percentage</td>
       <td class="td_profile">
		  <? printf("%.1f", $sixPercentageTotal*100); ?>
	  </td>
    </tr> 
    
    <tr>
      <td style="padding-top:10px;"><a href="/sixserver/games.php?p=<?= $name ?>&t=unfinished">Disconnects</a></td>
      <td style="padding-top:10px;" class="td_profile">
		  <?= $sixDC ?>
	  </td>
    <td></td>
      <td style="padding-top:10px;"><a href="/sixserver/games.php?p=<?= $name ?>&t=unfinished">Disconnects</a></td>
       <td style="padding-top:10px;" class="td_profile">
		  <?= $sixDCTotal ?>
	  </td>
    </tr>   
    <tr>
      <td>DC Percentage</td>
      <td class="td_profile">
		  <b><span style="cursor:help;" title="<?= $sixDC ?> DC in <?= ($sixGames+$sixDC) ?> games"><?= $DCDisplay ?></span></b>
	  </td>
    <td></td>
      <td>DC Percentage</td>
       <td class="td_profile">
		  <b><span style="cursor:help;" title="<?= $sixDCTotal ?> DC in <?= ($sixGamesTotal+$sixDCTotal) ?> games"><?= $DCDisplayTotal ?></span></b>
	  </td>
    </tr>   
    
</table>
<?= getBoxBottom() ?>
<? 
	}
}
?>

<?= getBoxTop("Ladder Stats", "", false, null); ?>
     <table class="layouttable">             
			<tr>
      <td style="width:30%;"><?= getLadderForVersion($version)?>&nbsp;Position</td>
      <td class="td_profile"><b><?php echo $positionPlayer ?></b></td>
      <td style="width:5%"></td>
      <td style="width:30%"><?= getLadderForVersion($version)?>&nbsp;Points</td>
      <td class="td_profile"><b><?= $points ?></td>
      <td style="width:5%"></td>     
    </tr>
   
    <tr>
      <td>ELOrating</td>
      <td class="td_profile"><?php printf("%.0f", $rating); ?></td>
      <td></td>
      <td>Streak</td>
      <td class="td_profile"><?php echo $streak ?></td>
      <td></td>
	</tr>
	<? if (!empty($uploadSpeed) && !empty($downloadSpeed)) { ?>
     <tr>
      <td>Upload&nbsp;spd</td>
      <td class="td_profile"><?php echo $uploadSpeed ?></td>
      <td></td>
      <td>Download&nbsp;spd</td>
      <td class="td_profile"><?php echo $downloadSpeed ?></td>
      <td></td>
     </tr>
     <? } ?>
    </table>
    
<?= getBoxBottom() ?>

<?= getBoxTop("Ladder Games", "", false, null); ?>
  <table class="layouttable">             
	 <tr>
      <td style="width:30%">
	    <? echo "<a href='$directory/games.php?player=$name' title=\"show $name's games\">"; ?><b>Games this season</b></a>
	  </td>
      <td class="td_profile">
	  	<span style="cursor:help;" title="<?= $teamGames ?> team games"><?= $games ?></span>
	  </td>
      <td style="width:5%;"></td>
      <td style="width:30%">
  	    <? echo "<a href='$directory/games.php?player=$name' title=\"show $name's games\">"; ?><b>Games total</b></a>
	  </td>     
	  <td class="td_profile">
		 <?php echo $totalgames ?> 	
	  </td>
      <td style="width:5%"></td>   
	 </tr>
	 
	 <tr>
      <td>
		Won	  
	  </td>
      <td class="td_profile">
	  	<span style="cursor:help;" title="<?= $teamWins ?> team wins"><?= $wins ?></span>
	  </td>
      <td></td>
      <td>
	  	Won
	  </td>     
	  <td class="td_profile">
		 <?php echo $totalwins ?> 	
	  </td>
      <td></td>   
	 </tr>

	 <tr>
      <td>Lost</td>
      <td class="td_profile">
		 <span style="cursor:help;" title="<?= $teamLosses ?> team losses"><?= $losses ?></span>
	  </td>
      <td></td>
      <td>Lost</td>
      <td class="td_profile">
	  	  <? echo $totallosses ?>
	  </td>
      <td></td>     
    </tr> 
    
    <tr>
      <td>Draw</td>
      <td class="td_profile">
        	<span style="cursor:help;" title="<?= $teamDraws ?> team draws"><?= $draws ?></span>
	  </td>
      <td></td>
      <td>Draw</td>
      <td class="td_profile">
	  	  <? echo $totaldraws ?>
	  </td>
      <td></td>     
    </tr> 
	 
    <tr>
      <td>Win Percentage</td>
      <td class="td_profile">
		  <? printf("%.1f", $percentage*100); ?>
	  </td>
      <td></td>
      <td>Win Percentage</td>
       <td class="td_profile">
		  <? printf("%.1f", $totalpercentage*100); ?>
	  </td>
    </tr> 
</table>
<?= getBoxBottom() ?>

  </td>
  <td width="50%" style="vertical-align:top">
	<table border="0" cellpadding="0" cellspacing="0"><tr><td>
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
   
   $awards = GetAwards($player_id, $name);
   if (!empty($awards)) {
	?>    
	<?= getBoxTop("Awards", "", false, null); ?>
  <?= $awards ?>
	<?= getBoxBottom() ?>
  <? }

   if (!empty($imgpath)) {
	?>    
	<?= getBoxTop("User Image", "", false, null); ?>
      <img src="<? echo $imgpath ?>" />
	<?= getBoxBottom() ?>
   <? } ?>
   &nbsp;
    </td></tr></table>
  </td>
 </tr>
</table>
    <?
    
   }
   else {
   	echo getOuterBoxTop("error", "");
   	echo "<p>The player <b>$name</b> could not be found in the database.</p>";
   }
?>

<?= getOuterBoxBottom() ?>

<?

require('bottom.php');

function GetAwards($player_id, $name) {
  require('variables.php');
  require('variablesdb.php');

  $awards = "";
  
// ladder awards
  $ladderAwards = "";
  $awards_sql = "SELECT position, season from $historytable ".
    "WHERE player_id = '$player_id' order by season";
  $awards_result = mysql_query($awards_sql);
  $seasonPosArray = array();
  
  while($awards_row = mysql_fetch_array($awards_result)) {
    $position = $awards_row['position'];
    $db_season = $awards_row['season'];
    if ($position < 7 && $position > 0) {
      $seasonPosArray = addSeasonAndPos($seasonPosArray, $db_season, $position);
    }	
  }
  foreach ($seasonPosArray as $position => $seasonArray) {
    $ladderAwards .= getImgForPosAndSeasonArray($position, $seasonArray, "bottom");
  }
  if (!empty($ladderAwards)) {
    $awards .= "<p>".$ladderAwards."</p>";
  }
  
  // sixserver awards
  $sixAwards = "";
  $awards_sql = "SELECT position, season from six_history ".
    "WHERE playerId = '$player_id' ORDER BY season";
  $awards_result = mysql_query($awards_sql);
  $sixSeasonPosArray = array();
  
  while($awards_row = mysql_fetch_array($awards_result)) {
    $position = $awards_row['position'];
    $db_season = $awards_row['season'];
    if ($position < 7 && $position > 0) {
      $sixSeasonPosArray = addSeasonAndPos($sixSeasonPosArray, $db_season, $position);
    }	
  }
  foreach ($sixSeasonPosArray as $position => $seasonArray) {
    $sixAwards .= getSixImgForPosAndSeasonArray($position, $seasonArray, "bottom");
  }
  if (!empty($sixAwards)) {
    $awards .= "<p>".$sixAwards."</p>";
  }
  
  
  // tournament awards
  $tournamentAwards = "";
  $cup_sql = "SELECT profileImage, cupName from $tournamenttable ".
    "WHERE firstPlace = '$name'";
  $cup_result = mysql_query($cup_sql);
  while($cup_row = mysql_fetch_array($cup_result)) {
    $profileImage = $cup_row['profileImage'];
    $cupName = $cup_row['cupName'];
    $img = '<img style="vertical-align:bottom;padding:0 0 0 2px;margin:0 0 0 2px;" src="'.
      $directory.'/Cup/'.$profileImage.'" />';
    $tournamentAwards .= '<span style="cursor:help;" title="Winner of '.$cupName.'">'.$img.'</span>';
  }
 
    // mini tournament wins
  $mini_sql = "SELECT count(*) AS cou FROM $minitournamenttable where PLAYER_ID = '$player_id'";
  $mini_result = mysql_query($mini_sql);
  $mini_row = mysql_fetch_array($mini_result);
  
  $miniTournamentWins = $mini_row['cou'];
   
  if ($miniTournamentWins > 0) {
    $profileImage = $gfx_mini_tournament_prefix.$miniTournamentWins.".gif";
    $titleText = $miniTournamentWins." mini tournament win";
    if ($miniTournamentWins > 1) {
      $titleText .= "s";
    }
    $img = '<img style="vertical-align:bottom; padding:0 0 0 2px;margin:0 0 0 2px; margin-bottom:-2px;padding-bottom:-2px;" src="'.
      $directory.'/gfx/awards/'.$profileImage.'" />';
    $tournamentAwards .= '<span style="cursor:help;" title="'.$titleText.'">'.$img.'</span>';
  }
  
  // team ladder - player
   $sql = "SELECT playerId from $teamladdertable where type='player' ORDER BY timestamp DESC LIMIT 0, 1";
   $result = mysql_query($sql);
   while ($row = mysql_fetch_array($result)) {
    $playerIdSaved = $row["playerId"];
    if ($playerIdSaved == $player_id) {
      $tournamentAwards .= '<span style="cursor:help;" title="Team Standings - Best player"><img style="vertical-align:bottom;padding:0 0 0 2px;margin:0 0 0 2px;" src="'.$directory.'/gfx/teamLadderPlayer.gif" /></span>';
    }
   }

   // team ladder - team
   $sql = "SELECT playerId, playerId2 from $teamladdertable where type='team' ORDER BY timestamp DESC LIMIT 0, 1";
   $result = mysql_query($sql);
   while ($row = mysql_fetch_array($result)) {
    $playerIdSaved = $row["playerId"];
    $playerId2Saved = $row["playerId2"];
    if ($playerIdSaved == $player_id || $playerId2Saved == $player_id) {
      if ($playerIdSaved == $player_id) {
        $teammateId = $playerId2Saved; 
      } else {
        $teammateId = $playerIdSaved;
      }
      $teammate = getPlayerNameForId($teammateId);
      $tournamentAwards .= '<span style="cursor:help;" title="Team Standings - Best team with '.$teammate.'"><img style="vertical-align:bottom;padding:0 0 0 2px;margin:0 0 0 2px;" src="'.$directory.'/gfx/teamLadderTeam.gif" /></span>';
    }
   }
  
  // special awards
  $special_sql = "SELECT profileImage, titleText from $awardstable where playerId = '$player_id'";
  $special_result = mysql_query($special_sql);
  $specialHtml = "";
  while($special_row = mysql_fetch_array($special_result)) {
    $profileImage = $special_row['profileImage'];
    $titleText = $special_row['titleText'];
    $img = '<img style="vertical-align:bottom;padding:0 0 0 2px;margin:0 0 0 2px;" src="'.
      $directory.'/gfx/awards/'.$profileImage.'" />';
    $specialHtml .= '<span style="cursor:help;" title="'.$titleText.'">'.$img.'</span>';
  }
  $tournamentAwards .= $specialHtml;
  
  // donators
  $donate_sql = "SELECT id from $donationstable where name = '$name'";
  $donate_result = mysql_query($donate_sql);
  if (mysql_num_rows($donate_result)) {
    $img = '<img style="vertical-align:bottom;padding:0 0 0 2px;margin:0 0 0 2px;" src="'.
      $directory.'/gfx/awards/donator.gif" />';
    $tournamentAwards .= '<span style="cursor:help;" title="'.$name.' donated to '.$leaguename.', thank you!">'.$img.'</span>';
  }
  
  if (!empty($tournamentAwards)) {
    $awards .= "<p>".$tournamentAwards."</p>";
  }
  
  return $awards;
}
?>