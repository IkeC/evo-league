<?php


// this contains all commonly used functions

/************************************************************************
/  	SmileyConvert($content,$directory)									/
/	Convert smileys in a message										/
/	Command :															/
/	$newcontent = SmileyConvert($content,$directory);					/
/	$content is the message in wich smileys will be converted			/
/   $directory is set in variable.php (the primary dir of webleague)	/	
/***********************************************************************/

function SmileyConvert($content, $directory) {
	$content = eregi_replace(quotemeta(":)"), '<img src="/smileys/smile.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":("), '<img src="/smileys/sad.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":D"), '<img src="/smileys/biggrin.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":'("), '<img src="/smileys/cry.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":o"), '<img src="/smileys/bigeek.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(";)"), '<img src="/smileys/wink.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta("(y)"), '<img src="/smileys/yes.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta("(n)"), '<img src="/smileys/no.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":p"), '<img src="/smileys/bigrazz.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":@"), '<img src="/smileys/mad.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":s"), '<img src="/smileys/none.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":x"), '<img src="/smileys/dead.gif" valign="middle">', $content);
	// $content = eregi_replace(quotemeta(":b"), '<img src="/smileys/cool.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":h"), '<img src="/smileys/laugh.gif" valign="middle">', $content);
	$content = eregi_replace(quotemeta(":r"), '<img src="/smileys/rolleyes.gif" valign="middle">', $content);
	return $content;
}

/********************************************************************************
/	Get_ip()																	/
/	Return the ip of a visitor													/
/	Command :																	/
/	$ip = Get_ip();																/
/   If fail, $ip = false, else $ip = visitor's ip.								/											
/*******************************************************************************/

function Get_ip() {
	global $REMOTE_ADDR;
	global $HTTP_X_FORWARDED_FOR, $HTTP_X_FORWARDED, $HTTP_FORWARDED_FOR, $HTTP_FORWARDED;
	global $HTTP_VIA, $HTTP_X_COMING_FROM, $HTTP_COMING_FROM;
	global $HTTP_SERVER_VARS, $HTTP_ENV_VARS;

	if (empty ($REMOTE_ADDR)) {
		if (!empty ($_SERVER) && isset ($_SERVER['REMOTE_ADDR'])) {
			$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['REMOTE_ADDR'])) {
				$REMOTE_ADDR = $_ENV['REMOTE_ADDR'];
			} else
				if (!empty ($HTTP_SERVER_VARS) && isset ($HTTP_SERVER_VARS['REMOTE_ADDR'])) {
					$REMOTE_ADDR = $HTTP_SERVER_VARS['REMOTE_ADDR'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['REMOTE_ADDR'])) {
						$REMOTE_ADDR = $HTTP_ENV_VARS['REMOTE_ADDR'];
					} else
						if (@ getenv('REMOTE_ADDR')) {
							$REMOTE_ADDR = getenv('REMOTE_ADDR');
						}
	}
	if (empty ($HTTP_X_FORWARDED_FOR)) {
		if (!empty ($_SERVER) && isset ($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$HTTP_X_FORWARDED_FOR = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['HTTP_X_FORWARDED_FOR'])) {
				$HTTP_X_FORWARDED_FOR = $_ENV['HTTP_X_FORWARDED_FOR'];
			} else
				if (!empty ($HTTP_SERVER_VARS) && isset ($HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'])) {
					$HTTP_X_FORWARDED_FOR = $HTTP_SERVER_VARS['HTTP_X_FORWARDED_FOR'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['HTTP_X_FORWARDED_FOR'])) {
						$HTTP_X_FORWARDED_FOR = $HTTP_ENV_VARS['HTTP_X_FORWARDED_FOR'];
					} else
						if (@ getenv('HTTP_X_FORWARDED_FOR')) {
							$HTTP_X_FORWARDED_FOR = getenv('HTTP_X_FORWARDED_FOR');
						}
	}
	if (empty ($HTTP_X_FORWARDED)) {
		if (!empty ($_SERVER) && isset ($_SERVER['HTTP_X_FORWARDED'])) {
			$HTTP_X_FORWARDED = $_SERVER['HTTP_X_FORWARDED'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['HTTP_X_FORWARDED'])) {
				$HTTP_X_FORWARDED = $_ENV['HTTP_X_FORWARDED'];
			} else
				if (!empty ($HTTP_SERVER_VARS) && isset ($HTTP_SERVER_VARS['HTTP_X_FORWARDED'])) {
					$HTTP_X_FORWARDED = $HTTP_SERVER_VARS['HTTP_X_FORWARDED'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['HTTP_X_FORWARDED'])) {
						$HTTP_X_FORWARDED = $HTTP_ENV_VARS['HTTP_X_FORWARDED'];
					} else
						if (@ getenv('HTTP_X_FORWARDED')) {
							$HTTP_X_FORWARDED = getenv('HTTP_X_FORWARDED');
						}
	}
	if (empty ($HTTP_FORWARDED_FOR)) {
		if (!empty ($_SERVER) && isset ($_SERVER['HTTP_FORWARDED_FOR'])) {
			$HTTP_FORWARDED_FOR = $_SERVER['HTTP_FORWARDED_FOR'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['HTTP_FORWARDED_FOR'])) {
				$HTTP_FORWARDED_FOR = $_ENV['HTTP_FORWARDED_FOR'];
			} else
				if (!empty ($HTTP_SERVER_VARS) && isset ($HTTP_SERVER_VARS['HTTP_FORWARDED_FOR'])) {
					$HTTP_FORWARDED_FOR = $HTTP_SERVER_VARS['HTTP_FORWARDED_FOR'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['HTTP_FORWARDED_FOR'])) {
						$HTTP_FORWARDED_FOR = $HTTP_ENV_VARS['HTTP_FORWARDED_FOR'];
					} else
						if (@ getenv('HTTP_FORWARDED_FOR')) {
							$HTTP_FORWARDED_FOR = getenv('HTTP_FORWARDED_FOR');
						}
	}
	if (empty ($HTTP_FORWARDED)) {
		if (!empty ($_SERVER) && isset ($_SERVER['HTTP_FORWARDED'])) {
			$HTTP_FORWARDED = $_SERVER['HTTP_FORWARDED'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['HTTP_FORWARDED'])) {
				$HTTP_FORWARDED = $_ENV['HTTP_FORWARDED'];
			} else
				if (!empty ($HTTP_SERVER_VARS) && isset ($HTTP_SERVER_VARS['HTTP_FORWARDED'])) {
					$HTTP_FORWARDED = $HTTP_SERVER_VARS['HTTP_FORWARDED'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['HTTP_FORWARDED'])) {
						$HTTP_FORWARDED = $HTTP_ENV_VARS['HTTP_FORWARDED'];
					} else
						if (@ getenv('HTTP_FORWARDED')) {
							$HTTP_FORWARDED = getenv('HTTP_FORWARDED');
						}
	}
	if (empty ($HTTP_VIA)) {
		if (!empty ($_SERVER) && isset ($_SERVER['HTTP_VIA'])) {
			$HTTP_VIA = $_SERVER['HTTP_VIA'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['HTTP_VIA'])) {
				$HTTP_VIA = $_ENV['HTTP_VIA'];
			} else
				if (!empty ($HTTP_SERVER_VARS) && isset ($HTTP_SERVER_VARS['HTTP_VIA'])) {
					$HTTP_VIA = $HTTP_SERVER_VARS['HTTP_VIA'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['HTTP_VIA'])) {
						$HTTP_VIA = $HTTP_ENV_VARS['HTTP_VIA'];
					} else
						if (@ getenv('HTTP_VIA')) {
							$HTTP_VIA = getenv('HTTP_VIA');
						}
	}
	if (empty ($HTTP_X_COMING_FROM)) {
		if (!empty ($_SERVER) && isset ($_SERVER['HTTP_X_COMING_FROM'])) {
			$HTTP_X_COMING_FROM = $_SERVER['HTTP_X_COMING_FROM'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['HTTP_X_COMING_FROM'])) {
				$HTTP_X_COMING_FROM = $_ENV['HTTP_X_COMING_FROM'];
			} else
				if (!empty ($HTTP_SERVER_VARS) && isset ($HTTP_SERVER_VARS['HTTP_X_COMING_FROM'])) {
					$HTTP_X_COMING_FROM = $HTTP_SERVER_VARS['HTTP_X_COMING_FROM'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['HTTP_X_COMING_FROM'])) {
						$HTTP_X_COMING_FROM = $HTTP_ENV_VARS['HTTP_X_COMING_FROM'];
					} else
						if (@ getenv('HTTP_X_COMING_FROM')) {
							$HTTP_X_COMING_FROM = getenv('HTTP_X_COMING_FROM');
						}
	}
	if (empty ($HTTP_COMING_FROM)) {
		if (!empty ($_SERVER) && isset ($_SERVER['HTTP_COMING_FROM'])) {
			$HTTP_COMING_FROM = $_SERVER['HTTP_COMING_FROM'];
		} else
			if (!empty ($_ENV) && isset ($_ENV['HTTP_COMING_FROM'])) {
				$HTTP_COMING_FROM = $_ENV['HTTP_COMING_FROM'];
			} else
				if (!empty ($HTTP_COMING_FROM) && isset ($HTTP_SERVER_VARS['HTTP_COMING_FROM'])) {
					$HTTP_COMING_FROM = $HTTP_SERVER_VARS['HTTP_COMING_FROM'];
				} else
					if (!empty ($HTTP_ENV_VARS) && isset ($HTTP_ENV_VARS['HTTP_COMING_FROM'])) {
						$HTTP_COMING_FROM = $HTTP_ENV_VARS['HTTP_COMING_FROM'];
					} else
						if (@ getenv('HTTP_COMING_FROM')) {
							$HTTP_COMING_FROM = getenv('HTTP_COMING_FROM');
						}
	}

	if (!empty ($REMOTE_ADDR)) {
		$direct_ip = $REMOTE_ADDR;
	}

	$proxy_ip = '';
	if (!empty ($HTTP_X_FORWARDED_FOR)) {
		$proxy_ip = $HTTP_X_FORWARDED_FOR;
	} else
		if (!empty ($HTTP_X_FORWARDED)) {
			$proxy_ip = $HTTP_X_FORWARDED;
		} else
			if (!empty ($HTTP_FORWARDED_FOR)) {
				$proxy_ip = $HTTP_FORWARDED_FOR;
			} else
				if (!empty ($HTTP_FORWARDED)) {
					$proxy_ip = $HTTP_FORWARDED;
				} else
					if (!empty ($HTTP_VIA)) {
						$proxy_ip = $HTTP_VIA;
					} else
						if (!empty ($HTTP_X_COMING_FROM)) {
							$proxy_ip = $HTTP_X_COMING_FROM;
						} else
							if (!empty ($HTTP_COMING_FROM)) {
								$proxy_ip = $HTTP_COMING_FROM;
							}
	if (!empty ($direct_ip)) {
		return $direct_ip;
	} else {
		$is_ip = ereg('^([0-9]{1,3}\.){3,3}[0-9]{1,3}', $proxy_ip, $regs);
		if ($is_ip && (count($regs) > 0)) {
			return $regs[0];
		} else {
			return FALSE;
		}
	}
}

function ReportGame($winnername, $winnername2, $losername, $losername2, $date, $comment, $gameid, 
	$winnerresult, $loserresult, $host, $fairness, $version, $winnerteam, $loserteam, $sixGameId) {
	
	require ('variables.php');
	require ('variablesdb.php');
	require_once ('log/KLogger.php');
	$log = new KLogger('/var/www/yoursite/http/log/report', KLogger::INFO);	
	$log->logInfo('Reporting '.$winnername.' vs. '.$losername);

	$resultText = "";
	
	$comment = htmlentities($comment);
	$comment = nl2br($comment);
	
	if (!empty($winnername2) || !empty($losername2)) {
		// game for team ladder
		$pointsField = "teamPoints";
		$gamesField = "teamGames";
		$winsField = "teamWins";
		$lossesField = "teamLosses"; 
		$drawsField = "teamDraws";
		$teamLadder = 1;
		$ladderType = "Team";
	} else {
		// single ladder
		$pointsField = getPointsFieldForVersion($version);
		$gamesField = getGamesFieldForVersion($version);
		$winsField = getWinsFieldForVersion($version);
		$lossesField = getLossesFieldForVersion($version);
		$drawsField = "draws";
		$teamLadder = 0;
		$ladderType = "Single";
	}
	
	$row =  getRatingAndPointsForPlayer($winnername, $pointsField);
	$ratingOldWinner1 = $row["rating"];	
	$pointsOldWinner1 = $row[$pointsField];

	$row = getRatingAndPointsForPlayer($losername, $pointsField);
	$ratingOldLoser1 = $row["rating"];
	$pointsOldLoser1 = $row[$pointsField];

	$type = "1on1";
	if (!empty($winnername2)) {
		$type = "2on1";
		$row = getRatingAndPointsForPlayer($winnername2, $pointsField);
		$ratingOldWinner2 = $row["rating"];
		$pointsOldWinner2 = $row[$pointsField];
	}
	if (!empty($losername2)) {
		$type = "1on2";
		$row = getRatingAndPointsForPlayer($losername2, $pointsField);
		$ratingOldLoser2 = $row["rating"];
		$pointsOldLoser2 = $row[$pointsField];
	}
	if (!empty($winnername2) && !empty($losername2)) {
		$type = "2on2";
	}

	if (($winnerresult-$loserresult) == 0) {
		$isDraw = 1;
		$winLoss = 0;
		$sqlDraw = " ".$drawsField." = ".$drawsField." + 1, totaldraws = totaldraws + 1, ";
		$addWinOrDefeat = 0;
	} else {
		$isDraw = 0;
		$winLoss = 1;
		$sqlDraw = "";
		$addWinOrDefeat = 1;
	}

	$calc = calculateGamePointsReportGame($winnername, $winnername2, $losername, $losername2, 
		$winnerresult, $loserresult, $winnerteam, $loserteam,  
		$ratingOldWinner1, $ratingOldWinner2, $ratingOldLoser1, $ratingOldLoser2, 
		$pointsOldWinner1, $pointsOldWinner2, $pointsOldLoser1, $pointsOldLoser2, 
		$type, $isDraw, $comment, $sixGameId);
	
	$ratingdiff = $calc["ratingdiff"];
	$ra2newwinneradd = $calc["addPointsWinner"];
	$ra2newloserremL1 = $calc["removePointsLoser1"];
	$ra2newloserremL2 = $calc["removePointsLoser2"];
	$ra2newwinner = $calc["pointsNewWinner1"];
	$ra2newwinner2 = $calc["pointsNewWinner2"];
	$ra2newloser = $calc["pointsNewLoser1"];
	$ra2newloser2 = $calc["pointsNewLoser2"];
	$teamBonusWinner = $calc["teamBonusWinner"];
	$message = $calc["message"];
	
	echo $message;
    
  // $log->logInfo('ra2newwinneradd: '.$ra2newwinneradd);
  // $log->logInfo('ra2newwinner: '.$ra2newwinner);
  // $log->logInfo('ra2newloser: '.$ra2newloser);
    
	if (strlen($comment) > 256) {
		$comment = substr($comment, 0, 255);			
	}
	
	$comment = mysql_real_escape_string($comment);
		
	$dateday = date("d/m/Y");
	$ip = Get_ip();
	$deleted = "no";
	
	$sql = "UPDATE $playerstable ".
		" SET $lossesField = $lossesField + $winLoss,  totallosses = totallosses + $winLoss, ".
		"$gamesField = $gamesField + 1, ".
		"totalgames = totalgames + 1, ".
		$sqlDraw.
		"streakwins = 0, "."streaklosses = streaklosses + $winLoss, ".
		"rating = rating - $ratingdiff, "."$pointsField = $ra2newloser "."WHERE name='$losername'";
    	
		$log->logInfo('sql: '.$sql);
		
		$res = mysql_query($sql);
		$result = $res;

		$log->logInfo('result: '.$result);
	
	$sql = "UPDATE $playerstable ".
		" SET $winsField = $winsField + $winLoss, totalwins = totalwins + $winLoss, $gamesField = $gamesField + 1, ".
		"totalgames = totalgames + 1, "."streakwins = streakwins + $winLoss, "."streaklosses = 0, ".
		$sqlDraw.
		"rating = rating + $ratingdiff, "."$pointsField = $ra2newwinner "."WHERE name='$winnername'";
    	
		$log->logInfo('sql: '.$sql);
		
    $res = mysql_query($sql);
		$result = $result + $res;

		$log->logInfo('result: '.$result);
	
	if (!empty($losername2)) {
		$sql = "UPDATE $playerstable ".
			" SET $lossesField = $lossesField + $winLoss,  totallosses = totallosses + $winLoss, ".
			"$gamesField = $gamesField + 1, ".
			"totalgames = totalgames + 1, "."streakwins = 0, "."streaklosses = streaklosses + $winLoss, ".
			$sqlDraw.
			"rating = rating - $ratingdiff, "."$pointsField = $ra2newloser2 "."WHERE name='$losername2'";
		
		$log->logInfo('sql: '.$sql);
			
		$res = mysql_query($sql);
		$result = $result + $res;

		$log->logInfo('result: '.$result);
		
//		echo "<p>".$sql." - <b>$res</b></p>";
	}
	
	if (!empty($winnername2)) {
		$sql = "UPDATE $playerstable ".
			" SET $winsField = $winsField + $winLoss, totalwins = totalwins + $winLoss, $gamesField = $gamesField + 1, ".
			"totalgames = totalgames + 1, "."streakwins = streakwins + $winLoss, "."streaklosses = 0, ".
			$sqlDraw.
			"rating = rating + $ratingdiff, "."$pointsField = $ra2newwinner2 "."WHERE name='$winnername2'";
		
		$log->logInfo('sql: '.$sql);
		
		$res = mysql_query($sql);
		$result = $result + $res;
		
		$log->logInfo('result: '.$result);
		
//		echo "<p>".$sql." - <b>$res</b></p>";
	}
	
	if (is_null($sixGameId)) {
    $sql = "INSERT INTO $gamestable ".
      "(isDraw, winner, winner2, loser, loser2, date, winnerresult, loserresult, comment, ".
      "dateday, winpoints, losepoints, losepoints2, ratingdiff, ip, deleted, ".
      "host, season, fairness, version, winnerteam, loserteam, teamBonus, teamLadder) ".
      "VALUES ('$isDraw', '$winnername', '$winnername2', '$losername', '$losername2', '$date', '$winnerresult', '$loserresult', '$comment', ".
      "'$dateday', '$ra2newwinneradd', '$ra2newloserremL1', '$ra2newloserremL2', '$ratingdiff', '$ip', '$deleted', '$host', ".
      "'$season', '$fairness', '$version', '$winnerteam', '$loserteam', '$teamBonusWinner', '$teamLadder')";
  } else {
    $sql = "INSERT INTO $gamestable ".
      "(isDraw, winner, winner2, loser, loser2, date, winnerresult, loserresult, comment, ".
      "dateday, winpoints, losepoints, losepoints2, ratingdiff, ip, deleted, ".
      "host, season, fairness, version, winnerteam, loserteam, teamBonus, teamLadder, sixGameId) ".
      "VALUES ('$isDraw', '$winnername', '$winnername2', '$losername', '$losername2', '$date', '$winnerresult', '$loserresult', '$comment', ".
      "'$dateday', '$ra2newwinneradd', '$ra2newloserremL1', '$ra2newloserremL2', '$ratingdiff', '$ip', '$deleted', '$host', ".
      "'$season', '$fairness', '$version', '$winnerteam', '$loserteam', '$teamBonusWinner', '$teamLadder', '$sixGameId')";
  }
  $log->logInfo('sql: '.$sql);
  
  $res = mysql_query($sql);
  $result = $result + $res;

  $log->logInfo('result: '.$result);

	if ($result > 2) { 
		$resultText .= "<p>Your game was successfully reported for the ".$ladderType." Ladder.</p>";
	} else {
		$resultText .= "<p>There seems to be a problem with the database, your game has not been reported.</p>".
			"<p>Please contact an admin if the problem persists.</p>"."<p>Error code: <b>$result</p>";
	}
	
	echo $resultText;
	return $result;
}

function getRatingDifference($oldWinnerRating, $oldLoserRating) {
	return round((32 * (1 - (1 / (pow(10, (- ($oldWinnerRating - $oldLoserRating) / 400)) + 1)))));
}

function getLadderPointsBetterWon($pointsOldWinner, $pointsOldLoser) {
	$result = round(64 * (1 - (1 / (pow(10, ($pointsOldWinner - $pointsOldLoser) / 650) + 1))));
	return $result;
}

function getLadderPointsBetterLost($pointsOldWinner, $pointsOldLoser) {
	$normal = getLadderPointsBetterWon($pointsOldWinner, $pointsOldLoser);
	$drawdiff = getDrawDifference(abs($pointsOldWinner-$pointsOldLoser));
	if ($pointsOldWinner < $pointsOldLoser) {
		$drawdiff = $drawdiff*2;
	}
	// echo "<p>Better player lost - Normal diff: $normal - Drawdiff: $drawdiff - Totaldiff: $totaldiff</p>";
	$result = $normal + $drawdiff; 
	return $result;
}

function getLadderPointsDraw($pointsOldWinner, $pointsOldLoser) {
	return getDrawDifference(abs($pointsOldWinner-$pointsOldLoser));
}

function getDrawDifference($pointsdiff)  {
	$result = round(sqrt($pointsdiff)*0.5);
	return $result;
}

function getLosePoints($oldPoints, $removePoints, &$losePoints) {
	$newPoints = $oldPoints - $removePoints;
	if ($newPoints < 0) {
		$newPoints = 0;
		$losePoints = $oldPoints;
	} else {
		$losePoints = $removePoints;
	}
	return $newPoints;
}

function getRatingAndPointsForPlayer($name, $pointsField) {
	require ('variables.php');
	require ('variablesdb.php');
	$sql = "SELECT rating, $pointsField FROM $playerstable WHERE name = '$name'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	return $row;
}

function calculateGamePointsReportGame(
	&$winner1, &$winner2, 
	&$loser1, &$loser2, 
	$winnerresult, $loserresult, 
	&$winnerteam, &$loserteam, 
	$ratingOldWinner1, $ratingOldWinner2, $ratingOldLoser1, $ratingOldLoser2, 
	$pointsOldWinner1, $pointsOldWinner2,$pointsOldLoser1, $pointsOldLoser2, 
	$type,
	$isDraw,
	&$comment,
  $sixGameId) {
	
	$msg = "";
	$singleBonus = 1.4;
	$doublePenalty = 0.6;
		
	if ($type == "1on1") {
		$ratingoldwinnerAvg = $ratingOldWinner1;
		$ra2oldwinnerAvg = $pointsOldWinner1;
		$ratingoldloserAvg = $ratingOldLoser1;
		$ra2oldloserAvg = $pointsOldLoser1;
	} else if ($type == "2on1") {
		$ratingoldwinnerAvg = round(($ratingOldWinner1+$ratingOldWinner2)/2);
		$ra2oldwinnerAvg = round(($pointsOldWinner1+$pointsOldWinner2)/2);
		$ratingoldloserAvg = $ratingOldLoser1;
		$ra2oldloserAvg = $pointsOldLoser1;
		$msg .= "<p>".getPlayerLinkBold($winner1)."&ndash;".getPlayerLinkBold($winner2)." points average: <b>$ra2oldwinnerAvg</b></p>";
	} else if ($type == "1on2") {
		$ratingoldwinnerAvg = $ratingOldWinner1;
		$ra2oldwinnerAvg = $pointsOldWinner1;
		$ratingoldloserAvg = round(($ratingOldLoser1+$ratingOldLoser2)/2);
		$ra2oldloserAvg = round(($pointsOldLoser1+$pointsOldLoser2)/2);
		$msg .= "<p>".getPlayerLinkBold($loser1)."&ndash;".getPlayerLinkBold($loser2)." points average: <b>$ra2oldloserAvg</b></p>";
	} else {
		$ratingoldwinnerAvg = round(($ratingOldWinner1+$ratingOldWinner2)/2);
		$ra2oldwinnerAvg = round(($pointsOldWinner1+$pointsOldWinner2)/2);
		$ratingoldloserAvg = round(($ratingOldLoser1+$ratingOldLoser2)/2);
		$ra2oldloserAvg = round(($pointsOldLoser1+$pointsOldLoser2)/2);
		$msg .= "<p>".getPlayerLinkBold($winner1)."&ndash;".getPlayerLinkBold($winner2)." points average: <b>$ra2oldwinnerAvg</b></p>";
		$msg .= "<p>".getPlayerLinkBold($loser1)."&ndash;".getPlayerLinkBold($loser2)." points average: <b>$ra2oldloserAvg</b></p>";
	}
	
	$ratingdiff = getRatingDifference($ratingoldwinnerAvg, $ratingoldloserAvg); 
	
	if ($isDraw > 0) {
		$ra2newwinneradd = getLadderPointsDraw($ra2oldloserAvg, $ra2oldwinnerAvg); 
		$ra2newloserrem = getLadderPointsDraw($ra2oldwinnerAvg, $ra2oldloserAvg);
	} else 	if ($ra2oldwinnerAvg > $ra2oldloserAvg) {
		$ra2newwinneradd = getLadderPointsBetterWon($ra2oldloserAvg, $ra2oldwinnerAvg); 
		$ra2newloserrem = getLadderPointsBetterWon($ra2oldwinnerAvg, $ra2oldloserAvg);
	} else if ($ra2oldwinnerAvg <= $ra2oldloserAvg) {
		$ra2newwinneradd = getLadderPointsBetterLost($ra2oldloserAvg, $ra2oldwinnerAvg); 
		$ra2newloserrem = getLadderPointsBetterLost($ra2oldwinnerAvg, $ra2oldloserAvg);
	} 
	
	if ($type == "2on1") {
		$penalty = round($ra2newwinneradd*$doublePenalty);
		$winPenalty = $ra2newwinneradd-$penalty;
		$msg.= "<p>$type win points penalty: <b>-$winPenalty</b></p>";
		$ra2newwinneradd = round($penalty);
		$ra2newloserrem = round($ra2newloserrem*$doublePenalty);
	} else if ($type == "1on2") {
		$bonus = round($ra2newwinneradd*$singleBonus);
		$winBonus = $bonus-$ra2newwinneradd;
		$msg.= "<p>$type win points bonus: <b>+$winBonus</b></p>";
		$ra2newwinneradd = round($bonus);
		$ra2newloserrem = round($ra2newloserrem*$singleBonus);
	} else if ($type == "2on2") {
		$ra2newwinneradd = round($ra2newwinneradd);
		$ra2newloserrem = round($ra2newloserrem);
	}
	
	// draw?
	if ($isDraw > 0) {
		$ratingdiff = 0;
		if ($ra2oldwinnerAvg > $ra2oldloserAvg) {
			// point winner(s) must go to the left side
			// names
			flip($winner1, $loser1);
			flip($winner2, $loser2);
			flip($pointsOldWinner1, $pointsOldLoser1);
			flip($pointsOldWinner2, $pointsOldLoser2);
			flip($winnerteam, $loserteam);
			
			if (is_null($sixGameId)) {
        // add reporter to comment 
        $comment = "[reported by $loser1] " . $comment;
      }
		} 
	} 
	
	$cowardFactor = getCowardFactor($ra2oldwinnerAvg, $ra2oldloserAvg);
	
	if ($cowardFactor < 1) {
		$msg .= "<p>500+ ladder points each - win/lose points reduced by factor <b>$cowardFactor</b></p>";
		$ra2newwinneradd = round($ra2newwinneradd*$cowardFactor);
		$ra2newloserrem = round($ra2newloserrem*$cowardFactor);
	}
	
	$teamBonusArray = getTeamBonus($winnerteam, $loserteam, $isDraw, $ra2newwinneradd);
	$teamBonusWinner = $teamBonusArray['bonusWinner'];
	$ra2winneraddbonus = $ra2newwinneradd + $teamBonusWinner;
	
	if ($teamBonusWinner > 0) {
		$teamBonusWinnerDisplay = "(+".$teamBonusWinner." for team)";
	} 

	$msg.= $teamBonusArray['msg'];

	// set new points amount
	$ra2newwinner = $pointsOldWinner1 + $ra2winneraddbonus;
	$msg .= "<p>".getPlayerLinkBold($winner1)." wins $ra2winneraddbonus $teamBonusWinnerDisplay for a total of $ra2newwinner points.</p>";
	
	if (!empty($winner2)) {
		$ra2newwinner2 = $pointsOldWinner2 + $ra2winneraddbonus; 
		$msg .= "<p>".getPlayerLinkBold($winner2)." wins $ra2winneraddbonus $teamBonusWinnerDisplay for a total of $ra2newwinner2 points.</p>";
	}
	
	$ra2newloserremL1 = 0;
	$ra2newloser = getLosePoints($pointsOldLoser1, $ra2newloserrem, $ra2newloserremL1);
	$msg .= "<p>".getPlayerLinkBold($loser1)." loses $ra2newloserremL1 for a total of $ra2newloser points.</p>";
	
	if (!empty($loser2)) {
		$ra2newloserremL2 = 0;
		$ra2newloser2 = getLosePoints($pointsOldLoser2, $ra2newloserrem, $ra2newloserremL2); 
		$msg .= "<p>".getPlayerLinkBold($loser2)." loses $ra2newloserremL2 for a total of $ra2newloser2 points.</p>";
	}
	   	
	$result = array();
	$result["ratingdiff"] = $ratingdiff;
	$result["pointsNewWinner1"] = $ra2newwinner;
	$result["pointsNewWinner2"] = $ra2newwinner2;
	$result["pointsNewLoser1"] = $ra2newloser;
	$result["pointsNewLoser2"] = $ra2newloser2;
	$result["addPointsWinner"] = $ra2winneraddbonus; 
	$result["removePointsLoser1"] = $ra2newloserremL1;
	$result["removePointsLoser2"] = $ra2newloserremL2;
	$result["teamBonusWinner"] = $teamBonusWinner;
	$result["message"] = $msg;
	return $result;
}  

function getCowardFactor($winnerPoints, $loserPoints) {
	$result = 1;
	if ($winnerPoints > 500 && $loserPoints > 500) {
		$result = format2DigitsMax(1000 / ($winnerPoints + $loserPoints));		
	}
	return $result;	
}

/****************************************************************************************
/  	GetInfo($idcontrol,$var)															/
/	Allow you to get back info from cookies/sessions									/
/	Command :																			/
/	$var = 'what_you_need';																/
/ 	$what_you_need = GetInfo($idcontrol,$var);											/
/	$idcontrol is to know if you are using sessions or cookies (set in variabledb.php	/
/   $var is to know what you want to get back (ex : 'username')							/
/	$what_you_need will contain the value 												/	
/***************************************************************************************/

function GetInfo($idcontrol, $var) {
	if ($idcontrol == 'sessions') {
		if (isset ($_SESSION[$var])) {
			return $_SESSION[$var];
		} else {
			return 'null';
		}
	}
	if ($idcontrol == 'cookies') {
		if (isset ($_COOKIE[$var])) {
			return $_COOKIE[$var];
		} else {
			return 'null';
		}
	}
}

/****************************************************************************************
/   DB_size($database)																	/
/	Get the whole database size															/
/	Command :																			/
/	$dbsize = DB_size($database)														/
/ 	$database is the targeted database name												/
/	$dbsize is the targeted database size												/
/	(this script suppose you have selected the database already)						/	
/***************************************************************************************/

function DB_size($database) {
	$sql = "SHOW TABLE STATUS FROM ".$database;
	$result = mysql_query($sql);
	if ($result) {
		$size = 0;
		while ($data = mysql_fetch_array($result)) {
			$size = $size + $data["Data_length"] + $data["Index_length"];
		}
		return $size;
	} else {
		return FALSE;
	}
}

/****************************************************************************************
/   ShowMenu($pagescreename, $pageurl, $pagename, $page)								/
/	Show an element in the menu															/
/	Command :																			/
/	ShowMenu($pagescreename, $pageurl, $pagename, $page)								/
/ 	$pagescreename is the text on the link												/
/	$pageurl is the targeted url (after $directory)										/
/	$pagename is the php name of this page (set by $page = 'xxx'						/
/	$page is the actual page php name													/	
/***************************************************************************************/

function ShowMenuExternal($pagescreename, $pageurl, $pagename, $page) {
	require ('variables.php');
	require ('variablesdb.php');
	echo "<img src='".$directory."/style/MorpheusX/images/darkblue/buttons_spacer.gif' />";
	echo '&nbsp;<a target="_new" href="http://'.$pageurl.'">'.$pagescreename.'</a>';
}


function formatDate($date) {
	return date('m/d/y h:i a', $date);
}

function formatLongDate($date) {
	return date('m/d/Y h:i a', $date);
}

function formatShortDate($date) {
	return date('m/d/y', $date);
}

function formatShortDateAndTime($date) {
	return date('m/d h:i', $date);
}

function formatDateDay($date) {
	return date('d/m/Y', $date);
}

function formatTime($date) {
	return date('h:i a', $date);
}

function formatNewsDate($date) {
	return date('m/d/Y', $date);
}

// colors the player name on light background
function colorNameClass($name, $approved) {
	if ($approved == "no") {
		return 'class="link-inactive"';
	} else {
		return 'class="link-pc"';
	}
}

// escapes nasty html tags from user input
function escapeComment($comment) {
	$comment = str_replace("\r\n", " ", $comment);
	$comment = str_replace("\n", " ", $comment);
	$comment = str_replace("\\", "", $comment);
	$comment = str_replace("<br />", " ", $comment);
	$comment = str_replace("  ", " ", $comment);
	$comment = str_replace("\\'", "'", $comment);
	return $comment;
}

function unhtmlspecialchars( $string )
{
  $string = str_replace ( '&amp;', '&', $string );
  $string = str_replace ( '&#039;', '\'', $string );
  $string = str_replace ( '&quot;', '"', $string );
  $string = str_replace ( '&lt;', '<', $string );
  $string = str_replace ( '&gt;', '>', $string );
  $string = str_replace ( '&uuml;', '?', $string );
  $string = str_replace ( '&Uuml;', '?', $string );
  $string = str_replace ( '&auml;', '?', $string );
  $string = str_replace ( '&Auml;', '?', $string );
  $string = str_replace ( '&ouml;', '?', $string );
  $string = str_replace ( '&Ouml;', '?', $string );   
  return $string;
}

function getPlayerLink($name) {
	return '<a href="/profile.php?name='.$name.'">'.$name.'</a>';
}

function getPlayerLinkBold($name) {
	return '<b><a href="/profile.php?name='.$name.'">'.$name.'</a></b>';
}

function getPlayerLinkId($name, $id) {
	return '<a href="/profile.php?id='.$id.'">'.$name.'</a>';
}

// gets the current rank of a player
function getPlayerPosition($user, $version) {
	require ("variables.php");
	$pointsField = getPointsFieldForVersion($version);
	$lossesField = getLossesFieldForVersion($version);
	$winsField = getWinsFieldForVersion($version);
	$gamesField = getGamesFieldForVersion($version);
	$sortby = "$pointsField DESC, percentage DESC, $lossesField ASC";
	$sql = "SELECT name, $winsField/$gamesField as percentage FROM $playerstable WHERE $gamesField > 0 AND approved='yes' ORDER BY $sortby";
	$result = mysql_query($sql);
	$position = 1;
	while ($row = mysql_fetch_array($result)) {
		if ($row['name'] == $user) {
			return $position;
		} else {
			$position ++;
		}
	}
	return "unknown";
}

// gets the current ranking as an array
function getRankingArray($version) {
	require ('variables.php');
	$pointsField = getPointsFieldForVersion($version);
	$winsField = getWinsFieldForVersion($version);
	$lossesField = getLossesFieldForVersion($version);
	$gamesField = getGamesFieldForVersion($version);
	
	$sortby = $pointsField." DESC, percentage DESC, $lossesField ASC";
	$sql = "SELECT name, $winsField/$gamesField as percentage FROM $playerstable WHERE $gamesField >= 1 AND approved='yes' ORDER BY $sortby";
	$result = mysql_query($sql);
	$position = 1;
	$positions = array ();
	while ($row = mysql_fetch_array($result)) {
		$positions[$row['name']] = $position;
		$position ++;
	}
	return $positions;
}

// calculates an activity factor for a player over the last 10 weeks for statistics
// the newer a game is, the more it counts
// the 'percentage' can be over 100% 
function getActivityForPlayer($user) {
	require ('variables.php');
	$dayspan = 60 * 60 * 24;
	$days = 70;
	$span = $dayspan * $days;
	$sql = "SELECT date, game_id from $gamestable "."where (winner = '$user' or loser = '$user') "."and deleted = 'no' "."and ((UNIX_TIMESTAMP() - date) < $span) "."ORDER BY date desc";

	$result = mysql_query($sql);
	$currentdate = time();

	$activityPoints = 0;
	while ($row = mysql_fetch_array($result)) {
		$date = $row["date"];
		$id = $row["game_id"];
		$datediff = $currentdate - $date;
		$daysago = ceil($datediff / $dayspan);
		$dayspoints = $days - $daysago;
		if ($dayspoints > 0) {
			$activityPoints += $dayspoints;
		} else {
			break;
		}
	}
	$activityPercent = $activityPoints / 15000 * 100;

	return $activityPercent;
}

// validates for valid email-format (eg. when signing up)
function isValidEmailAddress($address) {
	if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $address)) {
		return true;
	} else {
		return false;
	}
}

/*
* Gets the proposed Points for winning / losing to show in profile
* 
* $winnername winnername
* $losername losername
*/
function getProposedPoints($user, $opponent, $version) {
	require ('variables.php');
	require ('variablesdb.php');

	$error = "error";

	$pointsField = getPointsFieldForVersion($version);

	$sql = "SELECT $pointsField FROM $playerstable WHERE name = '$user' and approved = 'yes'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		return $error;
	}
	$ra2olduser = $row[$pointsField];

	$sql = "SELECT $pointsField FROM $playerstable WHERE name = '$opponent' and approved = 'yes'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$num = mysql_num_rows($result);
	if ($num == 0) {
		return $error;
	}
	$ra2oldopponent = $row[$pointsField];

	// <-- Ra2 -->
	$useradd = round(64 * (1 - (1 / (pow(10, ($ra2oldopponent - $ra2olduser) / 550) + 1))));
	$userlose = $useradd;
	$usertotallose = $ra2olduser - $userlose;

	if ($usertotallose < 0) {
		$userlose = $ra2olduser;
	}
	return array ($useradd, $userlose);
}

// sends a new user notification email to the player admin
function sendJoinMail($user) {
	require ('./variables.php');
	require ('./variablesdb.php');
	$subject = "[$leaguename] new user: $user";
	$message = $directory."/Admin/approvePlayers.php";
	$head = "From:".$adminmail."\r\nReply-To:".$adminmail."";
	$toAddress = $admin_signup."@".$leaguename.".com";
	@ mail($toAddress, $subject, $message, $head, $mailconfig);
}

// gets the proper season awards image for 1st to 6th place for the profile
// the images are defined in variables.php  
function getImgForRank($rank, $align) {
	require ('variables.php');
	$img1 = '<img style="vertical-align:'.$align.';" src="'.$directory.'/gfx/';
	$img2 = '" />';

	if ($rank == 1) {
		$img = $img1.$gfx_rank1.$img2;
	} else
		if ($rank == 2) {
			$img = $img1.$gfx_rank2.$img2;
		} else
			if ($rank == 3) {
				$img = $img1.$gfx_rank3.$img2;
			} else
				if ($rank == 4) {
					$img = $img1.$gfx_rank4.$img2;
				} else
					if ($rank == 5) {
						$img = $img1.$gfx_rank5.$img2;
					} else
						if ($rank == 6) {
							$img = $img1.$gfx_rank6.$img2;
						} else {
							$img = "";
						}
	return $img;
}

// gets the current streak for a player (won/lost in row)
function getStreak($user) {
	require ('variables.php');
	$sql = "SELECT winner, winner2, loser, loser2 from $gamestable ".
		"where (winner = '$user' or winner2 = '$user' or loser = '$user' ".
		"or loser2 = '$user') and deleted = 'no' ".
		"order by game_id desc";
	$result = mysql_query($sql);
	$num_rows = mysql_num_rows($result);
	$winner_losestreak = 0;
	$winner_winstreak = 0;
	$result_array = array ();
	$isWinstreak = true;
	for ($i = 0; i < $num_rows; $i ++) {
		$row = mysql_fetch_array($result);
		$row_winner = $row['winner'];
		$row_loser = $row['loser'];
		$row_winner2 = $row['winner2'];
		$row_loser2 = $row['loser2'];
		$isDraw = $row['isDraw'];
		if ($i == 0) {
			$isWinstreak = ($row_winner == $user || $row_winner2 == $user);
			if ($isDraw > 0) {
				break;
			} else if ($isWinstreak) {
				$winner_winstreak ++;
			} else {
				$winner_losestreak ++;
			}
		} else {
			if ($isWinstreak) {
				if (($row_winner == $user || $row_winner2 == $user) && $isDraw == 0) {
					$winner_winstreak ++;
				} else
					break;
			} else {
				if (($row_loser == $user || $row_loser2 == $user) && $isDraw == 0) {
					$winner_losestreak ++;
				} else
					break;
			}
		}
	}
	return array ($winner_winstreak, $winner_losestreak);
}

// gets the player fairness as an average from weblm_players
function getPlayerFairnessArray($name) {
	require ('variables.php');
	$sql = "SELECT avg(fairness) as avg, count(*) as votes FROM $gamestable WHERE deleted = 'no' AND fairness != '' AND loser = '$name'";
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	$avg = $row['avg'];
	$votes = $row['votes'];
	$votesDisplay = "average of ".$votes." votes";

	if ($avg > 0 && $votes > 2) {
		return array (formatFairness($avg), $votesDisplay);
	} else {
		return array ("unknown", "not enough votes yet");
	}
}

// formats the fairness value in green, yellow or red depending on the value
function formatFairness($fairness) {
	if ($fairness >= 4) {
		$color = "#009D0B";
	} else
		if ($fairness >= 3) {
			$color = "#FD8E00";
		} else {
			$color = "#FF0000";
		}
	$formatted = sprintf("%.2f", $fairness);
	return '<font color="'.$color.'">'.$formatted.'</font>';
}

// formats the DC value in green, yellow or red depending on the value
function formatDC($dc, $gamesTotal) {
	if ($gamesTotal < 20) {
    $color = "#9F9F9F";
   } elseif ($dc < 3) {
		$color = "#009D0B";
	} elseif ($dc < 6) {
    $color = "#FD8E00";
  } else {
    $color = "#FF0000";
  }
	$formatted = sprintf("%.2f", $dc);
	return '<font color="'.$color.'">'.$formatted.'</font>';
}

// get player status line for profile
function getPlayerStatusLine($name) {
	require ('variables.php');
	$sql = "SELECT * FROM $playerstatustable WHERE userName = '$name' "."AND active = 'Y' ORDER BY type ASC, date DESC";
	$result = mysql_query($sql);

	$highlight = "&amp;highlight=".$name;

	if (mysql_num_rows($result) < 1) {
		return "";
	}
	$row = mysql_fetch_array($result);
	$forumLink = $row['forumLink'];
  $type = $row['type'];
  $additionalInfo = $row['additionalInfo'];
  $userId = $row['userId'];
  
	$type == 'B' ? ($gfx = $gfx_ban) : ($type == 'W' ? $gfx = $gfx_warn : $gfx = $gfx_inactive);

	$reason = "<span style='font-size:11px'>".$row['reason']."</span>";
  $expireDate = $row['expireDate'];
  
  $expireText = "";
  if ($type != 'I') {
  	if (empty($forumLink)) {
      $link = "http://www.".$leaguename."/forum/viewtopic.php?t=5387"; // "I've been warned/banned!"
    } else {
      $link = $forumLink;
    }
    if (empty($expireDate)) {
      $expireText = "permanent";
    } else {
      $expireDay = $expireDate + (60*60*24);
      $expireDate = mktime(0, 5, 0, date('n', $expireDay), date('j', $expireDay), date('Y', $expireDay));
      $expireText = "expires ".formatLongDate($expireDate)." (in ".formatFutureSpanPlain($expireDate).")";
    }
    $expireText = ' (click for details)</a></b><br><span class="grey-small">'.$expireText.'</span>';
  } else {
  	$link = "http://www.".$leaguename."/forum/viewtopic.php?t=1084";
  }
	$text = '<a href="'.$link.'"><b>'.$reason.'</b>'.$expireText;
  if (strlen($additionalInfo) > 0) {
    $title = " title=\"Wrote '$additionalInfo' in chat\"";
    $img = "<a href=\"$directory/sixserver/insults.php?pid=$userId\"><img $title src='".$directory."/gfx/".$gfx."' style='vertical-align:middle;'></a>";
  } else {
    $img = "<img src='".$directory."/gfx/".$gfx."' style='vertical-align:middle;'>";
  }
	return "<table><tr><td>".$img."&nbsp;</td><td>".$text."</td></tr></table>";
}

function getPlayerStatusIcon($name) {
	require ('variables.php');
	$sql = "SELECT * FROM $playerstatustable WHERE userName = '$name' ".
		"AND active = 'Y' ORDER BY type ASC, date DESC";
	
	$result = mysql_query($sql);

	$highlight = "&amp;highlight=".$name;

	if (mysql_num_rows($result) < 1) {
		return "";
	}
	$row = mysql_fetch_array($result);
	$type = $row['type'];
	$type == 'B' ? ($gfx = $gfx_ban) : ($type == 'W' ? $gfx = $gfx_warn : $gfx = $gfx_inactive);

	$forumLink = $row['forumLink'];
	if (empty ($forumLink)) {
		$link = $directory."/cards.php";
	} else {
		if (strpos($forumLink, "#")) {
			$link = $forumLink;
		} else {
			$link = $forumLink.$highlight;
		}
	}
	$reasonTitle = $row['reason'];
	$reason="";
	$img = "<img border='0' src='".$directory."/gfx/".$gfx."' style='vertical-align:middle;'>";
	$retString = "&nbsp;<span title='".$reason."' alt='".$reason."'><a style='border:0;' href='".$link."'>".$img."</a></span>&nbsp;";

	return $retString;
}

// get the image for player status
function getStatusImgForType($type) {
	require ('variables.php');
	$type == 'B' ? ($gfx = $gfx_ban) : ($type == 'W' ? $gfx = $gfx_warn : $gfx = $gfx_inactive);
	return '<img src="'.$directory.'/gfx/'.$gfx.'" border="0" style="vertical-align:bottom;"/>';
}

// get image for active/inactive
function getImgForActive($active) {
	require ('variables.php');
	$active == 'Y' ? ($gfx = $gfx_active_yes) : ($gfx = $gfx_active_no);
	return '<img src="'.$directory.'/gfx/'.$gfx.'" border="0" style="vertical-align:middle;">';
}

// get image for active/inactive
function getImgForDeletedGame() {
	require ('variables.php');
	return '<img src="'.$directory.'/gfx/'.$gfx_active_no.'" border="0" style="vertical-align:-4;">';
}

function getBoxTop($title, $boxheight, $isNew, $linksArray) {
	return getBoxTopImg($title, $boxheight, $isNew, $linksArray, '', '');
}

function getBoxTopImg($title, $boxheight, $isNew, $linksArray, $bgrImg, $align) {
	require ('variables.php');
	$widthPercent = "100";
	$margin = 'style="margin-right:0px;"';
	if ($isNew) {
		$cornerColor = 'orange';
	} else {
		$cornerColor = 'blue';
	}
	$links = "";
	if (!empty ($linksArray)) {
		foreach ($linksArray as $valArray) {
			$links .= '<span class="img-quote"><a href="'.$valArray[0].'"><img src="'.$directory.'/style/images/'.$valArray[1].'.gif" title="'.$valArray[2].'" border="0"></a></span>';
		}
	}

	$resultString = '<table style="padding-right:10px;" width="'.$widthPercent.'%" cellspacing="0" cellpadding="0">'.'<tr>'.'<td>'.'<table width="'.$widthPercent.'%" class="hdr" cellspacing="0" cellpadding="0">'.'<tr>'.'<td align="left" width="35"><img src="'.$directory.'/style/images/hdr_football.gif" width="35" height="25" alt="" /></td>'.'<td width="100%" valign="middle" align="left" nowrap="nowrap"><span class="cattitle">'.$title.'</span></td>'.'<td width="120" align="right"><img src="'.$directory.'/style/MorpheusX/images/darkblue/hdr_right_'.$cornerColor.'.gif" width="115" height="25" alt="" /></td>'.'</tr>'.'</table>'.'<table ';
	if ($bgrImg != null && strcmp($bgrImg, '' != 0)) {
		$resultString .= 'style="background-image: url(\'/gfx/watermarks/'.$bgrImg.'\'); background-position: '.$align.'; background-repeat: no-repeat;" ';
	}
	$resultString .= 'class="post2" cellspacing="0" cellpadding="0" width="'.$widthPercent.'%" >'.'<tr>'.'<td class="post-right" align="left" valign="top" width="100%">'.'<table valign="top" cellpadding="0" cellspacing="0" width="100%">'.'<tr>'.'<td class="posttop" align="right" valign="top" width="100%"><img valign="top" src="'.$directory.'/style/MorpheusX/images/darkblue/post_top.gif" alt="" border="0" height="8" width="21"></td>'.'<td class="post-buttons" nowrap="nowrap" valign="top">'.$links.'</td>'.'</tr>'.'</table>'.'<table class="boxtable" height="'.$boxheight.'">'.'<tr>'.'<td valign="top" style="margin:0 0 0 0; padding: 0 0 0 0;">';
	return $resultString;
}

function getBoxTopNoMargin($title, $boxheight, $align) {
	require ('variables.php');
	$widthPercent = "100";
	$margin = 'style="margin-right:0px;"';
	$cornerColor = 'blue';
	$resultString = '<table style="padding-right:10px;" width="'.$widthPercent.'%" cellspacing="0" cellpadding="0">'.'<tr>'.'<td>'.'<table width="'.$widthPercent.'%" class="hdr" cellspacing="0" cellpadding="0">'.'<tr>'.'<td align="left" width="35"><img src="'.$directory.'/style/images/hdr_football.gif" width="35" height="25" alt="" /></td>'.'<td width="100%" valign="middle" align="left" nowrap="nowrap"><span class="cattitle">'.$title.'</span></td>'.'<td width="120" align="right"><img src="'.$directory.'/style/MorpheusX/images/darkblue/hdr_right_'.$cornerColor.'.gif" width="115" height="25" alt="" /></td>'.'</tr>'.'</table>'.'<table ';
	$resultString .= 'class="post2" cellspacing="0" cellpadding="0" width="'.$widthPercent.'%" >'.'<tr>'.'<td class="post-right" align="left" valign="top" width="100%" height="'.$boxheight.'">';
	 // '<table valign="top" cellpadding="0" cellspacing="0" width="100%">'.'<tr>'.'<td class="posttop" align="right" valign="top" width="100%"><img valign="top" src="'.$directory.'/style/MorpheusX/images/darkblue/post_top.gif" alt="" border="0" height="8" width="21"></td>'.'<td class="post-buttons" nowrap="nowrap" valign="top">'.$links.'</td>'.'</tr>'.'</table>'.
	 // '<table class="boxtable" height="'.$boxheight.'">'.'<tr>'.'<td valign="top" style="margin:0 0 0 0; padding: 0 0 0 0;">';
	return $resultString;
}

function getBoxBottom() {
	require ('variables.php');
	$widthPercent = "100";
	return '</td>'.'</tr>'.'</table>'.
		'</td>'.'</tr>'.'</table>'.
		'<table width="'.$widthPercent.'%" cellspacing="0" cellpadding="0" border="0">'.
			'<tr>'.
				'<td align="right" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_left.gif" width="8" height="8" alt="" /></td>'.
				'<td class="ftr"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="1" height="8" alt="" /></td>'.
				'<td align="left" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_right.gif" width="8" height="8" alt="" /></td>'.
			'</tr>'.
		'</table>'.
		'</td>'.'</tr>'.'</table>';
}

function getBoxBottomNoMargin() {
	require ('variables.php');
	$widthPercent = "100";
	return //'</td>'.'</tr>'.'</table>'.
		'</td>'.'</tr>'.'</table>'.
		'<table width="'.$widthPercent.'%" cellspacing="0" cellpadding="0" border="0">'.
			'<tr>'.
				'<td align="right" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_left.gif" width="8" height="8" alt="" /></td>'.
				'<td class="ftr"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="1" height="8" alt="" /></td>'.
				'<td align="left" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_right.gif" width="8" height="8" alt="" /></td>'.
			'</tr>'.
		'</table>'.
		'</td>'.'</tr>'.'</table>';
}

function getBoxBottomLinks($links) {

	require ('variables.php');
//	return '</td>'.'<td class="content-right"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="9" height="1" alt="" /></td>'.'</tr>'.'<tr>'.'<td align="right" width="9" valign="top"><img src="'.$directory.'/style/MorpheusX/images/darkblue/c_left_bottom_simple.gif" width="9" height="10" alt="" /></td>'.'<td class="content-bottom" width="100%"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="1" height="10" alt="" /></td>'.'<td valign="top" width="9"><img src="'.$directory.'/style/MorpheusX/images/darkblue/c_right_bottom_simple.gif" width="9" height="10" alt="" /></td>'.'</tr>'.'</table>';

	return '</td>'.'</tr>'.'</table>'.
		'</td>'.'</tr>'.'</table>'.
		'<table width="'.$widthPercent.'%" cellspacing="0" cellpadding="0" border="0">'.
			'<tr>'.
				'<td align="right" width="9" valign="top"><img src="'.$directory.'/style/MorpheusX/images/darkblue/evo_left_bottom_nav.gif" width="9" height="17" alt="" /></td>'.
				'<td class="evo-nav-bottom" width="100%"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="1" height="17" alt="" /></td>'.
				'<td valign="top" width="9"><img src="'.$directory.'/style/MorpheusX/images/darkblue/evo_right_bottom_nav.gif" width="9" height="17" alt="" /></td>'.
			'</tr>'.
		'</table>'.
		'</td>'.'</tr>'.'</table>';

//	return '</td>'.'</tr>'.'</table>'.'</td>'.'</tr>'.'</table>'.'<table width="'.$widthPercent.'%" cellspacing="0" cellpadding="0" border="0">'.'<tr>'.'<td align="right" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_left.gif" width="8" height="8" alt="" /></td>'.'<td class="ftr"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="1" height="8" alt="" /></td>'.'<td align="left" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_right.gif" width="8" height="8" alt="" /></td>'.'</tr>'.'</table>'.'</td>'.'</tr>'.'</table>';

}

function getOuterBoxTop($title_left, $title_right) {
	require ('variables.php');
	return 
		'<table width="100%" cellspacing="0" cellpadding="0">'.
		'<tr>'.
			'<td align="right" valign="bottom"><img src="'.$directory.'/style/MorpheusX/images/darkblue/c_left_top_nav.gif" width="9" height="24" alt="" /></td>'.
			'<td class="navbar-top" valign="middle">'.
				'<table border="0" cellspacing="0" cellpadding="0" width="100%">'.
					'<tr>'.
						'<td align="left" class="navbar-links">'.$title_left.'</td>'.
						'<td align="right" class="navbar-text">'.$title_right.'</td>'.
					'</tr>'.
				'</table>'.
			'</td>'.
			'<td valign="bottom"><img src="'.$directory.'/style/MorpheusX/images/darkblue/c_right_top_nav.gif" width="9" height="24" alt="" /></td>'.
		'</tr>'.
		'<tr>'.
			'<td class="content-left"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="9" height="1" alt="" /></td>'
			.'<td class="content content-navbar">';
}

function getOuterBoxBottom() {
	require ('variables.php');
	return '</td>'.
			'<td class="content-right"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="9" height="1" alt="" /></td>'.
		'</tr>'.
		'<tr>'.
			'<td align="right" width="9" valign="top"><img src="'.$directory.'/style/MorpheusX/images/darkblue/c_left_bottom_simple.gif" width="9" height="10" alt="" /></td>'.
			'<td class="content-bottom" width="100%"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="1" height="10" alt="" /></td>'.
			'<td valign="top" width="9"><img src="'.$directory.'/style/MorpheusX/images/darkblue/c_right_bottom_simple.gif" width="9" height="10"  /></td>'
		.'</tr>'.'</table>';
}

function getOuterBoxBottomLinks($title_left, $title_right) {
	require ('variables.php');
	return '</td>'.
			'<td class="content-right"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="9" height="1" alt="" /></td>'.
		'</tr>'.
		'<tr>'.
				'<td align="right" width="9" valign="top"><img src="'.$directory.'/style/MorpheusX/images/darkblue/evo_outer_left_bottom_nav.gif" width="9" height="22" alt="" /></td>'.
				'<td class="evo-outer-nav-bottom" valign="middle" width="100%">'.
					'<table border="0" cellspacing="0" cellpadding="0" width="100%">'.
						'<tr>'.
							'<td align="left" class="navbar-text">'.$title_left.'</td>'.
							'<td align="right" class="navbar-text">'.$title_right.'</td>'.
						'</tr>'.
					'</table>'.
				'</td>'.
				'<td valign="top" width="9"><img src="'.$directory.'/style/MorpheusX/images/darkblue/evo_outer_right_bottom_nav.gif" width="9" height="22" alt="" /></td>'.
			'</tr>'.
		'</table>';
}

function getRankBoxTop($title, $columnTitlesArray) {
	require ('variables.php');
	$widthPercent = "100";
	$margin = 'style="margin-right:15px;"';

	$columnsTable = '<table class="forumline" ';
	$columnsTable .= ' align="center" border="0" cellpadding="4" cellspacing="1" width="100%">'.'<tr>';

	foreach ($columnTitlesArray as $colTitle) {
		$columnsTable .= '<th class="thTop" nowrap="nowrap">&nbsp;'.$colTitle.'&nbsp;</th>';
	}
	$columnsTable .= '</tr>';

	$resultString = '<table style="padding-right:10px;';
	$resultString .= '" width="'.$widthPercent.'%" cellspacing="0" cellpadding="0"><tr><td>'.'<table ';
	$resultString .= ' width="'.$widthPercent.'%" class="hdr" cellspacing="0" cellpadding="0">'.'<tr>'.'<td align="left" width="35"><img src="'.$directory.'/style/images/hdr_football.gif" width="35" height="25" alt="" /></td>'.'<td width="100%" valign="middle" align="left" nowrap="nowrap"><span class="cattitle">'.$title.'</span></td>'.'<td width="120" align="right"><img src="'.$directory.'/style/MorpheusX/images/darkblue/hdr_right_blue.gif" width="115" height="25" alt="" /></td>'.'</tr>'.'</table>'.$columnsTable;
	return $resultString;
}

function getCrosstabTop($title, $columnTitlesArray) {
	require ('variables.php');

	$widthPercent = "100";
	$margin = 'style="margin-right:15px;"';

	$columnsTable = '<table class="forumline" ';
	$columnsTable .= ' align="center" border="0" cellpadding="4" cellspacing="1" width="100%">'.'<tr>';

	foreach ($columnTitlesArray as $colTitle) {
		$columnsTable .= '<td style="background-color:#7393cd;text-align:center" nowrap="nowrap">&nbsp;'.$colTitle.'&nbsp;</th>';
	}
	$columnsTable .= '</tr>';

	$resultString = '<table style="padding-right:10px;';
	$resultString .= '" width="'.$widthPercent.'%" cellspacing="0" cellpadding="0"><tr><td>'.'<table ';
	$resultString .= ' width="'.$widthPercent.'%" class="hdr" cellspacing="0" cellpadding="0">'.'<tr>'.'<td align="left" width="35"><img src="'.$directory.'/style/images/hdr_football.gif" width="35" height="25" alt="" /></td>'.'<td width="100%" valign="middle" align="left" nowrap="nowrap"><span class="cattitle">'.$title.'</span></td>'.'<td width="120" align="right"><img src="'.$directory.'/style/MorpheusX/images/darkblue/hdr_right_blue.gif" width="115" height="25" alt="" /></td>'.'</tr>'.'</table>'.$columnsTable;
	return $resultString;
}

function getRankBoxBottom() {
	require ('variables.php');
	$widthPercent = "100";
	return '</table>'.'<table width="'.$widthPercent.'%" cellspacing="0" cellpadding="0" border="0">'.'<tr>'.'<td align="right" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_left.gif" width="8" height="8" alt="" /></td>'.'<td class="ftr"><img src="'.$directory.'/style/MorpheusX/images/darkblue/spacer.gif" width="1" height="8" alt="" /></td>'.'<td align="left" width="8"><img src="'.$directory.'/style/MorpheusX/images/darkblue/ftr_right.gif" width="8" height="8" alt="" /></td>'.'</tr>'.'</table></td></tr></table>';
}

function getCheckboxesForAllVersions($setCheckedString) {
	require ('variables.php');
	$sql = "SELECT * from $versionstable";
	$result = mysql_query($sql);
	$count = 0;
	$resultString = "<tr>";
	while ($row = mysql_fetch_array($result)) {
		$count ++;
		$name = $row['name'];
		$image = $row['image'];
		$version = $row['version'];
		$resultString .= "<tr>";
		$resultString .= "<td>";
		if ($count == 1) {
			$resultString .= "Games";
		}
		$resultString .= "</td>";
		$resultString .= '<td><input type="checkbox" ';
		if (stristr($setCheckedString, $version)) {
			$resultString .= 'checked="checked" ';
		}
		$resultString .= 'name="version_'.$version.'" value="'.$version.'" />';
		$resultString .= '&nbsp;&nbsp;<img style="vertical-align:middle;" src="'.$versionspath.$image.'">&nbsp;&nbsp;'.$name.'</td>';
		$resultString .= "<td>";
		if ($count == 1) {
			$resultString .= "Game versions you play online";
		}
		$resultString .= "</td></tr>";
	}
	return $resultString;
}

function getCheckboxesForSupportedVersions($setCheckedString) {
	require ('variables.php');
	$sql = "SELECT * from $versionstable WHERE INSTR('".GetSupportedVersions()."',version) > 0 ORDER BY id ASC";
	$result = mysql_query($sql);
	$count = 0;
	$resultString = "<tr>";
	while ($row = mysql_fetch_array($result)) {
		$count ++;
		$name = $row['name'];
		$image = $row['image'];
		$version = $row['version'];
		$resultString .= "<tr>";
		$resultString .= "<td>";
		if ($count == 1) {
			$resultString .= "Games";
		}
		$resultString .= "</td>";
		$resultString .= '<td><input type="checkbox" ';
		if (stristr($setCheckedString, $version)) {
			$resultString .= 'checked="checked" ';
		}
		$resultString .= 'name="version_'.$version.'" value="'.$version.'" />';
		$resultString .= '&nbsp;&nbsp;<img style="vertical-align:middle;" src="'.$versionspath.$image.'">&nbsp;&nbsp;'.$name.'</td>';
		$resultString .= "<td>";
		if ($count == 1) {
			$resultString .= "Game versions you play online";
		}
		$resultString .= "</td></tr>";
	}
	return $resultString;
}

function getSelectboxForAllVersions($setDefault) {
	return getSelectboxForAllVersionsInput($setDefault, 'defaultversion');
}

function getSelectboxForAllVersionsInput($setDefault, $inputName) {
	require ('variables.php');
	$sql = "SELECT * from $versionstable";
	$result = mysql_query($sql);
	$count = 0;
	$resultString = "<select name='".$inputName."' class='width150'>";
	while ($row = mysql_fetch_array($result)) {
		$count ++;
		$name = $row['name'];
		$image = $row['image'];
		$version = $row['version'];
		$resultString .= '<option ';
		if ($version == $setDefault) {
			$resultString .= 'selected ';
		}
		$resultString .= 'value="'.$version.'">'.$name.'</option>';
	}
	$resultString .= "</select>";
	return $resultString;
}

function getSelectboxForSupportedVersions($setDefault) {
	return getSelectboxForSupportedVersionsInput($setDefault, 'defaultversion');
}

function getSelectboxForSupportedVersionsInput($setDefault, $inputName) {
	require ('variables.php');
	$sql = "SELECT * from $versionstable WHERE INSTR('".GetSupportedVersions()."',version) > 0 ORDER BY id ASC";
	$result = mysql_query($sql);
	$count = 0;
	$resultString = "<select name='".$inputName."' class='width150'>";
	while ($row = mysql_fetch_array($result)) {
		$count ++;
		$name = $row['name'];
		$image = $row['image'];
		$version = $row['version'];
		$resultString .= '<option ';
		if ($version == $setDefault) {
			$resultString .= 'selected ';
		}
		$resultString .= 'value="'.$version.'">'.$name.'</option>';
	}
	$resultString .= "</select>";
	return $resultString;
}

function getImgForVersion($version) {
	require ('variables.php');
	$sql = "SELECT * from $versionstable where version = '$version'";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$image = $row['image'];
		$version = $row['version'];
		$resultString = "<img width='16' height='16' style='vertical-align:middle' src='".$versionspath.$image."' "."title='".$name."'>";
	}
	return $resultString;
}

function getGamesSelectboxForPlayer($playerName) {
	require ('variables.php');
	$resultString = '<select class="width150" name="version">';
	$sql = "SELECT versions, defaultversion from $playerstable where name='$playerName'";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$versions = $row['versions'];
		$defaultversion = $row['defaultversion'];
		$sql2 = "SELECT version, name from $versionstable";
		$result2 = mysql_query($sql2);
		while ($row2 = mysql_fetch_array($result2)) {
			$name = $row2['name'];
			$version = $row2['version'];
			// no manual reporting for PES6
			if (stristr($versions, $version) && ($playerName == 'Ike' || !stristr($versions_pes6, $version))) {
			// if (stristr($versions, $version)) {
				$resultString .= "<option value='".$version."' ";
				if ($version == $defaultversion) {
					$resultString .= "selected";
				}
				$resultString .= ">".$name."</option>";
			}
		}
	}
	$resultString .= "</select>";
	return $resultString;
}

function getVersionsImages($versions) {
	require ('variables.php');
	$resultString = "";
	$sql = "SELECT version, name, image from $versionstable order by id asc";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$version = $row['version'];
		$image = $row['image'];
		if (stristr($versions, $version)) {
			$resultString .= "<img height='16' width='16' style='vertical-align:middle' src='".$versionspath.$image."' title='".$name."'>&nbsp;";
		}
	}
	return $resultString;
}

function getVersionsImagesNoSpace($versions) {
	require ('variables.php');
	$resultString = "";
	$sql = "SELECT version, name, image from $versionstable order by id asc";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$version = $row['version'];
		$image = $row['image'];
		if (stristr($versions, $version)) {
			$resultString .= "<img height='16' width='16' style='vertical-align:middle;margin:0 2px 2px 0;' src='".$versionspath.$image."' title='".$name."'>";
		}
	}
	return $resultString;
}

function GetVersionsNames($versions) {
	require ('variables.php');
	$resultString = "";
	$sql = "SELECT version, name FROM $versionstable ORDER BY ID ASC";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$version = $row['version'];
		if (stristr($versions, $version)) {
			$name = $row['name'];
			if (strlen($resultString) > 0) {
				$resultString.="/";
			}
			$resultString .= $name;
		}
	}
	return $resultString;
}

function getCheckboxesForAllVersionsSearch($setCheckedString) {
	require ('variables.php');
	$sql = "SELECT * from $versionstable";
	$result = mysql_query($sql);
	$count = 0;
	$resultString = "";
	while ($row = mysql_fetch_array($result)) {
		$count ++;
		$name = $row['name'];
		$image = $row['image'];
		$version = $row['version'];
		$resultString .= '<input type="checkbox" ';
		if (stristr($setCheckedString, $version)) {
			$resultString .= 'checked="checked" ';
		}
		$resultString .= 'name="version_'.$version.'" value="'.$version.'" />';
		$resultString .= '&nbsp;<img style="vertical-align:middle;" title="'.$name.'" src="'.$versionspath.$image.'">&nbsp;&nbsp;';
	}
	return $resultString;
}

function getTeamsAllOptions($selected) {
	require_once('variables.php');
	require_once('variablesdb.php');
	$result = '';
	$separator = getTeamsOptionsSeparator();
	$oldscope = '';
	$teamsInstance = getTeamsArray();
	foreach ($teamsInstance as $rowArray) {
		$scope = $rowArray[2];
		if ($scope != $oldscope) {
			$result .= $separator.'<option value="0">-  '.getTeamScopeName($scope).'</option>'.$separator;
			$oldscope = $scope;
		}

		$result .= '<option ';
		if ($rowArray[0] == $selected) {
			$result .= 'selected ';
		}
		$result .= 'value="'.$rowArray[0].'">'.$rowArray[1].'</option>';
	}
	return $result;
}

function getTeamsAllJavascript() {
	require_once('variables.php');
	require_once('variablesdb.php');
	$result = "";
	$teamsInstance = getTeamsArray();
	foreach ($teamsInstance as $rowArray) {
		$result .= '"'.addslashes($rowArray[1]).'", ';
	}
	$result = substr($result, 0, strlen($result)-2);
	return $result;
}

function getPatchesAllJavascript() {
	require_once('variables.php');
	require_once('variablesdb.php');
	$result = "";
	$sql = "SELECT name FROM six_patches GROUP BY name ORDER BY name ASC";
  $res = mysql_query($sql);
  while ($row = mysql_fetch_array($res)) {
		$result .= '"'.addslashes($row[0]).'", ';
	}
	$result = substr($result, 0, strlen($result)-2);
	return $result;
}
function getPlayersActiveJavascript() {
	require_once('variables.php');
	require_once('variablesdb.php');
	$result = "";
	$playersInstance = getPlayersApprovedArray();
	foreach ($playersInstance as $name) {
		$result .= '"'.addslashes($name).'", ';
	}
	$result = substr($result, 0, strlen($result)-2);
	return $result;
}

function getPlayersAllJavascript() {
	require_once('variables.php');
	require_once('variablesdb.php');
	$result = "";
	$playersInstance = getPlayersAllArray();
	foreach ($playersInstance as $name) {
		$result .= '"'.addslashes($name).'", ';
	}
	$result = substr($result, 0, strlen($result)-2);
	return $result;
}

function getTeamsArray() {
	require ('variables.php');
	static $teamsInstance;
	if ($teamsInstance == null) {
		$teamsInstance = array ();
		$sql = "SELECT * FROM $teamstable order by scope asc, country asc, name asc";
		$result = mysql_query($sql);
		//echo "<!--";
		while ($row = mysql_fetch_array($result)) {
			$id = $row['ID'];
			$name = $row['NAME'];
			$scope = $row['SCOPE'];
			$category = $row['CATEGORY'];
			$abbreviation = $row['ABBREVIATION'];
      $country = $row['COUNTRY'];
			$rowArray = array ();
			$rowArray[] = $id;
			$rowArray[] = $name;
			$rowArray[] = $scope;
			$rowArray[] = $category;
			$rowArray[] = $abbreviation;
      $rowArray[] = $country;
			$teamsInstance[] = $rowArray;
			//echo $category.".".$name;
		}
		//echo "-->";
	}
	return $teamsInstance;
}

function getPlayersApprovedArray() {
	require('variables.php');
	require_once('variablesdb.php');
	static $playersApprovedInstance;
	global $resettimeApproved;
	$time = time();
	if ((($time - $resettimeApproved) > 120) || $playersApprovedInstance == null) {
		$resettimeApproved = time();
		$playersApprovedInstance = array();
		$sql = "SELECT name FROM ".$playerstable." where approved = 'yes' order by name asc";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			$playersApprovedInstance[] = $row['name'];
		}
	}
	return $playersApprovedInstance;
}


function getPlayersAllArray() {
	require('variables.php');
	require_once('variablesdb.php');
	static $playersAllInstance;
	global $resettimeAll;
	$time = time();
	if ((($time - $resettimeAll) > 120) || $playersAllInstance == null) {
		$resettimeAll = time();
		$playersAllInstance = array ();
		$sql = "SELECT name, hash6, player_id FROM ".$playerstable." ORDER BY name ASC";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			$rowName = $row['name'];
      if (strlen($row['hash6']) > 0) {
        $sql = "SELECT name FROM six_profiles WHERE user_id=".$row['player_id']." ORDER BY name ASC";
        $profiles = array();
        $resultProfiles = mysql_query($sql);
        while ($rowProfiles = mysql_fetch_array($resultProfiles)) {
          $profiles[] = $rowProfiles['name'];
        }
        if (sizeof($profiles) > 0) {
          $rowName .= " (".getCommaSeparatedString($profiles).")";
        }
      }
      $playersAllInstance[] = $rowName;
		}
	}
	return $playersAllInstance;
}

function getTeamsAllArray() {
	require_once('variables.php');
	require_once('variablesdb.php');
	static $teamsAllInstance;
	global $resettimeTeams;
	$time = time();
	if ((($time - $resettimeTeams) > 120) || $teamsAllInstance == null) {
		$resettimeTeams = time();
		$teamsAllInstance = array();
		$sql = "SELECT id, name FROM $teamstable order by name asc";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result)) {
			$teamsAllInstance[] = array($row['id'], $row['name']);
		}
	}
	return $teamsAllInstance;
}

function getTeamScopeName($scope) {
	if ($scope == 'A') {
		return "Premier League";
	} else
		if ($scope == 'B') {
			return "Serie A";
		} else
			if ($scope == 'C') {
				return "La Liga";
			} else
				if ($scope == 'D') {
					return "Bundesliga";
				} else
					if ($scope == 'E') {
						return "Ligue 1";
					} else
						if ($scope == 'F') {
							return "Eredivisie";
						} else
							if ($scope == 'O') {
								return "Other Club Teams";
							} else
								if ($scope == 'X') {
									return "National Teams";
								}
}

function getTeamOptionForId($id) {
	$teamsInstance = getTeamsArray();
	if ($id == "0") {
		return "";
	}
	foreach ($teamsInstance as $rowArray) {
		if ($rowArray[0] == $id) {
			return '<option value="'.$id.'">'.$rowArray[1].'</option>';
		}
	}
}


function getCategoryForTeam($team) {
	$teamsInstance = getTeamsArray();
	foreach ($teamsInstance as $rowArray) {
		if ($rowArray[0] == $team) {
			return $rowArray[3];
		}
	}
	return null;
}

function getNameForTeam($team) {
	$teamsInstance = getTeamsArray();
	foreach ($teamsInstance as $rowArray) {
		if ($rowArray[0] == $team) {
			return $rowArray[1];
		}
	}
	return null;
}

function getCategoriesArray() {
	require ('variables.php');
	static $categoriesInstance;
	if ($categoriesInstance == null) {
		$categoriesInstance = array ( '1' => '24', '2' => '18', '3' => '12', '4' => '6', '5' => '0', '0' => '0');
	}
	return $categoriesInstance;
}

function getTeamBonus($winteam, $loseteam, $isDraw, $winpoints) {
	// team bonus
	$msg = "";
	$winteamCategory = getCategoryForTeam($winteam);
	$loseteamCategory = getCategoryForTeam($loseteam);
	
	$categories = getCategoriesArray();
	$bonusWinner = 0;
	
	if ($winteamCategory == 0 || $loseteamCategory == 0) {
		$msg.= "<p>Uncategorised team(s), no team bonus</p>";
	} else {
		$winteamPoints = $categories[$winteamCategory];
		$loseteamPoints = $categories[$loseteamCategory];
		$winteamName = getNameForTeam($winteam);
		$loseteamName = getNameForTeam($loseteam);	
		if ($winteamPoints < $loseteamPoints) {
		 	$msg.= "<p>$winteamName team points: <b>$winteamPoints</b><br>";
		 	$msg.= "$loseteamName team points: <b>$loseteamPoints</b><br>";
		 	if ($isDraw > 0) {
		 		$normalBonus = abs($loseteamPoints-$winteamPoints);
				$bonusFactor = format2DigitsMax($winpoints/30);
				$bonusWinner = round($normalBonus*$bonusFactor);
		 		$msg.= "Team draw bonus using $winteamName: $normalBonus*$bonusFactor ~ <b>$bonusWinner</b></p>";
		 	} else {
			 	$bonusWinnerDifference = $loseteamPoints-$winteamPoints;
			 	$bonusFactor = format2DigitsMax($winpoints/30);
			 	$bonusWinner = round($bonusWinnerDifference*$bonusFactor);
			 	$msg.= "Team bonus using $loseteamName: $bonusWinnerDifference*$bonusFactor ~ <b>$bonusWinner</b></p>";
		 	}	 		
		} else if ($winteamPoints == $loseteamPoints) {
			$msg.= "<p>Equal teams, no team bonus</p>";
		} else if ($isDraw > 0) {
			$msg.= "<p>No team bonus</p>";
		} else {
			$msg = "<p>Better team won, no team bonus</p>";
		}	 	
	}
	return array('bonusWinner' => $bonusWinner, 'msg' => $msg);
}

function getTeamNameForId($id) {
	$teamsInstance = getTeamsArray();
	if ($id == "0") {
		return "";
	}
	foreach ($teamsInstance as $rowArray) {
		if ($rowArray[0] == $id) {
			return $rowArray[1];
		}
	}
}

function getTeamNameForIdAdditionalInfo($id) {
	$teamsInstance = getTeamsArray();
	if ($id == "0") {
		return "";
	}
	foreach ($teamsInstance as $rowArray) {
		if ($rowArray[0] == $id) {
			if ($rowArray[5] == "") {
        $ret = '<span style="cursor:help;" title="Category '.$rowArray[3].'">'.$rowArray[1].'</span>';
      } else {
        $ret = '<span style="cursor:help;" title="Team from '.$rowArray[5].', team category '.$rowArray[3].'">'.$rowArray[1].'</span>';
      }
      return $ret;
		}
	}
}

function getTeamsOptionsSeparator() {
	return '<option value="0">-------------------------------------</option>';
}

function getTeamsOptionsNone($selected) {
	$result = '<option ';
	if ($selected) {
		$result .= 'selected ';
	}
	$result .= 'value="0">[select]</option>';
	return $result;
}

function getImgForTeam($team) {
	$teamsInstance = getTeamsArray();
	if ($team == '0') {
		return '<img style="cursor:help;vertical-align:middle;opacity:0.4;" title="Not specified" '.'src="/gfx/badges/0.bmp" width="18" height="15" border="1">';
	}
	foreach ($teamsInstance as $rowArray) {
		if ($rowArray[0] == $team) {
			return '<img style="cursor:help;vertical-align:middle;" title="'.$rowArray[1].'" src="/gfx/badges/'.$team.'.bmp" width="18" height="15" border="1">';
		}
	}
}

function getImgForTeamPlayerName($team, $player) {
	$teamsInstance = getTeamsArray();
	if ($team == '0') {
		return '<img style="cursor:help;vertical-align:middle;" title="Not specified" '.'src="/gfx/badges/0.bmp" width="18" height="15" border="1">';
	}
	foreach ($teamsInstance as $rowArray) {
		if ($rowArray[0] == $team) {
			return '<img style="cursor:help;vertical-align:middle;" title="'.$rowArray[1].' ('.$player.')" src="/gfx/badges/'.$team.'.bmp" width="18" height="15" border="1">';
		}
	}
}

function getTeamsOptionsForLeague($league) {
	require ('variables.php');
	$teamsInstance = getTeamsArray();
	$resultString = "";
	$sql = "SELECT * FROM $leaguestable where league = '$league' order by player";

	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$team = $row['team'];
		$player = $row['player'];
		$teamName = getTeamNameForId($team);
		$resultString .= '<option value="'.$team.'">'.$player.' ('.$teamName.')</option>';
	}
	return $resultString;
}
function getSubNavigationArraysInstance($cookie_name) {
	require ('variables.php');
	static $subNavInstance;
	if ($subNavInstance == null) {

		$subNavInstance = array ();
		$playerDir = "/player/";
		$leaguesDir = "/leagues/";
		$sixserverDir = "/sixserver/";
		// $filesDir = "/files/";
		$chatDir = "/Chat/";
    $adminDir = "/Admin/";
		$subNavInstance[] = array (
			'playerfind' => array ('/playerfind.php', 'Quickfinder'), 
			'players' => array ('/players.php', 'Playerlist'), 
			'joinlist' => array ('/joinlist.php', 'Joinlist'));
		$subNavInstance[] = array (
			'profile' => array ('/profile.php'.$playerDummy, 'Profile'), 
			'history' => array ($playerDir.'history.php'.$playerDummy, 'History'), 
			'teamstats' => array ($playerDir.'teamstats.php'.$playerDummy, 'Ladder Teamstats'), 
      'teamstatssix' => array ($playerDir.'teamstatssix.php'.$playerDummy, 'Sixserver Teamstats'), 
			'streaks' => array ($playerDir.'streaks.php'.$playerDummy, 'Streaks'), 
			'trends' => array ($playerDir.'trends.php'.$playerDummy, 'Trend Graphs'), 
			'editprofile' => array ('/editprofile.php'.$playerDummy, 'Edit Profile')
      );
		$subNavInstance[] = array (
			'games' => array('/games.php', 'Games'),
			'standings' => array ('/standings.php', 'Single Standings'), 
			'teamStandings' => array ('/teamStandings.php', 'Team Standings'), 
			'statistics' => array ('/statistics.php', 'Statistics'), 
			'calculator' => array('/calculator.php', 'Points Calculator'),
			'categories' => array('/categories.php', 'Team Categories'),
			'standingsHistory' => array ('/standingsHistory.php', 'Previous Seasons'),
			'champions' => array ('/champions.php', 'Champions'));
		$subNavInstance[] = array (
			'index' => array($sixserverDir, 'Overview'),
			'sixgames' => array($sixserverDir.'games.php', 'Games'),
			'sixstandings' => array($sixserverDir.'standings.php', 'Standings'),
      'sixteamstandings' => array($sixserverDir.'teamStandings.php', 'Team Standings'),
			'disconnects' => array($sixserverDir.'disconnects.php', 'Disconnects'),
      'lobbies' => array($sixserverDir.'lobbies.php', 'Lobbies'),
			'sixstatistics' => array($sixserverDir.'statistics.php', 'Statistics'),
			'sixstandingsHistory' => array ($sixserverDir.'standingsHistory.php', 'Previous Seasons'),
			'sixchampions' => array ($sixserverDir.'champions.php', 'Champions')
      );
		$subNavInstance[] = array (
			'playerstatus' => array($adminDir."playerStatus.php", 'Cards'),
      'playerstatus-yellow' => array($adminDir."playerStatus.php?mode=new&type=W", 'New yellow card'),
      'playerstatus-red' => array($adminDir."playerStatus.php?mode=new&type=B", 'New red card')
      );
    $subNavInstance[] = array (
			'checks' => array($adminDir."checkPoints.php", 'Check points'),
      'checks-count-up' => array($adminDir."checkPointsCountUp.php", 'Count up points'),
      'checks-games' => array($adminDir."gamesCheck.php", 'Check games')
    );
      
    $leaguesArray = array();
    $sql = "SELECT * FROM weblm_leagues_meta WHERE isActive=1 ORDER BY leagueId DESC";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
      $leagueName = $row['leagueName'];
      $leaguesArray[$leagueName] = array($leaguesDir.'league.php?id='.$row['leagueId'], $row['leagueName']);
    }
    $leaguesArray['leagues'] = array($leaguesDir.'leagues.php', 'All Leagues');
    
		$subNavInstance[] = $leaguesArray;
    
		$subNavInstance[] = array (
			'news' => array ('/news.php', 'News Archive'), 
			'cards' => array ('/cards.php', 'Warning Cards'), 
			'credits' => array ('/credits.php', 'Credits'), 
			'donate' => array ('/donate.php', 'Donate!')
			);
		//$subNavInstance[] = array (
		//	'browseGoals' => array($filesDir.'browseGoals.php', 'Goals'),
		//	'voters' => array($filesDir.'voters.php', 'Voters'),
		//	'upload' => array($filesDir.'upload.php', 'File Upload'), 
		//	'browseFiles' => array($filesDir.'browseFiles.php', 'Browse Files'));
		$subNavInstance[] = array (
			'quakenet' => array(getQuakenetWebchatUrl($cookie_name), 'Quakenet Webchat'),
			'chat' => array($chatDir.'chat.php', 'Java-based Chat'),
      'mirc' => array('/forum/viewtopic.php?t=1991', 'How to install mIRC'));	
	}
	return $subNavInstance;
}

function getSubNavArrayForPage($page, $player) {
	require ('variables.php');

	// $cookie_name = mysql_real_escape_string(GetInfo('cookies', 'cookie_name'));
  $subNavArrays = getSubNavigationArraysInstance($player);
	
	while ($checkArray = current($subNavArrays)) {
		if (array_key_exists($page, $checkArray)) {
			return $checkArray;
		}
		next($subNavArrays);
	}
	return array();
}

function getSubNavigation($page, $player) {
	require ('variables.php');
	$resultString = "&nbsp;";
	$separator = "&nbsp;<img src='/style/MorpheusX/images/darkblue/buttons_spacer.gif' />&nbsp;";

	$useArray = getSubNavArrayForPage($page, $player);
	$count = count($useArray);

	while ($item = current($useArray)) {
		$key = key($useArray);
		$span = false;
		$url = $item[0];
		if (strcmp($page, $key) == 0) {
			$resultString .= '<span class="link-menucurrent">';
			$span = true;
		}
		
		if (strpos($url, $playerDummy) > 0) {
			$urlFormatted = str_replace($playerDummy, "?name=".$player, $url);
		} else {
			$urlFormatted = $url;
		}
		$resultString .= '<a href="'.$urlFormatted.'">'.$item[1].'</a>';
		if ($span) {
			$resultString .= '</span>';
		}
		if ($count > 1) {
			$resultString .= $separator;
		}

		$count --;
		next($useArray);
	}
	return $resultString;
}

function getRaquo() {
	return " <span class='grey-small'>&raquo;</span>";
}

function getPointsFieldForVersion($version) {
	require ('variables.php');
	if (stristr($versions_pes4, $version)) {
		$DBField_points = "ra2pes4";
	} else if (stristr($versions_pes5, $version)) {
		$DBField_points = "ra2pes5";
	} else {
		$DBField_points = "ra2pes5";
	}
	return $DBField_points;
}

function getGamesFieldForVersion($version) {
	require ('variables.php');
	if (stristr($versions_pes4, $version)) {
		$DBField_points = "pes4games";
	} else if (stristr($versions_pes5, $version)) {
		$DBField_points = "pes5games";
	} else {
		$DBField_points = "pes5games";
	}
	return $DBField_points;
}

function getWinsFieldForVersion($version) {
	require ('variables.php');
	if (stristr($versions_pes4, $version)) {
		$DBField_points = "pes4wins";
	} else {
		$DBField_points = "pes5wins";
	}
	return $DBField_points;
}

function getLossesFieldForVersion($version) {
	require ('variables.php');
	if (stristr($versions_pes4, $version)) {
		$DBField_points = "pes4losses";
	} else {
		$DBField_points = "pes5losses";
	}
	return $DBField_points;
}

function getLadderForVersion($version) {
	require ('variables.php');
	if (stristr($versions_pes4, $version)) {
		return "PES4/WE8I";
	} else if (stristr($versions_pes5, $version)) {
		return "PES5";
	} else {
		return "PES/WE";
	}
}

function getVersionForLadder($ladder) {
	require ('variables.php');
	if (strcmp("PES4/WE8I", $ladder) == 0) {
		return 'A';
	} else if (strcmp("PES5", $ladder) == 0) {
		return 'D';
	} else if (strcmp("PES/WE", $ladder) == 0) {
		return 'H';
	} else if (strcmp("PES 2008", $ladder) == 0) {
		return 'O';
	} else {
		return null;
	}
}

function getVersionsForLadder($ladder) {
	require ('variables.php');
	if (strcmp("PES4", $ladder) == 0) {
		return $versions_pes4;
	} else if (strcmp("PES5", $ladder) == 0) {
		return $versions_pes5;
	} else if (strcmp("PES6", $ladder) == 0) {
		return $versions_pes6;
	} else if (strcmp("WE2007", $ladder) == 0) {
		return $versions_we2007;
	} else if (strcmp("PES2008", $ladder) == 0) {
		return $versions_pes08;
	} else if (strcmp("PES2009", $ladder) == 0) {
		return $versions_pes09;
	} else if (strcmp("PES2010", $ladder) == 0) {
		return $versions_pes10;
	} else if (strcmp("PES2011", $ladder) == 0) {
		return $versions_pes11;
	} else if (strcmp("PES2012", $ladder) == 0) {
		return $versions_pes12;
	} else if (strcmp("PES2013", $ladder) == 0) {
		return $versions_pes13;
	} else if (strcmp("PES2014", $ladder) == 0) {
		return $versions_pes14;
	} else {
		return null;
	}
}

function getUnsupportedVersions() {
	require ('variables.php');
	return $versions_pes4.$versions_pes5.$versions_we2007.$versions_pes08.$versions_pes09.$versions_pes10.$versions_pes11.$versions_pes12.$versions_pes13.$versions_pes14;
}

function getSupportedVersions() {
	require ('variables.php');
	return $versions_pes6;
}

function in_array_nocase($search, &$array) {
  $search = strtolower($search);
  foreach ($array as $item)
   if (strtolower($item) == $search)
     return TRUE;
  return FALSE;
}

function format2DigitsMax($string) {
	$return = sprintf("%.2f", $string);
	$return = str_replace(".00", "", $return);
	return $return;
}

function formatAgoSpan($uploaded) {
	$spanSec = time() - $uploaded;
	$min = 60;
	$hour = $min*60;
	$day = $hour*24;
	$week = $day*7;
	$year = $week*52.2;
	$month = $year/12; 

	if ($spanSec < $min*2) {
		$span = $spanSec;
		$unit = "seconds";
	} else if ($spanSec < $hour*2) {
		$span = floor($spanSec/$min);
		$unit = "minutes";
	} else if ($spanSec < $day*2) {
		$span = floor($spanSec/$hour);
		$unit = "hours";
	} else if ($spanSec < $week*2) {
		$span = floor($spanSec/$day);
		$unit = "days";
	} else if ($spanSec < $month*2) {
		$span = floor($spanSec/$week);
		$unit = "weeks";
	} else if ($spanSec < $year*2) {
		$span = floor($spanSec/$month);
		$unit = "months";
	} else {  
		$span = floor($spanSec/$year);
		$unit = "years";
	}
	
	$spanLine = $span. " ". $unit;
	if ($spanSec < $hour*6) {
		$spanLine = '<span style="color:green;">'.$spanLine.'</span>';
	}
	return $spanLine;
}

function formatTimeDiff($zeroTime, $compareTime) {
	$spanSec = $zeroTime - $compareTime;
	if ($spanSec > 0) {
		$timeShow = "before";
	} else {
		$spanSec = abs($spanSec);
		$timeShow = "after";
	}
	$min = 60;
	$hour = $min*60;
	$day = $hour*24;
	$week = $day*7;
	$year = $week*52.2;
	$month = $year/12; 

	if ($spanSec < $min*2) {
		$span = $spanSec;
		$unit = "seconds";
	} else if ($spanSec < $hour*2) {
		$span = floor($spanSec/$min);
		$unit = "minutes";
	} else if ($spanSec < $day*2) {
		$span = floor($spanSec/$hour);
		$unit = "hours";
	} else if ($spanSec < $week*2) {
		$span = floor($spanSec/$day);
		$unit = "days";
	} else if ($spanSec < $month*2) {
		$span = floor($spanSec/$week);
		$unit = "weeks";
	} else if ($spanSec < $year*2) {
		$span = floor($spanSec/$month);
		$unit = "months";
	} else {  
		$span = floor($spanSec/$year);
		$unit = "years";
	}
	
	$spanLine = $span. " ". $unit ." ".$timeShow;
	if ($spanSec < $hour*6) {
		$spanLine = '<span style="color:green;">'.$spanLine.'</span>';
	}
	return $spanLine;
}

function formatAgoSpanPlain($uploaded) {
	$spanSec = time() - $uploaded;
	$min = 60;
	$hour = $min*60;
	$day = $hour*24;
	$week = $day*7;
	$year = $week*52.2;
	$month = $year/12; 

	if ($spanSec < $min*2) {
		$span = $spanSec;
		$unit = "seconds";
	} else if ($spanSec < $hour*2) {
		$span = floor($spanSec/$min);
		$unit = "minutes";
	} else if ($spanSec < $day*2) {
		$span = floor($spanSec/$hour);
		$unit = "hours";
	} else if ($spanSec < $week*2) {
		$span = floor($spanSec/$day);
		$unit = "days";
	} else if ($spanSec < $month*2) {
		$span = floor($spanSec/$week);
		$unit = "weeks";
	} else if ($spanSec < $year*2) {
		$span = floor($spanSec/$month);
		$unit = "months";
	} else {  
		$span = floor($spanSec/$year);
		$unit = "years";
	}
	
	$spanLine = $span. " ". $unit;
	return $spanLine;
}

function formatFutureSpanPlain($futureTime) {
	$spanSec = $futureTime - time();
	$min = 60;
	$hour = $min*60;
	$day = $hour*24;
	$week = $day*7;
	$year = $week*52.2;
	$month = $year/12; 

	if ($spanSec < $min*2) {
		$span = $spanSec;
		$unit = "seconds";
	} else if ($spanSec < $hour*2) {
		$span = floor($spanSec/$min);
		$unit = "minutes";
	} else if ($spanSec < $day*2) {
		$span = floor($spanSec/$hour);
		$unit = "hours";
	} else if ($spanSec < $week*2) {
		$span = floor($spanSec/$day);
		$unit = "days";
	} else if ($spanSec < $month*2) {
		$span = floor($spanSec/$week);
		$unit = "weeks";
	} else if ($spanSec < $year*2) {
		$span = floor($spanSec/$month);
		$unit = "months";
	} else {  
		$span = floor($spanSec/$year);
		$unit = "years";
	}
	
	$spanLine = $span. " ". $unit;
	return $spanLine;
}
function getPlayersOptionsApprovedSelected($selected) {
	require('variables.php');
	$returnString = "";
	$sortby = "name ASC";
    $sql = "SELECT name FROM $playerstable WHERE approved = 'yes' ORDER BY $sortby";
    $result = mysql_query($sql);
    $num = mysql_num_rows($result);
    $cur = 1;
    while ($num >= $cur) {
        $row = mysql_fetch_array($result);
        $name = $row["name"];
		$returnString .= "<option ";
        if (!empty($name) && $name == $selected) {
        	$returnString .= ' selected="selected"';
        } 
		$returnString .= ">".$name."</option>";
        $cur++;
    }
    return $returnString; 
}

function getPlayersOptionsAllIdSelected($selectedId) {
	require('variables.php');
	$returnString = "";
	$sortby = "name ASC";
  $sql = "SELECT player_id, name FROM $playerstable ORDER BY $sortby";
  $result = mysql_query($sql);
  $num = mysql_num_rows($result);
  $cur = 1;
  while ($num >= $cur) {
    $row = mysql_fetch_array($result);
    $name = $row["name"];
    $player_id = $row["player_id"];
    $returnString .= '<option value="'.$player_id.'"';
    if (!empty($player_id) && $player_id == $selectedId) {
      $returnString .= ' selected="selected"';
    } 
    $returnString .= ">".$name."</option>";
    $cur++;
  }
  return $returnString; 
}

function getPlayersOptionsApproved() {
	return getPlayersOptionsApprovedSelected(null);
}

function getPlayersOptionsRecentPlayers() {
	 require('variables.php');
	 require('variablesdb.php');
	$returnString = "";
	// $cookie_name = mysql_real_escape_string(GetInfo('cookies', 'cookie_name'));
	$sql = "select user, max(accesstime) as max_time, ip " .
       		"from $logtable " .
       		"where user != '' ".
       		"group by user " .
       		"order by 2 desc LIMIT 0, 16";
   $result = mysql_query($sql);
   while ($row = mysql_fetch_array($result)) {
     $user = $row['user'];
     if ($user != $cookie_name) {
     	$returnString.= "<option>".$user."</option>";
     }
   }
   return $returnString;
}

function addSeasonAndPos($seasonPosArray, $season, $pos) {
	if (array_key_exists($pos, $seasonPosArray)) {
		$posArray = $seasonPosArray[$pos];
		$posArray[] = $season;
		asort($posArray);
		$seasonPosArray[$pos] = $posArray;
	} else {
		$posArray = array();
		$posArray[] = $season;
		$seasonPosArray[$pos] = $posArray;
	} 
	ksort($seasonPosArray);
	return $seasonPosArray;
}

function getImgForPosAndSeasonArray($position, $seasonArray, $align) {
	require ('variables.php');
	
	$result = "";

	if ($position == 1) {
		$posImg = $gfx_rank1;
	} else
	if ($position == 2) {
		$posImg = $gfx_rank2;
	} else
	if ($position == 3) {
		$posImg = $gfx_rank3;
	} else
	if ($position == 4) {
		$posImg = $gfx_rank4;
	} else
	if ($position == 5) {
		$posImg = $gfx_rank5;
	} else
	if ($position == 6) {
		$posImg = $gfx_rank6;
	} 
	
	if (count($seasonArray) > 1) {
		$posImg = substr($posImg, 0, strlen($posImg)-4)."_x".count($seasonArray).substr($posImg, strlen($posImg)-4);
		$s = "s";
	} else {
		$s = "";
	}
	$seasons = "";
	$num = 0;
	foreach($seasonArray as $season) { 
		$seasons .= $season."/";
		$num++;
	}
	if ($num > 1) {
		$times = " (".$num." times)"; 
	} else {
		$times = "";
	}
	
	$seasons = substr($seasons, 0, strlen($seasons)-1);
	
	$result = '<img style="cursor:help; padding-right:2px;vertical-align:'.$align.';" title="Rank '.$position.' in ladder season'.$s.' '.$seasons.$times.'" 
				src="'.$directory.'/gfx/'.$posImg.'" />';	
	return $result;
}

function flip(&$s1, &$s2) {
	$s1tmp = $s1;
	$s1 = $s2;
	$s2 = $s1tmp;
}

function doParagraph($wrap) {
	return "<p>".$wrap."</p>";
}

function doBold($wrap) {
	return "<b>".$wrap."</b>";
}

function getIdForPlayer($name) {
	require('variables.php');
	$res = "";
	$sql = "select player_id from $playerstable where name = '$name'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$res = $row['player_id'];		
	} 
	return $res;
}

function getPlayerNameForId($id) {
	require('variables.php');
	$res = "";
	$sql = "select name from $playerstable where player_id = '$id'";
	$result = mysql_query($sql);
	if (mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);
		$res = $row['name'];		
	} 
	return $res;
}

function endsWith( $str, $sub ) {
   return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
}

function escapePostText($topic_text) {
            // BB-Code processing
            
            // Extra character removal (these characters are identifiers that
            // are concatenated into the BB-code commands themselves in order
            // to help phpBB track appropriated opening and closing commands).
            $topic_text = preg_replace("/\:[0-9a-z\:]+\]/si", "]", $topic_text);
            // Bold
            $topic_text = str_replace("[b]", "", $topic_text);
            $topic_text = str_replace("[/b]", "", $topic_text);
            
            // Italics
            $topic_text = str_replace("[i]", "", $topic_text);
            $topic_text = str_replace("[/i]", "", $topic_text);
            
            // Underline
            $topic_text = str_replace("[u]", "", $topic_text);
            $topic_text = str_replace("[/u]", "", $topic_text);

            // Quote (style #1)
            $topic_text = preg_replace("/\[quote=(.*)\]/Usi", "[Quote: ", $topic_text);
            $topic_text = preg_replace("/\[quote:(.*)\]/Usi", "[Quote: ", $topic_text);
            
            // Quote (style #2)
            $topic_text = str_replace("[quote]", "[Quote: ", $topic_text);
            $topic_text = str_replace("[/quote]", "] ", $topic_text);
            // Code
            $topic_text = str_replace("[code]", "", $topic_text);
            $topic_text = str_replace("[/code]", "", $topic_text);
            // List
            $topic_text = preg_replace("/\[list\](.*)\[\/list\]/si", "$1", $topic_text);
            $topic_text = preg_replace("/\[list=(.*)\](.*)\[\/list\]/si", "$1", $topic_text);
            // Image
            $topic_text = str_replace("[img]", "[Image: ", $topic_text);
            $topic_text = str_replace("[/img]", "] ", $topic_text);
            // URL
            $topic_text = preg_replace("/\[url\](.*)\[\/url\]/Usi", "[Url: $1] ", $topic_text);
            $topic_text = preg_replace("/\[url=(.*)\](.*)\[\/url\]/Usi", "[Url: $2] ", $topic_text);
            //Size
            $topic_text = preg_replace("/\[size\](.*)\[\/size\]/Usi", "$1", $topic_text);
            $topic_text = preg_replace("/\[size=(.*)\](.*)\[\/size\]/Usi", "$2", $topic_text);
            // Endlines
            $topic_text = str_replace("\r\n", " ", $topic_text);
            $topic_text = str_replace("\n", " ", $topic_text);
            
            return $topic_text;
}

function getHelpHint($text) {
	return '<span class="grey-small" style="cursor:help;" title="'.$text.'">(?)</span>';
}

function getMibbitUrl($cookie_name) {
	return "http://widget.mibbit.com/?settings=19f8826dff0a91e25b709c5f183f0380&server=irc.quakenet.org&channel=%23".$leaguename."&noServerNotices=true&noServerMotd=true&autoConnect=true&nick=".getChatNick($cookie_name);
}

function getQuakenetWebchatUrl($cookie_name) {
  require ('variables.php');
	require ('variablesdb.php');
	return "http://webchat.quakenet.org/?nick=".getChatNick($cookie_name)."&channels=".$leaguename."&prompt=1";
}

function getWebchatIFrame() {
	return '<iframe border="0" allowtransparency="true" frameborder="0" src="'.getQuakenetWebchatUrl($cookie_name).'" width="100%" height="100%"></iframe>';
}

function getChatBoxTitle() {
	return 'Chatroom&nbsp;&nbsp;<span class="grey-small">(Install <a class="grey-small" href="http://www.'.$leaguename.'/forum/viewtopic.php?t=1991">mIRC</a> for a better chat experience)</span>';
}

function getChatNick($cookie_name) {
	// $cookie_name = mysql_real_escape_string(GetInfo('cookies', 'cookie_name'));
	if (!empty($cookie_name) && $cookie_name != '') {
	    $chatNick = str_replace(" ", "_", $cookie_name);
		$regEx = "[^a-z,A-Z,0-9,.,_,\-]";
	    $replaceWith = "";
	    $chatNick = ereg_replace($regEx, $replaceWith, $chatNick);
	    $chatNick = str_replace(".", "", $chatNick);
	    $numUC = preg_match_all("@[A-Z]@",$chatNick, $m, PREG_OFFSET_CAPTURE);
	    if ($numUC > 2) {
	    	$chatNick = strtolower($chatNick);
	    } 
	    
	    if (strpos($chatNick, "-") > 0) {
	    	$index1 = strpos($chatNick, "-");
	    	if (strpos($chatNick, "-",$index1 > 0)) {
	    		$chatNick = str_replace("-", "", $chatNick);		
	    	}
	    }
	}  
	if (empty($chatNick)) {
	  $chatNick = "evo-player-".rand(100,999);
	}
	return $chatNick;
}

function getCommaSeparatedString($array) {
	$result = "";
	if ($array != null && is_array($array)) {
		$last_item = end($array); 
		foreach ( $array as $value ) {
       		$result .= $value;
       		if ($value != $last_item) {
       			$result .= ", ";
       		}	
		}
	}
	return $result;	
}

function isLeagueActive($id) {
	require('variables.php');
	$res = false;
	$sql = "select isActive from $leaguesmetatable where leagueId=$id";
	$result = mysql_query($sql);
	if (!empty($result) && mysql_num_rows($result) > 0) {
		$row = mysql_fetch_array($result);	
		if ($row['isActive'] == 1) {
			$res = true;
		}			
	} 
	return $res;
}


function getOptionsActiveLeagues($selected) {
	require('variables.php');
	$res = "";
	$sql = "select leagueId, leagueName from $leaguesmetatable where isActive=1";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result)) {
		$leagueName = $row['leagueName'];
		$leagueId = $row['leagueId'];
		$res.='<option ';
		if (!empty($selected) && $selected == $leagueId) {
			$res.= 'selected="selected" '; 
		}
		$res.= 'value="'.$leagueId.'">'.$leagueName.'</option>';
	}
	return $res;
}

function getTeamStandingsPerTeamArray() {
	require('variables.php');
	require_once('classes.php');
	$sql = "SELECT * FROM $gamestable WHERE deleted='no' AND teamLadder = 1 AND date > UNIX_TIMESTAMP(date_sub(now(), INTERVAL 90 DAY)) ORDER BY game_id ASC";
	$result = mysql_query($sql);
	
	$teamStatSet = array();
	while ($row = mysql_fetch_array($result)) {
		$winner = $row['winner'];
		$winner2 = $row['winner2'];
		$loser = $row['loser'];
		$loser2 = $row['loser2'];
		$winpoints = $row['winpoints'];
		$losepoints = $row['losepoints'];
		$losepoints2 = $row['losepoints2'];
		$draw = $row['isDraw'] == 1;
		
		// winning team
		$found = false;
		foreach ($teamStatSet as $teamStat) {
			if ($teamStat->isTeam($winner, $winner2)) {
				$found = true;
				// update element
				$statElementWinners = &$teamStat;
				break;			
			}
		}
		if (!$found) {
			// new element
			 $statElementWinners = new TeamStat($winner, $winner2);
			 $teamStatSet[] = $statElementWinners;
		}
	
		 // udpate
		 if (!empty($winner2)) {
		 	$statElementWinners->addPtsWon($winpoints*2);
		 } else {
		 	$statElementWinners->addPtsWon($winpoints);
		 }
		 if ($draw) {
			$statElementWinners->draw++; 		 	
		 } else {
		 	$statElementWinners->won++;
		 }
		
		// losing team
		$found = false;
		foreach ($teamStatSet as $teamStat) {
			if ($teamStat->isTeam($loser, $loser2)) {
				$found = true;
				// update element
				$statElementLosers = &$teamStat;
				break;			
			}
		}
		if (!$found) {
			// new element
			 $statElementLosers = new TeamStat($loser, $loser2);
			 $teamStatSet[] = $statElementLosers;
		}
	
		 // udpate
		 $statElementLosers->addPtsLost($losepoints+$losepoints2);
		 if ($draw) {
			$statElementLosers->draw++; 		 	
		 } else {
		 	$statElementLosers->lost++;
		 }
	}
	
	usort($teamStatSet, array("TeamStat", "compare"));
	return $teamStatSet;
} 

function updateTeamladders() {
	require('variables.php');
	
	$playerLeader = "";
	$playerLeaderSaved = "";
	$teamLeader1 = "";
	$teamLeader1Saved = "";
	$teamLeader2 = "";
	$teamLeader2Saved = "";
	
	$time = time();
	
	// get current calculated leaders of both ladders
	// per player
	$sql = "SELECT player_id, teamWins/teamGames as percentage FROM $playerstable ".
		"WHERE teamGames > 0 AND approved='yes' ORDER BY teamPoints DESC, percentage DESC, teamLosses ASC";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	if ($num > 0) {
		$row = mysql_fetch_array($result);
		$playerLeader = $row["player_id"];
	}
	// per team
	$teamStatSet = getTeamStandingsPerTeamArray();
	if (count($teamStatSet) > 0) {
		$teamLeader1 = getIdForPlayer($teamStatSet[0]->player1);
		$teamLeader2 = getIdForPlayer($teamStatSet[0]->player2);
	}
	// check current entries of teamladder table
	$sql = "SELECT playerId from $teamladdertable where type='player' ORDER BY timestamp DESC LIMIT 0, 1";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	if ($num > 0) {
		$row = mysql_fetch_array($result);
		$playerLeaderSaved = $row["playerId"];
	}	
	$sql = "SELECT playerId, playerId2 from $teamladdertable where type='team' ORDER BY timestamp DESC LIMIT 0, 1";
	$result = mysql_query($sql);
	$num = mysql_num_rows($result);
	if ($num > 0) {
		$row = mysql_fetch_array($result);
		$teamLeader1Saved = $row["playerId"];
		$teamLeader2Saved = $row["playerId2"];
	}	
	// update teamladder table if neccessary
	if ($playerLeader != $playerLeaderSaved) {
		// save new entry
		$sql = "INSERT INTO $teamladdertable (playerId, type, timestamp) VALUES ('$playerLeader', 'player', '$time')";
		mysql_query($sql);
	}
	if ($teamLeader1 != $teamLeader1Saved || $teamLeader2 != $teamLeader2Saved) {
		$sql = "INSERT INTO $teamladdertable (playerId, playerId2, type, timestamp) VALUES ('$teamLeader1', '$teamLeader2', 'team', '$time')";
		mysql_query($sql);
	} 
}

function GetSixserverWins($profileId) {
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants=2 ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home>six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home<six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverLosses($profileId) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants=2 ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home<six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home>six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDraws($profileId) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants=2 ".
		"AND (six_matches.score_home=six_matches.score_away)";
	
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}


function GetSixserverWinsAllTime($profileId) {
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    // "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    // "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants=2 ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home>six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home<six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverLossesAllTime($profileId) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    // "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    // "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants=2 ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home<six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home>six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDrawsAllTime($profileId) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    // "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    // "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants=2 ".
		"AND (six_matches.score_home=six_matches.score_away)";
	
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}


function GetSixserverWinsTeam($profileId) {
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants>2 ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home>six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home<six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverLossesTeam($profileId) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants>2 ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home<six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home>six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDrawsTeam($profileId) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=six_stats.season ".
    "AND six_matches.numParticipants>2 ".
		"AND (six_matches.score_home=six_matches.score_away)";
	
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverWinsForSeason($profileId, $season) {
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=$season ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home>six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home<six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverLossesForSeason($profileId, $season) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=$season ".
		"AND ((six_matches_played.home=1 AND six_matches.score_home<six_matches.score_away) ".
		"OR (six_matches_played.home=0 AND six_matches.score_home>six_matches.score_away))";
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDrawsForSeason($profileId, $season) {			
	$sql = "SELECT count(*) FROM six_matches_played ".
		"LEFT JOIN six_matches ON six_matches_played.match_id=six_matches.id ".
    "LEFT JOIN six_stats ON six_matches.season=six_stats.season ".
		"WHERE six_matches_played.profile_id=".$profileId." ".
    "AND six_matches.season=$season ".
		"AND (six_matches.score_home=six_matches.score_away)";
	
	$row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}
function GetSixserverGamesTotal($playerId) {
	$sql = "SELECT count(*) FROM six_matches_played smp ".
		"LEFT JOIN six_profiles sp ON sp.id=smp.profile_id ".
    "LEFT JOIN weblm_players wp ON wp.player_id=sp.user_id ".
    "LEFT JOIN six_matches ON smp.match_id=six_matches.id ".
    "WHERE wp.player_id=".$playerId. " ".
    "AND six_matches.numParticipants=2";
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverWinsHistory($profileId) {
  $sql = "SELECT SUM(wins) FROM six_history WHERE profileId=".$profileId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverLossesHistory($profileId) {
  $sql = "SELECT SUM(losses) FROM six_history WHERE profileId=".$profileId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDrawsHistory($profileId) {
  $sql = "SELECT SUM(draws) FROM six_history WHERE profileId=".$profileId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverWinsHistoryForSeason($profileId, $season) {
  $sql = "SELECT SUM(wins) FROM six_history WHERE season=$season AND profileId=".$profileId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverLossesHistoryForSeason($profileId, $season) {
  $sql = "SELECT SUM(losses) FROM six_history WHERE season=$season AND profileId=".$profileId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDrawsHistoryForSeason($profileId, $season) {
  $sql = "SELECT SUM(draws) FROM six_history WHERE season=$season AND profileId=".$profileId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDCHistory($profileId) {
  $sql = "SELECT SUM(dc) FROM six_history WHERE profileId=".$profileId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

function GetSixserverDCHistoryPerPlayer($playerId) {
  $sql = "SELECT SUM(dc) FROM six_history WHERE playerId=".$playerId;
  $row2 = mysql_fetch_array(mysql_query($sql));
	return $row2[0];
}

// function getSixserverScore($perf, $num_games) {
//   $w1 = 1;
//   $w2 = 0;
//   return $w1*$perf;
// }

//function getSixserverPoints($wins, $draws, $losses) {
//  $num_games = $wins + $draws + $losses;
//  if ($num_games == 0) {
//    $perf = 0;
//  } else {
//    $perf = ($wins + 0.5*$draws)/$num_games;
//  }
//  return intval(1000*getSixserverScore($perf,$num_games)*(1-(2/($num_games+2))));
//}

// function getSixserverPoints($wins, $draws, $losses) {
//   $num_games = $wins + $draws + $losses;
//   if ($num_games == 0) {
//     $perf = 0;
//   } else {
//     $perf = ($wins + 0.5*$draws)/$num_games;
//   }
//   if ($num_games <= 24) {
//     $multiplier = $num_games/50+0.5;
//   } elseif ($num_games <= 50) {
//     $multiplier = ($num_games-24)/(50*26)+0.98;
//   } else {
//     $multiplier = 1;
//   }
//   return intval(1000*$perf*$multiplier);
// }

function getSixserverPoints($wins, $draws, $losses) {
  $num_games = $wins + $draws + $losses;
  if ($num_games == 0) {
    $perf = 0;
  } else {
    $perf = ($wins + 0.5*$draws)/$num_games;
  }
  if ($num_games <= 20) {
    $multiplier = $num_games/50+0.5;
  } elseif ($num_games < 100) {
    $multiplier = 0.9+(0.1*($num_games-20)/80);
  } else {
    $multiplier = 1;
  }
  return intval(1000*$perf*$multiplier);
}

# w1: 0.44, 0.56
#    def getScore(self, perf, num_games):
#        return self.w2 + self.w1*perf*perf + self.w2*(
#            -math.exp(-num_games*0.05))
#
#    def getPoints(self, stats):
#        num_games = stats.wins + stats.draws + stats.losses
#        if num_games == 0:
#            perf = 0.0
#        else:
#            perf = (stats.wins + 0.333*stats.draws)/num_games
#        return int(1000*self.getScore(perf, num_games))
  
function IsFishySixserverGame($scoreLeft, $scoreRight, $minutes) {
  $isFishy = false;
  if (($scoreLeft-1) > $scoreRight && $minutes > 0) {
    // two goals difference
    $isFishy = true;
  } elseif ($scoreLeft >= $scoreRight && $minutes >= 15) {
    // one goal difference or draw
    $isFishy = true;
  }
  return $isFishy;
}

function CheckSimilarAccounts($ip, $name, $pwdHash, $mail) {
  require_once ('log/KLogger.php');
  require('variables.php');
  $log = new KLogger('/var/www/yoursite/http/log/join', KLogger::INFO);	
  $logPrefix = 'CheckSimilarAccounts ('.$name.','.$ip.','.$pwdHash.','.$mail.'): ';
  $log->logInfo($logPrefix." start");
  $similarAccounts = "";
  
  // IP-based checks
  if (!empty($ip)) {
    // check access table
    $sql = "SELECT user, ip from ".$logtable." WHERE ip='".$ip."' " .
      "AND ip != '' AND user != '".$name."' " .
      "AND accesstime > unix_timestamp(date_sub(now(), INTERVAL 7 DAY)) " .
      "GROUP BY IP order by accesstime DESC";
    // $log->logInfo($logPrefix.' sql:'.$sql);
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)) {
      $log->logInfo($logPrefix.' access: user='.$row['user'].' ip='.$row['ip']);
      $similarAccounts = "yes";
    }

    $sql = "SELECT name, ip FROM ".$playerstable." where ip='".$ip."' " .
      "AND ip != '' AND name != '$name'";
    // $log->logInfo($logPrefix.' sql:'.$sql);
    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)) {
      $log->logInfo($logPrefix.' players: name='.$row['name'].' ip='.$row['ip']);
      $similarAccounts = "yes";
    }
  }
  
  // mail
  $sql = "SELECT name, mail from ".$playerstable." WHERE mail like '%".$mail."%' and name != '".$name."'";
  // $log->logInfo($logPrefix.' sql:'.$sql);
  $result = mysql_query($sql);
  while($row = mysql_fetch_array($result)) {
    $log->logInfo($logPrefix.' mail: name='.$row['name'].' mail='.$row['mail']);
    $similarAccounts = "yes";
  }
  
  // Password (non-failing check)
  $sql = "SELECT name,pwd from ".$playerstable." WHERE pwd='".$pwdHash."' AND name != '".$name."'";
  // $log->logInfo($logPrefix.' sql:'.$sql);
  $result = mysql_query($sql);
	while($row = mysql_fetch_array($result)) {
		$log->logInfo($logPrefix.' pwd: name='.$row['name'].' pwdHash='.$row['pwdHash']);
	    // $similarAccounts = "yes";
	}
  return $similarAccounts;
}

function GetPerfTime($start) {
  return sprintf("%07.3f", microtime(true) - $start);
}

function LogPerfTime($log, $start, $ip, $user, $text) {
  $genTime = GetPerfTime($start);
  $log->logInfo('GenTime='.$genTime.' IP=['.$ip.'] User=['.$user.'] URL=['.$_SERVER['REQUEST_URI'].'] ('.$text.')');
}

function GetActiveBansIds() {
  require('variables.php');
  $res = array();
  $sql = "SELECT userId FROM $playerstatustable WHERE type='B' AND active='Y'";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $res[] = $row[0];
  }
  return $res;
}

function GetActiveWarningsIds() {
  require('variables.php');
  $res = array();
  $sql = "SELECT userId FROM $playerstatustable WHERE type='W' AND active='Y'";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    $res[] = $row[0];
  }
  return $res;
}

// sends a new user a sign-up mail
function sendActivationLinkMail($toAddress, $playerName, $link) {
	require ('variables.php');
	require ('variablesdb.php');
	
	$fromAddress = $admin_signup."@".$leaguename.".com";
	$head = "From: ".$leaguename." <".$noReplyMail.">\r\nReply-To:".$noReplyMail."";
	$subject = "[$leaguename] Account activation";
	$message = "Hi ".$playerName."!

Your evo-league account has been reviewed and is ready for activation. You can activate your account by visiting the following link:

".$link."

If you need help, please read the FAQ before asking questions: http://www.".$leaguename."/forum/viewtopic.php?t=5438

Please do not reply to this email. If you have questions or problems, post in our forums: http://www.".$leaguename."/forum/

- The yoursite staff";

	@ mail($toAddress, $subject, $message, $head, $mailconfig);
	 
	$res = logSentMail($playerName, $toAddress, "activation");
	return getTextForSendResult($res);
}

function logSentMail($user, $toAddress, $type) {
	require('variables.php');
	require('variablesdb.php');
	
	$sql = "INSERT INTO $mailtable (user, toAddress, mailType, logTime) " .
			"VALUES ('$user', '$toAddress', '$type', '".time()."')";
	$result = mysql_query($sql);
	
	return $result;	
}

function getTextForSendResult($res) {
	if (!empty($res) && $res == 1) {
		return "success";
	} else {
		return "<b>failed</b>";
	}
}

function getDonationButton() {
	return '';
}

function InsertTeamAndUpdate($patchId, $sixTeamId, $ladderTeamId, $logRep, $season, $cookie_name) {
  
  $exists = false;
  $sql = "SELECT * FROM six_teams WHERE patchId=".$patchId." AND sixTeamId=".$sixTeamId;
  $res = mysql_query($sql);
  
  $logRep->logInfo("InsertTeamAndUpdate: sql=".$sql);
  
  while ($row = mysql_fetch_array($res)) {
    $exists = true;
    $existingLadderTeamId = $row['ladderTeamId'];
    if ($existingLadderTeamId <> $ladderTeamId) {
      $logRep->logInfo('InsertTeamAndUpdate: ERROR: existingLadderTeamId='.$existingLadderTeamId.' ladderTeamId='.$ladderTeamId);
    } else {
      $logRep->logInfo('InsertTeamAndUpdate: Team already defined, existingLadderTeamId='.$existingLadderTeamId);
    }
  }

  $logRep->logInfo('InsertTeamAndUpdate: sql='.$sql.' exists='.$exists);
  if (!$exists) {
    $sql = "SELECT player_id FROM weblm_players WHERE name='$cookie_name'";
    $resPlayer = mysql_query($sql);
    $rowPlayer = mysql_fetch_array($resPlayer);
    $playerId = $rowPlayer[0];

    $sql = "INSERT INTO six_teams (patchId, sixTeamId, ladderTeamId, playerId) ".
      "VALUES ('".$patchId."','".$sixTeamId."','".$ladderTeamId."','".$playerId."')";
    mysql_query($sql);
    $logRep->logInfo('InsertTeamAndUpdate: sql='.$sql.' affected='.mysql_affected_rows());
  }

  // Update other games
  $sql = "SELECT wg.game_id, wg.winner, wg.winner2, wg.winnerteam, wg.loserteam, wg.sixGameId, sm.* FROM weblm_games wg ".
    "LEFT JOIN six_matches sm ON wg.sixGameId=sm.id ".
    "LEFT JOIN six_patches sp1 ON sm.hashHome=sp1.hash ".
    "WHERE wg.sixGameId IS NOT NULL ".
    "AND wg.season=".$season." ".
    "AND sm.reported=1 ".
    "AND sp1.id=".$patchId." ".
    "AND (wg.winnerteam=0 OR wg.loserteam=0) ".
    "AND (sm.team_id_home=".$sixTeamId." OR sm.team_id_away=".$sixTeamId.") ";
  $res = mysql_query($sql);
  
  $logRep->logInfo('InsertTeamAndUpdate: Other Games: sql='.$sql.' affected='.mysql_affected_rows());
  
  while ($row = mysql_fetch_array($res)) {
    $game_id = $row['game_id'];
    $winnerteam = $row['winnerteam'];
    $loserteam = $row['loserteam'];
    $scoreHome = $row['score_home'];
    $scoreAway = $row['score_away'];
    $setWinner = false;
    $setLoser = false;
    
    if ($scoreHome < $scoreAway) {
      $sixWinTeam = $row['team_id_away'];
      $sixLoseTeam = $row['team_id_home'];
    } elseif ($scoreHome > $scoreAway) {
      $sixWinTeam = $row['team_id_home'];
      $sixLoseTeam = $row['team_id_away'];
    } else {
      // draw could have been flipped
      $winner = $row['winner'];
      $winner2 = $row['winner2'];
      $sixGameId = $row['sixGameId'];
      
      $winnerIsHome = false;
      
      $sql = "SELECT wp.name FROM weblm_players wp ".
        "LEFT JOIN six_profiles sp ON sp.user_id=wp.player_id ".
        "LEFT JOIN six_matches_played smp ON smp.profile_id=sp.id ".
        "WHERE smp.match_id=".$sixGameId." AND smp.home=1";
      $drawRes = mysql_query($sql);

      $logRep->logInfo('InsertTeamAndUpdate: DrawFlip: sql='.$sql.' affected='.mysql_affected_rows());

      while ($drawRow = mysql_fetch_array($drawRes)) {
        $homeName = $drawRow['name'];
        if ($homeName == $winner || $homeName == $winner2) {
          $winnerIsHome = true;
        }
      }

      $logRep->logInfo('InsertTeamAndUpdate: DrawFlip: winnerIsHome='.$winnerIsHome);

      if ($winnerIsHome) {
        $sixWinTeam = $row['team_id_home'];
        $sixLoseTeam = $row['team_id_away'];
      } else {
        $sixWinTeam = $row['team_id_away'];
        $sixLoseTeam = $row['team_id_home'];
      }
    }
    
    if ($sixWinTeam == $sixTeamId && $winnerteam == 0) {
      $sql = "UPDATE weblm_games SET winnerteam=".$ladderTeamId." WHERE game_id=".$game_id;
      mysql_query($sql);
      $logRep->logInfo('InsertTeamAndUpdate: Other Games: sql='.$sql.' affected='.mysql_affected_rows());
      $setWinner = true;
    }
    if ($sixLoseTeam == $sixTeamId && $loserteam == 0) {
      $sql = "UPDATE weblm_games SET loserteam=".$ladderTeamId." WHERE game_id=".$game_id;
      mysql_query($sql);
      $logRep->logInfo('InsertTeamAndUpdate: Other Games: sql='.$sql.' affected='.mysql_affected_rows());
      $setLoser = true;
    }

    $logRep->logInfo('InsertTeamAndUpdate: Other Games: winnerteam='.$winnerteam.' loserteam='.$loserteam.' setWinner='.$setWinner.' setLoser='.$setLoser);
    
    if (($setWinner && $loserteam > 0) || ($setLoser && $winnerteam > 0) || ($setWinner && $setLoser)) {
      UpdateLadderGamePoints($game_id, $logRep);
      $sql = "UPDATE weblm_games SET edited=1 WHERE game_id=".$game_id;
      mysql_query($sql);
      $logRep->logInfo('InsertTeamAndUpdate: Other Games: sql='.$sql.' affected='.mysql_affected_rows());
    }
  }
}

function RecalculateDcForProfile($profileId) {
  $sql = "SELECT season FROM six_stats";
  $sixSeason = mysql_fetch_array(mysql_query($sql))[0];

  $sql = "SELECT sms.season, sms.minutes, sms.scoreHome, sms.scoreAway, sms.profileHome, sms.profileAway, ".
	"sp1.user_id AS userIdHome, sp2.user_id AS userIdAway, ".
	"UNIX_TIMESTAMP( sms.updated ) AS updatedTS ".
	"FROM six_matches_status sms ".
	"LEFT JOIN six_profiles sp1 ON sp1.id = sms.profileHome ".
	"LEFT JOIN six_profiles sp2 ON sp2.id = sms.profileAway ".
	"WHERE (sms.profileHome=$profileId OR sms.profileAway=$profileId) ".
	"AND sms.profileHome2=0 ".
	"AND sms.profileAway2=0 ".
  "AND sms.lobbyName <> 'Training' ".
  "AND sms.season=$sixSeason";
  
  // echo "<p>$sql</p>";
  
  $result = mysql_query($sql);

  $profiles = array();
  $users = array();
  $dc = 0;

  while ($row = mysql_fetch_array($result)) {
    $scoreHome = $row['scoreHome'];
    $scoreAway = $row['scoreAway'];
    $minutes = $row['minutes'];
    $updated = $row['updatedTS'];
    $season = $row['season'];
    
    if ($scoreHome > $scoreAway) {
      $scoreLeft = $scoreHome;
      $scoreRight = $scoreAway;
      $profileIdLoser = $row['profileAway'];
      $userIdLoser = $row['userIdAway'];
    } else {
      $scoreLeft = $scoreAway;
      $scoreRight = $scoreHome;
      $profileIdLoser = $row['profileHome'];
      $userIdLoser = $row['userIdHome'];
    }
    
    if (IsFishySixserverGame($scoreLeft, $scoreRight, $minutes) && $profileIdLoser == $profileId) {
      $dc++;
    }
  }
  
  $sql = "SELECT disconnects from six_profiles WHERE id=$profileId";
  $dcOld = mysql_fetch_array(mysql_query($sql))[0];
  $sql = "UPDATE six_profiles SET disconnects=$dc WHERE id=$profileId";
  mysql_query($sql);
  $result = "RecalculateDcForProfile: profileId=$profileId dcOld=$dcOld dc=$dc";
  return $result;
}

function RecalculatePointsForProfile($id, $dc, $pts, $rating) {
    
    // current season
    $wins = getSixserverWins($id);
    $draws = getSixserverDraws($id);
    $losses = getSixserverLosses($id);
    $ptsNew = getSixserverPoints($wins, $draws, $losses+$dc);
   
    $msg.= "<br>This season: wins=$wins draws=$draws losses=$losses dc=$dc id=$id pts=$pts ptsNew=$ptsNew";
    
    $sql = "UPDATE six_profiles SET points2=points WHERE id=$id";
    $msg.= "<br>$sql";
    
    mysql_query($sql);

    $sql = "UPDATE six_profiles SET points=$ptsNew WHERE id=$id";
    $msg.= "<br>$sql";
    
    mysql_query($sql);
    
    // rating: all seasons
    
    $sql2 = "SELECT sum(dc) FROM six_history WHERE profileId=".$id;
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);

    $wins = getSixserverWinsAllTime($id);
    $draws = getSixserverDrawsAllTime($id);
    $losses = getSixserverLossesAllTime($id);
    
    $dc = $dc+$row2[0];
    $ratingNew = getSixserverPoints($wins, $draws, $losses+$dc);
    $msg.= "<br>All time: wins=$wins draws=$draws losses=$losses dc=$dc id=$id rating=$rating ratingNew=$ratingNew";
    
    $sql = "UPDATE six_profiles SET rating=$ratingNew WHERE id=$id";
    $msg.= "<br>$sql";
    
    mysql_query($sql);
    
    return $msg;
}

function RecalculateStreak($sixProfileId) {
    $sql = "SELECT smp.home, sm.score_home, sm.score_away FROM six_matches_played smp ".
       "LEFT JOIN six_matches sm ON smp.match_id=sm.id ".
       "WHERE smp.profile_id=$sixProfileId ". 
       "AND sm.numParticipants=2 ".
       "ORDER BY smp.id DESC";
    $result = mysql_query($sql);
    $msg = "<p>$sql</p>";

    $streak = 0;
    while ($row = mysql_fetch_array($result)) {
      if ($row['home'] == 1 && ($row['score_home'] > $row['score_away'])) {
        $streak++;
      } elseif ($row['home'] == 0 && ($row['score_home'] < $row['score_away'])) {
        $streak++;
      } else {
        break;
      }
    }
    $sql = "SELECT * FROM six_streaks WHERE profile_id=$sixProfileId";
    $result = mysql_query($sql);
    while ($row = mysql_fetch_array($result)) {
      $best = $row['best'];
      $msg.= "<p>Old streak for $sixProfileId: Wins=".$row['wins']." Best=".$best."</p>";
      if ($best < $streak) {
        $best = $streak;
      }
      $sql = "UPDATE six_streaks SET wins=$streak, best=$best WHERE profile_id=$sixProfileId";
      $msg.= "<br>$sql";
    
      mysql_query($sql);
    }
    return $msg;
}

function getSubNavMenuString($pagename, $player) {
  require ('variables.php');
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
    return $subMenuContent;
	}
}


function showLatestTopics() {
	require('./variables.php');
	$forum_exclude = "(12, 15, 97)";  //admin and mod forums
	
	$PREFIX = "phpbb3_";
	$TOPICS_TABLE = $PREFIX."topics";
	$FORUMS_TABLE = $PREFIX."forums";
	$POSTS_TABLE = $PREFIX."posts";
	$USERS_TABLE = $PREFIX."users";
			
	$forum_path = "/forum/";
	
	$topics = 10;
	
	$sql_topics = "SELECT topic from ".$topicstable." where active ='Y' ORDER BY prio DESC";
	$result = mysql_query($sql_topics);
	$in_topics = "";
	$not_in_topics = "";
	$num = mysql_num_rows($result);
	$count = 0;
  
  $topicsExclude = "";
  $inline = "";
	
  if ($num > 0) {
		$inline = "";
		while ($row = mysql_fetch_array($result)){
			$count++;
			$inline .= $row[0];
			if ($count < $num) {
				$inline.=", ";
			}
		}
		$in_topics = " AND t.topic_id in (".$inline.") ";
	
    $sql_sticky = "SELECT t.forum_id, t.topic_id, t.topic_last_post_id, t.topic_title, p.post_text, p.post_time, u.username 
      FROM phpbb3_topics t, phpbb3_posts p, phpbb3_users u, ".$topicstable." tt
      WHERE t.topic_approved='1' 
      AND tt.active='Y' 
      AND tt.topic=t.topic_id
      AND t.forum_id NOT IN " . $forum_exclude . " 
      AND t.topic_last_post_id=p.post_id 
      AND p.poster_id = u.user_id ".
      "ORDER BY tt.prio DESC, t.topic_last_post_time DESC limit 0,".$topics;
		
		$result = mysql_query($sql_sticky);
		
    while ($row = mysql_fetch_array($result)) {

			$mar_title = $row["topic_title"];
			$mar_url = $forum_path . 'viewtopic.php?t='.$row["topic_id"];
			$mar_user = $row["username"];
			
			$topic_link = '<a href="'.$mar_url.'">'.$mar_title.'</a>';
			$post_time = formatAgoSpan($row["post_time"]). " ago"; 
			$topic_user = '<span class="grey-small">'." (by <b>$mar_user</b>, $post_time)".'</span>';
			
			
			$line = $topic_link . $topic_user;
			$title = htmlentities(escapePostText($row["post_text"]));
			echo '<tr title="'.$title.'">';
      if ($row['prio'] >= 1000) {
        echo '<td class="link-stickytopic-hot" colspan="3">';
      } else {
        echo '<td class="link-stickytopic" colspan="3">';
      }
			echo $line;
			echo "</td>";
			echo "</tr>";
			$topics--;
		}
	}
	
  if (!empty($inline)) {
    $topicsExclude =  "AND t.topic_id NOT IN (".$inline.") ";
  }
  
  $sql = "SELECT t.forum_id, t.topic_id, t.topic_last_post_id, t.topic_title, p.post_text, p.post_time, u.username 
    FROM phpbb3_topics t, phpbb3_posts p, phpbb3_users u 
    WHERE t.topic_approved='1' 
    AND t.forum_id NOT IN " . $forum_exclude . " 
     ".$topicsExclude."
    AND t.topic_last_post_id=p.post_id 
    AND p.poster_id = u.user_id ORDER BY t.topic_last_post_time DESC limit 0,".$topics;
	
	$result = mysql_query($sql);
  
	while ($row = mysql_fetch_array($result)) {
    $mar_title = $row["topic_title"];
    $mar_url = $forum_path . 'viewtopic.php?p='.$row["topic_last_post_id"].'#p'.$row["topic_last_post_id"];
    $mar_user = $row["username"];
    
    $topic_link = '<a href="'.$mar_url.'">'.$mar_title.'</a>';
    $post_time = formatAgoSpan($row["post_time"]). " ago"; 
    $topic_user = '<span class="grey-small">'." (by <b>$mar_user</b>, $post_time)".'</span>';
    
    
    $line = $topic_link . $topic_user;
    $title = htmlentities(escapePostText($row["post_text"]));
    echo '<tr title="'.$title.'">';
    echo '<td colspan="3">';
    echo $line;
    echo "</td>";
    echo "</tr>";
	}
}

function sendActivation($id, $logJoin) {
 require ('variables.php');
 require ('variablesdb.php');
 require_once ('log/KLogger.php');

 $msg = "";
 
 $logJoin->logInfo('sendActivation: autoApprove='.$autoApprove);
  $msg .= "autoApprove=".$autoApprove;
  if ($autoApprove > 0) {
    $sql = "SELECT mail, name, signup from $playerstable where player_id = '$id'";
    $logJoin->logInfo('sendActivation: sql='.$sql);
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    $toAddress = $row['mail'];
    $name = $row['name'];
    $signupHash = $row['signup'];
    $msg .= "<br>signupHash=".$signupHash;
    if (!empty($signupHash)) {
      $link = "http://www.".$leaguename."/activate.php?id=".$signupHash;
      $res = sendActivationLinkMail($toAddress, $name, $link);
      $logJoin->logInfo('sendActivation: res='.$res);
      $update_sql = "UPDATE $playerstable SET signupSent=1 WHERE player_id = '".$id."'";
      $update_result = mysql_query($update_sql);
      $logJoin->logInfo('sendActivation: update_result='.$update_result);
      $msg .= "<br>sent activation: toAddress=".$toAddress." name=".$name;
    }
  }
  return $msg;
}

?>