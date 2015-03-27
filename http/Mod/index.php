<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "index";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./../top.php');

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Moderator Index", ""); ?>
<? if ($isAdminFull || $isModFull) { 
	echo doParagraph("Welcome, ".doBold($cookie_name).".");
	echo doParagraph("Please click an item in the submenu above to continue.");
} else {
	echo doParagraph("<p>Access denied.</p>");
}
?>
<?= getOuterBoxBottom() ?>
<?
require('./../bottom.php');
?>