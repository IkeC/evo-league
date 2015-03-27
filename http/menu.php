<?php
include_once ('functions.php');

// this displays the menu entries
$menuorder = 
	"Info:news:news*".
	"Report:report:report*".
	"Ladder:games:games*".
	"Sixserver:sixserver/index:index*".
	"Players:playerfind:players*".
	"Leagues:leagues/leagues:leagues*".
	"Tournaments:tournaments:tournaments*".
	// "Videos:files/browseGoals:browseGoals*".
	"Chat:Chat/chat:chat*".
	"Forum:forum/index:forum";
	
list ($pageinfo1, $pageinfo2, $pageinfo3, $pageinfo4, $pageinfo5, $pageinfo6, $pageinfo7, $pageinfo8) = explode("*", $menuorder);

list ($pagescreename1, $pageurl1, $pagename1) = explode(":", $pageinfo1);
list ($pagescreename2, $pageurl2, $pagename2) = explode(":", $pageinfo2);
list ($pagescreename3, $pageurl3, $pagename3) = explode(":", $pageinfo3);
list ($pagescreename4, $pageurl4, $pagename4) = explode(":", $pageinfo4);
list ($pagescreename5, $pageurl5, $pagename5) = explode(":", $pageinfo5);
list ($pagescreename6, $pageurl6, $pagename6) = explode(":", $pageinfo6);
list ($pagescreename7, $pageurl7, $pagename7) = explode(":", $pageinfo7);
list ($pagescreename8, $pageurl8, $pagename8) = explode(":", $pageinfo8);

?>
<div style="white-space:nowrap;">
  <div id="menuwrapper">
    <ul id="p7menubar"><?
  if (empty($cookie_name) || $cookie_name == 'null' || $cookie_name == null ) {
    ShowFirstMenu($pagescreename1, $pageurl1, $pagename1, $page, $cookie_name);
  } else {
    ShowFirstMenu("Profile", "profile", "profile", $page, $cookie_name);
    ShowMenu($pagescreename1, $pageurl1, $pagename1, $page, $cookie_name);
  }

  ShowMenu($pagescreename2, $pageurl2, $pagename2, $page, $cookie_name);
  ShowMenu($pagescreename3, $pageurl3, $pagename3, $page, $cookie_name);
  ShowMenu($pagescreename4, $pageurl4, $pagename4, $page, $cookie_name);
  ShowMenu($pagescreename5, $pageurl5, $pagename5, $page, $cookie_name);
  ShowMenu($pagescreename6, $pageurl6, $pagename6, $page, $cookie_name);
  // ShowMenu($pagescreename7, $pageurl7, $pagename7, $page, $cookie_name);
  // ShowMenu($pagescreename8, $pageurl8, $pagename8, $page, $cookie_name);
  ShowMenuChat($pagescreename7, getQuakenetWebchatUrl($cookie_name), $pagename7, $page, "p7bullet", $cookie_name);
  // ShowMenu("FIFA", "fifa/index", "FIFA", "", $cookie_name);
  ShowLastMenu($pagescreename8, $pageurl8, $pagename8, $page, $cookie_name);
  ?></ul><br class="clearit">
  </div>
</div>
<?
function ShowFirstMenu($pagescreename, $pageurl, $pagename, $page, $cookie_name) {
	ShowMenu2($pagescreename, $pageurl, $pagename, $page, "p7nobullet", $cookie_name);
}	

function ShowMenu($pagescreename, $pageurl, $pagename, $page, $cookie_name) {
	ShowMenu2($pagescreename, $pageurl, $pagename, $page, "p7bullet", $cookie_name);
}	

function ShowLastMenu($pagescreename, $pageurl, $pagename, $page, $cookie_name) {
	ShowMenu2($pagescreename, $pageurl, $pagename, $page, "p7lastbullet", $cookie_name);
}	

function ShowMenu2($pagescreename, $pageurl, $pagename, $page, $style, $player) {
	require ('variables.php');
	require ('variablesdb.php');
	$menuString = "";
	$menuString .= '<li';
	$menuString .= ' id="'.$style.'"';
	$profileadd = "";
	if ($pagename == 'profile' && !(empty($player))) {
		$profileadd = "?name=".$player;
	}
	$menuString .= '><a class="trigger" href="/'. $pageurl . '.php'.$profileadd .'">';

	if ($page == "" . $pagename . "") {
		$menuString .= "<font class='menu-active'>";
	} elseif ($pagescreename == 'Sixserver') {
		$menuString .= '<font color="OrangeRed">';
	} elseif ($pagescreename == 'FIFA') {
		$menuString .= '<font style="font-weight:bold;" color="007213">';
	}
	$menuString .= $pagescreename;

	if ($page == "" . $pagename . "" || $pagescreename == 'Sixserver' || $pagescreename == 'FIFA') {
		$menuString .= "</font>";
	}
	
	$menuString .= "</a>";
	$menuString .= getSubNavMenuString($pagename, $player);
  
	$menuString .= "</li>";
	echo $menuString;
}

function ShowMenuChat($pagescreename, $pageurl, $pagename, $page, $style, $player) {
	require ('variables.php');
	require ('variablesdb.php');
	$menuString = "";
	$menuString .= '<li';
	$menuString .= ' id="'.$style.'"';
	$menuString .= '><a href="'. $pageurl . '" target="_new">';
	if ($page == "" . $pagename . "") {
		$menuString .= "<font class='menu-active'>";
	} 
	$menuString .= $pagescreename;
	$menuString .= "</font>";
	
	$menuString .= "</a>";
	$subNavArray = getSubNavArrayForPage($pagename, $player);
	if (!empty($subNavArray)) {
		$subMenuContent = '<ul>';
		while ($subItem = current($subNavArray)) {
			$key = key($subNavArray);
			$url = $subItem[0];
			if (strpos($url, $playerDummy) > 0) {
				$urlFormatted = str_replace($playerDummy, "?name=".$player, $url);
			} else {
				$urlFormatted = $url;
			}
			$text = $subItem[1];
			$subMenuContent .= '<li><a href="'.$urlFormatted.'">'.$text.'</a></li>';
			next($subNavArray);
		}
		$subMenuContent .= "</ul>";
		$menuString .= $subMenuContent;
	}
	$menuString .= "</li>";
	echo $menuString;
}
?>