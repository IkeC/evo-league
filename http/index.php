<?php


// the index page showing the last 10 games, top 10 players from standings,
// the latest news entry and the last 10 players that joined and were approved

session_start();
$token = md5(uniqid(rand(), true));
$_SESSION['token'] = $token;

$page = "home";
$subpage = "";
require ('variables.php');
require ('variablesdb.php');
require ('functions.php');
require ('top.php');

$boxheight = "225";
$rowspan2 = 'rowspan="2"';

$box1Img = "8.jpg";
$box1Align = "left bottom";
$box2Img = "2.jpg";
$box2Align = "left bottom";
$box3Img = "5.jpg";
$box3Align = "left center";
$box4Img = "9.jpg";
$box4Align = "left center";

$sql = "SELECT begindate, enddate from $seasonstable where season = '$season'";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$begindate = $row['begindate'];
$enddate = $row['enddate'];

$timezone = date("T");
if (strlen($timezone) > 4) {
	$timezone = "";
}

$left = 'Welcome to '.$leaguename.'!'.'&nbsp;&nbsp;'.'<span class="grey-small" style="font-weight:normal">evo time is '.date("m/d h:i a").' '.$timezone.'</span>';
$right = '<span class="black-small">Ladder Season '.$season.'&nbsp;('.$begindate.' - '.$enddate.')</span>';

$sql = "SELECT * FROM $newstable ORDER BY news_id DESC LIMIT 0, 1";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$news = $row["news"];
$news = nl2br($news);
$news = SmileyConvert($news, $directory);
$date = $row["date"];
$title = $row["title"];
$user = $row["user"];
$parsedTime = strtotime($date);

/*
if ((time() - $parsedTime) < 60 * 60 * 24 * 3) {
	$isNew = true;
} else {
	$isNew = false;
}*/
$isNew = true;

?>

<?= getOuterBoxTop($left, $right) ?>

<!-- <link rel="stylesheet" type="text/css" href="/wtag/css/main-style.css" /> -->
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="wtag/css/ie-style.css" />
<![endif]-->
<!--
<script type="text/javascript" src="/wtag/js/dom-drag.js"></script>
<script type="text/javascript" src="/wtag/js/scroll.js"></script>
<script type="text/javascript" src="/wtag/js/ajax.js"></script>
-->
<table width="100%">
	<tr>
		<td width="50%" valign="top"><!-- games --> <?

		$gamesLinksArray[] = array ('./games.php', 'show_games', 'show all games');
		echo getBoxTopImg("Games", $boxheight, false, $gamesLinksArray, $box1Img, $box1Align);
		?>
		<table class="gamesbox">
		<?

		$sql = "SELECT * FROM $gamestable where deleted = 'no' ORDER BY game_id DESC LIMIT 0, 10";
		$result = mysql_query($sql);
		$compteur = 1;

		$rankArrayPes4 = getRankingArray('A');
		$rankArrayPes5 = getRankingArray('D');

		while (10 >= $compteur) {
			$row = mysql_fetch_array($result);
			$id = $row['game_id'];
			$gamedate = formatTime($row['date']);
			$comment = $row['comment'];
			$winpoints = $row['winpoints'];
			$losepoints = $row['losepoints'];
			$losepoints2 = $row['losepoints2'];
			$host = $row['host'];
			$winnerhost = ($host == "W");
			$loserhost = ($host == "L");
			$version = $row['version'];
			if (stristr($versions_pes4, $version)) {
				$rankArray = $rankArrayPes4;
			} else {
				$rankArray = $rankArrayPes5;
			}
			$ladder = getLadderForVersion($version);
			$winnerteam = $row['winnerteam'];
			$loserteam = $row['loserteam'];

			if (!empty ($comment)) {
				$comment = escapeComment($comment);
				$tooltip = "style='cursor:help;' title=\"$comment\"";
			} else {
				$tooltip = "style='cursor:default;'";
			}
			$rowheight = 19;
			if (!empty ($row)) {
				$winner = $row['winner'];
				$winner2 = $row['winner2'];
				$loser = $row['loser'];
				$loser2 = $row['loser2'];

				$teamBonus = $row['teamBonus'];
				if ($teamBonus > 0) {
					$teamBonusDisplay = " (+".$teamBonus." for team)";
				} else {
					$teamBonusDisplay = "";
				}
				$wintooltip = "title='wins $winpoints points".$teamBonusDisplay."'";
				$losetooltip = "title='loses $losepoints points'";
				$idtooltip = "style='cursor:help;' title='ID #$id'";
				
				if (isset($rankArray[$winner])) {
					$winnerrank = $rankArray[$winner];
				} else {
					$winnerrank = "-";
				}
				if (isset($rankArray[$loser])) {
					$loserrank = $rankArray[$loser];
				} else {
					$loserrank = "-";
				}

				$winnerdisplay = '<span style="cursor:help" title="'.$ladder.' ladder position" class="grey-small">('.$winnerrank.')</span>&nbsp'.
			"<span><a '.$wintooltip.' href='$directory/profile.php?name=$winner'>$winner</a></span>";

				$loserdisplay = "<span><a ".$losetooltip." href='$directory/profile.php?name=$loser'>$loser</a></span>".
			'&nbsp;<span style="cursor:help" title="'.$ladder.' ladder position" class="grey-small">('.$loserrank.')</span>';		

				if (!empty($winner2) || !empty($loser2)) {
					if (!empty($winner2)) {
						$winnerdisplay = '<span><a '.$wintooltip.' href="'.$directory.'/profile.php?name='.$winner.'">'.$winner.'</a>'.
				'<span class="grey-small">&ndash;</span>'.
				'<a '.$wintooltip.' href="'.$directory.'/profile.php?name='.$winner2.'">'.$winner2.'</a></span>';
					}
					if (!empty($loser2)) {
						$losetooltip2 = "title='loses $losepoints2 points'";
						$loserdisplay = '<span><a '.$losetooltip.' href="'.$directory.'/profile.php?name='.$loser.'">'.$loser.'</a>'.
				'<span class="grey-small">&ndash;</span>'.
				'<a '.$losetooltip2.' href="'.$directory.'/profile.php?name='.$loser2.'">'.$loser2.'</a></span>';
					}
				} else {
					$rowspan = "";
				}
        
        if ($winnerteam > 0) {
          $flagLeft = getImgForTeam($winnerteam);
        } else {
          $sql2 = "SELECT nationality FROM $playerstable where name='$winner'";
          $row2 = mysql_fetch_array(mysql_query($sql2));
          $nationalityLeft = $row2['nationality'];
          $flagLeft = '<img title="'.$nationalityLeft.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft.'.bmp" width="18" height="15" border="1">';
        }
        
        if ($loserteam > 0) {
          $flagRight = getImgForTeam($loserteam);
        } else {
          $sql2 = "SELECT nationality FROM $playerstable where name='$loser'";
          $row2 = mysql_fetch_array(mysql_query($sql2));
          $nationalityRight = $row2['nationality'];
          $flagRight = '<img title="'.$nationalityRight.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight.'.bmp" width="18" height="15" border="1">';
        }

				?>
			<tr style='height:<?= $rowheight ?>px;'>

				<td <?= $tooltip ?> nowrap style="padding-right: 5px;"><?= getImgForVersion($version) ?></td>
				<td <?= $idtooltip ?> nowrap style="padding-right: 15px;"><span
					class="darkgrey-small"><?= $gamedate ?></span></td>
				<td <?= $wintooltip ?> nowrap align="right"><?= $winnerdisplay ?></td>
				<td><?= $flagLeft ?></td>
				<td align="right" <?= $tooltip ?> class="rightalign_gamesbox"><b><?= $row['winnerresult'] ?></b></td>
				<td <?= $tooltip ?>>-</td>
				<td <?= $tooltip ?>><b><?= $row["loserresult"]; ?></b></td>

				<td><?= $flagRight ?></td>
				<td nowrap><?= $loserdisplay ?></td>
			</tr>

			<? } else { ?>
			<tr style='height:<?= $rowheight ?>px;'>
				<td colspan="6">(no game)</td>
			</tr>
			<? } ?>

			<?
			$compteur ++;
}
?>
		</table>
		<? echo getBoxBottom() ?></td>
		
		<td valign="top"><!-- standings --> <?
    
		$standingsLinksArray[] = array ('./standings.php', 'show_standings', 'show complete standings');

		// if (!stristr(getSupportedVersions(), $cookie_version)) {
		$showVersion = "H";
		// } else {
		// 	$showVersion = $cookie_version;
		// }
		$boxTitle = "Single Standings";
		echo getBoxTopImg($boxTitle, $boxheight, false, $standingsLinksArray, $box2Img, $box2Align);
		?>
		<table>
		<?php

		$pointsField = getPointsFieldForVersion($showVersion);
		$winsField = getWinsFieldForVersion($showVersion);
		$lossesField = getLossesFieldForVersion($showVersion);
		$gamesField = getGamesFieldForVersion($showVersion);

		$sortby = $pointsField." DESC, percentage DESC, $lossesField ASC";
		$sql = "SELECT *, $winsField/$gamesField as percentage FROM $playerstable WHERE $gamesField >= 1 AND approved = 'yes' ORDER BY $sortby, $gamesField ASC LIMIT 0, 10";
		$result = mysql_query($sql);
		$num = mysql_num_rows($result);
		$compteur = 1;
		$col = "color:#555555";
		while (10 >= $compteur) {
			$row = mysql_fetch_array($result);
			if (!empty ($row["nationality"])) {
				$nationality = $row["nationality"];
			} else {
				$nationality = 'No country';
			}
			if ($row['streakwins'] > 0) {
				$streak = $row['streakwins'];
			} else
			if ($row['streaklosses'] > 0) {
				$streak = - $row['streaklosses'];
			} else {
				$streak = 0;
			}
			$name = $row['name'];
			$nameClass = colorNameClass($name, $row['approved']);

			echo "<tr style='height:19px;'>";
			if (!empty ($row)) {
				echo "<td style='text-align:right;cursor:hand;'>".$compteur.".</td>";
				echo "<td><img src='$directory/flags/$nationality.bmp' width='18' height='15' border='1' title='streak: $streak'></td>";
				echo "<td width='35%' $nameClass style='white-space:nowrap;'><a href='$directory/profile.php?name=$name' title='view profile'>$name</a>&nbsp;&nbsp;</td>";
				echo "<td style=\"white-space:nowrap;\" align=\"right\" width='80'>";
				echo "<b>".$row[$pointsField]."</b>&nbsp;&nbsp;<span style='$col'>points</span>&nbsp;&nbsp;";
				echo "</td>";
				echo "<td style=\"white-space:nowrap;\" align=\"right\"><b>".$row[$winsField]."</b><span style='$col'>&nbsp;W</span><td>";
				echo "<td style=\"white-space:nowrap;\" align=\"right\"><b>".$row["draws"]."</b><span style='$col'>&nbsp;D</span><td>";
				echo "<td style=\"white-space:nowrap;\" align=\"right\"><b>".$row[$lossesField]."</b><span style='$col'>&nbsp;L</span></td>";

			} else {
				echo '<td colspan="6"><span class="darkgrey-small">(no player)</td></td>';
			}
			echo "</tr>";

			$compteur++;
		}
		?>
		</table>
		<? echo getBoxBottom() ?></td>
	</tr>
	<tr>
    
    <td width="50%" valign="top">
    <? 
    $hasSixserverGamesToEdit = false;
    if ($loggedIn) {
      $sql = "SELECT COUNT(*) FROM $gamestable ".
        "WHERE sixGameId IS NOT NULL ".
        "AND edited=0 ".
        "AND deleted=0 ".
        "AND (winner='$cookie_name' OR loser='$cookie_name' OR winner2='cookie_name' OR loser2='$cookie_name') ".
        "AND season=".$season." ".
        "AND (winnerteam=0 OR loserteam=0)";
      $row = mysql_fetch_array(mysql_query($sql));
      $hasSixserverGamesToEdit = $row[0] > 0;
    }
    if ($hasSixserverGamesToEdit) {
    	include "./sixgamesForLadder.php";
    } else {
      $sql = "SELECT sm.id, sm.hashHome, spu.hash ".
        "FROM six_matches sm ".
        "LEFT JOIN six_patches sp1 ON sp1.hash = sm.hashHome ".
        "LEFT JOIN six_matches_played smp on smp.match_id=sm.id ".
        "LEFT JOIN six_profiles sp ON sp.id=smp.profile_id ".
        "LEFT JOIN weblm_players wp ON wp.player_id=sp.user_id ".
        "LEFT JOIN six_patches_unknown spu ON (spu.userId=sp.user_id AND sm.hashHome=spu.hash) ".
        "WHERE sm.hashHome = sm.HashAway ".
        "AND sm.hashHome <> '' ".
        "AND sp1.name IS NULL ".
        "AND spu.hash IS NULL ".
        "AND wp.name='".$cookie_name."' ".
        "ORDER BY sm.id DESC ".
        "LIMIT 0,1";
        
      if ($row = mysql_fetch_array(mysql_query($sql))) {
        $spMatchId = $row['id'];
        $spHash = $row['hashHome'];
        include "./sixpatch.php";
      } else {
        // $logRep = new KLogger('/var/www/yoursite/http/log/sixgamesDirect/', KLogger::INFO);	
        
        $sql = "SELECT
smp.match_id, st1.ladderTeamId AS ladderTeamIdHome, st2.ladderTeamId As ladderTeamIdAway, 
wt1.name AS ladderTeamNameHome, wt2.name AS ladderTeamNameAway, 
sm.score_home, sm.score_away, sm.team_id_home, sm.team_id_away, UNIX_TIMESTAMP(sm.played_on) as played_on, 
sp1.id as patchId
FROM six_matches_played smp 
LEFT JOIN six_matches sm ON sm.id=smp.match_id 
LEFT JOIN six_profiles sp ON sp.id=smp.profile_id
LEFT JOIN weblm_players wp ON wp.player_id=sp.user_id
LEFT JOIN six_patches sp1 ON sm.hashHome=sp1.hash
LEFT JOIN six_patches sp2 ON sm.hashAway=sp2.hash
LEFT JOIN six_teams st1 ON (st1.patchId=sp1.id AND st1.sixTeamId=sm.team_id_home) 
LEFT JOIN weblm_teams wt1 ON wt1.id=st1.ladderTeamId 
LEFT JOIN six_teams st2 ON (st2.patchId=sp2.id AND st2.sixTeamId=sm.team_id_away) 
LEFT JOIN weblm_teams wt2 ON wt2.id=st2.ladderTeamId 
WHERE wp.name='$cookie_name'
AND (st1.ladderTeamId IS NULL OR st2.ladderTeamId IS NULL) 
AND sm.reported=0
AND sm.edited=0 
AND sp1.hash=sp2.hash 
AND sp1.hash IS NOT NULL 
ORDER BY sm.id DESC";
        
        $result = mysql_query($sql);
        // $logRep->logInfo('sql='.$sql.' mysql_num_rows='.mysql_num_rows($result));
        if (mysql_num_rows($result) > 0) {
          include "./sixgamesDirect.php";
        } else {
    if ($isNew) {
    ?>
    
    <div id="newsbox" class="newsbox">
	    <?

				
		$newsLinksArray[] = array ('./news.php', 'show_news', 'show all news');
		$boxtitle = 'News';
		echo getBoxTopImg($boxtitle, $boxheight, $isNew, $newsLinksArray, $box3Img, $box3Align);
		
		?>
		<table class="layouttable">
			<tr class="row_newsheader">
				<td><?

				echo "<b>$date - $title</b>&nbsp;&nbsp;<span class='grey-small'>(posted by $user)</span>";
				?></td>
			</tr>
			<tr>
				<td><?php echo"$news" ?></td>
			</tr>
		</table>
		<? echo getBoxBottom() ?>
		</div>
    
    <?
    } else {
    ?>
      <div id="shoutbox" class="shoutbox">
      <?
      echo getBoxTopImg("Chatroom", $boxheight, false, null, $box3Img, $box3Align);
      include "./chatbox.php";
      echo getBoxBottom(); 
      ?>
      </div>
    <?  
          }
        } 
      } 
    } 
    ?>
	</td>
	
	<td valign="top">				<!-- joins -->
						<? 
						$joinLinksArray[] = array('./players.php', 'show_players', 'show all players');
						echo getBoxTop("New Players", $boxheight, false, $joinLinksArray); ?>
							  <table>
								<?
								   $sql="SELECT * FROM $playerstable where approved ='yes' ORDER BY joindate DESC LIMIT 0, 10";
								   $result = mysql_query($sql);
								   $compteur = 1;
								   while (10 >= $compteur) {
								     $row = mysql_fetch_array($result);
								
								     $name = $row["name"];
								     $alias = $row["alias"];
								     $platform = $row["platform"];
								     $approved = $row["approved"];
								     $nameClass = colorNameClass($name, $approved, $platform);
								     
								     if (!empty($row["nationality"])) {
								         $nationality = $row["nationality"];
								     } else {
								         $nationality = 'No country';
								     }
								     
								     $joined = formatDate($row['joindate']);
								     
								     echo "<tr>";
								     echo "<td nowrap><span class='size11'>".$joined."</span>&nbsp;</td>";
								     echo "<td nowrap><img src='$directory/flags/$nationality.bmp' border='1' title='$nationality'></td>";
								     if (!empty($alias)) {
								     	$showalias = " (aka $alias)";
								     } 
								     else {
								     	$showalias = "";
								     }
								     echo "<td $nameClass>&nbsp;<a href='$directory/profile.php?name=$name' title='view profile'>$name$showalias</a></td>";
								     echo "</tr>";
								
								     $compteur++;
								   }
								
								?>
								</table> 
						<? echo getBoxBottom() ?></td>
	</tr>
</table>
		<?
		$rssgif = '<img src="/gfx/rss.gif" style="vertical-align: middle" border="0">';
		$sitemapgif = '<img src="/gfx/sitemap.gif" style="vertical-align: middle" border="0">';
		$shopgif = '<img src="/gfx/shirt.gif" style="vertical-align: middle" border="0">';
		$fbimg = '<img src="/gfx/facebook.jpg" style="vertical-align: middle" border="0">';
		$linksBottomLeft = '<a style="vertical-align: middle" href="/sitemap.php">'.$sitemapgif.'&nbsp;Sitemap</a>&nbsp;&nbsp;';
		$linksBottomLeft .= '<a style="vertical-align: middle" target="_new" href="http://www.facebook.com/"'.$leaguename.'>'.$fbimg.'&nbsp;Facebook</a>&nbsp;&nbsp;';
		// $linksBottomRight .= '<a style="vertical-align: middle" href="/podcast/">'.$rssgif.' Podcast</a>&nbsp;';
		?>
		<?= getOuterBoxBottomLinks($linksBottomLeft, $linksBottomRight) ?>

		<?php
		require ('bottom.php');

?>

