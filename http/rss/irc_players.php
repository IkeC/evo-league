<?php
require('../variables.php');
require('../variablesdb.php');
require('../functions.php');
include("feedcreator.class.php");
$br = "<br />";

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = $leaguename.".com Players";
$rss->description = "The latest players on $leaguename!";
$rss->link = $directory;
$rss->syndicationURL = $directory.$PHP_SELF;

$sql = "select * from $playerstable where approved = 'yes' order by player_id desc limit 0, 3";

$res = mysql_query($sql);

while ($data = mysql_fetch_object($res)) {
    $id = $data->player_id;
    $location = $data->country;
    $name = $data->name;
    $alias = $data->alias;
    $titleRSS = "Player :: ".$name;
    if (!empty($alias)) {
    	$nameDisplay = $name. " (aka $alias)";
    } else {
    	$nameDisplay = $name;
    }
	$description = $nameDisplay." from ".$location." just joined ".$leaguename."! Games: ".GetVersionsNames($data->versions);
    $item = new FeedItem();
    $item->title = $titleRSS;
    $item->link = $directory."/player.php#".$data->player_id;
    $item->description = $description;
    $item->date = date('Y-m-d\TH:m:s\Z', $data->joindate);
    $item->source = "http://www.yoursite";
    $item->author = "yoursite";
    $rss->addItem($item);
}

$rss->saveFeed("RSS1.0", "feed_irc_players.xml"); 

?>