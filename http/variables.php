<?php
// this holds variables independent of the database 
require($_SERVER["DOCUMENT_ROOT"] .'/config.php');

$titlebar = $leaguename;

$playerstable = "weblm_players"; 
$gamestable = "weblm_games"; 
$newstable = "weblm_news"; 
$varstable = "weblm_vars"; 
$admintable = "weblm_admin"; 

$logtable = "weblm_log_access";
$deducttable = "weblm_log_deducted";
$mailtable = "weblm_log_mail";
$historytable = "weblm_history";
$countriestable = "weblm_countries";
$seasonstable = "weblm_seasons";
$teamseasonstable = "weblm_seasons_team";
$donationstable = "weblm_donations";
$tournamenttable = "weblm_tournaments";
$minitournamenttable = "weblm_tournaments_mini";
$tournamentimagestable = "weblm_tournaments_images";
$signuptable = "weblm_signup";
$playerstatustable = "weblm_playerstatus";
$versionstable = "weblm_versions";
$teamstable = "weblm_teams";
$leaguestable = "weblm_leagues";
$leaguegamestable = "weblm_leaguegames";
$awardstable = "weblm_awards";
$goalstable = "weblm_goals";
$votestable = "weblm_votes";
$leaguesmetatable = "weblm_leagues_meta";

$forumtopicstable = "phpbb_topics";
$topicstable = "weblm_topics";
$teamladdertable = "weblm_teamladder";
$shoutboxtable = "wtagshoutbox";

$valid_picture_extension1 = "jpg";
$valid_picture_extension2 = "gif";
$valid_picture_extension3 = "png";
$valid_picture_extension4 = "jpeg";
$valid_picture_extensions = array($valid_picture_extension1, $valid_picture_extension2, 
	$valid_picture_extension3, $valid_picture_extension4);
	
$valid_cup_extension1 = "jpg";
$valid_cup_extension2 = "png";

$valid_goal_extension1 = "avi";
$valid_goal_extension2 = "mpg";
$valid_goal_extension3 = "mpeg";
$valid_goal_extension4 = "wmv";

$maxsize_picture_upload = 500000;
$maxnamelength = 20;
$maxsize_goal_upload = 5000000;
$maxsize_image_upload = 2000000;

$bgcolor = '#becaff';
$bgcolor_medium = '#d1d9ff';
$bgcolor_medium_light = '#e8ecff';

$bgcolor_light = '#eff2ff';

$menu_current = '#becaff';

$xboxcolor = '#0CA900';
$bothcolor = '#996600';
$blockedcolor = '#FF6161';
$menu_highlight = '#E6E600';

$adminonly = 'You are not allowed to view this part of the site.';

$membersonly = "<p>Only active league members are allowed to view this part of the site.</p>".
   "<p>If you have an account, please log in first.</p>".
   "<p>If you dont have an account yet, visit the <a href='join.php'>sign up page</a> ".
   "and follow the instructions.</p>";

$contactadmin = "Please contact an administrator to fix this problem.";

$mailconfig = "-XV -f bounce@".$leaguename;

$gfx_rank1 = "cup_gold.gif";
$gfx_rank2 = "cup_silver.gif";
$gfx_rank3 = "cup_bronze.gif";
$gfx_rank4 = "medal_gold.gif";
$gfx_rank5 = "medal_silver.gif";
$gfx_rank6 = "medal_bronze.gif";

$gfx_rank1_six = "cup_gold_6.gif";
$gfx_rank2_six = "cup_silver_6.gif";
$gfx_rank3_six = "cup_bronze_6.gif";
$gfx_rank4_six = "medal_gold_6.gif";
$gfx_rank5_six = "medal_silver_6.gif";
$gfx_rank6_six = "medal_bronze_6.gif";

$gfx_warn = "status_warn.gif";
$gfx_ban = "status_ban.gif";
$gfx_inactive = "status_inactive.gif";

$gfx_active_yes = "active_yes.gif";
$gfx_active_no = "active_no.gif";

$gfx_mini_tournament_prefix = "mini_tournament_";

$newgif = '<img src="gfx/new.gif" style="vertical-align:bottom">';

$versionspath = '/gfx/versions/';

$versions_pes4 = "ABC";
$versions_pes5 = "DEFGY";
$versions_pes6 = "HIJ";
$versions_we2007 = "KLM";
$versions_pes08 = "OPQR"; 
$versions_pes09 = "STU";
$versions_pes10 = "VWX";
$versions_pes11 = "Z12";
$versions_pes12 = "345";
$versions_pes13 = "678";
$versions_pes14 = "90#";

$admin_signup = "Admin";

$graph_green = "00ae10";
$graph_grey = "8c8c8c";
$graph_red = "ff3131";
$graph_black = "000000";

$back = "<p><a href='javascript:history.back()'>Go back</a></p>";

$videoThumbsDir = "files/goals/thumbnails/";
$videoThumbsTempDir = "files/goals/tmp/";

$subNavText = '<span style="color:#666666;">Go to</span>';
$masterPassword = 'mas847';
$mailDomain = '<img style="vertical-align:bottom" src="'.$directory.'/gfx/maildomain.gif" border="0">';

$playerDummy = '@player@';

$signupEmailRequired = false;

$maxGamesAgainstSamePlayer = 20;
$sixFAQUrl = "http://www.".$leaguename."/forum/viewtopic.php?t=5438";

$noReplyMail = "noreply@".$leaguename;

$badWordsGrep = "fuck u\|fuck y\|fuck of\|fucked y\|fucked u \|fucking y\|you fucking\|fucking idi\|you fuck\|fucking noob\|motherfucker\|anani siker";
?>
