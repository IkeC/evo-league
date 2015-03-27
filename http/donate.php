<?php

// this page shows when you click the 'donate!' menu entry. don't forget to remove my text/details.
// if you want to remove all, you need to change /menu.php and the field weblm_vars.menuorder

   header("Cache-Control: no-cache");
   header("Pragma: no-cache");
	
   $page = 'info';
   $subpage = 'donate';

   require('./variables.php');
   require('./variablesdb.php');
   require('./functions.php');
   require('./top.php');
?> 

<script type="text/javascript">
/* <![CDATA[ */
    (function() {
        var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
        s.type = 'text/javascript';
        s.async = true;
        s.src = 'http://api.flattr.com/js/0.6/load.js?mode=auto';
        t.parentNode.insertBefore(s, t);
    })();
/* ]]> */</script>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>

<table class="layouttable">
<tr><td width="60%" valign="top">
<?= getBoxTop("How to donate", "", false, null); ?>
<p>If you want to support this site, please contact an administrator.</p>
<?= getBoxBottom() ?>
</td>
<td width="40%" align="center" valign="top">
<?= getBoxTop("Donators", "", false, null); ?>
<table class="layouttable"><tr><td>
<p>Donators&nbsp;&nbsp;<span class='grey-small' style='font-weight:normal';>(latest first)</span></p>
<table>
<? $sql = "SELECT wd.*, wp.player_id FROM $donationstable wd LEFT JOIN weblm_players wp ON wd.name=wp.name ORDER BY ID desc";
    $result = mysql_query($sql);
	while($row = mysql_fetch_array($result)){
	$name = $row["name"];
	$donationDate = $row["donationDate"];
  $playerId = $row['player_id'];
  if (!is_null($playerId)) {
    $playerLink = '<a style="font-size:11px;" href="'.$directory.'/profile.php?id='.$playerId.'">'.$name.'</a>';
  } else {
    $playerLink = $name;
  }
	echo '<tr>'.
		'<td style="font-size:11px;">'.$donationDate.'</td>'.
		'<td>'.$playerLink.'</td>'.
		'</tr>';
	} // while
?>
</table>
</td></tr></table>
<?= getBoxBottom() ?>	
</td>
</tr></table>
<?= getOuterBoxBottom() ?>
<?php
require('./bottom.php');
?>
