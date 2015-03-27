<? 
// this will show the page footer for all pages.
// the current season start/end dates are pulled from weblm_seasons
// the last season winners are pulled from weblm_history
// the last logged in users are pulled from weblm_access_log
// the active players count is pulled from weblm_players  

require_once('variables.php');
require_once('variablesdb.php');
require_once('functions.php');

?>
	</td>
</tr>

<tr>
	<td colspan="3">
	<?
    $isSixserver = stristr($_SERVER['PHP_SELF'], "sixserver");
    $title_left = GetBottomLine($isSixserver);
		$title_right = $leaguename.' powered by <a href="https://github.com/IkeC/evo-league">evo-league</a>';
    
	?>
	<?= getOuterBoxTop($title_left, $title_right) ?>
		<table id="bottom" class="layouttable">
		<tr class="row_bottom">
		<td>
		<b>Last online</b> -
		<?php
		       $ago = time()-60*60*12;
           $sql = "SELECT user, accesstime, ip FROM weblm_log_access WHERE user <>'' AND user <>'Ike' ORDER BY id DESC limit 0,50";
		       $accessPlayers = array();
           $ips = array();
           $result = mysql_query($sql);
		       $bottomLine = "";
           while (($row = mysql_fetch_array($result)) && count($accessPlayers) < 8) {
		         $user = $row['user'];
             $ip = $row['ip'];
             if (!in_array($user,$accessPlayers)) {
               $accessPlayers[] = $user;
               if (count($accessPlayers) > 0) {
                $title = formatAgoSpanPlain($row["accesstime"]). " ago";
                if ($cookie_name == 'Ike') {
                  $title .= " (".$row['ip'].")";
                }
                if (count($accessPlayers) > 1) {
                  $bottomLine .= ", ";
                }
               } 
               $sql = "SELECT approved FROM $playerstable WHERE name='$user'"; 
               $playerresult = mysql_query($sql);
			         $playerrow = mysql_fetch_array($playerresult);
               $approved = $playerrow["approved"];
               $nameClass = colorNameClass($user, $approved);    
               $bottomLine .= '<span title="'.$title.'" '.$nameClass.'><a href="'.$directory.'/profile.php?name='.$user.'">'.$user.'</a></span>';
               if ($cookie_name == 'Ike') {
                  if (array_key_exists($ip, $ips)) {
                    $sql = "SELECT player_id FROM $playerstable WHERE name='".$user."'";
                    $idrow = mysql_fetch_array(mysql_query($sql));
                    $bottomLine .= '&nbsp;<a href="/Admin/access.php#'.$idrow[0].'"><img src="/gfx/exclamation.png" title="same as '.$ips[$ip].'" border="0" style="vertical-align:bottom"></a>';
                  } else {
                    $ips[$ip] = $user;
                  }
                }
             } 
		       }
		       echo $bottomLine;
		?></td>
		<?
		       $sql = "SELECT onlineUsers FROM six_stats";
		       $rowresult = mysql_query($sql);
		       $row2 = mysql_fetch_array($rowresult);
		       $onlineCount = $row2['onlineUsers'];
// echo "oc:".$onlineCount;
		       $total_sql = "SELECT COUNT(*) FROM $playerstable WHERE serial6<>'' AND approved='yes'";
					 $result = mysql_query($total_sql);
					 $row =  mysql_fetch_array($result);
					 $totalCount = $row[0];
		?>
		<td style="text-align:right;"><a href="/sixserver" title="The network server to play PES 6 online">Sixserver:</a> <b><?= $totalCount ?></b> players</span> (<?= $onlineCount ?> <a href="/sixserver/lobbies.php">online</a>)
		&nbsp;</td>
		</tr></table> 
<?
  echo getOuterBoxBottom();
  LogPerfTime($log, $startPHP, $visitorIP, $cookie_name, "bottom end");
  
  
  function GetBottomLine($isSixserver) {
    require('variables.php');
    require('variablesdb.php');
    
    $players = "";
    $type = "";
    if ($isSixserver) {
      $sql = "SELECT season FROM six_stats";
      $row = mysql_fetch_array(mysql_query($sql));
      $sixSeason = $row[0];
      $oldseason = $sixSeason-1;
      $type = "Sixserver";
      $sql = "SELECT wp.name as player_name FROM six_history sh ".
        "LEFT JOIN weblm_players wp ON sh.playerId=wp.player_id ".
        "where sh.season=". $oldseason ." AND sh.position=1";
        
    } else {
      $oldseason = $season-1;
      $type = "Ladder";
      $sql = "SELECT player_name from $historytable where season=". $oldseason ." AND position=1";
    }
    $result = mysql_query($sql);
    
    while ($row = mysql_fetch_array($result)) {
      $name = $row['player_name'];
      $sql = "select approved from $playerstable where name = '$name'";
      $playerresult = mysql_query($sql);
      $playerrow = mysql_fetch_array($playerresult);
      $approved = $playerrow["approved"];
      $nameClass = colorNameClass($name, $approved);
    }
    
    $players .= "$type Season $oldseason: <span $nameClass><a style='text-decoration: none;' href='$directory/profile.php?name=$name' title='view profile'>$name</a></span>";
    
    // team ladder - player
    $sql = "SELECT playerId from $teamladdertable where type='player' ORDER BY timestamp DESC LIMIT 0, 1";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
      $playerIdSaved = $row["playerId"];
      $playerName = getPlayerNameForId($playerIdSaved);
    }
    $players .= " - Team Standings leader: ".getPlayerLinkId($playerName, $playerIdSaved);
              
    // team ladder - team
    $sql = "SELECT playerId, playerId2 from $teamladdertable where type='team' ORDER BY timestamp DESC LIMIT 0, 1";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
      $playerIdSaved = $row["playerId"];
      $playerName = getPlayerNameForId($playerIdSaved);
      $playerId2Saved = $row["playerId2"];
      $player2Name = getPlayerNameForId($playerId2Saved);
    }
              
    $players .= " - Best Team: ".getPlayerLinkId($playerName, $playerIdSaved);
    $players .= '<span class="grey-small">&ndash;</span>';
    $players .= getPlayerLinkId($player2Name, $playerId2Saved);
    
     if ($oldseason > 0) {
      $title_left = '<span style="font-size:10px;color:#000000">'.
      "<a style='text-decoration:none;font-weight:bold' href='champions.php'>Champions</a>&nbsp;-&nbsp;".
      "<span style='font-size:10px;font-weight:normal;text-decoration: none;'>". $players.
      "</span></span>";
    }
    return $title_left;
}
?>
</td></tr></table>
</body>
</html>
