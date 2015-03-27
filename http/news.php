<?php

// the news page showing all news entries

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "info";
$subpage = "news";

require('./variables.php');
require('./variablesdb.php');
require('./functions.php');
require('./top.php');
require('./style_rules.php');
?>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>

<table class="layouttable">
<tr>
<td width="60%">

<!-- big block content -->
<?php
    $sortby = "news_id DESC";
    $sql="SELECT * FROM $newstable ORDER BY $sortby";
    $result=mysql_query($sql,$db);
    $num = mysql_num_rows($result);
    $cur = 1;
    ?>
    <?
    while ($num >= $cur) {
        $row = mysql_fetch_array($result);
        $news = $row["news"];
        $news = nl2br($news);
        $news = SmileyConvert($news,$directory);
        $date = $row["date"];
        $title = $row["title"];
        $id = $row["news_id"];
        $user = $row["user"];
        ?>
		<a name="<?= $id ?>"></a>
		<?= getBoxTop($title, "", false, null); ?>
        <table class="layouttable">
        <tr class="row_newsheader">
		    <td><b><?php echo "$date - $title" ?></b>&nbsp;&nbsp;<span class="grey-small">(posted by <?= $user ?>)</span></td>
        </tr>
        <tr>
         <td><?php echo "$news" ?></td>
        </tr>
        </table>
		<?= getBoxBottom() ?>
        <?php
        $cur++;
    }
    ?>
</td>
<td width="40%" valign="top">
    <?php
    $sortby = "news_id DESC";
    $sql="SELECT * FROM $newstable ORDER BY $sortby";
    $result=mysql_query($sql);
    $num = mysql_num_rows($result);
    $cur = 1;
    ?>
	<?= getBoxTop("News Index", "", false, null); ?>
    <table>
    <?
    while ($num >= $cur) {
        $row = mysql_fetch_array($result);
        $date = $row["date"];
        $title = $row["title"];
        $id = $row["news_id"];
        ?>
         <tr>
            <td><?php echo"<a href='#$id'><b>$date</b></a>" ?></td>
            <td><?php echo"<a href='#$id'>$title</a>" ?></td>
        </tr>
        <?php
        $cur++;
    }
    ?> 
    </table>
	<?= getBoxBottom() ?>
</td>
</tr>
</table>

<?= getOuterBoxBottom() ?>
<?php
require('bottom.php');
?>


