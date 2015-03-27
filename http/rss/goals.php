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
	
	if ($rating > 0) {
		if ($votes > 1) {
			$s = "s";
		} else {
			$s = "";
		}
		$rateText = "<p>Rated <b>".format2DigitsMax($rating)."</b> of 5 from <b>".$votes."</b> vote$s</p>";
	} else {
		$rateText = "<p>(not rated yet)</p>";
	}
	$fileNameNoExt = $player_id."_".$uploaded; 
	$fileName = $fileNameNoExt.'.'.$extension;
	$fileUrl = 'files/goals/' . $fileName;	
	$imgSrcStatic = '/files/goals/thumbnails/' . $fileNameNoExt.'_thumb.png';
	
	if (file_exists($wwwroot.$imgSrcStatic)) {
		$imgSrc = '<img height="96" width="128" src="'.$directory.$imgSrcStatic.'" border="1">';
	} else {
		$imgSrc = '<img height="96" width="128" src="'.$directory.'/gfx/thumb_missing.gif" border="1">';
	}

	$fileLink = '<a href="' . $fileUrl . '">' . $fileName . '</a>';
	$typeDesc = getTextForGoalType($type);
	
	$description = "<p>$typeDesc by <b>" . $name . '</b> - <a href="'.$directory."/".$fileUrl.'">Download</a></p>'.
		'<p>'.$comment.'</p>'.
		'<p>'.$imgSrc.'</p>'.
		$rateText;
		
	
	if (strlen($fileName) > 4) {
		$titleRSS = $typeDesc." :: " . $name . "  (" . formatLongDate($uploaded) . ")";
		$item = new FeedItem();
		$item->title = $titleRSS;
		$item->link = $directory."/files/browseGoals.php?goal=" . $goal_id;
		$item->description = $description;
		$item->date = date('Y-m-d\TH:m:s\Z', $uploaded);
		$item->source = $directory."/".$fileUrl;
		$item->author = $name;
		$item->editor = $name;
		$rss->addItem($item);
	}
}

$rss->saveFeed("RSS1.0", "feed_goals.xml");

function getTextForGoalType($type) {
	if ($type == 'A') return "Goal";
	if ($type == 'C') return "Compilation";
	if ($type == 'M') return "Game Scene";
	if ($type == 'O') return "Other";
}
?>