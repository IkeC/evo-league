<?php

// the rules and information page

header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "sitemap";

require ('variables.php');
require ('variablesdb.php');
require ('functions.php');
require ('top.php');


list($pageinfo1, $pageinfo2, $pageinfo3, $pageinfo4, $pageinfo5, 
	$pageinfo6, $pageinfo7, $pageinfo8, $pageinfo9, $pageinfo10, 
	$pageinfo11, $pageinfo12, $pageinfo13, $pageinfo14) = explode("*", $menuorder);

$pageinfos = explode("*", $menuorder);

?>

<?= getOuterBoxTop($leaguename.getRaquo(). " Sitemap", "") ?>
<p style="padding: 5px 3px 5px 0;">If you forgot where a certain page is or the dynamic menu isn't working for you, this table shows all main and sub menu items. Click the page name 
		to go to the page.</p>
<table border="1" style="width:800px; border-collapse:collapse;">
	<tr>
		
<?
$count = 0;
$tdstyle = 'style="vertical-align:top; border-collapse:collapse; border:1px solid #AAAAAA;  padding: 10px 10px 10px 10px"';
if (!empty($cookie_name) && $cookie_name != 'null') {
	$subs = getSubNavArrayForPage('profile', $cookie_name);
	echo '<td nowrap colspan="4" width="800" '.$tdstyle.'><p><b><a href="/profile.php?name='.$cookie_name.'">My Profile</a></b>'.
		'&nbsp;&nbsp;<span class="grey-small">(only when logged in or by clicking other players name)</span></p><p>';
	foreach ($subs as $sub) {
		$urlFormatted = str_replace($playerDummy, "?name=".$cookie_name, $sub[0]);
		echo '--&nbsp;<a href="'.$urlFormatted.'">'.$sub[1].'</a><br>';
	}
	echo "</p></td></tr><tr>";
}

foreach ($pageinfos as $pageinfo) {
	if ($count > 0 && $count % 2 == 0) {
		echo "</tr><tr>";	
	}
	list($pagescreename, $pageurl, $pagename) = explode(":", $pageinfo);
	echo '<td width="200" '.$tdstyle.'><p><b><a href="'.$pageurl.'.php">'.$pagescreename.'</a></b></p><p>';
	$subs = getSubNavArrayForPage($pagename, null);
	foreach ($subs as $sub) {
		echo '--&nbsp;<a href="'.$sub[0].'">'.$sub[1].'</a><br>';
	}
	echo "</p></td>";
	$count++;
?>

<?
}
?>
		
	</tr>
</table>
<br>
<?= getOuterBoxBottom() ?>

<? require ('bottom.php'); ?>


