<?
  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require_once($appRoot.'../log/KLogger.php');
  require_once($appRoot.'../variables.php');
  
  $logLogin = new KLogger('/var/www/yoursite/http/log/login/', KLogger::INFO);
  
  $isAdminFull = false;
  $isAdminBan = false;
  $isModFull = false;
  
  $logLogin->logInfo('MOD Login: '.$cookie_name);
  
  $sql = "SELECT player_id FROM weblm_players WHERE name='$cookie_name'";
  $logLogin->logInfo('MOD Login: sql='.$sql);
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $playerId = $row['player_id'];
    $sql2 = "SELECT * FROM weblm_admin WHERE player_id=$playerId";
    $logLogin->logInfo('MOD Login: sql2='.$sql2);
    $result2 = mysql_query($sql2);
    while ($row2 = mysql_fetch_array($result2)) {
      if ($row2['admin_full'] == 'yes') {
        $isAdminFull = true;
      }
      if ($row2['admin_ban'] == 'yes') {
        $isAdminBan = true;
      }
      if ($row2['mod_full'] == 'yes') {
        $isModFull = true;
      }
    }
  }
  
  $logLogin->logInfo("MOD Login: isAdminFull=".$isAdminFull." isAdminBan=".$isAdminBan." isModFull=".$isModFull);
?>
<a href="<?php echo"$directory"?>/Mod/index.php"><?php
if ($page == "index") {	echo"<font class='menu-active'>"; } ?>
&nbsp;&nbsp;Index<?php
if ($page == "index") {
	echo"</font>";
}?></a>
<? if ($isAdminFull || $isModFull) { ?>

<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />

<a href="<?php echo"$directory"?>/Mod/reportMini.php">
<?php if ($page =="reportMini") {echo"<font class='menu-active'>";}?>
Report Mini Tournament<?php if ($page =="reportMini") {echo"</font>";}?></a>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Mod/reportLeague.php">
<?php if ($page =="reportLeague") {echo"<font class='menu-active'>";}?>
Report League Game<?php if ($page =="reportLeague") {echo"</font>";}?></a>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Mod/uploadTournamentImage.php">
<?php if ($page =="uploadTournamentImage") {echo"<font class='menu-active'>";}?>
Upload Tournament Image<?php if ($page =="uploadTournamentImage") {echo"</font>";}?></a>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Mod/setupLeague.php">
<?php if ($page =="setupLeague") {echo"<font class='menu-active'>";}?>
Setup League<?php if ($page =="setupLeague") {echo"</font>";}?></a>
&nbsp;&nbsp;
<? } ?>