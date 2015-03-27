<?php

// this will show links in the admin panel menu bar depending on the admin permissions

$isFullAdmin = is_null($admin_full) ? GetInfo($idcontrol, 'admin_full') == 'yes' : $admin_full == 'yes';
$isNewsAdmin = is_null($admin_news) ? GetInfo($idcontrol, 'admin_news') == 'yes' : $admin_news == 'yes';
$isApproveAdmin = is_null($admin_approve) ? GetInfo($idcontrol, 'admin_approve') == 'yes' : $admin_approve == 'yes';
$isSeasonAdmin = is_null($admin_season) ? GetInfo($idcontrol, 'admin_season') == 'yes' : $admin_season == 'yes';
$isPlayerAdmin = is_null($admin_player) ? GetInfo($idcontrol, 'admin_player') == 'yes' : $admin_player == 'yes';
?>
<a href="<?php echo"$directory"?>/Admin/index.php"><?php
if ($page =="index") {	echo"<font class='menu-active'>"; } ?>
Admin Index<?php
if ($page =="index") {
	echo"</font>";
}?></a>

<? if ($isFullAdmin || $isApproveAdmin) { ?>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Admin/approvePlayers.php">
<?php if ($page =="approvePlayers") {echo"<font class='menu-active'>";}?>
Approve Players<?php if ($page =="approvePlayers") {echo"</font>";}?></a>
<? } ?>

<? if ($isFullAdmin || $isPlayerAdmin) { ?>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Admin/playerStatus.php">
<?php if ($page =="playerStatus") {echo"<font class='menu-active'>";}?>
Player Status<?php if ($page =="playerStatus") {echo"</font>";}?></a>
<? } ?>

<? if ($isFullAdmin || $isNewsAdmin) { ?>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Admin/News/view.php">
 <?php if ($page =="view") { echo "<font class='menu-active'>";}?>
News Admin<?php if ($page =="view") { echo"</font>";}?>
</a>
<? } ?>

<? if ($isFullAdmin || $isNewsAdmin) { ?>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Admin/News/post.php">
<?php if ($page =="post") {echo"<font class='menu-active'>";}?>
Post News<?php if ($page =="post") {echo"</font>";}?></a>
<? } ?>

<? if ($isFullAdmin) { ?>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Admin/deductPoints.php">
<?php if ($page =="deductPoints") {echo"<font class='menu-active'>";}?>
Deduct Points<?php if ($page =="deductPoints") {echo"</font>";}?></a>
<? } ?>

<? if ($isFullAdmin) { ?>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Admin/pruneUnused.php">
<?php if ($page =="pruneUnused") {echo"<font class='menu-active'>";}?>
Prune Unused<?php if ($page =="pruneUnused") {echo"</font>";}?></a>
<? } ?>

<? if ($isFullAdmin || $isSeasonAdmin) { ?>
<img src="<?= $directory ?>/style/MorpheusX/images/darkblue/buttons_spacer.gif" />
<a href="<?php echo"$directory"?>/Admin/newSeason.php">
<?php if ($page =="newSeason") {echo"<font class='menu-active'>";}?>
Start New Season<?php if ($page =="newSeason") {echo"</font>";}?></a>
<? } ?>
