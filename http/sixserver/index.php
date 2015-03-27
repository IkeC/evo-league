<?php

$page = "index";
$subpage = "index";
require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');

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

$sql = "SELECT six_seasons.begindate, six_seasons.enddate, six_stats.season from six_seasons LEFT JOIN six_stats ON six_seasons.season=six_stats.season " .
  "WHERE six_seasons.season=six_stats.season";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
$begindate = $row['begindate'];
$enddate = $row['enddate'];
$sixSeason = $row['season'];

$left = $subNavText.getRaquo().getSubNavigation($subpage, null);
$right = '<span class="black-small">Sixserver Season '.$sixSeason.'&nbsp;('.$begindate.' - '.$enddate.')</span>';

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

?>

<?= getOuterBoxTop($left, $right) ?>

<!--
<link rel="stylesheet" type="text/css" href="/wtag/css/main-style.css" />
-->
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="/wtag/css/ie-style.css" />
<![endif]-->
<!--
<script type="text/javascript" src="/wtag/js/dom-drag.js"></script>
<script type="text/javascript" src="/wtag/js/scroll.js"></script>
<script type="text/javascript" src="/wtag/js/ajax.js"></script>
-->
<style>
    /* Prevents slides from flashing */
    .container {
      margin-top:6px;
      margin-left:4px;
      width: 481;
    }
   	
   	#slides {
      display: none
    }

    #slides .slidesjs-navigation {
      margin-top:3px;
    }

    #slides .slidesjs-previous {
      margin-right: 5px;
      float: left;
    }

    #slides .slidesjs-next {
      margin-right: 5px;
      float: left;
    }

    .slidesjs-pagination {
      margin: 5px 4px 0 0;
      float: right;
      list-style: none;
    }

    .slidesjs-pagination li {
      float: left;
      margin: 0 1px;
    }

    .slidesjs-pagination li a {
      display: block;
      width: 13px;
      height: 0;
      padding-top: 13px;
      background-image: url(/gfx/pagination.png);
      background-position: 0 0;
      float: left;
      overflow: hidden;
    }

    .slidesjs-pagination li a.active,
    .slidesjs-pagination li a:hover.active {
      background-position: 0 -13px
    }

    .slidesjs-pagination li a:hover {
      background-position: 0 -26px
    }

    #slides a:link,
    #slides a:visited {
      color: #333
    }

    #slides a:hover,
    #slides a:active {
      color: #9e2020
    }

    .navbar {
      overflow: hidden
    }
  </style>
  <!-- End SlidesJS Optional-->


  <script src="http://code.jquery.com/jquery-latest.min.js"></script>
  <script src="/js/jquery.slides.min.js"></script>

  <script>
    $(function(){
      $("#slides").slidesjs({
        width: 481,
        height: 193,
        navigation: false
      });
    });
  </script>

<table width="100%">
	<tr>
		<td width="50%" valign="top"><!-- games --> <?

		$gamesLinksArray[] = array ('./games.php', 'show_games', 'show all games');
		echo getBoxTopImg("Sixserver Games", $boxheight, false, $gamesLinksArray, $box1Img, $box1Align);
		?>
		<table class="gamesbox">
		<?

		$sql = "SELECT sm.id, UNIX_TIMESTAMP(sm.played_on) as played_on, sm.score_home, sm.score_away, ".
            "st1.ladderTeamId as ladderTeamHome, st2.ladderTeamId as ladderTeamAway ". 
            "FROM six_matches sm ".
            "LEFT JOIN six_patches sp1 ON sm.hashHome=sp1.hash ".
            "LEFT JOIN six_patches sp2 ON sm.hashAway=sp2.hash ".
            "LEFT JOIN six_teams st1 ON (st1.sixTeamId=sm.team_id_home AND st1.patchId=sp1.id) ".
            "LEFT JOIN six_teams st2 ON (st2.sixTeamId=sm.team_id_away AND st2.patchId=sp2.id) ".
            "ORDER BY sm.id DESC LIMIT 0, 10";
            
//    $log = new KLogger('/var/www/yoursite/http/log/general/', KLogger::INFO);	
//    $log->logInfo('xx sql='.$sql);

		$result = mysql_query($sql);
		$compteur = 1;
		while (10 >= $compteur) {
			$row = mysql_fetch_array($result);
			if (!empty ($row)) {
				$id = $row['id'];
				$gamedate = formatTime($row['played_on']);
				$score_home = $row['score_home'];
				$score_away = $row['score_away'];
        $ladderTeamHome = $row['ladderTeamHome'];
        $ladderTeamAway = $row['ladderTeamAway'];

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
				
        $player_home2 = "";
        $nationalityHome2 = "";
        $profileNameHome2 = "";

        $player_home3 = "";
        $nationalityHome3 = "";
        $profileNameHome3 = "";

        $row2 = mysql_fetch_array($result2);
				if (!empty($row2)) {
					$player_home2 = $row2['name'];
					$nationalityHome2 = $row2['nationality'];
					$profileNameHome2 = $row2['profileName'];
          $row2 = mysql_fetch_array($result2);
          if (!empty($row2)) {
            $player_home3 = $row2['name'];
            $nationalityHome3 = $row2['nationality'];
            $profileNameHome3 = $row2['profileName'];
          } 
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
				
        $player_away2 = "";
        $nationalityAway2 = "";
        $profileNameAway2 = "";

        $player_away3 = "";
        $nationalityAway3 = "";
        $profileNameAway3 = "";

				$row2 = mysql_fetch_array($result2);
				if (!empty($row2)) {
					$player_away2 = $row2['name'];
					$nationalityAway2 = $row2['nationality'];
					$profileNameAway2 = $row2['profileName'];
          $row2 = mysql_fetch_array($result2);
          if (!empty($row2)) {
            $player_away3 = $row2['name'];
            $nationalityAway3 = $row2['nationality'];
            $profileNameAway3 = $row2['profileName'];
          } 
				} 
        
				$rowheight = 19;
				$idtooltip = "style='cursor:help;' title='ID #$id'";
				
				if ($score_home < $score_away) {
					$scoreLeft = $score_away;
					$scoreRight = $score_home;
					$nationalityLeft = $nationalityAway;
					$nationalityRight = $nationalityHome;
          $ladderTeamLeft = $ladderTeamAway;
          $ladderTeamRight = $ladderTeamHome;
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

					$nationalityLeft3 = $nationalityAway3;
					$nationalityRight3 = $nationalityHome3;
          $profileNameLeft3 = $profileNameAway3;
					$profileNameRight3 = $profileNameHome3;
					$playerLeft3 = $player_away3;
					$playerRight3 = $player_home3;
        } else {
					$scoreLeft = $score_home;
					$scoreRight = $score_away;
					$nationalityLeft = $nationalityHome;
					$nationalityRight = $nationalityAway;
          $ladderTeamLeft = $ladderTeamHome;
          $ladderTeamRight = $ladderTeamAway;
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

          $nationalityLeft3 = $nationalityHome3;
					$nationalityRight3 = $nationalityAway3;
					$profileNameLeft3 = $profileNameHome3;
					$profileNameRight3 = $profileNameAway3;
					$playerLeft3 = $player_home3;
					$playerRight3 = $player_away3;
				}

				if (empty($playerLeft2)) {
					$winnerdisplay = '<span style="cursor:help" title="server profile" class="grey-small">('.$profileNameLeft.')</span>&nbsp<span><a href="'.$directory.'/profile.php?name='.$playerLeft.'">'.$playerLeft.'</a></span>';
				} else {
					$winnerdisplay = '<span><a href="'.$directory.'/profile.php?name='.$playerLeft.'" title="server profile: '.$profileNameLeft.'">'.$playerLeft.'</a></span>'.
						'<span class="grey-small">&ndash;</span>'.
						'<span><a href="'.$directory.'/profile.php?name='.$playerLeft2.'" title="server profile: '.$profileNameLeft2.'">'.$playerLeft2.'</a></span>';
          if (!empty($playerLeft3)) {
            $winnerdisplay .= '<span class="grey-small">&ndash;</span>'.
              '<span><a href="'.$directory.'/profile.php?name='.$playerLeft3.'" title="server profile: '.$profileNameLeft3.'">'.$playerLeft3.'</a></span>';
          }
        }
				if (empty($playerRight2)) {
					$loserdisplay = '<span><a href="'.$directory.'/profile.php?name='.$playerRight.'">'.$playerRight.'</a></span>'.
						'&nbsp;<span style="cursor:help" title="server profile" class="grey-small">('.$profileNameRight.')</span>';
				} else {
					$loserdisplay = '<span><a href="'.$directory.'/profile.php?name='.$playerRight.'" title="server profile: '.$profileNameRight.'">'.$playerRight.'</a></span>'.
						'<span class="grey-small">&ndash;</span>'.
						'<span><a href="'.$directory.'/profile.php?name='.$playerRight2.'" title="server profile: '.$profileNameRight2.'">'.$playerRight2.'</a></span>';
            if (!empty($playerRight3)) {
              $loserdisplay .= '<span class="grey-small">&ndash;</span>'.
                '<span><a href="'.$directory.'/profile.php?name='.$playerRight3.'" title="server profile: '.$profileNameRight3.'">'.$playerRight3.'</a></span>';
            }
				}
				
        if (is_null($ladderTeamLeft)) {
          $flagLeft = '<img title="'.$nationalityLeft.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityLeft.'.bmp" width="18" height="15" border="1">';
        } else {
          $flagLeft = getImgForTeam($ladderTeamLeft);
        }
        
        if (is_null($ladderTeamRight)) {
          $flagRight = '<img title="'.$nationalityRight.'" style="opacity:0.4;" src="'.$directory.'/flags/'.$nationalityRight.'.bmp" width="18" height="15" border="1">';
        } else {
          $flagRight = getImgForTeam($ladderTeamRight);
        }
        
				$rowspan = "";

				?>
			<tr style='height:<?= $rowheight ?>px;'>

				<td nowrap style="padding-right: 5px;"><?= getImgForVersion('H') ?></td>
				<td <?= $idtooltip ?> nowrap style="padding-right: 15px;"><span class="darkgrey-small"><?= $gamedate ?></span></td>
				<td nowrap align="right"><?= $winnerdisplay ?></td>
				<td><?= $flagLeft ?></td>
				<td align="right" class="rightalign_gamesbox"><b><?= $scoreLeft ?></b></td>
				<td>-</td>
				<td><b><?= $scoreRight ?></b></td>
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

		$boxTitle = "Sixserver Standings";
		echo getBoxTopImg($boxTitle, $boxheight, false, $standingsLinksArray, $box2Img, $box2Align);
		?>
		
		<table>
		
		<?php

		$sql = "SELECT six_profiles.points, six_profiles.name as profileName, six_profiles.id AS profileId, six_profiles.rank, " .
			"weblm_players.name AS playerName, weblm_players.nationality, weblm_players.approved FROM six_profiles " .
			"LEFT JOIN weblm_players ON weblm_players.player_id=six_profiles.user_id " .
      "WHERE weblm_players.approved='yes' ".
			"ORDER BY six_profiles.points DESC, playerName ASC LIMIT 0,10";

		$result = mysql_query($sql);
		$num = mysql_num_rows($result);
		$compteur = 1;
		$col = "color:#555555";
		$oldPoints = -1;
		while (10 >= $compteur) {
			$row = mysql_fetch_array($result);

			if (!empty ($row["nationality"])) {
				$nationality = $row["nationality"];
			} else {
				$nationality = 'No country';
			}
			
			$name = $row['playerName'];
			$nameClass = colorNameClass($name, $row['approved']);
			$points = $row['points'];
			$profileId = $row['profileId'];
			$wins = GetSixserverWins($profileId);
			$losses = GetSixserverLosses($profileId);
			$draws = GetSixserverDraws($profileId);
			
			$pos = "";
			if ($oldPoints != $points) {
				$pos = $compteur;
				$oldPoints = $points;
			}
			
			echo "<tr style='height:19px;'>";
			if (!empty ($row)) {
				echo "<td style='text-align:right;cursor:hand;'>".$pos.".</td>";
				echo "<td><img src='$directory/flags/$nationality.bmp' width='18' height='15' border='1'></td>";
				echo "<td width='180' $nameClass style='white-space:nowrap;'><a href='$directory/profile.php?name=$name' title='view profile'>$name</a>&nbsp;&nbsp;<span style='cursor:help;' class='darkgrey-small' title='server profile'>(".$row['profileName'].")</span></td>";
				echo "<td align=\"right\" width='80'>";
				echo "<b>".$points."</b>&nbsp;&nbsp;<span style='$col'>points</span>&nbsp;&nbsp;";
				echo "</td>";
				echo "<td align=\"right\"><b>".$wins."</b><span style='$col'>&nbsp;W</span><td>";
				echo "<td align=\"right\"><b>".$draws."</b><span style='$col'>&nbsp;D</span><td>";
				echo "<td align=\"right\"><b>".$losses."</b><span style='$col'>&nbsp;L</span></td>";

			} else {
				echo '<td colspan="6"><span class="darkgrey-small">(no player)</span></td>';
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
     <div id="newsbox" class="newsbox">
	    <?

				
		$newsLinksArray[] = array ('/news.php', 'show_news', 'show all news');
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
		</td>
		
		<td valign="top">				
    <!-- joins -->
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
						<? echo getBoxBottom() ?>
	</tr>
</table>
		<?
		$rssgif = '<img src="/gfx/rss.gif" style="vertical-align: middle" border="0">';
		$sitemapgif = '<img src="/gfx/sitemap.gif" style="vertical-align: middle" border="0">';
		$shopgif = '<img src="/gfx/shirt.gif" style="vertical-align: middle" border="0">';
		$fbimg = '<img src="/gfx/facebook.jpg" style="vertical-align: middle" border="0">';
		$linksBottomLeft = '<a style="vertical-align: middle" href="/sitemap.php">'.$sitemapgif.'&nbsp;Sitemap</a>&nbsp;&nbsp;';
		$linksBottomLeft .= '<a style="vertical-align: middle" target="_new" href="http://www.facebook.com/'.$leaguename.'">'.$fbimg.'&nbsp;Facebook</a>&nbsp;&nbsp;';
		// $linksBottomRight .= '<a style="vertical-align: middle" href="/podcast/">'.$rssgif.' Podcast</a>&nbsp;';
		?>
		<?= getOuterBoxBottomLinks($linksBottomLeft, $linksBottomRight) ?>

		<?php

		require ('../bottom.php');
?>

