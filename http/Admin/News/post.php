<?PHP


// displays the form to post news

$page = "post";

require ('./../../variables.php');
require ('./../../variablesdb.php');
require ('./../../functions.php');
require ('./../functions.php');
require ('./../../top.php');

?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Post News", "") ?>
<?


if (!$isAdminFull) {
    echo "<p>Access denied.</p>";
} else {
	if (isset ($_GET['submit'])) {
		$submit = mysql_real_escape_string($_GET['submit']);
	} else {
		$submit = 0;
	}
	if ($submit) {
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
		
		$sql = "INSERT INTO $newstable (user, date, title, news) " .
		"VALUES ('$username', '$date', '$titlenews', '$news')";
		$result = mysql_query($sql);

		$sql = "SELECT * FROM $newstable ORDER BY news_id DESC LIMIT 0, 1";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$newsid = $row['news_id'];
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

		echo '<p>News #' . $newsid . ' posted | <a href="edit.php?edit=' . $newsid . '">Edit again</a> | <a href="view.php">News Index</a></p>';
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
?>
<table width="50%"><tr><td>
<?= getBoxTop("Post News", "", false, null) ?>
<? $newsDate = formatNewsDate(time()); ?>
<form name="form1" method="post" action="post.php?submit=1">
<table class="formtable">
<tr>
<td>Date</td>
</tr>
<tr>
<td><input type="Text" class="width100" size="10" name="date" value="<?php echo "$newsDate" ?>"></td>
</tr>
<tr>
<td>Title</td>
</tr>
<tr>
<td><input type="Text" class="width250" size="45" name="titlenews" value="<?php echo "$titlenews" ?>"></td>
</tr>
<tr>
<td>Text</td>
</tr>
<tr>
<td><textarea name="news" cols="60" rows="12" wrap="VIRTUAL"><?php echo "$news" ?></textarea></td>
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
	<input type="Submit" class="width150" name="submit" value="post news">
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
