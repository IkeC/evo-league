<?php

header('Content-type: text/css');

$config = array(
	'color'			=> isset($_GET['color']) ? $_GET['color'] : '',
	'hover'			=> empty($_GET['hover']) ? false : true,
	'top'			=> empty($_GET['top']) ? false : true,
	'topicview'		=> isset($_GET['topicview']) ? $_GET['topicview'] : 'left',
	'grad_bg'		=> empty($_GET['grad_bg']) ? false : true,
	'grad_color'	=> isset($_GET['grad_color']) ? $_GET['grad_color'] : 'profile',
	'grad'			=> empty($_GET['grad']) ? false : true,
	);

$colors_list = array(
	'blue',
	'darkblue',
	'red',
	'pink',
	'green',
	'purple',
	'gray',
	'sblue',
);
if(!in_array($config['color'], $colors_list))
{
	$config['color'] = 'blue';
}
include('style_' . $config['color'] . '.php');


?>

/* main tags */
html {
	margin: 0;
	padding: 0;
	background-color: #E0E0E0;
	height:100%;
}

body { padding: 4px; 
	margin: 0;
}

body, font, th, td, p { 
	font-family: Verdana, Arial, Helvetica, sans-serif; 
	font-size: 11px; 
}


table {
	border: 0;
}

hr { 
	height: 0;
	border: solid <?php echo $colors['hr']; ?> 0px;
	border-top-width: 1px;
}

html>body label span { color: #444; }
html>body label:hover span { color: #000; border-bottom: dotted 1px #888; }

/* links */
a { 
	text-decoration: none; 
	color: <?php echo $colors['link']; ?>; 
}
a:visited { 
	color: <?php echo $colors['link_visited']; ?>;
}
a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
	float: none;
}
a:active {
	color: <?php echo $colors['link']; ?>;
}

/* main tables */
.forumline	{ 
	background-color: <?php echo $colors['border']; ?>; 
	text-align: left; 
}
.forumline th { 
	text-align: center; 
	white-space: nowrap;
}

.forumline-empty {
	border: solid 1px <?php echo $colors['border']; ?>;
}



/* rows */
.row, .row1, .row2, .row3, .row4, .row1h, .row1h-new, .row2h, .row3h, .row1g { font-size: 11px; }

.row, .row1, .row1h, .row1h-new { background-color: #FFF; }
.row2, .row2h { background-color: #FDFDFD; }
.row3, .row3h, .row4 { background-color: #FAFAFA; }

<?php if($config['grad_bg']) { ?>
.row1g { background: #FFF url(images/<?php echo $config['color']; ?>/bg_post.gif) top left repeat-x; }
<?php } else { ?>
.row1g { background-color: #FFF; }
<?php } ?>

.row1h, .row1h-new { padding-left: 4px; }

<?php if($config['hover']) { ?>
.row1h:hover, .row1hHover { background: #F6F6F6 url(images/<?php echo $config['color']; ?>/bg_row2.gif) bottom right no-repeat; }
.row1h-new:hover, .row1h-newHover { background: #F8F8F8 url(images/<?php echo $config['color']; ?>/bg_row2_new.gif) bottom right no-repeat; }
.row2h:hover, .row2hHover { background: #F6F6F6 url(images/<?php echo $config['color']; ?>/bg_row2.gif) bottom right no-repeat; }
.row3h:hover, .row3hHover { background: #F4F4F4 url(images/<?php echo $config['color']; ?>/bg_row2.gif) bottom right no-repeat; }
<?php } ?>

/* th */
th, td.th { 
	font-size: 11px; 
	font-weight: bold; 
	height: 25px; 
	border: solid 1px <?php echo $colors['th_border']; ?>; 
	border-width: 0px 1px 0px 1px; 
	padding: 0; 
	white-space: nowrap; 
	background: <?php echo $colors['th_bg']; ?> url(images/<?php echo $config['color']; ?>/bg_th.gif) top left repeat-x;
	color: #F0F0F0;
}

/* bottom row */
td.catBottom { 
	color: #F6F6F6;
	font-size: 11px; 
	height: 27px; 
	padding: 1px;
	background: <?php echo $colors['catbottom']; ?> url(images/<?php echo $config['color']; ?>/bg_cat.gif) top left repeat-x;
}

.gen { font-size: 11px; }
.genmed { font-size: 11px; }
.gensmall { font-size: 10px; }

td.spacerow { 
	background: <?php echo $colors['th_bg']; ?> url(images/<?php echo $config['color']; ?>/bg_th.gif);
	height: 2px; 
	padding: 0px; 
}

/* index css */
.forumlink { 
	font-weight: bold; 
	font-size: 11px; 
}
a.forumlink, a.forumlink:visited { 
	text-decoration: none; 
}
a.forumlink:hover, a.forumlink:active { 
	text-decoration: underline; 
}

.forumlink-new { 
	font-weight: bold; 
	font-size: 11px;
}
a.forumlink-new, a.forumlink-new:visited { 
	color: <?php echo $colors['forumlink_new']; ?>;
	text-decoration: none; 
}
a.forumlink-new:hover, a.forumlink-new:active { 
	text-decoration: underline; 
}

.moderators { font-size: 10px; color: #666; }
.moderators a, .moderators a:visited { color: #A2A2A2; text-decoration: underline; }
.moderators a:hover { color: <?php echo $colors['link_active']; ?>; text-decoration: underline; } 
.moderators a:active { color: #666; text-decoration: underline; } 

/* viewforum css */
.topiclink { 
	font-weight: bold; 
	font-size: 11px; 
}
a.topiclink, a.topiclink:visited { text-decoration: none; }
a.topiclink:hover, a.topiclink:active { text-decoration: underline; }

.topiclink-new { 
	font-weight: bold; font-size: 11px; 
}
a.topiclink-new { 
	text-decoration: none; 
	color: <?php echo $colors['forumlink_new']; ?>;
}
a.topiclink-new:visited	{ 
	text-decoration: none; 
	color: <?php echo $colors['forumlink_new']; ?>;
}
a.topiclink-new:hover, a.topiclink-new:active { text-decoration: underline; }

.pagination { 
	display: block;
	color: #666; 
	font-weight: bold; 
	margin: 0px; 
	margin-top: 5px; 
	margin-bottom: 3px; 
	font-size: 11px; 
}
.pagination a, .pagination a:visited { 
	color: #000; 
	border: #BBB 1px solid; 
	padding: 1px; background-color: <?php echo $colors['page_bg']; ?>; 
}
.pagination a:hover { 
	color: #444;
	border: <?php echo $colors['page_border']; ?> 1px solid;
	padding: 1px; 
	background-color: #FFF;
}
.pagination a:active { 
	color: #666;
	border: #BBB 1px solid;
	padding: 1px; 
	background-color: #FFF;
}

.gotopage { 
	display: block;
	margin: 0px; 
	margin-top: 5px; 
	margin-bottom: 1px; 
}
.gotopage a, .gotopage a:visited { 
	border: <?php echo $colors['page_border2']; ?> 1px solid; 
	padding: 1px; 
	background-color: <?php echo $colors['page_bg2']; ?>; 
}
.gotopage a:hover { 
	border: #DDD 1px solid; 
	padding: 1px; 
	background-color: #FFF; 
}
.gotopage a:active { 
	border: <?php echo $colors['page_border2']; ?> 1px solid; 
	padding: 1px; 
	background-color: #FFF;
}
.gotopage img { display: none; }

/* viewtopic css */
table.post1, table.post2 {
	border: solid 1px <?php echo $colors['border']; ?>; 
}

<?php
switch($config['topicview'])
{
	case 'left':
		$border = 'border-right';
		break;
	case 'right':
		$border = 'border-left';
		break;
	case 'above':
		$border = 'border-bottom';
		break;
	default:
		$border = 'border-right';
}
?>
td.post-left1, td.post-left2 {
	<?php echo $border; ?>: solid 1px <?php echo $colors['border']; ?>; 
	padding: 2px;
}

<?php

$color_post =  $config['grad'] ? 'background: #FFF url(images/' . $config['color'] . '/bg_post.gif) top left repeat-x;' : 'background-color: #FFF;';
$color_profile = $config['grad'] ? 'background: #FAFAFA url(images/' . $config['color'] . '/bg_profile.gif) top left repeat-x;' : 'background-color: #FAFAFA;';

switch($config['grad_color'])
{
	case 'post':
			$post = array($color_profile, $color_profile);
			$profile = array($color_post, $color_post);
			break;
	case 'light':
			$post = array($color_post, $color_post);
			$profile = array($color_post, $color_post);
			break;
	case 'dark':
			$post = array($color_profile, $color_profile);
			$profile = array($color_profile, $color_profile);
			break;
	case 'cycle':
			$post = array($color_post, $color_profile);
			$profile = array($color_profile, $color_post);
			break;
	case 'cycle2':
			$post = array($color_post, $color_profile);
			$profile = array($color_post, $color_profile);
			break;
	default: // 'profile'
			$post = array($color_post, $color_post);
			$profile = array($color_profile, $color_profile);
}

?>
table.post1 {
	<?php echo $post[0]; ?>
}
table.post2 {
	<?php echo $post[1]; ?>
}
td.post-left1 {
	<?php echo $profile[0]; ?>
}
td.post-left2 {
	<?php echo $profile[1]; ?>
}

td.post-right {
	padding: 0;
}
td.post-bottom {
	padding: 0;
}
td.post-signature {
	padding: 4px;
	font-size: 11px; 
	color: #444;
}
.post-date {
	border-bottom: solid 1px <?php echo $colors['link']; ?>;
	padding: 2px;
}

.post-buttons a img { background-color: <?php echo $colors['icon_bg']; ?>; }
.post-buttons a:hover img { background-color: <?php echo $colors['icon_hover_bg']; ?>; }
.post-buttons a:active img { background-color: <?php echo $colors['icon_click_bg']; ?>; }

.post-buttons .img-quote a img { background-color: <?php echo $colors['icon_quote_bg']; ?>; }
.post-buttons .img-quote a:hover img { background-color: <?php echo $colors['icon_hover_bg']; ?>; }
.post-buttons .img-quote a:active img { background-color: <?php echo $colors['icon_bg']; ?>; }

.post-buttons-single a img { background-color: <?php echo $colors['icon_quote_bg']; ?>; border: solid 1px #C0C0C0; border-width: 0 1px 1px 1px; }
.post-buttons-single a:hover img { background-color: <?php echo $colors['icon_hover_bg']; ?>; }
.post-buttons-single a:active img { background-color: <?php echo $colors['icon_bg']; ?>; }

.name { 
	<?php if($config['topicview'] !== 'above') { ?>
	line-height: 1.6em;
	<?php } ?>
	font-weight: bold; 
	font-size: <?php echo $config['topicview'] == 'above' ? '16px' : '11px'; ?>; 
}
.name a, .name a:visited { text-decoration: underline; }
.name a:hover, a.name a:active { text-decoration: underline; }
.postdetails { color: #646464; font-size: 9px; }
.postdate { font-size: 10px; color: #646464; }

.postbody { font-size: 11px; color: #202020; }
html>body .postbody { display: block; overflow: hidden; }

/* bbcode */
table.quote, table.code {
	background-color: #C0C0C0; 
	margin: 3px 0; 
}
td.quote_user, td.code_header { 
	background: #E0E0E0 url(images/<?php echo $config['color']; ?>/bg_quote_user.gif) top left repeat-x; 
	font-size: 11px; 
	color: #555; 
	padding: 2px 5px; 
}
td.quote, td.code { 
	font-size: 11px;
	background: #FFF url(images/<?php echo $config['color']; ?>/bg_quote.gif) top left repeat-x; 
	wrap-option: emergency; 
}

td.code { font-family: Courier, 'Courier New', sans-serif; color: #444; wrap-option: emergency; }


/* header */
.logo {
	background: url(images/<?php echo $config['color']; ?>/logo_bg.gif) bottom left repeat-x;
}
.buttons {
	background: url(images/<?php echo $config['color']; ?>/buttons_bg2.gif) top left repeat-x;
	vertical-align: top;
}
.buttons1 {
	background: url(images/<?php echo $config['color']; ?>/buttons_bg1.gif) top left repeat-x;
	vertical-align: top;
}

/* headers/footers/corners for main table */
.navbar-top {
	background: url(images/<?php echo $config['color']; ?>/c_top_nav.gif) bottom left repeat-x;
	padding: 2px 3px 0 3px;
}
.navbar-bottom {
	background: url(images/<?php echo $config['color']; ?>/c_bottom_nav.gif) top left repeat-x;
	padding: 0 3px 2px 3px;
}
.content-top {
	background: url(images/<?php echo $config['color']; ?>/c_top_simple.gif) bottom left repeat-x ;
}
.content-bottom {
	background: url(images/<?php echo $config['color']; ?>/c_bottom_simple.gif) top left repeat-x ;
}
.content-left {
	background: url(images/<?php echo $config['color']; ?>/c_left.gif) top right repeat-y;
}
.content-right {
	background: url(images/<?php echo $config['color']; ?>/c_right.gif) top left repeat-y;
}
.content {
	background-color: #F4F4F4;
	padding: 5px 2px;
	font-size: 9px;
}
.content-navbar {
	padding: 8px 2px;
}
.content-newmsg {
	padding-top: 0;
}
.content-nopadding {
	padding: 0 2px;
}

.evo-nav-bottom {
	background: url(images/<?php echo $config['color']; ?>/evo_bottom_nav_line.gif) top left repeat-x ;
}

.evo-outer-nav-bottom {
	background: url(images/<?php echo $config['color']; ?>/evo_outer_bottom_nav_line.gif) top left repeat-x ;
	padding: 0 3px 1px 3px;
}
/* headers/footers for tables */
.hdr { 
	height: 25px; 
	border: 0px; 
	font-weight: bold; 
	font-size: 11px; 
	letter-spacing: 1px;
	background-image: url(images/<?php echo $config['color']; ?>/hdr_bg.gif); 
	color: <?php echo $colors['hdr_text']; ?>; 
}

.hdr-cell {
	background: <?php echo $colors['th_bg']; ?> url(images/<?php echo $config['color']; ?>/bg_th.gif) top left repeat-x;
	
}

.hdr a, .hdr a:visited { 
	text-decoration: underline; 
	color: <?php echo $colors['hdr_text']; ?>; 
}
.hdr a:hover { 
	text-decoration: underline; 
	color: <?php echo $colors['link_active']; ?>; 
}
.hdr a:active { 
	text-decoration: underline; 
	color: <?php echo $colors['link']; ?>; 
}
.ftr {
	background-image: url(images/<?php echo $config['color']; ?>/ftr_bg.gif);
}
.ftr-new {
	background-image: url(images/<?php echo $config['color']; ?>/ftr_bg_new.gif);
}

/* new private messages box */
.newpm {
	background: url(images/<?php echo $config['color']; ?>/pm_bg.gif) top left repeat-x;
	white-space: nowrap;
}
.newpm a, .newpm a:visited {
	color: <?php echo $colors['link_active']; ?>;
	font-size: 11px;
	font-weight: bold;
	text-decoration: underline;
}
.newpm a:hover {
	color: <?php echo $colors['newpm']; ?>;
	text-decoration: underline;
}
.newpm a:active {
	color: <?php echo $colors['link_active']; ?>;
	text-decoration: underline;
}


/* navbar */
.navbar-links, .navbar-header {
	font-size: 11px;
	font-weight: bold;
	color: #A0A0A0;
	padding: 0;
}
.navbar-links a, .navbar-links a:hover {
	color: <?php echo $colors['link']; ?>;
	font-weight: normal;
	text-decoration: none;
}
.navbar-links a:hover {
	color: <?php echo $colors['link_active']; ?>;
	font-weight: normal;
}
.navbar-links a:active {
	color: <?php echo $colors['link']; ?>;
	font-weight: normal;
}
.navbar-header {
	color: <?php echo $colors['link_active']; ?>;
}
.navbar-text {
	font-size: 10px;
	color: #555;
	padding: 1px;
}

/* top buttons */
.buttons { 
	padding-top: 3px;
	font-size: 11px;
}
.buttons a, .buttons a:visited {
	color: <?php echo $colors['link']; ?>;
	text-decoration: none;
}
.buttons a:hover {
	color: <?php echo $colors['link_active']; ?>;
	text-decoration: none;
}
.buttons a:active {
	color: <?php echo $colors['link']; ?>;
	text-decoration: none;
}

/* copyright */
.copyright {
	font-size: 9px;
	color: #A0A0A0;
}
.copyright a, .copyright a:visited {
	color: <?php echo $colors['copyright']; ?>;
	text-decoration: none;
}
.copyright a:hover {
	color: <?php echo $colors['link_active']; ?>;
	text-decoration: none;
}
.copyright a:active {
	color: #888;
	text-decoration: none;
}
.admin-link {
	font-size: 10px;
}

/* inputs */
form { 
	display: inline; 
	padding: 0;
	margin: 0;
}

form.inline-form {
	display: block;
	margin-top: 3px;
}

input { text-indent: 2px; }
input, textarea, select { 
	color: #333; 
	font: normal 11px Verdana, Arial, Helvetica, sans-serif; 
	vertical-align: middle;
	margin: 0;
	box-sizing: content-box;
	-moz-box-sizing: content-box;
}
select {
	-moz-box-sizing: border-box;
}

input.post, input.mainoption, input.liteoption, textarea, select { 
	background-color: #FFF; 
	border: solid 1px <?php echo $colors['input_border']; ?>;
	margin: 0;
}
.catBottom input.post, .catBottom input.mainoption, .catBottom input.liteoption, .catBottom select { 
	border-color: #333;
}
input.post, input.mainoption, input.liteoption, select { 
	height: 17px; 
}

input.post, input.mainoption, input.liteoption {
	background: #FFF url(images/<?php echo $config['color']; ?>/bg_input.gif) top left repeat-x;
}

textarea {
	background: #FFF url(images/<?php echo $config['color']; ?>/bg_textarea.gif) top left repeat-x;
}

input.button, select.button { 
	background-color: #FFFFFF; 
	color: #666;
	font-size: 11px; 
	border: solid 1px #CCC; 
}
.button { color: #666; }

input.mainoption { font-weight: bold; }
input.liteoption { font-weight: normal; }

select optgroup { background-color: <?php echo $colors['optgroup_bg']; ?>; color: #404040; font-style: normal; font-size: 11px; }
select option, select optgroup option { background-color: #FFF; color: #000; font-size: 11px; }

input.helpline { background-color: #FDFDFD; border: solid 1px #FDFDFD; color: #444; }

html>body input.post, html>body input.mainoption, html>body input.liteoption, html>body textarea {
	background-color: #F8F8F8;
	background-image: none;
	color: #555;
	border-color: #888;
}
html>body .catBottom input.post, html>body .catBottom input.mainoption, html>body .catBottom input.liteoption {
	border-color: #444;
}
html>body input.post:hover, html>body input.mainoption:hover, html>body input.liteoption:hover, html>body textarea:hover {
	background-color: #FFF;
	color: #444;
	border-color: <?php echo $colors['input_border']; ?>;
}
html>body .catBottom input.post:hover, html>body .catBottom input.mainoption:hover, html>body .catBottom input.liteoption:hover {
	border-color: #000;
}
html>body input.post:focus, html>body input.mainoption:focus, html>body input.liteoption:focus, html>body textarea:focus {
	background-color: #FFF;
	color: #000;
	border-color: <?php echo $colors['input_border']; ?>;
}
html>body .catBottom input.post:focus, html>body .catBottom input.mainoption:focus, html>body .catBottom input.liteoption:focus {
	border-color: #000;
}
html>body input.post:focus, html>body input.mainoption:focus, html>body input.liteoption:focus {
	background: #FFF url(images/<?php echo $config['color']; ?>/bg_input.gif) top left repeat-x;
}
html>body textarea:focus {
	background: #FFF url(images/<?php echo $config['color']; ?>/bg_textarea.gif) top left repeat-x;
}


/* inputs heigh fix for browser that incorrectly count it: IE, Mozilla/Firefox */
* html .inline-form input.mainoption, * html .inline-form input.liteoption {
	height: 19px;	/* for IE */
}
* html .inline-form input.post, * html .inline-form select {
	height: 19px;	/* for IE 5.5 */
	voice-family: "\"}\""; 
	voice-family: inherit;
	height: 15px;	/* for IE 6.0 */
}
html>body .inline-form input.post, html>body .inline-form select {
	height: 15px;	/* for Mozilla/Firefox */
}
@media all and (min-width: 0px) {
	html>body .inline-form input.post, html>body .inline-form select {
		height: 17px;	/* return normal height for Opera - the only browser that gets it right */
	}
}

.boxtable {
	width:98%;
	margin-left:10px;
	margin-top:3px;
	margin-right:10px;
	margin-bottom:10px;
	overflow: hidden;
}

.boxtable td {
	padding-right:10px;
	font-size:11px;	
}

.gamesbox td {
	padding-right:6px;
	font-size:11px;	
}

.boxtable p {
	font-size:11px;
}

.logintable td {
	font-size:10px;
}

.layouttable { 
	width:100%;
	border:0px;
	position:static;
}

.layouttable p {
	margin-top: 0px;
	
}

.formtable td {
	height:30px;
	padding-right:10px;
}

.reporttable td {
	padding-bottom:20px;
	vertical-align:top;
}

.smileytable {
	border: thin dotted;
	margin-top:20px;
}

.link-inactive a {
	color:#FF6161;
}

.link-inactive a:visited { 
	color:#FF6161;
}

.link-inactive a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
}
.link-inactive a:active {
	color: <?php echo $colors['link']; ?>;
}

.link-pc a {
	color: <?php echo $colors['link']; ?>;
}

.link-pc a:visited { 
	color: <?php echo $colors['link']; ?>;
}

.link-pc a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
}
.link-pc a:active {
	color: <?php echo $colors['link']; ?>;
}

.link-xbox a {
	color:#0CA900;
}

.link-xbox a:visited { 
	color:#0CA900;
}

.link-xbox a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
}
.link-xbox a:active {
	color: <?php echo $colors['link']; ?>;
}

.link-client a {
	color: <?php echo $colors['link_client']; ?>;
}

.link-client a:visited { 
	color: <?php echo $colors['link_client']; ?>;
}

.link-client a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
}
.link-client a:active {
	color: <?php echo $colors['link']; ?>;
}

.grey-small {
	color: #9f9f9f;
	font-size: 10px;
}

.darkgrey-small {
	color: #535353;
	font-size: 10px;
	padding-right:5px;
}
.black-small {
	color: #000000;
	font-size: 10px;
}
.red-small {
	color: #db0000;
	font-size: 10px;
  font-weight: bold; 
}
.width20 {
	width:20px;
}

.width50 {
	width:50px;
}

.width70 {
	width:70px;
}

.width100 {
	width:100px;
}

.width150 {
	width:150px;
}

.width200 {
	width:200px;
}

.width250 {
	width:250px;
}

.width300 {
	width:300px;
}

.width400 {
	width:400px;
}

.width500 {
	width:500px;
}

.imgMargin {
	margin-right:7px;
	vertical-align:middle;
}	

/* rows */

.row td {
	padding-left:8px;
	padding-right:8px;	
}

.row_active {
	background-color:#D4E4FF;
}

.cell_active {
	background-color:#D4E4FF;
}

.row_active td {
	padding-left:8px;
	padding-right:8px;	
}

.row_alert {
	background-color:#fff0e6;
}

.row_alert td {
	padding-left:8px;
	padding-right:8px;	
}
.row_deleted {
	background-color:#ffd4d4;
}

.row_deleted td {
	padding-left:8px;
	padding-right:8px;	
}

.row_approve {
	background-color:#FFFFFF;
}

.row_approve td {
	vertical-align:top;
	font-size:10px;
}

.row_bottom {
	height:11px;
}

.row_bottom td {
	font-size:11px;
	height:11px;
}

.row_newsheader td {
	padding-bottom:10px;
}

.td_profile {
   width:15%;
   text-align:right;
}

.padding-button {
	padding-top:15px; 
	padding-bottom:15px;
}

ol {
	list-style-type:square;
}

.menu-active {
    color:<?= $colors['menu_active']; ?>;
}

.link-menucurrent a {
	color: <?php echo $colors['menu_active']; ?>;
}

.link-menucurrent a:visited { 
	color: <?php echo $colors['menu_active']; ?>;
}

.link-menucurrent a:hover { 
	color: <?php echo $colors['menu_active']; ?>; 
}
.link-menucurrent a:active {
	color: <?php echo $colors['menu_active']; ?>;
}

.link-black a {
	color: black;
}

.link-black a:visited { 
	color: black;
}

.link-black a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
}
.link-black a:active {
	color: <?php echo $colors['link']; ?>;
}

/* Big box with list of options */
#ajax_listOfOptions{
	position:absolute;	/* Never change this one */
	width:152px;	/* Width of box */
	height:50px;	/* Height of box */
	overflow:auto;	/* Scrolling features */
	border:1px solid <?= $colors['input_border']; ?>;
	background-color:#FFF;	
	text-align:left;
	font-size:1em;
	z-index:100;
}
#ajax_listOfOptions div{	/* General rule for both .optionDiv and .optionDivSelected */
	margin:1px;		
	padding:1px;
	cursor:pointer;
	font-size:1em;
	
}
#ajax_listOfOptions .optionDiv{	/* Div for each item in list */
	
}
#ajax_listOfOptions .optionDivSelected{ /* Selected item in the list */
	background-color: #8294b4;
	color:#FFF;
}
#ajax_listOfOptions_iframe{
	background-color:#F00;
	position:absolute;
	z-index:5;
	vertical-align:bottom;
}

.vidSelect {
	position:relative;
	float:left;
	padding:5px 5px 5px 5px;
	margin:0 5px 5px 0;
	border:1px solid <?= $colors['input_border']; ?>;
	background-color:#e5eefe;	
}

.star-rating {
	list-style: none; 
   	margin: 3px; 
   	padding: 0px; 
   	width: 100px; 
   	height: 20px; 
   	position: relative;  
   	background: url(/gfx/star_rating.gif) top left repeat-x; 
}

.star-rating li {
	padding:0px; 
	margin:0px; 
	/*\*/ 
	float: left; 
	/* */ 
}

.star-rating li a {
	display:block;
   	width:20px;
   	height: 20px;
   	text-decoration: none;
   	text-indent: -9000px;
   	z-index: 20;
   	position: absolute;
   	padding: 0px;
  	background-image:none;
}

.star-rating li a:hover{
	background: url(/gfx/star_rating.gif) left bottom;
  	z-index: 1;
  	left: 0px;
}

.star-rating a.one-star{
	left: 0px;
}
.star-rating a.one-star:hover{
	width:20px;
}
.star-rating a.two-stars{
	left:20px;
}
.star-rating a.two-stars:hover{
	width: 40px;
}
.star-rating a.three-stars{
	left: 40px;
}
.star-rating a.three-stars:hover{
	width: 60px;
}
.star-rating a.four-stars{
	left: 60px;
}
.star-rating a.four-stars:hover{
	width: 80px;
}
.star-rating a.five-stars{
	left: 80px;
}
.star-rating a.five-stars:hover{
	width: 100px;
}

.rating {
	margin:0 0 10px 0;
}

.tab2cols {
	width: 100%;
}

.tab2td {
	width: 50%;
	vertical-align: top;
}

.forumcode {
	border: 1px solid <?= $colors['input_border']; ?>;
	text-align: left;
	background-color: #eaeaea;
	color: #333333;
	font-size: 9px;
	position: absolute;
	margin-top: -14px;
	right: 69px;
	width: 900px;
	padding: 2 2 2 2;
	
}

.evo_submenu_list li {
	margin: 2 2 2 2;
  white-space: nowrap;
}

.evo_submenutable {
	height:22px;
  white-space: nowrap;
}
.evo_submenutable tbody tr td {
	vertical-align:top;
	height:22px;
  white-space: nowrap;
}

.link-stickytopic {
	color: <?php echo $colors['link_active_darker']; ?>;
	font-weight:bold;
}

.link-stickytopic a {
	color: <?php echo $colors['link_active_darker']; ?>;
	font-weight:bold;
}

.link-stickytopic a:visited { 
	color: <?php echo $colors['link_active_darker']; ?>;
	font-weight:bold;
}

.link-stickytopic a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
}

.link-stickytopic a:active {
	color: <?php echo $colors['link']; ?>;
}

.link-stickytopic-hot {
	color: OrangeRed;
	font-weight:bold;
}

.link-stickytopic-hot a {
	color: OrangeRed;
	font-weight:bold;
}

.link-stickytopic-hot a:visited { 
	color: OrangeRed;
	font-weight:bold;
}

.link-stickytopic-hot a:hover { 
	color: <?php echo $colors['link_active']; ?>; 
}

.link-stickytopic-hot a:active {
	color: <?php echo $colors['link']; ?>;
}

a.box-switchlink {
	color: <?php echo $colors['link']; ?>;
	text-decoration:none;
}

div.autocomplete {
  margin:0px;  
  padding:0px;  
  width:250px;
  background:#fff;
  border:1px solid #888;
  position:absolute;
}

div.autocomplete ul {
  margin:0px;
  padding:0px;
  list-style-type:none;
}

div.autocomplete ul li.selected { 
  background-color:#E0E0E0; 
}

div.autocomplete ul li {
  margin:0;
  padding:2px;
  height:16px;
  display:block;
  list-style-type:none;
  cursor:pointer;
}

.link-fifa a {
	color: #007213;
}

.link-fifa a:visited { 
	color: #007213;
}

.link-fifa a:hover { 
	color: <?php echo $colors['menu_active']; ?>; 
}
.link-fifa a:active {
	color: <?php echo $colors['menu_active']; ?>;
}

table td.shrink {
    white-space:nowrap;
    font-size:10px;
    padding-right:5px;
}
table td.expand {
    width: 99%
}