<?PHP

// this displays a form to edit a game.

header("Cache-Control: no-cache");
header("Pragma: no-cache");

$page = "playerstatus";

require('./../variables.php');
require('./../variablesdb.php');
require('./../functions.php');
require('./../top.php');

$back = "<p><a href='javascript:history.back()'>go back</a></p>";
$index = "<p><a href='playerStatus.php'>go to index</a></p>";

$banthread = "";
$msg = "";

?> 

<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Player Status", "") ?>
<?
if (!$isAdminFull && !$isAdminBan) {
    echo "<p>Access denied.</p>";
} else {
	
if (isset($_GET['mode']) && $_GET['mode']=='new') {
if (isset($_GET['name'])) {
	$name = mysql_real_escape_string($_GET['name']);
} else {
	$name = '';
}
if (isset($_GET['reason'])) {
	$reason = mysql_real_escape_string($_GET['reason']);
} else {
	$reason = '';
}


?>
<table width="50%"><tr><td>
<?= getBoxTop("New Entry", "", false, null) ?>
<form method="post" action="playerStatus.php?mode=saveNew">
<table class="formtable">
	<tr>
		<td>Username</td>
		<td>
			<!--
      <input type="text" maxlength="20" class="width150" name="userName" value="<?= $name ?>"/>
      -->
      
      <input class="width300" type="text" id="userName" name="userName" value="" autocomplete="off"><br>
		    <div id="userNameDiv" class="autocomplete"></div>
		    <script type="text/javascript" language="javascript" charset="utf-8">
		    // <![CDATA[
				new Autocompleter.Local('userName', 'userNameDiv', new Array(<?= getPlayersAllJavascript() ?>), { choices: 25, tokens: new Array(';','\n'), fullSearch: true, partialSearch: true});
		    // ]]>
		    </script>
		</td>
	</tr>
	<tr>
		<td>Expire in 
			<span class="grey-small" style="cursor:help;" 
			title="enter number of days after this entry should expire automatically (optional)">(?)</span>
		</td>
		<td>
			<input type="text" maxlength="5" class="width50" name="expireDays" />
		</td>
	</tr>
	<tr>
		<td>Forum link 
			<span class="grey-small" style="cursor:help;" 
			title="supply a full valid link to the forum, or leave empty for default warning/bans thread">(?)</span>
		</td>
		</td>
		<td>
			<input type="text" maxlength="200" class="width400" name="forumLink" />
		</td>
	</tr>
	<tr>
		<td>Reason
			<span class="grey-small" style="cursor:help;" 
			title="a short reason for the ban that can be seen in the profile">(?)</span>
		</td>
		<td>
			<input type="text" maxlength="75" class="width300" name="reason" value="<?= $reason ?>"/>
		</td>
	</tr>
	<tr class="padding-button">
		<td colspan="2">
			<input type="hidden" name="type" value="<?= mysql_real_escape_string($_GET['type']) ?>" />
			<input type="Submit" name="submit" value="create" class="width200">
		</td>
	</tr>
</table>
</form>
<?= getBoxBottom() ?>
</td></tr></table>
<?
}
else if (isset($_GET['mode']) && $_GET['mode']=='saveNew') {
	$userName = mysql_real_escape_string($_POST['userName']);
  $profilesPos = strpos($userName, " (");
  if ($profilesPos > 0) {
    $userName = trim(substr($userName, 0, $profilesPos));
  }
	$sql = "SELECT player_id as userId from $playerstable where name = '$userName'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) != 1) {
		$msg .= '<p>Invalid Username: '.$userName."</p>".$back;
	}
	else {
		$row = mysql_fetch_array($result);
		$userId = $row['userId'];	
		
		$type = strtoupper(mysql_real_escape_string($_POST['type']));
		if ($type != 'W' && $type != 'B') {
			 $msg .= '<p>Invalid type: '.$type."</p>";
		}
		$expireDays = mysql_real_escape_string($_POST['expireDays']);
		$expireDate = "";
		if (!empty($expireDays)) {
			 if (!is_numeric($expireDays)) {
			 	$msg .= '<p>Invalid expire days: '.$expireDays."</p>";
			 }
			 else {
			 	$expireDate = time() + (60*60*24*$expireDays);
			 }
		}
		$forumLink = mysql_real_escape_string($_POST['forumLink']);
		$reason = mysql_real_escape_string($_POST['reason']);
		if (empty($reason)) { 
			$msg .= '<p>Error: No reason supplied</p>';
		}
		
		if (strlen($msg) > 0) {
			$msg .= $back;
		} 
		else {
			$date = time();
			
       $sql = "INSERT INTO $playerstatustable (userId, userName, type, active, date, expireDate, forumLink, reason) ".
				"VALUES ('$userId', '$userName', '$type', 'Y', '$date', '$expireDate', '$forumLink', '$reason')";
			$result = mysql_query($sql);
      if ($result != 1) {
				$msg .= '<p>Error running query: '.$sql.'</p>'.$back;
			}
			else {
				$msg .= '<p>New status entry saved successfully.</p>';
        
        $forumText = "";
        
        if ($type == 'B') {
          $forumText .= ':red:';
        } else {
          $forumText .= ':yellow:';
        }
        
        $forumText .= "\r\n".'Player: [url=http://www.'.$leaguename.'/profile.php?id='.$userId.']'.$userName.'[/url]'."\r\n";
        
        if (!empty($reason)) {
          $forumText .= "Reason: ".$reason."\r\n";
        }
         
         $forumText .= "Length: ";
        if (!empty($expireDays)) {
          if ($expireDays == 1) {
            $forumText .= $expireDays." day";
          }
          else {
            $forumText .= $expireDays." days";
          }
        } else {
          $forumText .= "Permanent";
        }

				if ($type == 'B') { 
					
          $sql = "UPDATE $playerstable SET approved = 'no' ".
						"WHERE player_id = '$userId'";
					$result = mysql_query($sql);
					if ($result != 1) {
						$msg.= "<p>Error setting approved status for user #".$userId.": ".$sql."</p>";
					}
					else {
						$msg .= "<p>Player's approved status set to <b>no</b>.</p>";	
					}	
                    
				}
        
        echo '<p><textarea onClick="javascript:focus();javascript:select();" style="width:600px" rows="4">'.$forumText.'</textarea></p>';
        
				if (!empty($forumLink)) {
          $msg.= '<p><a href="'.$forumLink.'">Forum link</a></p>';
        }
        $msg .= $index;
			}
		}
	}
}
else if (isset($_GET['mode']) && $_GET['mode']=='edit') {
	$id = mysql_real_escape_string($_GET['id']);
	$sql = "SELECT * FROM $playerstatustable WHERE id = '$id'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) != 1) {
		$msg .= '<p>Requested entry #'.$id.' not found.</p>'.$back;
	} 
	else {
	$row = mysql_fetch_array($result);
	
  $expireDate = $row['expireDate'];
  
	if (!empty($expireDate)) {
		$expireDays = ($row['expireDate'] - time())/(60*60*24);
		$expireDaysFormatted = sprintf("%.1f", $expireDays);
	}
	else {
		$expireDaysFormatted = "";
	}
?>
<table width="50%"><tr><td>
<?= getBoxTop("Edit Entry", "", false, null) ?>
<form method="post" action="playerStatus.php?mode=saveEdit">
<table class="formtable">
	<tr>
		<td>Username</td>
		<td>
			<b><?= $row['userName'] ?></b>
		</td>
	</tr>
	<tr>
		<td>Active 
			<span class="grey-small" style="cursor:help;" 
			title="values: Y (active), N (inactive)">(?)</span>
		</td>
		<td>
			<input type="text" maxlength="1" class="width50" name="active" value="<?= $row['active'] ?>" />
		</td>
	</tr>
	<tr>
		<td>Type</td>
		<td>
			<b><?= $row['type'] ?></b>
		</td>
	</tr>
	<tr>
		<td>Expire in 
			<span class="grey-small" style="cursor:help;" 
			title="enter number of days after this entry should expire automatically (optional)">(?)</span>
		</td>
		<td>
			<input type="text" maxlength="5" class="width50" name="expireDays" value="<?= $expireDaysFormatted ?>" />
		</td>
	</tr>
	<tr>
		<td>Forum link 
			<span class="grey-small" style="cursor:help;" 
			title="supply a full valid link to the forum, or leave empty for default warning/bans thread">(?)</span>
		</td>
		</td>
		<td>
			<input type="text" maxlength="200" class="width500" name="forumLink" value="<?= $row['forumLink'] ?>" />
		</td>
	</tr>
	<tr>
		<td>Reason
			<span class="grey-small" style="cursor:help;" 
			title="a short reason for the ban that can be seen in the profile">(?)</span>
		</td>
		<td>
			<input type="text" maxlength="75" class="width300" name="reason" value="<?= $row['reason'] ?>" />
		</td>
	</tr>
	<tr class="padding-button">
		<td colspan="2">
			<input type="hidden" name="id" value="<?= $id ?>" />
			<input type="hidden" name="type" value="<?= $row['type'] ?>" />
			<input type="hidden" name="oldActive" value="<?= $row['active'] ?>" />
			<input type="hidden" name="userId" value="<?= $row['userId'] ?>" />
			<input type="Submit" name="submit" value="save" class="width200">
		</td>
	</tr>
</table>
</form>
<?= getBoxBottom() ?>
</td></tr></table>
<?			
	} // entry not found
?>

<?
}
else if (isset($_GET['mode']) && $_GET['mode']=='saveEdit') {
	$id = mysql_real_escape_string($_POST['id']);
	$type = mysql_real_escape_string($_POST['type']);
	$oldActive = strtoupper(mysql_real_escape_string($_POST['oldActive']));
	$userId = mysql_real_escape_string($_POST['userId']);
	$active = strtoupper(mysql_real_escape_string($_POST['active']));
	if ($active != 'Y' && $active != 'N') {
		$msg .= '<p>Invalid active value: '.$active."</p>";
	}
	$expireDays = $_POST['expireDays'];
	$expireDate = "";
	if (!empty($expireDays)) {
		 if (!is_numeric($expireDays)) {
		 	$msg .= '<p>Invalid expire days: '.$expireDays."</p>";
		 }
		 else {
		 	$expireDate = time() + (60*60*24*$expireDays);
		 }
	}
	$forumLink = mysql_real_escape_string($_POST['forumLink']);
	$reason = mysql_real_escape_string($_POST['reason']);
	if (empty($reason)) {
		$msg .= '<p>Error: No reason supplied</p>';
	}
	
	if (strlen($msg) > 0) {
		$msg .= $back;
	} 
	else {
		$sql = "UPDATE $playerstatustable SET active='$active', expireDate='$expireDate', " .
				"forumLink='$forumLink', reason='$reason' ".
				"WHERE id = '$id'";
		$result = mysql_query($sql);
		if ($result != 1) {
			$msg .= '<p>Error running query: '.$sql."</p>".$back;
		}
		else {
			$msg .= "<p>Update successful.</p>";
	
			if ($type == 'B' && $oldActive != $active) {
				($active == 'Y') ? $approve = 'no' : $approve = 'yes';
				$sql = "UPDATE $playerstable SET approved = '$approve' ".
					"WHERE player_id = '$userId'";
				$result = mysql_query($sql);
				if ($result != 1) {
					$msg .= "<p>Error setting approved status for user #".$userId.": ".$sql."</p>";
				}
				else {
					echo "<p>Player's approved status set to <b>".$approve."</b>.</p>";	
				}		
			}
			$msg .= $index;
		}
	}
}
else { // default

$inactiveMode = ($_GET['mode'] == 'inactive');

if ($inactiveMode) {
	$whereClause = "WHERE type = 'I'";
	$statusLinks[] = array('playerStatus.php?mode=inactive&amp;limit=no', 'show_all_inactive', 'show all inactive');
	$statusLinks[] = array('playerStatus.php', 'show_warnings_bans', 'show warnings and bans');
	if (isset($_GET['limit'])) {
		$limit = "";		
	} 
	else {
		$limit = " LIMIT 0, 200";
	} 
} else {
	$whereClause = "WHERE type != 'I'";
	$statusLinks[] = array('playerStatus.php?mode=inactive', 'show_inactive', 'show inactive');
	$limit = "LIMIT 0, 200";
}

?>
<table class="layouttable"><tr><td width="50%">
<?= getBoxTop("Actions", "", true, $statusLinks) ?>
<table class="layouttable">
	<tr>
		<td width="120">
			<a href="playerStatus.php?mode=new&amp;type=W"><?= getStatusImgForType('W') ?>&nbsp;New Warning</a>
		</td>
		<td>
			<a href="playerStatus.php?mode=new&amp;type=B"><?= getStatusImgForType('B') ?>&nbsp;New Ban</a>
		</td>
	</tr>
</table>	
<?= getBoxBottom() ?>
</td><td width="50%"></td>
</td></tr></table>

<?
	$sql = "SELECT * FROM $playerstatustable $whereClause ORDER BY 'date' DESC, id DESC $limit";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) > 0) {

		$playerStatusArray = array('Player', 'Type', 'Active', 'Date', 'Expires', 'Reason', '');
		?>
		<?= getRankBoxTop("Player Status", $playerStatusArray) ?>
		<?
		
		while ($row = mysql_fetch_array($result)) {
			$expireDate = $row['expireDate'];
			if (!empty($expireDate)) {
				$expireDays = ($expireDate - time())/(60*60*24);
				$expireDaysFormatted = sprintf("%.1f", $expireDays);
			} else {
				$expireDaysFormatted = "";
			}
		?>
			<tr class="row">
				<td><b><a href="../profile.php?name=<?= $row['userName'] ?>"><?= $row['userName'] ?></a></b></td>
				<td><?= getStatusImgForType($row['type']) ?></td>
				<td><?= getImgForActive($row['active']) ?></td>
				<td nowrap><?= formatLongDate($row['date']) ?></td>
				<td><?= $expireDaysFormatted ?></td>
				<td><?= $row['reason'] ?></td>
				<td class="boxlink">
					<? if (!$inactiveMode) { ?>
					<a href="playerStatus.php?mode=edit&id=<?= $row['id'] ?>">[edit]</a>
					<? } ?>
				</td>
			</tr>
		<?
		} // end while
		?>
		<?= getRankBoxBottom() ?>
		<?
	} // rows > 0 
?>
<?
} // mode end
?>
  <?= $msg ?>
<?
}
?>
<?= getOuterBoxBottom() ?>
<?
require('./../bottom.php');
?>
