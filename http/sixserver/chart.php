<?php
header('Content-Type: image/png');

require('../pChart/class/pDraw.class.php');
require('../pChart/class/pImage.class.php');
require('../pChart/class/pData.class.php');
require('../variables.php');
require('../variablesdb.php');

require_once('../log/KLogger.php');
$log = new KLogger('/var/www/yoursite/http/log/general/', KLogger::INFO);	

$type = 1;
if (isset($_GET['type'])) {
  $type = mysql_real_escape_string($_GET['type']);
}
$days = 60;
if (isset($_GET['days'])) {
  $days = mysql_real_escape_string($_GET['days']);
}

$tableName = "six_matches";
$dateField = "played_on";

$explanation = "";
$subtitle = "";

$width = 800;
$height = 200;
if ($type == 4 || $type == 5) {
  $height = 300;
}

$myData = new pData();
$myPicture = new pImage($width,$height,$myData);

$myPicture->Antialias = FALSE;
$myPicture->setShadow(FALSE);

if ($type == 1) {
  $title = "Games per Day";
  $explanation = "Last ".$days." days";

  $sql = "SELECT date( ".$dateField." ) AS pdate, COUNT( id ) AS num
  FROM ".$tableName." 
  WHERE ".$dateField." > date_sub( date( now( ) ) , INTERVAL ".$days." DAY )
  AND ".$dateField." < CURRENT_DATE()
  GROUP BY pdate
  ORDER BY pdate ASC";
  $result = mysql_query($sql);

  $log->logInfo($sql);
  
  $arrFin = array();
  while ($row = mysql_fetch_array($result)) {
    $pdate = $row['pdate'];
    $arrFin[$pdate] = $row['num'];
    $log->logInfo("Fin pDate:" % $pdate);
  }
  
  $tableName = "six_matches_status";
  $dateField = "updated";
  
  $sql = "SELECT date( ".$dateField." ) AS pdate, COUNT( id ) AS num
  FROM ".$tableName." 
  WHERE ".$dateField." > date_sub( date( now( ) ) , INTERVAL ".$days." DAY )
  AND ".$dateField." < CURRENT_DATE()
  GROUP BY pdate
  ORDER BY pdate ASC";
  $result = mysql_query($sql);
  
  $log->logInfo($sql);
  
  $arrUnfin = array();
  while ($row = mysql_fetch_array($result)) {
    $arrUnfin[$row['pdate']] = $row['num'];
  }
  
  $startDate = new DateTime();
  $startDate->add(DateInterval::createfromdatestring('-'.($days).' day'));
  $day = DateInterval::createfromdatestring('+1 day');
  while ($startDate->getTimestamp() < time()) {
    $dateStr = $startDate->format('Y-m-d');
    $startDate = $startDate->add($day);
    
    if (array_key_exists($dateStr, $arrFin)) {
      $pts = $arrFin[$dateStr];
      $myData->AddPoints($pts, "Finished");
    } else {
      $myData->AddPoints(VOID, "Finished");
    }
    
    if (array_key_exists($dateStr, $arrUnfin)) {
      $pts = $arrUnfin[$dateStr];
      $myData->AddPoints($pts, "Unfinished");
    } else {
      $myData->AddPoints(VOID, "Unfinished");
    }
    $myData->AddPoints($dateStr,"Date");
  }
  $myData->setAbscissa("Date");
  $myData->setPalette("Finished", array("R"=>66,"G"=>116,"B"=>207));
  $myData->setPalette("Unfinished", array("R"=>243,"G"=>168,"B"=>16));
  
  $labelskip = floor($days/10);
  $scaleSettings = array("XMargin"=>0,"YMargin"=>0,"Floating"=>TRUE,"GridR"=>200, "GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE, "CycleBackground"=>TRUE, "LabelSkip"=>$labelskip, "Mode"=>SCALE_MODE_START0);
$format = array("Gradient"=>TRUE, "GradientEndR"=>181, "GradientEndG"=>200, "GradientEndB"=>236, "GradientStartR"=>66, "GradientStartG"=>116, "GradientStartB"=>207,"GradientAlpha"=>100);
  $myPicture->setGraphArea(40,50,760,$height-30);
  
} elseif ($type == 2) {
  $title = "Games per Lobby";
  $explanation = "Last ".$days." days";
  
  $sql = "SELECT count( * ) AS cnt, lobbyName
    FROM ".$tableName." 
    WHERE lobbyName <> ''
    AND ".$dateField." > date_sub( date( now( ) ) , INTERVAL ".$days." DAY )
    GROUP BY lobbyName 
    ORDER BY cnt DESC";
  $result = mysql_query($sql);
  
  while ($row = mysql_fetch_array($result)) {
      $myData->AddPoints($row['cnt'],"Games");
      $myData->AddPoints($row['lobbyName'],"Lobby");
  }
  $myData->setAbscissa("Lobby");
  $scaleSettings = array("XMargin"=>10,"YMargin"=>0,"Floating"=>FALSE,"GridR"=>200, "GridG"=>200,"GridB"=>200,"DrawSubTicks"=>FALSE, "CycleBackground"=>TRUE, "LabelSkip"=>0, "Pos" => SCALE_POS_TOPBOTTOM, "MinDivHeight" => 50, "Mode"=>SCALE_MODE_START0);
  $format = array("DisplayPos"=>LABEL_POS_OUTSIDE,"DisplayValues"=>TRUE, "DisplayR"=>50, "DisplayG"=>50,"DisplayB"=>50,"Gradient"=>TRUE, "GradientStartR"=>181, "GradientStartG"=>200, "GradientStartB"=>236, "GradientEndR"=>66, "GradientEndG"=>116, "GradientEndB"=>207,"GradientAlpha"=>100);
  $myPicture->setGraphArea(180,55,760,$height-20);
  
} elseif ($type == 3) {
  $title = "Games per Hour";
  $explanation = "Last ".$days." days";
  
  $sql = "SELECT count( * ) AS cnt, hour( played_on ) AS hr ".
    "FROM ".$tableName." ".
    "WHERE ".$dateField." > date_sub( date( now( ) ) , INTERVAL ".$days." DAY ) ".
    "GROUP BY hr ASC";
  $result = mysql_query($sql);  
  while ($row = mysql_fetch_array($result)) {
    $myData->AddPoints($row['cnt'],"Games");
    $myData->AddPoints($row['hr'],"Hour");
  }
  $myData->setAbscissa("Hour");
  $myData->setPalette("Games", array("R"=>66,"G"=>116,"B"=>207));
  $scaleSettings = array("XMargin"=>10,"YMargin"=>2,"Floating"=>FALSE,"GridR"=>200, "GridG"=>200,"GridB"=>200,"DrawSubTicks"=>FALSE, "CycleBackground"=>TRUE, "LabelSkip"=>0, "MinDivHeight" => 50, "Mode"=>SCALE_MODE_START0);
  $format = array("GradientEndR"=>181, 
    "GradientEndG"=>200, 
    "GradientEndB"=>236, 
    "GradientStartR"=>66, 
    "GradientStartG"=>116, 
    "GradientStartB"=>207,
    "GradientAlpha"=>100);
  $myPicture->setGraphArea(40,55,760,$height-20);
  
} elseif ($type == 4) {
  $title = "Games per Patch";
  $explanation = "Last ".$days." days";
  $subtitle = "Top 20";
  
  $sql = "SELECT count( * ) AS cnt, sp.name
    FROM ".$tableName." sm
    RIGHT JOIN six_patches sp ON sp.hash = sm.hashHome
    WHERE sm.hashHome = sm.hashAway
    AND sm.hashHome <> ''
    AND sm.".$dateField." > date_sub( date( now( ) ) , INTERVAL ".$days." DAY )
    GROUP BY sp.name ASC
    ORDER BY cnt DESC
    LIMIT 0,20";
  $log->logInfo($sql);
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
      $myData->AddPoints($row['cnt'],"Games");
      $myData->AddPoints(trim($row['name']),"Patch");
  }
  $myData->setAbscissa("Patch");
  $scaleSettings = array("XMargin"=>10,"YMargin"=>0,"Floating"=>FALSE,"GridR"=>200, "GridG"=>200,"GridB"=>200,"DrawSubTicks"=>FALSE, "CycleBackground"=>TRUE, "LabelSkip"=>0, "Pos" => SCALE_POS_TOPBOTTOM, "MinDivHeight" => 50, "Mode"=>SCALE_MODE_START0);
  $format = array("DisplayPos"=>LABEL_POS_OUTSIDE,"DisplayValues"=>TRUE, "DisplayR"=>50, "DisplayG"=>50,"DisplayB"=>50,"Gradient"=>TRUE, "GradientStartR"=>181, "GradientStartG"=>200, "GradientStartB"=>236, "GradientEndR"=>66, "GradientEndG"=>116, "GradientEndB"=>207,"GradientAlpha"=>100);
  $myPicture->setGraphArea(180,55,760,$height-20);
} elseif ($type == 5) {
  $title = "Games per Team";
  $explanation = "Last ".$days." days";
  $subtitle = "Top 20";
  
  $sql = "SELECT name, sum(cnt) as sumcnt FROM (SELECT count(sm.id) as cnt, wt1.name FROM six_matches sm
LEFT JOIN six_patches sp1 ON sm.hashHome=sp1.hash 
LEFT JOIN six_teams st1 ON (st1.sixTeamId=sm.team_id_home AND st1.patchId=sp1.id) 
LEFT JOIN weblm_teams wt1 ON (wt1.ID=st1.ladderTeamId) 
WHERE wt1.name IS NOT NULL
AND sm.".$dateField." > date_sub( date( now( ) ) , INTERVAL ".$days." DAY )
GROUP BY name

UNION ALL

SELECT count(sm.id) as cnt, wt2.name FROM six_matches sm
LEFT JOIN six_patches sp2 ON sm.hashAway=sp2.hash 
LEFT JOIN six_teams st2 ON (st2.sixTeamId=sm.team_id_away AND st2.patchId=sp2.id) 
LEFT JOIN weblm_teams wt2 ON (wt2.ID=st2.ladderTeamId) 
WHERE wt2.name IS NOT NULL
AND sm.".$dateField." > date_sub( date( now( ) ) , INTERVAL ".$days." DAY )
GROUP BY name) AS derived
GROUP BY name
ORDER BY sumcnt DESC 
LIMIT 0,20";
  $log->logInfo($sql);
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
      $myData->AddPoints($row['sumcnt'],"Games");
      $myData->AddPoints($row['name'],"Team");
  }
  $myData->setAbscissa("Team");
  $scaleSettings = array("XMargin"=>10,"YMargin"=>0,"Floating"=>FALSE,"GridR"=>200, "GridG"=>200,"GridB"=>200,"DrawSubTicks"=>FALSE, "CycleBackground"=>TRUE, "LabelSkip"=>0, "Pos" => SCALE_POS_TOPBOTTOM, "MinDivHeight" => 50, "Mode"=>SCALE_MODE_START0);
  $format = array("DisplayPos"=>LABEL_POS_OUTSIDE,"DisplayValues"=>TRUE, "DisplayR"=>50, "DisplayG"=>50,"DisplayB"=>50,"Gradient"=>TRUE, "GradientStartR"=>181, "GradientStartG"=>200, "GradientStartB"=>236, "GradientEndR"=>66, "GradientEndG"=>116, "GradientEndB"=>207,"GradientAlpha"=>100);
  $myPicture->setGraphArea(180,55,760,$height-20);
}


$myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/VeraMoBd.ttf","FontSize"=>12));
$myPicture->Antialias = TRUE;
$myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
$myPicture->drawText(15,15, $title, array("FontSize"=>11,"Align"=>TEXT_ALIGN_TOPLEFT));
$myPicture->setShadow(FALSE);
$myPicture->Antialias = FALSE;
$myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/Minecraftia.ttf","FontSize"=>6));
$myPicture->drawText(792, 7, $explanation, array("R"=>100,"G"=>100,"B"=>100, "Align"=>TEXT_ALIGN_TOPRIGHT));
$myPicture->drawScale($scaleSettings);

if ($subtitle <> '') {
  $myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/Minecraftia.ttf","FontSize"=>6));
  $myPicture->drawText(16, 33, $subtitle, array("R"=>100,"G"=>100,"B"=>100, "Align"=>TEXT_ALIGN_TOPLEFT));
}

$myPicture->setShadow(TRUE,array("X"=>2,"Y"=>2,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
$myPicture->Antialias = TRUE;
if ($type == 1) {
  $myPicture->drawLegend(340,20, array("R"=>240,"G"=>240,"B"=>240, "Mode"=>LEGEND_HORIZONTAL, "Style"=>LEGEND_BOX));
  $myPicture->drawSplineChart($format);
} elseif ($type == 3) { 
  $myPicture->drawSplineChart($format);
} else {
  $myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/verdana.ttf","FontSize"=>5));
  $myPicture->drawBarChart($format);
}
$myPicture->setShadow(FALSE);
$myPicture->Antialias = FALSE;
$myPicture->Stroke();

?>