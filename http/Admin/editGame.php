<?PHP

// this displays a form to edit a game.

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "editGame";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./../top.php');

$back = "<p><a href='javascript:history.back()'>go back</a></p>";
$index = "<p><a href='index.php'>go to index</a></p>";

$msg = "";

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Edit Game", ""); ?>
<?

if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {

	
	if (!empty($_GET['submit'])) {
		$winnergoals = $_POST['winnergoals'];
		if (empty($winnergoals) && $winnergoals != '0') {
			$msg .= '<p>Error: Winner Goals empty.</p>';
		}
		$losergoals = $_POST['losergoals'];
		if (empty($losergoals) && $losergoals != '0') {
			$msg .= '<p>Error: Loser Goals empty.</p>';
		}
		$fairness = $_POST['fairness'];
		if (empty($fairness)) {
			$msg .= '<p>Error: Fairness empty.</p>';
		}
		$host = $_POST['host'];
		if (empty($host)) {
			$msg .= '<p>Error: Host empty.</p>';
		}
		$comment = $_POST['comment'];
		if (empty($comment)) {
			$msg .= '<p>Error: Comment empty.</p>';
		}
		$draw = $_POST['draw'];
		if (($draw > 0) && ($winnergoals != $losergoals)) {
		 	$msg .= "<p>Error: Can't change draw to non-draw.</p>";
		} 
		if (($draw == 0) && ($winnergoals == $losergoals)) {
		 	$msg .= "<p>Error: Can't change non-draw to draw.</p>";
		} 
		
		$deleteReason = $_POST['deleteReason'];
		$version = $_POST['version'];
		$oldversion = $_POST['oldversion'];
		
		$winner = $_POST['winner'];
		$loser = $_POST['loser'];
		
		$winnerteam = $_POST['winnerteam'];
		$oldwinnerteam = $_POST['oldwinnerteam'];
		$loserteam = $_POST['loserteam'];
		$oldloserteam = $_POST['oldloserteam'];
		$teamBonusOld = $_POST['teamBonus'];
		$winpoints = $_POST['winpoints'];
		if (strlen($msg) > 0) {
			$msg .= $back;
		}
		else {
			$gameId = $_POST['gameId'];
			if (strcmp(getGamesFieldForVersion($version), getGamesFieldForVersion($oldversion)) != 0) {
				$msg .= "<p>Changing game version is unsupported</p>";
			} 
			else {
				$adminname = GetInfo($idcontrol,'admin_username');
				$comment = mysql_real_escape_string($comment);
				$sql = "UPDATE $gamestable set winnerresult = '$winnergoals', ".
					"loserresult = '$losergoals', ".
					"winnerteam = '$winnerteam', loserteam = '$loserteam', ".
					"fairness = '$fairness', ".
					"host = '$host', ".
					"comment = '$comment', ".
					"deleteReason = '$deleteReason', ".
					"deletedBy = '$adminname', ".
					"version = '$version' ".
					"WHERE game_id = '$gameId'";
				$result = mysql_query($sql);
				
				$teamBonusNewArray = getTeamBonus($winnerteam, $loserteam, $draw, $winpoints-$teamBonusOld);
				$teamBonusNew = $teamBonusNewArray['bonusWinner'];
				$msg.= $teamBonusNewArray['msg'];
				if ($teamBonusOld != $teamBonusNew) {
					$teamBonusDiff = $teamBonusNew - $teamBonusOld;
					$msg.= "<p>Team bonus changed: <b>$teamBonusOld</b> -&gt; <b>$teamBonusNew</b></p>";
					
					$sql = "UPDATE $gamestable SET winpoints = winpoints + $teamBonusDiff, ".
						"teamBonus = '$teamBonusNew' WHERE game_id = '$gameId'";
					$msg.= "<p>Updating game - SQL: $sql</p>";
					$result = mysql_query($sql);

					$pointsField = getPointsFieldForVersion($version);
					
					$sqlp = "SELECT $pointsField from $playerstable where name = '$winner'";
					$resultp = mysql_query($sqlp);
					$rowp = mysql_fetch_array($resultp);
					$pointsOld = $rowp[$pointsField];
					$pointsNew = $pointsOld + $teamBonusDiff;						
					if ($pointsNew < 0) {
						$pointsNew = 0;
					}
					$sql = "UPDATE $playerstable SET $pointsField = $pointsNew ".
						"WHERE name = '$winner'";
					
					$msg.= "<p>Updating player - SQL: $sql</p>";
					$result = mysql_query($sql);
				}
		 
				if (!($result > 0)) {
					$msg .= '<p>Error running query: <b>'.$sql.'</b></p>'.$back;
				}
				else {
					$msg .= '<p>Game #'.$gameId.' successfully updated.</p>'.$index;
				}
			}
		}
	}
	else {
		
		$editGame = $_POST['editGame'];
		$sql = "SELECT * FROM $gamestable where game_id='$editGame'";
		$result = mysql_query($sql);
		
		if (mysql_num_rows($result) != 1) {
			$msg .= '<p>Game #'.$editGame.' not found in database.</p>'.$back;
		}
		else {	
			$row = mysql_fetch_array($result);
			
			$date = formatDate($row['date']);
			$ip = $row['ip'];
			$winner = $row['winner'];
			$loser = $row['loser'];
			$winpoints = $row['winpoints'];
			$losepoints = $row['losepoints'];
			$winnerresult = $row['winnerresult'];
			$loserresult = $row['loserresult'];
			$winnerteam = $row['winnerteam'];
			$loserteam = $row['loserteam'];
			$season = $row['season'];
			$deleted = $row['deleted'];
			$version = $row['version'];
			$host = $row['host'];
			$fairness = $row['fairness'];
			$deleteReason = $row['deleteReason'];
			$comment = $row['comment'];
			$draw = $row['isDraw'];
			$teamBonus = $row['teamBonus']; 
			$winpoints = $row['winpoints'];
			?>
			<table width="70%"><tr><td>
			<?= getBoxTop("Edit Game", "", false, null) ?>
			<form name="form1" method="post" action="editGame.php?submit=1">
			<table class="formtable">
			<tr>
				<td class="width150">Date</td>
				<td class="width200"><?= $date ?></td>
				<td class="width150">IP</td>
				<td class="width200"><?= $ip ?></td>
			</tr>
			<tr>
				<td>Deleted</td>
				<td><?= $deleted ?></td>
				<td>Season</td>
				<td><?= $season ?></td>
			</tr>
			<tr>
				<td>Winner</td>
				<td><b><?= $winner ?></b>
				<input type="hidden" name="winner" value="<?= $winner ?>" />
				</td>
				<td>Loser</td>
				<td><b><?= $loser ?></b>
				<input type="hidden" name="loser" value="<?= $loser ?>" /></td>
			</tr>
			<tr>
				<td>Gained Points</td>
				<td><?= $winpoints ?></td>
				<td>Lost Points</td>
				<td><?= $losepoints ?></td>
			</tr>
			<tr>
				<td>Winner Goals</td>
				<td><input name="winnergoals" type="Text" size="2" value="<?= $winnerresult ?>" /></td>
				<td>Loser Goals</td>
				<td><input name="losergoals" type="Text" size="2" value="<?= $loserresult ?>" /></td>
			</tr>
			<tr>
				<td>Winner Team</td>
				<td><select class="width200" name="winnerteam">
					<?= getTeamsOptionsNone(false) ?>
					<?= getTeamOptionForId($winner_favteam1); ?>
					<?= getTeamOptionForId($winner_favteam2); ?>
					<?= getTeamsAllOptions($winnerteam) ?>
				</td>
				<td>Loser Team</td>
				<td><select class="width200" name="loserteam">
					<?= getTeamsOptionsNone(false) ?>
					<?= getTeamOptionForId($loser_favteam1); ?>
					<?= getTeamOptionForId($loser_favteam2); ?>
					<?= getTeamsAllOptions($loserteam) ?>
				</td>
			</tr>
			<tr>
				<td>Fairness <span class="grey-small" style="cursor:help;" title="values: 1-5">(?)</span></td>
				<td><input name="fairness" type="Text" size="1" value="<?= $fairness ?>" /></td>
				<td>Host <span class="grey-small" style="cursor:help;" title="values: B (PES5), W (winner host), L (loser host), A (aggregate game)">(?)</span></td>
				<td>
					<input name="host" type="Text" size="1" value="<?= $host ?>" /> 
					
				</td>
			</tr>
			<tr>
				<td>Game version </td>
				<td>
					<?= getSelectboxForAllVersionsInput($version, 'version') ?>
					<input type="hidden" name="oldversion" value="<?= $version ?>" />
					<input type="hidden" name="oldwinnerteam" value="<?= $winnerteam ?>" />
					<input type="hidden" name="oldloserteam" value="<?= $loserteam ?>" />
					<input type="hidden" name="draw" value="<?= $draw ?>" />
					<input type="hidden" name="teamBonus" value="<?= $teamBonus ?>" />
					<input type="hidden" name="winpoints" value="<?= $winpoints ?>" />
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Delete Reason</td>
				<td colspan="3">
					<input name="deleteReason" type="Text" style="width:600px;" size="90" value="<?= htmlspecialchars($deleteReason) ?>" />
				</td>
			</tr>
			<tr>
				<td>Comment</td>
				<td>
					<textarea name="comment" rows="6" cols="30"><?= $comment ?></textarea>
				</td>
				<td colspan="2"><?= $back ?></td>
			</tr>
			<tr>
				<td colspan="4"><input type="hidden" name="gameId" value="<?= $editGame ?>" /></td>
			</tr>
			<tr>
				<td colspan="4" class="padding-button">
					<input type="Submit" name="submit" value="save changes">
				</td>
			</tr>
			</table>
			</form>
			<?= getBoxBottom() ?>
			</td></tr></table>
		<? 
		}
	}
echo $msg;
}

?>
<?= getOuterBoxBottom() ?>
<?
	require('./../bottom.php');
?>
