<?php

$page = "setupLeague";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');

require_once('../xajax/xajax_core/xajax.inc.php');

$xajax = new xajax();
$xajax->configure("javascript URI","/xajax/");
$xajax->registerFunction("getTeamsList");
$xajax->registerFunction("getPlayersList");
$xajax->processRequest();

$back = "<p><a href='javascript:history.back()'>Go back</a></p>";

require ('../top.php');

$msg = "";
?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Report League Game", "") ?>
<table class="layouttable">
  <tr><td style="width:60%;horizontal-align:right;">
<?


if (!$isAdminFull && !$isModFull) {
	echo "<p>Access denied.</p>";
} else {
	if (!empty ($_POST["league"])) {
		$league = mysql_real_escape_string($_POST["league"]);
		$team = mysql_real_escape_string($_POST['team']);
		$player = mysql_real_escape_string($_POST['player']);
		if ($team == 0) {
			$msg = "No team selected!";
		} else {
			$sql = "INSERT INTO $leaguestable (team, player, league) VALUES ('$team', '$player', '$league')";
			$result = mysql_query($sql);
			$msg = "Running query '".$sql."'<p>Result: ".$result;
		}
	}
?>
 
	<?= getBoxTop("Setup League Players", "", false, null) ?>
	<form method="post" action="<?= $page ?>.php" name="formReport">
    <table class="formtable">
    <tr>
	    <td>League</td>
	    <td width="150"><select class="width250" size="1" name="league">		
			<?= getOptionsActiveLeagues($league) ?>
	    </select>
    </tr>
	<tr>
		<td>Player</td>
		<td>
		<select class="width250" name="player">
		<?= getPlayersOptionsApproved() ?>
		</select>
		</td>
	</tr>
	<tr>
		<td>Team</td>
		<td>
		<select class="width250" name="team">
		<?= getTeamsAllOptions() ?>
		</select>
		</td>
	</tr>
  	<tr><td colspan="2" class="padding-button"><input type="Submit" class="width150" name="submit" value="Add player">
  		</td>
  	</tr>
	</table>
    </form>
	<?= getBoxBottom() ?>

<?
	
}
?>
</td>
<td style="width:40%" valign="top">
<? if (!empty($msg)) { ?>
<?= getBoxTop("Info", "", true, null); ?>
<?= $msg ?>
<?= getBoxBottom() ?>
<?
}
?>
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?
	require ('../bottom.php');
?>


