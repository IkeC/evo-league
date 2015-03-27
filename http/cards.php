<?PHP

// this displays a form to edit a game.

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "info";
$subpage = "cards";

require('./variables.php');
require('./variablesdb.php');
require('./functions.php');
require('./top.php');

$back = "<p><a href='javascript:history.back()'>go back</a></p>";
$index = "<p><a href='playerStatus.php'>go to index</a></p>";

$banthread = "";

$username = GetInfo($idcontrol,'admin_username');
$password = GetInfo($idcontrol,'admin_password');
$admin_full = GetInfo($idcontrol, 'admin_full');
$admin_player = GetInfo($idcontrol, 'admin_player');

$sql="SELECT * FROM $admintable WHERE name = '$username' AND password = '$password'";
$result=mysql_query($sql,$db);
$number = mysql_num_rows($result);
$msg = "";

$selectId = "";
if (!empty($_GET['id'])) {
  $selectId = mysql_real_escape_string($_GET['id']);
} elseif (!empty($_POST['id'])) {
  $selectId = mysql_real_escape_string($_POST['id']);
}

?> 
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table width="100%">
  <tr>
    <td style="text-align:left;vertical-align:middle;">
    <form method="post" name="formCards" action="<?= $_SERVER['PHP_SELF'] ?>">
    Player&nbsp;&nbsp;
    <select class="width150" name="id">
    <option value="">(all players)</option>
    <option value=""></option>
    <?= getPlayersOptionsAllIdSelected($selectId) ?>
    </select>
    &nbsp;&nbsp;<input type="Submit" class="width100" name="submit2" value="Show" /></form>
    </td>
  </tr>
</table>
 <?= getOuterBoxBottom() ?>

<?= getOuterBoxTop($left, $right) ?>
<table width="95%"><tr><td>
<?
if (isset($_GET['mode']) && $_GET['mode'] == 'inactive') {
	$inactiveMode = true;
} else {
	$inactiveMode = false;
}

if ($inactiveMode) {
	$whereClause = "WHERE type = 'I'";
	$statusLinks[] = array('playerStatus.php?mode=inactive&amp;limit=no', 'show_all_inactive', 'show all inactive');
	$statusLinks[] = array('playerStatus.php', 'show_warnings_bans', 'show warnings and bans');
	if (isset($_GET['limit'])) {
		$limit = "";		
	} 
	else {
		$limit = " LIMIT 0, 50";
	} 
} else {
	$whereClause = "WHERE type != 'I'";
	$statusLinks[] = array('playerStatus.php?mode=inactive', 'show_inactive', 'show inactive');
	$limit = " LIMIT 0, 200";
}
if (!empty($selectId)) {
  $whereClause .= " AND userId=".$selectId;
}

	$sql = "SELECT * FROM $playerstatustable $whereClause ORDER BY 'date' DESC, id DESC $limit";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) > 0) {		
		$playerStatusArray = array('Player', 'Type', 'Active', 'Date', 'Expiry', 'Reason');
		?>
		<?= getRankBoxTop("Player Status", $playerStatusArray) ?>
		<?
		
		while ($row = mysql_fetch_array($result)) {
			$name = $row['userName'];
			$expireDate = $row['expireDate'];
			if (!empty($expireDate)) {
				$expireDate = $expireDate + (60*60*24);
				$expireDaysFormatted = formatShortDate($expireDate) ."&nbsp;12:05&nbsp;am";
				
			} else {
				$expireDaysFormatted = "";
			}
			
			$forumLink = $row['forumLink'];
			if (strstr($forumLink, 'forum')) {
				$reason = '<a href="'.$forumLink.'">'.$row['reason'].'</a>';
			} else {
				$reason = $row['reason'];
			}
      $additionalInfo = $row['additionalInfo'];
			
		?>
			<tr<?

		if (strcmp($cookie_name, $name) == 0) {
			echo " class='row_active'";
		} else {
			echo " class='row'";
		}
?>>
				<td><b><a href="profile.php?name=<?= $name ?>"><?= $name ?></a></b></td>
				<td><?= getStatusImgForType($row['type']) ?></td>
				<td><?= getImgForActive($row['active']) ?></td>
				<td nowrap><?= formatLongDate($row['date']) ?></td>
				<td><?= $expireDaysFormatted ?></td>
				<td title="<?= $additionalInfo ?>"><?= $reason ?></td>
			</tr>
		<?
		} // end while
		?>
		<?= getRankBoxBottom() ?>
		<?
	} // rows > 0 
  else {
    echo "No results";
  }
?>

<?= $msg ?>
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?
require('./bottom.php');
?>
