<?PHP


// this displays a form to edit news.

header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "edit";
require ('./../../variables.php');
require ('./../../variablesdb.php');
require ('./../functions.php');
require ('./../../functions.php');
require ('./../../top.php');
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Edit News", "") ?>
<?


$index = "<p><a href='view.php'>news index</a></p>";

if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {

	if (isset ($_GET['submit'])) {
		$submit = mysql_real_escape_string($_GET['submit']);
	} else {
		$submit = 0;
	}

	if ($submit == 1) {
		if (!empty ($_POST['edit'])) {
			$edit = mysql_real_escape_string($_POST['edit']);
		} else {
			die("<p>No news id submitted, can't save the changes.</p>" . $index);
		}
		if (!empty ($_POST['date'])) {
			$date = mysql_real_escape_string($_POST['date']);
		} else {
			$date = date('d/m/Y');
		}
		if (!empty ($_POST['titlenews'])) {
			$titlenews = mysql_real_escape_string($_POST['titlenews']);
		} else {
			$titlenews = 'Title';
		}
		if (!empty ($_POST['news'])) {
			$news = mysql_real_escape_string($_POST['news']);
		} else {
			$news = 'Insert your text here.';
		}
		
		replacePlayerLinks($news);
		
		$sql = "UPDATE $newstable SET date = '$date', title = '$titlenews', news = '$news' WHERE news_id = '$edit'";
		$result = mysql_query($sql);

		$sql = "SELECT * FROM $newstable where news_id = '$edit' ORDER BY news_id DESC LIMIT 0, 1";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$news = $row["news"];
		$news = nl2br($news);
		$news = SmileyConvert($news, $directory);
		
		$date = $row["date"];
		$title = $row["title"];
		$user = $row["user"];
		$parsedTime = strtotime($date);
		if ((time() - $parsedTime) < 60 * 60 * 24 * 3) {
			$isNew = true;
		} else {
			$isNew = false;
		}

		echo '<p>News #' . $edit . ' edited | <a href="edit.php?edit=' . $edit . '">Edit again</a> | <a href="view.php">News Index</a></p>';
?>
<table width="50%"><tr><td>
<?

		echo getBoxTop("News", "", false, null);
?>
						  <table class="layouttable">
						      <tr class="row_newsheader">
						         <td><?


		echo "<b>$date - $title</b>&nbsp;&nbsp;<span class='grey-small'>(posted by $user)</span>";
?></td>
						      </tr>
						      <tr>
						         <td><?php echo"$news" ?></td>
						      </tr>
						  </table>
	<? echo getBoxBottom() ?>
</td></tr></table>
<?

	} else {
		if (!empty ($_GET['edit'])) {
			$edit = mysql_real_escape_string($_GET['edit']);
		} else {
			die("<p>No news id submitted, can't save the changes.</p>" . $index);
		}
		$sortby = "news_id DESC";
		$start = "0";
		$finish = "1";
		$sql = "SELECT * FROM $newstable WHERE news_id = '$edit' ORDER BY $sortby LIMIT $start, $finish";
		$result = mysql_query($sql, $db);
		$row = mysql_fetch_array($result);
		$newsold = $row["news"];
		$dateold = $row["date"];
		$titleold = $row["title"];
?>
<table width="50%"><tr><td>
<?= getBoxTop("Edit Entry ".$edit, "", false, null) ?>
<form name="form1" method="post" action="edit.php?submit=1">
<table class="formtable">
<tr>
<td>Date</td>
</tr>
<tr>
<td><input type="Text" class="width100" size="10" name="date" value="<?php echo "$dateold" ?>"></td>
</tr>
<tr>
<td>Title</td>
</tr>
<tr>
<td><input type="Text" class="width250" size="45" name="titlenews" value="<?php echo "$titleold" ?>"></td>
</tr>
<tr>
<td>Text</td>
</tr>
<tr>
<td><textarea name="news" cols="60" rows="12" wrap="VIRTUAL"><?php echo "$newsold" ?></textarea></td>
</tr>
<tr>
	<td>
		Use the &lt;pl&gt; tag to create player links, eg. <b>&lt;pl&gt;</b>Ike<b>&lt;/pl&gt;</b> will come out as <?= getPlayerLink("Ike") ?>. 
	</td>
</tr>
<tr><td>
<table class="smileytable">
<tr>
	<td style="padding-left:10px" align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/smile.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/sad.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/biggrin.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/cry.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/none.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/mad.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/rolleyes.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/laugh.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/bigrazz.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/dead.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/wink.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/bigeek.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/cool.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/no.gif" width="15" height="15"></td>
	<td align="center"><img border="0" src="<?php echo "$directory" ?>/smileys/yes.gif" width="15" height="15"></td>
</tr>
<tr>
	<td style="padding-left:10px"  align="center">:)</td>
	<td align="center">:(</td>
	<td align="center">:d</td>
	<td align="center">:'(</td>
	<td align="center">:s</td>
	<td align="center">:@</td>
	<td align="center">:r</td>
	<td align="center">:h</td>
	<td align="center">:p</td>
	<td align="center">:x</td>
	<td align="center">;)</td>
	<td align="center">:o</td>
	<td align="center">:b</td>
	<td align="center">(n)</td>
	<td align="center">(y)</td>
</tr>
</table>
</td></tr>
<tr><td class="padding-button">
	<input type='hidden' name='edit' value="<?php echo "$edit" ?>">
	<input type="Submit" class="width150" name="submit" value="save changes">
</td></tr>
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

require ('./../../bottom.php');
?>
