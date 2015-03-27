<?php
header("Expires: Sat, 05 Nov 2005 00:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: text/xml; charset=UTF-8");

$linesWanted = 20;
$linesDisplayed = 0;

$xml = '<?xml version="1.0" ?>';
$xml .= '<root>';

$timeDay = time();

while ($linesDisplayed < $linesWanted) {
  $dateString = date('Y-m-d', $timeDay);
  $sourceFile = "/opt/eggdrop/logs/".$leaguename.".log.".$dateString;
  $file = @file($sourceFile);
  for ($i = count($file) - 1; ($i >= 0) && ($linesDisplayed < $linesWanted); $i--) {
    $line = $file[$i];
    $message = htmlspecialchars($line);	   
    if (!strstr($line,"joined #".$leaguename) 
        && !strstr($line,"left #".$leaguename) 
        && !strstr($line,"left irc")
        && !strstr($line,"Last message repeated")
        && !strstr($line,"mode change")
        && !strstr($line,"got netsplit")
        && !strstr($line,"got lost in the net-split")
        && !strstr($line,"returned to #".$leaguename)
        && !strstr($line,"Nick change")
        && !strstr($line,"PES ladder position")
        //&& !strstr($line,"00:00:00")
        ) {
      $xml .= '<message><![CDATA[' . stripControlCharacters($message). ']]></message>';
      $linesDisplayed++;
    }
  }
  $timeDay = $timeDay-60*60*24;
}

$xml .= '</root>';
echo $xml;

function stripControlCharacters($text) {
    $controlCodes = array(
        '/(\x03(?:\d{1,2}(?:,\d{1,2})?)?)/',    // Color code
        '/\x02/',                               // Bold
        '/\x0F/',                               // Escaped
        '/\x16/',                               // Italic
        '/\x1F/'                                // Underline
    );
    $text = preg_replace($controlCodes,'',$text);
	return preg_replace('/[\x00-\x1f]/', '', $text);
}
?>
