<?php
$page = "index";
$subpage = "disconnects";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');

$left = $subNavText.getRaquo().getSubNavigation($subpage, null);
$sql = "SELECT season FROM six_stats";
$sixSeason = mysql_fetch_array(mysql_query($sql))[0];

?>
<?= getOuterBoxTop($left, "") ?>
<table width="750"><tr><td>
<?
  $columnTitlesArray = array ('', 'Name', 'DC last week', 'DC last month', 'DC total', 'Games total', 'DC %');
  $boxTitle = "Sixserver Disconnects";
  ?>
  <?= getRankBoxTop($boxTitle, $columnTitlesArray) ?>
      <?

    $users = array();
        
    $warningsIds = GetActiveWarningsIds();
    $bansIds = GetActiveBansIds();

    $sql = "SELECT sms.dc, sms.minutes, sms.scoreHome, sms.scoreAway, sms.season, ".
      "sp1.user_id AS userIdHome, sp2.user_id AS userIdAway, ".
      "UNIX_TIMESTAMP( sms.updated ) AS updatedTS ".
      "FROM six_matches_status sms ".
      "LEFT JOIN six_profiles sp1 ON sp1.id = sms.profileHome ".
      "LEFT JOIN six_profiles sp2 ON sp2.id = sms.profileAway ".
      "WHERE sms.updated < date_sub( now( ) , INTERVAL 15 MINUTE) ".
      "AND sms.updated > date_sub( now( ) , INTERVAL 30 DAY) ".
      "AND sms.profileHome2=0 ".
      "AND sms.profileAway2=0 ".
      "AND sms.lobbyName <> 'Training'";
      
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
      $scoreHome = $row['scoreHome'];
      $scoreAway = $row['scoreAway'];
      $minutes = $row['minutes'];
      $updated = $row['updatedTS'];
      $season = $row['season'];
      $dcgame = $row['dc'];
      
      $dcHome = false;
      $dcAway = false;
      if ($dcgame == 1) {
        $dcHome = true;
      } elseif ($dcgame == 2) {
        $dcAway = true;
      }
      
      if ($scoreHome > $scoreAway) {
        $scoreLeft = $scoreHome;
        $scoreRight = $scoreAway;
        $userIdLoser = $row['userIdAway'];
        $dcLeft = $dcHome;
        $dcRight = $dcAway;
        
      } else {
        $scoreLeft = $scoreAway;
        $scoreRight = $scoreHome;
        $userIdLoser = $row['userIdHome'];
        $dcLeft = $dcAway;
        $dcRight = $dcHome;
      }
      
      if (IsFishySixserverGame($scoreLeft, $scoreRight, $minutes)) {
        
        $punish = false;
        if (($scoreLeft >= $scoreRight) && $dcRight) {
          $punish = true;
        } elseif (($scoreLeft == $scoreRight) && $dcLeft)  {
          $punish = true;
          $userIdLoser = $row['userIdAway'];
        }
          
        if ($punish) {
          if (array_key_exists($userIdLoser, $users)) {
            $users[$userIdLoser]['monthDC'] = $users[$userIdLoser]['monthDC'] + 1;
          } else {
            $users[$userIdLoser] = array('weekDC' => 0, 'monthDC' => 1, 'seasonDC' => 0, 'userId' => $userIdLoser);
          }
          
          if ((time() - $updated) < 60*60*24*7) {
            $users[$userIdLoser]['weekDC'] = $users[$userIdLoser]['weekDC'] + 1; 
          } 
          if ($sixSeason == $season) {
            $users[$userIdLoser]['seasonDC'] = $users[$userIdLoser]['seasonDC'] + 1;
          }
        }
      }
    }

    usort($users, function($a, $b) {
        return $b['weekDC'] - $a['weekDC'];
    });

		foreach ($users as $userId => $userArray) {
      $playerId = $userArray['userId'];
      $sql = "SELECT weblm_players.name, weblm_players.approved, weblm_players.nationality " .
        "FROM weblm_players " .
        "WHERE weblm_players.player_id=".$playerId;
      $result = mysql_query($sql);
      $row = mysql_fetch_array($result);
      
      $name = $row['name'];
      $nationality = $row['nationality'];
      $nameClass = colorNameClass($name, $row['approved']);
      if ($name == $cookie_name) {
        $rowclass = "row_active";
      } else {
        $rowclass = "row";
      }
      
      $DCTotal = GetSixserverDCHistoryPerPlayer($playerId)+$userArray['seasonDC'];
      
      $sixGamesTotal = GetSixserverGamesTotal($playerId)+$DCTotal;
      if ($sixGamesTotal <= 0) {
        $DCPercentageTotal = 0.000;
      } else {
        $DCPercentageTotal = $DCTotal / $sixGamesTotal * 100;
      }
      $DCPercentageDisplayTotal = formatDC($DCPercentageTotal, $sixGamesTotal);
      $DCInfo = $DCTotal." disconnects in ".$sixGamesTotal." games";
      
      $card = "";
      if (in_array($playerId, $bansIds)) {
        $card = $gfx_ban;
      } 
      elseif (in_array($playerId, $warningsIds)) {
        $card = $gfx_warn;
      }
      if (!empty($card)) {
        $card = '<img src="'.$directory.'/gfx/'.$card.'" style="vertical-align:middle;">';
      }
          ?>
        <tr class="<?= $rowclass ?>">
          <td width="20"><?= $card ?></td>
          <td align="left" height="35" <?= $nameClass ?>><img class="imgMargin" src="<?= $directory ?>/flags/<?= $nationality ?>.bmp" width="18" height="15" border="1"><a href="<?= $directory?>/profile.php?name=<?= $name ?>" title="view profile"><?= $name ?></a></td>
          <td width="100" align="right"><?= $userArray['weekDC'] ?></td>
          <td width="100" align="right"><?= $userArray['monthDC'] ?></td>
          <td width="100" align="right"><a href="<?= $directory ?>/sixserver/games.php?t=unfinished&amp;p=<?= $name ?>"><?= $DCTotal ?></a></td>
          <td width="100" align="right"><?= $sixGamesTotal ?></td>
          <td width="100" align="right"><span style="cursor:help;" title="<?= $DCInfo ?>"><?= $DCPercentageDisplayTotal ?></span></td>
        </tr>
  <? } ?>
  <?= getRankBoxBottom() ?>
  
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?php
	require ('../bottom.php');
?>

