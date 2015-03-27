<?php
header("Cache-Control: no-cache");
header("Pragma: no-cache");
$page = "info";
require('../functions.php');
require('../variables.php');
require('../variablesdb.php');
require('../top.php');

if (empty($_GET['nosound'])) { 
	$otherversion = '<a href="chat.php?nosound=true">Turn sound off</a>';
} else {
	$otherversion = '<a href="chat.php">Turn sound on</a>';
}

if (!empty($_GET['channel'])) {
	$channel = mysql_real_escape_string($_GET['channel']);
} else {
	$channel = $leaguename;
}

$chatNick = getChatNick();
?>
<?= getOuterBoxTop($leaguename. " <span class='grey-small'>&raquo;</span> Chat", $otherversion) ?>


<p>This chat applet connects you to the IRC channel #<?= $channel ?> on <a href="http://www.quakenet.org" target="_new">Quakenet</a>.</p>
<p>Not working for you? Select one of the other chat methods from the Chat menu above.</p>

<applet code=IRCApplet.class archive="irc.jar,pixx.jar" width="640" height="400">
    <param name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">
    
<param name="nick" value="<?= $chatNick ?>">
    <param name="alternatenick" value="<?
	if (!empty($chatNick)) {
	 	echo $chatNick."-evo";   
	}
	else {
		echo "evo-player-".rand(100,999);
	}
	?>">
    <param name="name" value="Java User">
    <param name="host" value="irc.quakenet.org">
    <param name="port" value="6667">
    <param name="gui" value="pixx">
    <param name="quitmessage" value="www.<?= $leaguename ?>">
    <param name="asl" value="true">
    <param name="command1" value="/join #<?= $channel ?>">
	<param name="language" value="./english">
    <param name="style:sourcefontrule1" value="none+Channel all Arial 11">
    <param name="alternateserver1" value="sw.de.quakenet.org 6667">
    <param name="alternateserver2" value="xs4all.nl.quakenet.org 6668">
    <param name="alternateserver3" value="demon.uk.quakenet.org 6667">
    <param name="pixx:language" value="./pixx-english">
	<param name="style:bitmapsmileys" value="true">
    <param name="style:smiley1" value=":) img/sourire.gif">
    <param name="style:smiley2" value=":-) img/sourire.gif">
    <param name="style:smiley3" value=":-D img/content.gif">
    <param name="style:smiley4" value=":d img/content.gif">
    <param name="style:smiley5" value=":-O img/OH-2.gif">
    <param name="style:smiley6" value=":o img/OH-1.gif">
    <param name="style:smiley7" value=":-P img/langue.gif">
    <param name="style:smiley8" value=":p img/langue.gif">
    <param name="style:smiley9" value=";-) img/clin-oeuil.gif">
    <param name="style:smiley10" value=";) img/clin-oeuil.gif">
    <param name="style:smiley11" value=":-( img/triste.gif">
    <param name="style:smiley12" value=":( img/triste.gif">
    <param name="style:smiley13" value=":-| img/OH-3.gif">
    <param name="style:smiley14" value=":| img/OH-3.gif">
    <param name="style:smiley15" value=":'( img/pleure.gif">
    <param name="style:smiley16" value=":$ img/rouge.gif">
    <param name="style:smiley17" value=":-$ img/rouge.gif">
    <param name="style:smiley18" value="(H) img/cool.gif">
    <param name="style:smiley19" value="(h) img/cool.gif">
    <param name="style:smiley20" value=":-@ img/enerve1.gif">
    <param name="style:smiley21" value=":@ img/enerve2.gif">
    <param name="style:smiley22" value=":-S img/roll-eyes.gif">
    <param name="style:smiley23" value=":s img/roll-eyes.gif">
    <param name="style:floatingasl" value="true">
    
	<param name="pixx:highlight" value="true">
    <param name="pixx:highlightnick" value="true">
    
	<param name="style:backgroundimage" value="true"> 
    <? 
	    $images = array("2","5","8","9"); 
	    $img = $images[array_rand($images)]; 
    ?>
    <param name="style:backgroundimage1" value="all all 515 /gfx/watermarks/<?= $img ?>.jpg">

    <? if (empty($_GET['nosound'])) { ?>
	<param name="soundword1" value="anyone snd/anyone.au"> 
	<param name="soundword2" value="any1 snd/anyone.au"> 
	<param name="soundquery" value="snd/ding.au">
	<? } ?>	
</applet>

<p>Here you can ask other players for a game. Be a little patient and don't leave when you do not get an instant reply.</p>
<p>Change your nick with the command <b>/nick &lt;newnick&gt;</b>, eg. /nick MyNickname.</p>
<p>If you're logged in to the site, you should appear in the chat with your username.</p>
<p>Visit other Quakenet channels without leaving #<?= $leaguename ?> with the command  <b>/join &lt;channel&gt;</b>, eg. /join #evo-trivia.</p>
<p>You can also use an IRC client like <a href="http://www.mirc.co.uk" target="_new">mIRC</a> 
to connect to the channel. This is way more comfortable if you use the chat often. A good script to enhance mIRC is <a href="http://www.nnscript.de" target="_new">NNScript</a>.</p>
<?= getOuterBoxBottom() ?>

<?
require('../bottom.php');
?>
