<?PHP

// displays a news overview where you can select to view, edit or delete news

header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "view";
require('./../../variables.php');
require('./../../variablesdb.php');
require('./../../functions.php');
require('./../../top.php');

if(isset($_GET['read'])) {
	$ready = 1;
	$read = mysql_real_escape_string($_GET['read']);
}
else {
	$ready = 0;
}
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> News Admin", "") ?>
<?
if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {
?>
<table width="60%">
<tr><td><p><a href="/Admin/News/post.php">Post News</a></p>
<?php
if ($ready) {
	$sortby = "news_id DESC";
	$start = "0";
	$finish = "1";
	$sql="SELECT * FROM $newstable WHERE news_id = '$read' ORDER BY $sortby LIMIT $start, $finish";
	$result=mysql_query($sql,$db);
	$row = mysql_fetch_array($result);
	$news = $row["news"];
	$news = nl2br($news);
	$news = SmileyConvert($news,$directory);
	$date = $row["date"];
	$title = $row["title"];
	$user = $row['user'];
	?>
	<?= getBoxTop("News Entry ".$read, "", true, null); ?>
		<table>
	      <tr style="height:30px;">
	         <td><? 
	         	echo "<b>$date - $title</b>&nbsp;&nbsp;<span class='grey-small'>(posted by $user)</span>";
	         ?></td>
	      </tr>
	      <tr>
	         <td><?php echo"$news" ?></td>
	      </tr>
	  </table>
	<?= getBoxBottom() ?>
<?
}
?>


<?= getRankBoxTop("News List", array('View', 'Edit', 'Delete', 'Title')) ?>

<?php
$sortby = "news_id DESC";
$start = "0";
$finish = "100000";
$sql="SELECT * FROM $newstable ORDER BY $sortby LIMIT $start, $finish";
$result=mysql_query($sql,$db);
$num = mysql_num_rows($result);
$cur = 1;
while ($num >= $cur) {
	$row = mysql_fetch_array($result);
	$date = $row["date"];
	$title = $row["title"];
	$user = $row['user'];
	$read = $row["news_id"];
	?>
	<tr class="row">
		<td width="30"><a href='view.php?read=<?= $read ?>'><img border='1' src='<?php echo "$directory" ?>/gfx/view.gif' width='18' height='18' align='middle'></a></td>
		<td width="30"><a href='edit.php?edit=<?= $read ?>'><img border='1' src='<?php echo "$directory" ?>/gfx/edit.gif' width='18' height='18' align='middle'></a></td>
		<td width="30"><a href='delete.php?edit=<?= $read ?>'><img border='1' src='<?php echo "$directory" ?>/gfx/delete.gif' width='18' height='18' align='middle'></a></td>
		<td><?= $date ?> - <?= $title ?> <span class='grey-small'>(<?= $user ?>)</td>
	</tr> 
	<?
	$cur++;
}
?>
<?= getRankBoxBottom() ?>
</td></tr></table>
<?php
}
?>
<?= getOuterBoxBottom() ?>
<?
require('./../../bottom.php');
?>
