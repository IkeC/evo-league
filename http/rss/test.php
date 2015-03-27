<?php
require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
include("feedcreator.class.php");
$br = "<br />";

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = $leaguename.".com Games";
$rss->description = "The latest games from $leaguename!";
$rss->link = $directory;
$rss->syndicationURL = $directory.$PHP_SELF;

$sql = "SELECT g.*, t.abbreviation as winabb, u.abbreviation as loseabb FROM $gamestable g ". 
	"left join $teamstable t on g.winnerteam = t.id ". 
	"left join $teamstable u on g.loserteam = u.id ". 
	"where g.deleted='no' ORDER BY g.game_id DESC LIMIT 0,20";

$res = mysql_query($sql);

while ($data = mysql_fetch_object($res)) {
    
    $winner = $data->winner;
    $winner2 = $data->winner2;
    $winabb = $data->winabb;
    $loseabb = $data->loseabb;
    if (strlen($winner2) > 0) {
    	$winnerdisplay = $winner."/".$winner2;
    } else {
    	$winnerdisplay = $winner;
    }

	$loser = $data->loser;
	$loser2 = $data->loser2;
	$losepoints = $data->losepoints;

    if (strlen($loser2) > 0) {
    	$loserdisplay = $loser."/".$loser2;
		$losepoints2 = $data->losepoints2;
		$losepointsdisplay = $losepoints."/".$losepoints2;
    } else {
    	$loserdisplay = $loser;
    	$losepointsdisplay = $losepoints;
    }
	
    $titleRSS = "Game :: ".$winnerdisplay."  ".$data->winnerresult." - ".$data->loserresult."  ".$loserdisplay;
    $winnerteam = getTeamNameForId($data->winnerteam);
    if (empty($winnerteam)) {
    	$winnerteam = "Not specified";
    }
    $loserteam = getTeamNameForId($data->loserteam);
    if (empty($loserteam)) {
    	$loserteam = "Not specified";
    }
	$ladder = getLadderForVersion($data->version);
	$description = 
		 "(+".$data->winpoints.") ".$winnerdisplay." (".$winabb.") ".$data->winnerresult.
		 " - ".$data->loserresult." (".$loseabb.") ".$loserdisplay.
		 " (-".$data->losepoints.") - ".$data->comment."";
    $item = new FeedItem();
    $item->title = $titleRSS;
    $item->link = $directory."/games.php#".$data->game_id;
    $item->description = $description;
    $item->date = date('Y-m-d\TH:m:s\Z', $data->date);
    $item->source = "http://www.yoursite";
    $item->author = "Ike";
    $rss->addItem($item);
}

$rss->saveFeed("RSS1.0", "feed_test.xml"); 

?>