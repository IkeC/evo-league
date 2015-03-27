<?PHP

// this will display a form to remove a news entry

$page = "view";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../functions.php');
require('./../../top.php');
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Player Status", "") ?>
<?php
$index = "<p><a href='view.php'>news index</a></p>";


if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {
?>
<?php
if (isset($_GET['submit']))
	{
	$submit = mysql_real_escape_string($_GET['submit']);
	}
	else
		{
		$submit = 0;
		}
if ($submit == 1) {
if(! empty($_POST['edit']))
	{
	$edit = mysql_real_escape_string($_POST['edit']);
	}
	else
		{
		$edit = '1';
		}
$sql = "DELETE FROM $newstable WHERE news_id = '$edit'";
$result = mysql_query($sql);
echo "<p>News entry $edit deleted</p>".$index;
}
else {
if(! empty($_GET['edit']))
	{
	$edit = mysql_real_escape_string($_GET['edit']);
	}
	else
		{
		$edit = '1';
		}
?>
<table width="50%"><tr><td>
<?= getBoxTop("Delete News Entry", "", false, null); ?>
<form name="form1" method="post" action="delete.php?submit=1">
<table>
<tr>
<td>Delete news?</td>
</tr>
<tr>
<td class="padding-button">
	<input type='hidden' name='edit' value="<?php echo "$edit" ?>">
	<input type="Submit" name="submit" value="delete" style="background-color: <?php echo"$color5" ?>; border: 1 solid <?php echo"$color1" ?>" class="text"><br>
</td>
</tr>
</table>
</form>
<?= getBoxBottom() ?>
</td></tr></table>
<?php
}
?>
<?php
}
?>
<?= getOuterBoxBottom() ?>
<?
require('./../../bottom.php');
?>
