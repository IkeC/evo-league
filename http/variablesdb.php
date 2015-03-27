<?php 

// this pulls frequently used variables from the database

$db = mysql_connect($databaseserver, $databaseuser, $databasepass);
$dbselct = mysql_select_db($databasename);
$sql="SELECT * FROM $varstable WHERE vars_id = 1";
$result=mysql_query($sql);
$row = mysql_fetch_array($result);
$color1 = $row["color1"];
$color2 = $row["color2"];
$color3 = $row["color3"];
$color4 = $row["color4"];
$color5 = $row["color5"];
$color6 = $row["color6"];
$color7 = $row["color7"];
$font   = $row["font"];
$fontweight   = $row["fontweight"];
$fontsize   = $row["fontsize"];
$header   = $fontsize + 2;
$numgamespage = $row["numgamespage"];
$numplayerspage = $row["numplayerspage"];
$statsnum = $row["statsnum"];
$hotcoldnum = $row["hotcoldnum"];
$gamesmaxday = $row["gamesmaxday"];
$gamesmaxdayplayer = $row["gamesmaxdayplayer"];
$approve = $row["approve"];
$approvegames = $row["approvegames"];
$system = $row["system"];
$pointswin = $row["pointswin"];
$pointsloss = $row["pointsloss"];
$report = $row["report"];
$newsitems = $row["newsitems"];
$copyright = $row["copyright"];
$ra2ladderneg  = $row['ra2ladderneg'];
$uplfichierreport = $row['uplfichierreport'];
$uplfichierreportforce = $row['uplfichierreportforce'];
$maxsizereplayupl = $row['maxsizereplayupl'];
$extvalable1 = $row['extvalable1'];
$extvalable2 = $row['extvalable2'];
$extvalable3 = $row['extvalable3'];
$idcontrol = $row['idcontrol'];
$reportresult = $row['reportresult'];
$adminmail = $row['adminmail'];
$allowpswdmail = $row['allowpswdmail'];
$maxplayers = $row['maxplayers'];
$maxgameslinkpage = $row['maxgameslinkpage'];
$maintenance  = $row['maintenance'];
$season = $row['season'];
$autoApprove = $row['autoApprove'];
?>