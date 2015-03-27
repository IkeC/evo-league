<?php


// displays the game reporting page if the user is logged in.
// the actual point calculation and database update is done in /functions.php#ReportGame

$page = "reportMini";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');

$back = "<p><a href='javascript:history.back()'>Go back</a></p>";
?>

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Report Mini Tournament", "") ?>
<table class="layouttable">
  <tr><td style="width:60%;horizontal-align:right;">
<?


if (!$isAdminFull && !$isModFull) {
	echo "<p>Access denied.</p>";
} else {
	if (!empty($_GET["submit"])) {
		$playerName = mysql_real_escape_string($_POST["playerName"]);
		$forumLink = mysql_real_escape_string($_POST["forumLink"]);
		if (empty($playerName)) {
			echo doParagraph("Please enter the winner name.".$back);
		} else if (empty($forumLink)) {
			echo doParagraph("Please enter the link to the tournament thread.".$back);
		} else {
			$sqlPlayer = "SELECT player_id from $playerstable where name = '$playerName'";
			$result = mysql_query($sqlPlayer);
			$rows = mysql_num_rows($result);
			if ($rows != 1) {
				echo doParagraph("Error: Player ".doBold($playerName)." found ".$rows." times in database.".$back);
			} else {
				$row = mysql_fetch_array($result);
				$playerId = $row['player_id'];
							
				$sqlInsert = "INSERT INTO $minitournamenttable (ID, PLAYER_ID, LINK) ".
					"VALUES ('', '$playerId', '$forumLink')";
				}
				$result = mysql_query($sqlInsert);
				echo doParagraph("Inserting tournament win for ".doBold($playerName)." - Result: ".doBold($result));
				
				echo doParagraph('Please check the <a href="'.$forumLink
					.'">forum link</a> and the <a href="/tournaments.php">tournaments page</a> to see if everything is correct.'); 
			}
		} else {
?>
 
	<?= getBoxTop("Report", "", false, null) ?>
	<form method="post" action="reportMini.php?submit=1" name="formReport">
    <table class="formtable">
    <tr>
	    <td>Tournament winner</td>
	    <td><select class="width150" size="1" name="playerName">		
		<option></option>
    <?php
    $sortby = "name ASC";
    $sql = "SELECT name FROM $playerstable WHERE approved = 'yes' ORDER BY $sortby";
    $result = mysql_query($sql);
    $num = mysql_num_rows($result);
    $cur = 1;
    while ($num >= $cur) {
        $row = mysql_fetch_array($result);
        $name = $row["name"];

        ?>
        <option><?php echo "$name" ?></option>
        <?php
        $cur++;
    } 
    ?>
    </select></td>
    </tr>
    <tr>
	    <td>Link to forum thread</td>
		<td align="left"><input type="text" name="forumLink" class="width250" maxlength="200"></td>
    </tr>
    <tr>
	    <td></td>
		<td align="left"><span class="grey-small">Example: http://www.<?= $leaguename ?>/forum/viewtopic.php?t=4141<br/>
		The link <b>must</b> end with the topic id! (4141 here)</span></td>
    </tr>
  	<tr>
  		<td colspan="2" class="padding-button"><input type="Submit" class="width100" name="submit" value="Report">
  	</tr>
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


