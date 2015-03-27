<?
require ('../variables.php');
require ('../variablesdb.php');
require ('../functions.php');
require ('../files/functions.php');

include ("feedcreator.class.php");

$br = "<br />";

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = $leaguename . ".com Goals";
$rss->description = "The latest uploaded player videos from $leaguename!";
$rss->link = $directory."/files/browseGoals.php";
$rss->syndicationURL = $directory . $PHP_SELF;


$sql = "SELECT g.*, p.name FROM $goalstable g ". 
	"LEFT JOIN $playerstable p on g.player_id = p.player_id ".
	"ORDER BY uploaded DESC LIMIT 0, 20";

$result = mysql_query($sql);
 
while ($row = mysql_fetch_array($result)) {
	$goal_id = $row['id'];
	$player_id = $row['player_id'];
	$type = $row['type']; 
	$uploaded = $row['uploaded'];
	$extension = $row['extension'];
	$comment = $row['comment'];
	$name = $row['name'];
	$rating = $row['rating'];
	$votes = $row['votes'];
	
	$fileUrl = $directory.'/files/browseGoals.php?goal=' . $goal_id;	
	$description = "($name) $comment ($fileUrl)";

	if (strlen($uploaded) > 4) {
		$titleRSS = $name . "  (" . formatLongDate($uploaded) . ")";
		$item = new FeedItem();
		$item->title = $titleRSS;
		$item->link = $fileUrl;
		$item->description = $description;
		$item->date = date('Y-m-d\TH:m:s\Z', $uploaded);
		$item->source = $fileUrl;
		$item->author = $name;
		$item->editor = $name;
		$rss->addItem($item);
	}
}

$rss->saveFeed("RSS1.0", "feed_irc_videos.xml");

?>