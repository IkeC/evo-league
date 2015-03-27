<?php

// shows players table and search. only available for logged in users.
// number of players per page is defined in weblm_vars.numplayerspage

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "players";
$subpage = "players";

$approvedYes = "approved = 'yes' ";
$whereclause = "";

$checked = "checked='checked'";
$sortby = "player_id DESC";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('top.php');

$playername = "";
$finishnumber = "";

if (! empty($_GET['startplayers'])) {
    $startplayers = mysql_real_escape_string($_GET['startplayers']);
} else {
    $startplayers = 0;
} 

if (!empty($_POST['playername'])) {
    $playername = mysql_real_escape_string($_POST['playername']);
    if (!empty($whereclause)) {
        $and = "AND";
    } else {
        $and = "WHERE";
    } 
    $whereclause = "$and (name like '%$playername%' or alias like '%$playername%' or msn like '%$playername%') ";
} 

if (!empty($_POST['groupname'])) {
    $groupname = mysql_real_escape_string($_POST['groupname']);
    if (!empty($whereclause)) {
        $and = "AND";
    } else {
        $and = "WHERE";
    } 
    $whereclause = "$and forum like '%$groupname%' ";
} 

if (!empty($_POST['nationality'])) {
    $nationality = mysql_real_escape_string($_POST['nationality']);
    if (!empty($whereclause)) {
        $and = "AND";
    } else {
        $and = "WHERE";
    } 
    $whereclause = $whereclause . "$and nationality = '$nationality' ";
} 


if (isset($_GET['selectversion'])) {
  $selectversion = mysql_real_escape_string($_GET['selectversion']);
} elseif (isset($_POST['selectversion'])) {
  $selectversion = mysql_real_escape_string($_POST['selectversion']);
} else {
  $selectversion = 'H';
}
$versionsGetString = "&amp;selectversion=".$selectversion;
if (!empty($whereclause)) {
    $whereclause .= "AND";
} else {
    $whereclause = "WHERE";
} 

$whereclause .= " versions LIKE '%".$selectversion."%' ";


if (!empty($whereclause)) {
    $whereclause = $whereclause . "AND $approvedYes ";
} else {
    $whereclause = "WHERE $approvedYes ";
} 

?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>

<?php
if ($cookie_name == 'null') {
    echo $membersonly;
} else {

?>
<table width="1100">
 <tr><td>
 <table class="formtable"><tr>
 <td>
 <form method="post" name="formPlayersearch" action="<?php echo"$directory"?>/players.php">
 <span style="vertical-align:middle">Name/Alias/MSN&nbsp;&nbsp;</span><input type="text" 
title="partitial match on name, alias or MSN" class="width100" name="playername" value="<?php echo $playername ?>" /><span style="vertical-align:middle">&nbsp;&nbsp;Group&nbsp;&nbsp;</span><input type="text" 
title="" class="width100" name="groupname" value="<?php echo $groupname ?>" />
<span style="vertical-align:middle">&nbsp;Nationality&nbsp;&nbsp;</span><select name="nationality" class="width150">
	 <option></option> 
	 <option>No country</option>
	<?php
    $sql = "SELECT country FROM $countriestable ORDER BY COUNTRY ASC";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
    	$row_country = $row['country'];	
    	if ($row_country == $nationality) {
    		$selected = "selected='selected'";
    	} else {
    		$selected = "";
    	}
        echo "<option $selected>$row_country</option>";
    }
    ?>
		</select><span style="vertical-align:middle">&nbsp;&nbsp;Game&nbsp;&nbsp;</span>
		
		<?= getSelectboxForAllVersionsInput($selectversion, 'selectversion') ?>
		
		&nbsp;&nbsp;<input title="find players" type="Submit" class="width100" name="submit" value="find" />
	</form>
  </td>
 </tr>
 
<?php
   
        $sql = "SELECT * FROM $playerstable $whereclause ORDER BY $sortby LIMIT 0,200";
        $result = mysql_query($sql);
        $yo = mysql_num_rows($result);
        if ($yo == 0) {
            echo "<tr><td><p>No matches found.</p></td></tr></table>";
        } 
		else {
        
        ?>
	</table>
  <br>
	<? $columnsArray = array("ID", "Joined", "Nation", "Location", "Name", "Alias", "Games", "ICQ", "MSN", "AIM", "Group"); ?>
	<?= getRankBoxTop("Players", $columnsArray); ?>
    <?

        if (! empty($_GET['finishplayers'])) {
            $finishplayers = mysql_real_escape_string($_GET['finishplayers']);
        } else {
            $finishplayers = $finishnumber;
        } 
        $sql = "SELECT * FROM $playerstable $whereclause ORDER BY $sortby LIMIT 0,200";
        if ($paging) {
            $sql = $sql . " LIMIT $startplayers, $finishplayers";
        } 
        $result = mysql_query($sql);
        $num = mysql_num_rows($result);
        $cur = 1;
        while ($num >= $cur) {
            $row = mysql_fetch_array($result);
            $name = $row["name"];
            $approved = $row["approved"];
            $id = $row['player_id'];
            $nameClass = colorNameClass($name, $approved);

            $alias = $row["alias"];
            $forum = $row["forum"];
            $versions = $row["versions"];
            $joined = FormatDate($row["joindate"]);
            
            if ($forum == "[none]") {
                $forum = "";
            } 

            $mail = $row["mail"];
            if ($mail == "n/a" || empty($mail)) {
                $mailaddress = "n/a";
                $mailpic = "";
            } 
			else {
                $mailaddress = "
	        <a href='mailto:$mail'>
	        <font color='$color1'>
	        mail
	        </font>
	        </a>
	        ";
                $mailpic = "
	        <img border='1' src='$directory/gfx/mail.gif' align='absmiddle'>
	        </a>
	        ";
            } 
            $icq = $row["icq"];
            if ($icq == "n/a" || empty($icq)) {
                $icqnumber = "n/a";
                $icqpic = "";
            } 
			else {
                $icqnumber = "
	        <a href='http://web.icq.com/whitepages/add_me?uin=$icq&action=add'>
	        <font color='$color1'>
	        add
	        </font>
	        </a>
	        ";
                $icqpic = "
	        <img src='$directory/gfx/icq.gif' border='1' align='absmiddle'>
	        </a>
	        ";
            } 
            $aim = $row["aim"];
            if ($aim == "n/a" || empty($aim)) {
                $aimname = "n/a";
                $aimpic = "";
            } 
			else {
                $aimname = "
	        <a href='aim:AddBuddy?ScreenName=$aim'>
	        <font color='$color1'>
	        add
	        </font>
	        </a>
	        ";
                $aimpic = "
	        <img border='1' src='$directory/gfx/aim.gif' align='absmiddle'>
	        </a>
	        ";
            } 
            $msn = $row["msn"];
            if ($msn == "n/a" || empty($msn)) {
                $msnname = "n/a";
                $msnpic = "";
            } else {
                $msnname = "
	        <a href='mailto:$msn'>
	        <font color='$color1'>
	        mail
	        </font>
	        </a>
	        ";
                $msnpic = "
	        <img border='1' src='$directory/gfx/msn.gif' align='absmiddle'>
	        </a>
	        ";
            } 
            $nationality = $row["nationality"];
            $country = $row['country'];
            ?>

	        <tr class="row">
	          <td style="text-align:center" width="9%" nowrap>
              #<?= $id ?>
            </td>
	          <td style="text-align:center" width="9%" nowrap>
              <?= $joined ?>
            </td>
            <td style="text-align:center" width="9%">
	            <?= "<img src='$directory/flags/$nationality.bmp' align='absmiddle' border='1' title='$nationality'>" ?>
	          </td>

	          <td style="text-align:center" width="9%">
	            <?= "<img src='$directory/flags/$country.bmp' align='absmiddle' border='1' title='$country'>" ?>
	          </td>
		  
              <td width="15%" class="<?= $nameClass ?>" nowrap>
	            <?php echo "<a href='$directory/profile.php?name=$name'>$name</a>" ?>
	          </td>

	          <td width="15%" nowrap>
	            <?php echo "$alias" ?>
	          </td>
	         
			  <td width="10%" nowrap>
	            <?= getVersionsImages($versions) ?>
	          </td>
			  
	          <td width="10%" nowrap>
	            <?php 
	            if ($cookie_name == 'null') {
	            	echo '<span style="color:#AAAAAA;">Hidden</span>';
	            }
	            else {
	            	echo "$icqpic $icqnumber";
	            }
	            ?>
	          </td>

	          <td width="10%" nowrap>
	            <?php 
	            if ($cookie_name == 'null') {
	            	echo '<span style="color:#AAAAAA;">Hidden</span>';
	            }
	            else {
	            	echo "$msnpic $msnname";
	            }
	            ?>
	          </td>

	          <td width="10%" nowrap>
	            <?php 
	            if ($cookie_name == 'null') {
	            	echo '<span style="color:#AAAAAA;">Hidden</span>';
	            }
	            else {
	            	echo "$aimpic $aimname";
	            }
	            ?>
	          </td>

	          <td width="12%" nowrap>
	            <?php echo "$forum" ?>
	          </td>

	        </tr>
<?
            $cur++;
        } 
?>
		<?= getRankBoxBottom() ?>

    <?php } // end no matches

    ?>

</td>
</tr></table>

<?php

} // end membersonly
?>
<?= getOuterBoxBottom() ?>
<? 
require('bottom.php');
?>
