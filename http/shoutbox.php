

<!-- 3. Shoutbox code start -->
<div id="chat">
	<div id="subtext">
		<span class="darkgrey-small">Shoutbox language is <b>English</b> only! Join the <b><a href="<? echo getQuakenetWebchatUrl()  ?>" target="_new">chatroom</a></b> to find other players!</span>
	</div>

	<div id="scrollArea">
	<div id="scroller">
	</div>
	</div>
	
	<div id="container">
	<div id='content'>
	</div>
	</div>
	
	<div id="form">		
		<form id="cform" name="chatform" action="#">
			<div id="field_set">
				<input type="text" id="message" name="message" value="" />
				<input type="hidden" id="token" name="token" value="<?php echo $token; ?>" />
				<input type="hidden" id="name" name="name" value="<? if ($inactiveAndBanned) echo "banned"; else echo $cookie_name; ?>" />
				<input type="hidden" id="url" name="url" value="">
				<input type="hidden" id="amount" name="amount" value="<?
$span = 60 * 60 * 24;
$sql = "SELECT COUNT(*) AS amount from wtagshoutbox WHERE name='".$cookie_name."' AND ((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(date)) < ".$span.")";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);
echo $row['amount'];
				?>" />
			</div>
			<div id="copyright" class="buttondiv" style="width: 5%;">
				<p><a href="http://spacegirlpippa.co.uk/" title="original wTag script by spacegirlpippa.co.uk">&copy;</a></p>
			</div>
			<div id="refresh" class="buttondiv">
				<p>refresh</p>
			</div>
			<div id="submit" class="buttondiv">
				<p>submit</p>
			</div>
		</form>
	</div> 
</div>

<!-- 3. Shoutbox code end -->