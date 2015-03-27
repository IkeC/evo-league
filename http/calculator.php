<?php

// the rules and information page

header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "games";
$subpage = "calculator";

require ('variables.php');
require ('variablesdb.php');
require ('functions.php');
require ('top.php');
$winnerresult = "";
$loserresult = "";

$msg = "";
if (!empty($_GET["submit"])) {
	$winnername = mysql_real_escape_string($_POST["winnername"]);
	$losername = mysql_real_escape_string($_POST["losername"]);
	$winnername2 = mysql_real_escape_string($_POST["winnername2"]);
	$losername2 = mysql_real_escape_string($_POST["losername2"]);
	$winnerresult = mysql_real_escape_string($_POST["winnerresult"]);
	$loserresult = mysql_real_escape_string($_POST["loserresult"]);
	$winnerteam = mysql_real_escape_string($_POST["winnerteam"]);
	$loserteam = mysql_real_escape_string($_POST["loserteam"]);
	
	if (empty($winnername)) {
		$msg.= "<p>Please select the winner!</p>";
	} else if (empty($losername)) {
		$msg.= "<p>Please select the loser!</p>";
	} else if (($winnername == $losername) 
     	|| ((strlen($winnername2) > 0) && (($winnername2 == $losername) || ($winnername2 == $losername2)))
     	|| ((strlen($losername2) > 0) && (($losername2 == $winnername) || ($losername2 == $winnername2)))
     	) {
         $msg.= "<p>You can't play against yourself.</p>".$back;
     } else if (strlen($winnername2) > 0 && ($winnername == $winnername2)) {
         $msg.= "<p>You can't be twice in the same team! Leave the 'Winner 2' box empty 
		or put the correct name of your teammate.</p>";
     }  else if (strlen($losername2) > 0 && ($losername == $losername2)) {
         $msg.= "<p>You can't be twice in the same team! Leave the 'Loser 2' box empty 
		or put the correct names of the two losing players.</p>";
    } else if (!is_numeric($winnerresult)) {
		$msg.= "<p>Please enter the goals for the winner!</p>";
	} else if (!is_numeric($loserresult)) {
		$msg.= "<p>Please enter the goals for the loser!</p>";
	} else if ($loserresult > $winnerresult) {
		$msg.= "<p>The loser can't have more goals than the winner!</p>";
	} 

	if (empty($msg)) {
		$version = getVersionForLadder("PES/WE");

		if (!empty($winnername2) || !empty($losername2)) {
			// game for team ladder
			$pointsField = "teamPoints";
			$gamesField = "teamGames";
			$winsField = "teamWins";
			$lossesField = "teamLosses"; 
			$ladderType = "Team";
		} else {
			// single ladder
			$pointsField = getPointsFieldForVersion($version);
			$gamesField = getGamesFieldForVersion($version);
			$winsField = getWinsFieldForVersion($version);
			$lossesField = getLossesFieldForVersion($version);
			$ladderType = "Single";
		}

		$row =  getRatingAndPointsForPlayer($winnername, $pointsField);
		$ratingOldWinner1 = $row["rating"];	
		$pointsOldWinner1 = $row[$pointsField];
	
		$row = getRatingAndPointsForPlayer($losername, $pointsField);
		$ratingOldLoser1 = $row["rating"];
		$pointsOldLoser1 = $row[$pointsField];
		
		$type = "1on1";
		if (!empty($winnername2)) {
			$type = "2on1";
			$row = getRatingAndPointsForPlayer($winnername2, $pointsField);
			$ratingOldWinner2 = $row["rating"];
			$pointsOldWinner2 = $row[$pointsField];
		}
		if (!empty($losername2)) {
			$type = "1on2";
			$row = getRatingAndPointsForPlayer($losername2, $pointsField);
			$ratingOldLoser2 = $row["rating"];
			$pointsOldLoser2 = $row[$pointsField];
		}
		if (!empty($winnername2) && !empty($losername2)) {
			$type = "2on2";
		}
		
		if (($winnerresult-$loserresult) == 0) {
			$isDraw = 1;
			$winLoss = 0;
			$sqlDraw = " draws = draws + 1, totaldraws = totaldraws + 1, ";
			$addWinOrDefeat = 0;
		} else {
			$isDraw = 0;
			$winLoss = 1;
			$sqlDraw = "";
			$addWinOrDefeat = 1;
		}
	
		if (($winnerresult-$loserresult) == 0) {
			$isDraw = 1;
		}
		$comment = "";
		$calc = calculateGamePointsReportGame($winnername, $winnername2, $losername, $losername2, 
			$winnerresult, $loserresult, $winnerteam, $loserteam,  
			$ratingOldWinner1, $ratingOldWinner2, $ratingOldLoser1, $ratingOldLoser2, 
			$pointsOldWinner1, $pointsOldWinner2, $pointsOldLoser1, $pointsOldLoser2, 
			$type,
			$isDraw, $comment, null);
		
		$ratingdiff = $calc["ratingdiff"];
		$ra2newwinneradd = $calc["addPointsWinner"];
		$ra2newloserremL1 = $calc["removePointsLoser1"];
		$ra2newloserremL2 = $calc["removePointsLoser2"];
		$ra2newwinner = $calc["pointsNewWinner1"];
		$ra2newwinner2 = $calc["pointsNewWinner2"];
		$ra2newloser = $calc["pointsNewLoser1"];
		$ra2newloser2 = $calc["pointsNewLoser2"];
		$message = $calc["message"];	
		
		$msg .= "<p>Game type: <b>$type</b> -> ".$ladderType." Ladder</p>";
		$msg .= "<p$message</p>";
	}
}

?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
	
<? ?> 
<table width="100%">
<tr>
<td width="50%" valign="top">
<?= getBoxTop("Simulate Game", "", false, null) ?>
	<form method="post" action="<?= $subpage ?>.php?submit=true" name="formReport">
    <table class="formtable">
    <tr>
    <td class="width150">Winner</td>
    <td>
    	<select class="width150" size="1" name="winnername">		
		<option></option>
		<? if (!empty($cookie_name) && ($cookie_name != 'null')) { ?>
		<option><?= $cookie_name ?></option>
		<option></option>
		<? } ?>
		<?= getPlayersOptionsApprovedSelected($winnername) ?>
	    </select>
	</td>
    </tr>
    <tr>
    <td class="width150">Winner 2</td>
    <td>
    	<select class="width150" size="1" name="winnername2">		
		<option></option>
		<? if (!empty($cookie_name) && ($cookie_name != 'null')) { ?>
		<option><?= $cookie_name ?></option>
		<option></option>
		<? } ?>
		<?= getPlayersOptionsApprovedSelected($winnername2) ?>
	    </select>
	</td>
	<td>(2on1 or 2on2)</td>
    </tr>
    <tr>
    <td>Loser</td>
    <td>
    	<select class="width150" size="1" name="losername">		
		<option></option>
		<? if (!empty($cookie_name) && ($cookie_name != 'null')) { ?>
		<option><?= $cookie_name ?></option>
		<option></option>
		<? } ?>
		<?= getPlayersOptionsApprovedSelected($losername) ?>
	    </select>
	</td>
    </tr>
    <tr>
    <td class="width150">Loser 2</td>
    <td>
    	<select class="width150" size="1" name="losername2">		
		<option></option>
		<? if (!empty($cookie_name) && ($cookie_name != 'null')) { ?>
		<option><?= $cookie_name ?></option>
		<option></option>
		<? } ?>
		<?= getPlayersOptionsApprovedSelected($losername2) ?>
	    </select>
	</td>
    <td>(1on2 or 2on2)</td>
    </tr>
    <tr>
    	<td>Winner Goals</td>
    	<td align="left"><input type="text" name="winnerresult" class="width50" maxlength="2" value="<?= $winnerresult ?>"></td>
    </tr>
    <tr>
    	<td>Loser Goals</td>
    	<td><input type="text" name="loserresult" class="width50" maxlength="2" value="<?= $loserresult ?>"></td>
    </tr>
	<tr>
		<td>Winner Team</td>
		<td><select class="width150" name="winnerteam">
			<?= getTeamsOptionsNone(true) ?>
			<?= getTeamOptionForId($winner_favteam1); ?>
			<?= getTeamOptionForId($winner_favteam2); ?>
			<?= getTeamsAllOptions($winnerteam) ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Loser Team</td>
		<td><select class="width150" name="loserteam">
			<?= getTeamsOptionsNone(true) ?>
			<?= getTeamOptionForId($loser_favteam1); ?>
			<?= getTeamOptionForId($loser_favteam2); ?>
			<?= getTeamsAllOptions($loserteam) ?>
			</select>
		</td>
	</tr>
		
    <tr><td colspan="2" class="padding-button">
    	<input type="Submit" class="width150" name="submit" value="Simulate game"></td></tr>
	</table>
    </form>
	<?= getBoxBottom() ?>
</td>
<td width="50%" valign="top">
<? if (!empty($msg)) { ?>
	<?= getBoxTop("Simulation Result", "", false, null) ?>
	<p><?= $msg ?></p>
	<?= getBoxBottom() ?>
<? } ?>
</td>
</tr></table>
<?= getOuterBoxBottom() ?>

<? require ('bottom.php'); ?>


