<?php


// displays the game reporting page if the user is logged in.
// the actual point calculation and database update is done in /functions.php#ReportGame

$page = "reportLeague";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');

$back = "<p><a href='javascript:history.back()'>Go back</a></p>";
?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Report League Game", "") ?>
<table class="layouttable">
  <tr><td style="width:60%;horizontal-align:right;">
<?


if (!$isAdminFull && !$isModFull) {
	echo "<p>Access denied.</p>";
} else {
	if (!empty ($_POST["deleteGame"])) {
		$deleteGame = mysql_real_escape_string($_POST["deleteGame"]);
		$sql = "SELECT id from $leaguegamestable where id = $deleteGame";
		$result = mysql_query($sql);
		if (empty($result)) {
			$num = 0;		
		} else {
			$num = mysql_num_rows($result);
		}
		$msg = "";
		if ($num == 1) {
			$sql = "DELETE FROM $leaguegamestable where id = $deleteGame";
			$result = mysql_query($sql);
			if ($result == 1) {
				$msg = "Game #$deleteGame successfully deleted.";		
			} else {
				$msg = "Error: Game #$deleteGame could not be deleted.<br>SQL: $sql";
			}
		} else {
			$msg = "Error: $num games found matching ID #$deleteGame!";
		}
		echo "<p>".$msg."</p>".$back;
	} 
	else if (!empty ($_POST["league"])) {
		$league = mysql_real_escape_string($_POST["league"]);
		if (!empty ($_GET['submit'])) {
			$submit = mysql_real_escape_string($_GET['submit']);
		} else {
			$submit = 0;
		}

		if ($submit == 1) {

			$wingoals = mysql_real_escape_string($_POST['wingoals']);
			$losegoals = mysql_real_escape_string($_POST['losegoals']);
			$winteam = mysql_real_escape_string($_POST['winteam']);
			$loseteam = mysql_real_escape_string($_POST['loseteam']);

			if ($wingoals < $losegoals) {
				$error = '<p>The loser cannot have more goals than the winner.</p>';
			}

			if (!is_numeric($wingoals) || !is_numeric($losegoals) || empty ($winteam) || empty ($loseteam)) {
				$error = "<p>One or more required informations are missing.</p>";
			}

			if (!empty ($error)) {
				echo "<p>".$error."</p><p><a href='javascript:history.back()'>go back</a></p>";
			} else {
				$date = time();
				$reportName = $cookie_name;

				$sql = "INSERT INTO $leaguegamestable "."(winteam, loseteam, winresult, loseresult, reportDate, reportUser, league) "."VALUES ('$winteam', '$loseteam', '$wingoals', '$losegoals', '$date', '$reportName', '$league')";
				$result = mysql_query($sql);

				if ($result == 1) {
					echo "<p>Game reported.</p>".$back;
				} else {
					echo "<p>The game could not be reported. Please contact the admin.</p>".$back;
				}
			}
		} else {
?>
 
	<?= getBoxTop("Report Game", "", false, null) ?>
	<form method="post" action="reportLeague.php?submit=1" name="formReport">
    <table class="formtable">
    <tr>
	    <td>Winner</td>
	    <td><select class="width250" size="1" name="winteam">		
			<option></option>
	    <?= getTeamsOptionsForLeague($league); ?>
	    ?>
	    </select></td>
    </tr>
    <tr>
	    <td>Loser</td>
	    <td><select class="width250" size="1" name="loseteam">		
			<option></option>
	    <?= getTeamsOptionsForLeague($league); ?>
	    ?>
	    </select></td>
    </tr>

	<tr>
        <td>Winner Goals</td>
        <td align="left"><input type="text" name="wingoals" class="width50" maxlength="2"></td>
    </tr>
    <tr>
        <td>Loser Goals</td>
        <td><input type="text" name="losegoals" class="width50" maxlength="2"></td>
    </tr>
		
  	<tr><td colspan="2" class="padding-button"><input type="Submit" class="width150" name="submit" value="Report Game">
  		<input type="hidden" name="league" value="<?= $league ?>"></td></tr>
	</table>
    </form>
	<?= getBoxBottom() ?>

<?


		}
	} 
	else if (!empty ($_POST["replaceInLeague"])) {
		$errorMsg = "";
		$league = mysql_real_escape_string($_POST["replaceInLeague"]);
		if (!empty ($_POST["oldName"])) {
			$oldName = mysql_real_escape_string($_POST["oldName"]);
		} else {
			$errorMsg .= "<p>Old player name empty.</p>";
		}
		if (!empty ($_POST["newName"])) {
			$newName = mysql_real_escape_string($_POST["newName"]);
		} else {
			$errorMsg .= "<p>New player name empty.</p>";
		}
		
		if (empty($errorMsg)) {
			$sql = "select name from $playerstable where name = '$oldName'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) == 1) {
				$row = mysql_fetch_array($result);
				$oldName = $row['name'];
			} else {
				$errorMsg .= "<p>The player <b>".$oldName."</b> could not be found in the database.</p>";
			}
		}
		
		if (empty($errorMsg)) {
			$sql = "select name from $playerstable where name = '$newName'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) == 1) {
				$row = mysql_fetch_array($result);
				$newName = $row['name'];
			} else {
				$errorMsg .= "<p>The player <b>".$newName."</b> could not be found in the database.</p>";
			}
		}
		if (empty($errorMsg)) {
			$sql = "select player from $leaguestable where player = '$oldName' AND league = '$league'";
			$result = mysql_query($sql);
			if (mysql_num_rows($result) == 1) {
				$row = mysql_fetch_array($result);
				$oldName = $row['player'];
			} else {
				$errorMsg .= "<p>The player <b>".$oldName."</b> could not be found in this league!</p>";
			}
		}			
			
		if (empty($errorMsg)) {
			$sql = "update $leaguestable set player = '$newName' where (player = '$oldName' and league = '$league')";
			$result = mysql_query($sql);
			if ($result == 1) {
				$errorMsg .= "<p><b>$oldName</b> was replaced by <b>$newName</b>.</p>";
				
				$sql = "select t.name from $leaguestable l ". 
					"left join $teamstable t on l.team = t.ID ".
					"where player = '$newName' and league = '$league'";
				$result = mysql_query($sql);
				while ($row = mysql_fetch_array($result)) {
					$team = $row['name'];
				}
				$errorMsg .= "<p>BBCode for replacement thread:</p>";
				$errorMsg .= '<p><textarea onClick="javascript:focus();javascript:select();" name="forumText" style="width:400px" rows="1">';
				$errorMsg .= '[b]'.$oldName.'[/b] has been replaced by [b]'.$newName.'[/b] ('.$team.')</textarea></p>';
				$errorMsg .= "<p>Topic update:</p>";
				$errorMsg .= '<p><textarea onClick="javascript:focus();javascript:select();" name="forumText2" style="width:400px" rows="1">';
				$errorMsg .= '('.$oldName.' --> '.$newName.')</textarea></p>';				
			} else {
				$errorMsg .= "<p><br>Update failed! Check the league table to see if something's wrong.</p>";
				$errorMsg .= "<p>SQL: $sql</p>";
			}
		}
		
		
		// error handling	
		if (!empty($errorMsg)) {
			echo $errorMsg.$back;
		}
		
	}
	else {
?>
	<?= getBoxTop("Report Game", "", false, null) ?>
	<form method="post" action="reportLeague.php" name="formReport">
    <table class="formtable">
    <tr>
	    <td width="150">League</td>
	    <td><select class="width250" size="1" name="league">		
			<?= getOptionsActiveLeagues(null) ?>
	    </select>
    </tr>

  	<tr><td colspan="2" class="padding-button"><input type="Submit" class="width150" name="submit" value="Select League"></td></tr>
	</table>
    </form>
	<?= getBoxBottom() ?>
	
	<?= getBoxTop("Delete Game", "", false, null) ?>
	<form method="post" action="reportLeague.php" name="formDelete">
    <table class="formtable">
    <tr>
	    <td width="150">Delete Game ID</td>
	    <td><input type="text" name="deleteGame" maxlength="5" class="width200"></td>
    </tr>

  	<tr><td colspan="2" class="padding-button"><input type="Submit" class="width150" name="submit" value="Delete Game"></td></tr>
	</table>
    </form>
	<?= getBoxBottom() ?>
	
	<?= getBoxTop("Replace Player", "", false, null) ?>
	<form method="post" action="reportLeague.php" name="formReport">
    <table class="formtable">
    <tr>
	    <td>League</td>
	    <td width="150"><select class="width250" size="1" name="replaceInLeague">		
			<?= getOptionsActiveLeagues(null) ?>
	    </select>
    </tr>
    <tr>
	    <td width="150">Replace player</td>
	    <td><input type="text" name="oldName" maxlength="30" class="width200"></td>
	</tr>
	<tr>
	    <td width="150">with this player</td>
	    <td><input type="text" name="newName" maxlength="30" class="width200"></td>
    </tr>

  	<tr><td colspan="2" class="padding-button"><input type="Submit" class="width150" name="submit" value="Replace Player"></td></tr>
	</table>
    </form>
	<?= getBoxBottom() ?>
<?
	}
}
?>
</td>
<td style="width:40%">
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?
	require ('../bottom.php');
?>


