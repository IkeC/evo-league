<?php

// the rules and information page

header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "credits";
$subpage = "credits";

require ('variables.php');
require ('variablesdb.php');
require ('functions.php');
require ('top.php');
?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
			
			<table class="layouttable">
				<tr>
					<td width="60%" valign="top">
						<!-- info -->

	<? echo getBoxTop("Credits", "", false, null); ?>
  <p>The code used to run <?= $leaguename ?> is based on the evo-league ladder system by Ike. It is freely available <a href="https://github.com/IkeC">here</a>.</p>
  <p>Parts of the code are based on the WebLeague module written by Peter Hendrix.</p>
  <p>Site design is based on the Morpheus skin for phpPP created by Vjacheslav Trushkin.</p>
	<?= getBoxBottom() ?>
</td>
</tr>
</table>
<?= getOuterBoxBottom() ?>

<?php

require ('bottom.php');
?>


