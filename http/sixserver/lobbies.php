<?php
$page = "index";
$subpage = "lobbies";

require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../top.php');

$left = $subNavText.getRaquo().getSubNavigation($subpage, null);
?>

<?= getOuterBoxTop($left, "") ?>
<table><tr><td width="900">
<?php 

$xml_query = "http://127.0.0.1:8192/stats";
$xml = simplexml_load_file($xml_query);

foreach( $xml->lobbies->lobby as $lobby ) {
	echo getBoxTop($lobby->attributes()->name, "", false, "");
	
	$users = array();
	foreach( $lobby->user as $user) {
		$users[] = (string) $user->attributes()->profile;
	}
	natcasesort($users);
  
  echo "<b>".sizeof($users)."</b> ";
	if (sizeof($users) == 0) {
		echo "users";
	} elseif (sizeof($users) == 1) {
		echo "user: ".getCommaSeparatedString($users);
	} elseif (sizeof($users) > 1) {
		echo "users: ".getCommaSeparatedString($users);
	}
	foreach ($lobby->matches as $matches) {
    foreach ($matches->match as $match) {
      echo "<p>";
      echo "Match: <b>".$match->attributes()->roomName."</b> - ".$match->attributes()->state." - Minute ".$match->attributes()->clock;
      echo " - ";
      $homePlayers = "";
      foreach ($match->homeTeam as $homeTeam) {
        if (strlen($homePlayers) > 0) {
          $homePlayers = $homePlayers."/".$homeTeam->profile->attributes()->name;
        } else {
          $homePlayers = $homeTeam->profile->attributes()->name;
        }
      }
      $awayPlayers = "";
      foreach ($match->awayTeam as $awayTeam) {
        if (strlen($awayPlayers) > 0) {
          $awayPlayers = $awayPlayers."/".$awayTeam->profile->attributes()->name;
        } else {
          $awayPlayers = $awayTeam->profile->attributes()->name;
        }
      }
      echo $homePlayers." <b>".$match->attributes()->score."</b> ".$awayPlayers;
      echo "</p>";
    }
	}
	echo getBoxBottom();
}

?>
</td></tr></table>
<?= getOuterBoxBottom() ?>
<?php
	require ('../bottom.php');
?>

