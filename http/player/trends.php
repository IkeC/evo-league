<?php

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "players";
$subpage = "trends";

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
require('../top.php');

$boxImg = "8.jpg";
$boxAlign = "left bottom";

$name = mysql_real_escape_string($_GET['name']);

$sql = "select name from $playerstable where name = '$name'";
$result = mysql_query($sql);

if (empty($name)) {
	echo "<p>No player specified!</p>";        
} else if (mysql_num_rows($result) == 0) {
	echo "<p>The player <b>$name</b> could not be found in the database.</p>";
} else {
	$row = mysql_fetch_array($result);
	$name = $row['name'];
	$trend_height = 150;
	$trend_width = 390;
	$teamsArray = array();
?>
    <?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, $name), "") ?>
	<table width="100%">
		<tr>
			<td colspan="2">
	<?= getBoxTop("Info", "", false, null) ?>
	<p>For every day <?= $name ?> has played one or more games, a trend value is calculated. Days where no games were played are not displayed.</p>   
	<?= getBoxBottom() ?>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top; width:50%;">
		
			<?= getBoxTopImg("Trend curve - Current season", null, false, "", '', ''); ?>
				 <table class="layouttable">
				    <tr>
				      <td>
				      	<img src="/graph/trend.php?name=<?= $name ?>&amp;season=<?= $season ?>&amp;height=<?= $trend_height ?>&amp;width=<?= $trend_width ?>" border="1">
				      </td>
					</tr>
				 </table>
				<?= getBoxBottom() ?>
				
			<?= getBoxTopImg("Trend curve - Previous season", null, false, "", '', ''); ?>
				 <table class="layouttable">
				    <tr>
				      <td>
				      	<img src="/graph/trend.php?name=<?= $name ?>&amp;season=<?= $season-1 ?>&amp;height=<?= $trend_height ?>&amp;width=<?= $trend_width ?>" border="1">
				      </td>
					</tr>
				 </table>
				<?= getBoxBottom() ?>
				
				
			
			
	</td>
		<td style="vertical-align:top; width:50%;">
			
			<? $days = 100; ?>
			<?= getBoxTopImg("Trend curve - Last ".$days." Days", null, false, "", '', ''); ?>
				 <table class="layouttable">
				    <tr>
				      <td>
				      	<img src="/graph/trend.php?name=<?= $name ?>&amp;days=<?= $days ?>&amp;height=<?= $trend_height ?>&amp;width=<?= $trend_width ?>" border="1">
				      </td>
					</tr>
				 </table>
				<?= getBoxBottom() ?>
	
			<? $days = 1000; ?>
			<?= getBoxTopImg("Trend curve - Since beginning", null, false, "", '', ''); ?>
				 <table class="layouttable">
				    <tr>
				      <td>
				      	<img src="/graph/trend.php?name=<?= $name ?>&amp;days=<?= $days ?>&amp;height=<?= $trend_height ?>&amp;width=<?= $trend_width ?>" border="1">
				      </td>
					</tr>
				 </table>
				<?= getBoxBottom() ?>

<?
}
?>
			</td>
		</tr>
	</table>
<?= getOuterBoxBottom() ?>

<? require('../bottom.php'); ?>

