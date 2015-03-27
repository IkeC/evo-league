<?php

// the rules and information page

header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "games";
$subpage = "categories";

require ('./variables.php');
require ('./variablesdb.php');
require ('./functions.php');
require ('./top.php');

$teamsInstance = getTeamsArray();
$catGlobal = getCategoriesArray();
?>

<?= getOuterBoxTop($subNavText.getRaquo().getSubNavigation($subpage, null), "") ?>
<table>
<?
	foreach ($catGlobal as $category => $catCurrent) {
		// $category = key($catGlobal);
		// echo  "<!-- cat: ".$category." -->";
		$teams = array();
		foreach ($teamsInstance as $rowArray) {
			if ($rowArray[3] == $category) {
				// put scope, id, name
				$teams[] = array($rowArray[2], $rowArray[0], $rowArray[1], $rowArray[5], $rowArray[4]);  
				// echo "<!--".$rowArray[2].".". $rowArray[0].".". $rowArray[1].".". $rowArray[4].".".$rowArray[5]."-->";
			}
		}
		
		if (sizeof($teams) > 0) {
			// asort($teams);
			
	?>
		<tr>
			<td style="width:600px;vertical-align:top;">
			<? // teams list
			
			
	
			$columnTitlesArray = array ('', 'Abr.','Team','Country','Scope');
			if ($category != 0) { 
				$boxTitle = "Category ".$category." - Teams";		
			} else {
				$boxTitle = "Uncategorised Teams";
			}
			?>
			<?= getRankBoxTop($boxTitle, $columnTitlesArray); ?>
	
			<? // rank differences
				foreach ($teams as $teamArray) { ?>
				<tr class="row">
					<td width="50" align="center" title="#<?= $teamArray[1] ?>"><?= getImgForTeam($teamArray[1]) ?></td>
					<td width="50"><?= $teamArray[4] ?></td>
          <td width="200"><?= $teamArray[2] ?></td>
					<td width="100"><?= $teamArray[3] ?></td>
					<td width="150"><?= getTeamScopeName($teamArray[0]) ?></td>
				</tr>
			<?
				}
			?>
			<?= getRankBoxBottom(); ?>
			</td>
			<td style="vertical-align:top;">
			<? 
			
			if ($category == 1) { 
				$columnTitlesArray = array ('Winner Team Category', 'Loser Team Category', 'Winner Bonus');
				$boxTitle = "Bonus";
				?>
				<?= getRankBoxTop($boxTitle, $columnTitlesArray); ?>
		
		 		<tr class="row"><td>2</td><td>1</td><td>+20%</td></tr>
		 		<tr class="row"><td>3</td><td>1</td><td>+40%</td></tr>
		 		<tr class="row"><td>3</td><td>2</td><td>+20%</td></tr>
		 		<tr class="row"><td>4</td><td>1</td><td>+60%</td></tr>
		 		<tr class="row"><td>4</td><td>2</td><td>+40%</td></tr>
		 		<tr class="row"><td>4</td><td>3</td><td>+20%</td></tr>
		 		<tr class="row"><td>5</td><td>1</td><td>+80%</td></tr>
		 		<tr class="row"><td>5</td><td>2</td><td>+60%</td></tr>
		 		<tr class="row"><td>5</td><td>3</td><td>+40%</td></tr>
		 		<tr class="row"><td>5</td><td>4</td><td>+20%</td></tr>

				<?= getRankBoxBottom(); ?>
				<? 
			}
			?>
			</td>
		</tr>
	<?
			next($catGlobal);
		}
	}
?>
</table>
<?= getOuterBoxBottom() ?>

<? require ('bottom.php'); ?>


