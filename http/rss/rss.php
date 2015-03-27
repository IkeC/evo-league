<?php

require('../variables.php');
require('../variablesdb.php');
require('../functions.php');

include("feedcreator.class.php");
$br = "<br />";

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = $leaguename.".com News";
$rss->description = "The latest info from $leaguename!";
$rss->link = $directory;
$rss->syndicationURL = $directory.$PHP_SELF;

/*

$image = new FeedImage();
$image->title = "dailyphp.net logo";
$image->url = "http://www.dailyphp.net/images/logo.gif";
$image->link = "http://www.dailyphp.net";
$image->description = "Feed provided by dailyphp.net. Click to visit.";
$rss->image = $image;

*/

$sql = "SELECT * FROM $newstable ORDER BY news_id DESC LIMIT 0, 1";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$news = $row["news"];
	$news = nl2br($news);
	$news = SmileyConvert($news, $directory);
	$date = $row["date"];
	$title = $row["title"];
	$user = $row["user"];
	
	$titleRSS = "News :: ".$title;
	$description = $news;
    $item = new FeedItem();
    $item->title = $titleRSS;
    $item->link = $directory."/news.php";
    $item->description = $description;
    $item->date = date('Y-m-d\TH:m:s\Z');
    $item->source = "http://www.yoursite";
    $item->author = "Ike";
    
    $rss->addItem($item);
	
$res = mysql_query("SELECT * FROM $gamestable where deleted='no' ORDER BY game_id DESC LIMIT 0,19");
while ($data = mysql_fetch_object($res)) {
    
    $winner = $data->winner;
    $winner2 = $data->winner2;
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
	$description = "<p>Game played: ".date("m/d/Y h:i a", $data->date).$br.
		"Ladder: ".$ladder."</p>".
    	 "<p>".$winnerdisplay." team: ".$winnerteam." (won ".$data->winpoints." points)".$br.
    	 $loserdisplay." team: ".$loserteam." (lost ".$losepointsdisplay." points)</p>".
    	 "<p>".$data->winner."'s comment: ".$br.$data->comment."</p>";
    $item = new FeedItem();
    $item->title = $titleRSS;
    $item->link = $directory."/games.php#".$data->game_id;
    $item->description = $description;
    $item->date = date('Y-m-d\TH:m:s\Z', $data->date);
    $item->source = "http://www.yoursite";
    $item->author = "yoursite";
    $rss->addItem($item);
}

$rss->saveFeed("RSS1.0", "feed.xml"); 

?>