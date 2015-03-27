<?php

$page = "playerfind";
$subpage = "playerfind";

$approved = "approved = 'yes' ";
$whereclause = "";
$paging = true;
$checked = "checked='checked'";
$sortby = "name ASC";

require('variables.php');
require('variablesdb.php');
require('functions.php');
require('top.php');
?>
<script type="text/javascript">
<!-- 
	function goTo(path, get) {
		name = document.formQuickfind.playernameTxt.value;
    if (name.indexOf(')', name.length - 1) !== -1) {
      if (name.lastIndexOf('(') > 0) {
        name = name.substr(0, name.lastIndexOf('(')-1);
      }
    }
    document.location.href= path + '.php?' + get + '=' + name;
	}
-->
</script>
<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table width="95%">
 <tr><td>
	<table width="60%"><tr><td> 	
 	<?= getBoxTop("Quickfinder", "", false, null) ?>
 	<form name="formQuickfind" action="javascript:goTo('profile', 'name')" method="POST">
 	<table class="formtable">
    <tr>
	    <td width="100">Player name</td>
	    <td>
	    	<input class="width300" type="text" id="playernameTxt" name="playernameTxt" value="" autocomplete="off"><br>
		    <div id="playernameDiv" class="autocomplete"></div>
		    <script type="text/javascript" language="javascript" charset="utf-8">
		    // <![CDATA[
				new Autocompleter.Local('playernameTxt', 'playernameDiv', new Array(<?= getPlayersAllJavascript() ?>), { choices: 25, tokens: new Array(';','\n'), fullSearch: true, partialSearch: true});
		    // ]]>
		    </script>
		</td>
		<td></td>
	</tr>
    <tr>
	    <td colspan="3" class="padding-button"><input type="button" class="width70" name="submit" onClick="javascript:goTo('profile', 'name')" value="Profile">
	    <input type="button" class="width70" name="submit" onClick="javascript:goTo('games', 'player')" value="Games">
	    <input type="button" class="width70" name="submit" onClick="javascript:goTo('/player/history', 'name')" value="History">
	    <input type="button" class="width70" name="submit" onClick="javascript:goTo('/player/teamstats', 'name')" value="Teams">
	    <input type="button" class="width70" name="submit" onClick="javascript:goTo('/player/streaks', 'name')" value="Streaks">
	    <input type="button" class="width70" name="submit" onClick="javascript:goTo('/player/trends', 'name')" value="Trends">
	    </td>
    </tr>
	</table>
	</form>
	<?= getBoxBottom() ?>
	</td></tr></table>
	</td>
 </tr>
</table>

<?= getOuterBoxBottom() ?>
<? 
require('bottom.php');
?>
