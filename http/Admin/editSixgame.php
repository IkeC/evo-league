<?PHP

// this displays a form to edit a sixserver game.

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "editSixgame";

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
	
	if (!empty($_POST['scoreHome']) || !empty($_POST['scoreAway'])) {
    $sixEditGameId = $_POST['sixEditGameId'];

    $scoreHome = $_POST['scoreHome'];
    $scoreAway = $_POST['scoreAway'];
    $scoreHomeReg = $_POST['scoreHomeReg'];
    $scoreAwayReg = $_POST['scoreAwayReg'];
    
    $sql = "UPDATE six_matches SET played_on=played_on, score_home=".$scoreHome.", score_away=".$scoreAway.", score_home_reg=".$scoreHomeReg.", score_away_reg=".$scoreAwayReg." ".
      "WHERE id=".$sixEditGameId;
    echo "<p>SQL: $sql</p>";
    
    mysql_query($sql);
    
    $sql = "SELECT smp.profile_id, sp.disconnects, sp.points, sp.rating FROM six_matches_played smp ".
      "LEFT JOIN six_profiles sp ON sp.id=smp.profile_id ".
      "WHERE smp.match_id=".$sixEditGameId;
    echo "<p>SQL: $sql</p>";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
      $profileId = $row['profile_id'];
      $msg = RecalculatePointsForProfile($profileId, $row['disconnects'], $row['points'], $row['rating']);
      echo "<p>$msg</p>";
      $msg = RecalculateStreak($profileId);
      echo "<p>$msg</p>";
    }
    $sql = "UPDATE six_matches_played SET points=NULL, pointsDiff=NULL, rating=NULL, ratingDiff=NULL WHERE match_id=".$sixEditGameId;
    echo "<p>SQL: $sql</p>";
    
    mysql_query($sql);
    
    echo '<br><a target="_new" href="http://www.yoursite/sixserver/games.php?id='.$sixEditGameId.'">Check game</a>';
	}
	elseif (!empty($_POST['sixEditGameId'])) {
		$sixEditGameId = $_POST['sixEditGameId'];
    
		$sql = "SELECT * FROM six_matches where id=$sixEditGameId";
		$result = mysql_query($sql);

		if (mysql_num_rows($result) != 1) {
			$msg .= '<p>Game #'.$sixEditGameId.' not found in database.</p>'.$back;
		}
		else {	
			$row = mysql_fetch_array($result);
      $scoreHome = $row['score_home'];
      $scoreAway = $row['score_away'];
      $scoreHomeReg = $row['score_home_reg'];
      $scoreAwayReg = $row['score_away_reg'];

      $sql = "SELECT smp.home, sp.name FROM six_matches_played smp ".
        "LEFT JOIN six_profiles sp ON sp.id=smp.profile_id ".
        "WHERE smp.match_id=$sixEditGameId";
      $result = mysql_query($sql);
      $profilesHome = "";
      $profilesAway = "";
      while ($row = mysql_fetch_array($result)) {
        if ($row['home'] == 1) {
          if (empty($profilesHome)) {
            $profilesHome = $row['name'];
          } else {
            $profilesHome = $profilesHome."<br>".$row['name'];
          }
        } else {
          if (empty($profilesAway)) {
            $profilesAway = $row['name'];
          } else {
            $profilesAway = $profilesAway."<br>".$row['name'];
          }
        }
      }
			?>
			<table width="70%"><tr><td>
			<?= getBoxTop("Edit Game", "", false, null) ?>
			<form name="form1" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
			<table class="formtable">
			<tr>
				<td class="width200"></td>
				<td class="width200">Home</td>
				<td class="width200">Away</td>
			</tr>
			<tr>
				<td>Profile(s)</td>
				<td><?= $profilesHome ?></td>
				<td><?= $profilesAway ?></td>
			</tr>
			<tr>
				<td>Score Reg.</td>
				<td>
          <input type="text" name="scoreHomeReg" value="<?= $scoreHomeReg ?>" />
        </td>
				<td>
          <input type="text" name="scoreAwayReg" value="<?= $scoreAwayReg ?>" />
        </td>
			</tr>
			<tr>
				<td>Score</td>
				<td>
          <input type="text" name="scoreHome" value="<?= $scoreHome ?>" />
        </td>
				<td>
          <input type="text" name="scoreAway" value="<?= $scoreAway ?>" />
        </td>
			</tr>
			<tr>
				<td colspan="3" class="padding-button">
          <input type="hidden" name="sixEditGameId" value="<?= $sixEditGameId ?>" />
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
