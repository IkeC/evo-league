<?php

// this will show links in the admin panel menu bar depending on the admin permissions

  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require_once($appRoot.'../log/KLogger.php');
  require_once($appRoot.'../variables.php');
  
  $logLogin = new KLogger('/var/www/yoursite/http/log/login/', KLogger::INFO);
  
  $isAdminFull = false;
  $isAdminBan = false;
  $isModFull = false;
  
  $logLogin->logInfo('ADMIN Login: '.$cookie_name);
  
  $sql = "SELECT player_id FROM weblm_players WHERE name='$cookie_name'";
  $logLogin->logInfo('ADMIN Login: sql='.$sql);
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $playerId = $row['player_id'];
    $sql2 = "SELECT * FROM weblm_admin WHERE player_id=$playerId";
    $logLogin->logInfo('ADMIN Login: sql2='.$sql2);
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
  
  $logLogin->logInfo("ADMIN Login: isAdminFull=".$isAdminFull." isAdminBan=".$isAdminBan." isModFull=".$isModFull);
?>
<div style="white-space:nowrap;">
  <div id="menuwrapper">
    <ul id="p7menubar"><li id="p7nobullet"><a href="<?php echo"$directory"?>/Admin/index.php"><?php
if ($page =="index") {	echo "<font class='menu-active'>"; } ?>
Index<?php
if ($page =="index") {
	echo"</font>";
}?></a></li>

<? if ($isAdminFull) { ?>
<li id="p7bullet">
<a href="<?php echo"$directory"?>/Admin/access.php">
<?php if ($page =="access") {echo"<font class='menu-active'>";}?>
Access<?php if ($page =="access") {echo"</font>";}?></a>
</li>
<? } ?>

<? if ($isAdminFull || $isAdminBan) { ?>
<li id="p7bullet">
<a href="<?php echo"$directory"?>/Admin/playerStatus.php">
<?php if ($page =="playerstatus") {echo"<font class='menu-active'>";}?>
Cards<?php if ($page =="playerstatus") {echo"</font>";}?></a>
<?= getSubNavMenuString("playerstatus", null); ?>
</li>
<? } ?>

<? if ($isAdminFull) { ?>
<li id="p7bullet">
<a href="<?php echo"$directory"?>/Admin/News/view.php">
 <?php if ($page =="view") { echo "<font class='menu-active'>";}?>
News<?php if ($page =="view") { echo"</font>";}?>
</a>
</li>
<? } ?>

<? if ($isAdminFull) { ?>
<li id="p7bullet">
<a href="<?php echo"$directory"?>/Admin/checkPoints.php">
<?php if ($page =="checkPoints") {echo"<font class='menu-active'>";}?>
Checks<?php if ($page =="checkPoints") {echo"</font>";}?></a>
<?= getSubNavMenuString("checks", null); ?>
</li>
<? } ?>

<? if ($isAdminFull) { 
?><li id="p7bullet"><a href="<?php echo"$directory"?>/Admin/approvePlayers.php">
<?php if ($page =="approvePlayers") {echo"<font class='menu-active'>";}?>
Approve<?php if ($page =="approvePlayers") {echo"</font>";}?></a>
</li>
<? } ?>

<? if ($isAdminFull) { ?>
<li id="p7bullet">
<a href="<?php echo"$directory"?>/Admin/newSeason.php">
<?php if ($page =="newSeason") {echo"<font class='menu-active'>";}?>
New Season<?php if ($page =="newSeason") {echo"</font>";}?></a>
</li>
<? } ?>

<? if ($isFullAdmin) { ?>
<li id="p7lastbullet">
<a href="<?php echo"$directory"?>/Admin/sendMassMail.php">
<?php if ($page =="sendMassMail") {echo"<font class='menu-active'>";}?>
Send Mass Mail<?php if ($page =="sendMassMail") {echo"</font>";}?></a>
</li>
<? } ?>
</ul><br class="clearit">
  </div>
</div>

<?
?>
