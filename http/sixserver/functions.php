<?php

function GetStandingsArray($currentSeasonOnly) {
  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'../variables.php');
  
  $pointsArray = array();
  
  $sql = "SELECT smp.profile_id, smp.home, sm.score_home, sm.score_away ".
    "FROM six_matches_played smp ".
    "LEFT JOIN six_matches sm ON sm.id = smp.match_id ".
    "WHERE sm.numParticipants=2 ";
  if ($currentSeasonOnly) {
    $sql .= "AND sm.season=(SELECT season FROM six_stats) ";
  }
  $result = mysql_query($sql);

  while ($row = mysql_fetch_array($result)) {
    if (!array_key_exists($row['profile_id'], $pointsArray)) {
      $pointsArray[$row['profile_id']] = array('w' => 0, 'd' => 0, 'l' => 0);
    }
    if ($row['score_home'] == $row['score_away']) {
      $pointsArray[$row['profile_id']]['d'] = $pointsArray[$row['profile_id']]['d'] + 1;
    } elseif (($row['home'] == 1 && $row['score_home'] > $row['score_away']) || ($row['home'] == 0 && $row['score_home'] < $row['score_away'])) {
      $pointsArray[$row['profile_id']]['w'] = $pointsArray[$row['profile_id']]['w'] + 1;
    } else {
      $pointsArray[$row['profile_id']]['l'] = $pointsArray[$row['profile_id']]['l'] + 1;
    }
  }
  
  return $pointsArray;

}

function GetSixTeamStandingsArray() {
  $appRoot = realpath( dirname( __FILE__ ) ).'/';
  require_once($appRoot.'../variables.php');
  require_once($appRoot.'../functions.php');
  
  $pointsArray = array();
  
  $sql = "SELECT sm.id, sm.score_home, sm.score_away FROM six_matches sm ".
    "WHERE sm.numParticipants > 2 AND played_on > date_sub(now(), INTERVAL 90 DAY)";
  $result = mysql_query($sql);
  while ($row = mysql_fetch_array($result)) {
    
    $scoreHome = $row['score_home'];
    $scoreAway = $row['score_away'];
    $matchId = $row['id'];
    $sql = "SELECT smp.home, sp.user_id FROM six_matches_played smp ".
      "LEFT JOIN six_profiles sp ON smp.profile_id=sp.id ".
      "WHERE smp.match_id=".$matchId." ORDER BY home ASC";
    $resultSmp = mysql_query($sql);
    $userIdHome = "";
    $userIdHome2 = "";
    $userIdHome3 = "";
    $userIdAway = "";
    $userIdAway2 = "";
    $userIdAway3 = "";
    while ($rowSmp = mysql_fetch_array($resultSmp)) {
      $home = $rowSmp['home'];
      if ($home == 1) {
        if ($userIdHome == "") {
          $userIdHome = $rowSmp['user_id'];
        } elseif ($userIdHome2 == "") {
          $userIdHome2 = $rowSmp['user_id'];
        } else {
          $userIdHome3 = $rowSmp['user_id'];
        }
      } else {
        if ($userIdAway == "") {
          $userIdAway = $rowSmp['user_id'];
        } elseif ($userIdAway2 == "") {
          $userIdAway2 = $rowSmp['user_id'];
        } else {
          $userIdAway3 = $rowSmp['user_id'];
        }
      }
    }
    
    if (empty($userIdHome3) && empty($userIdAway3)) {
      if ($userIdHome2 <> "") {
        if ($userIdHome2 < $userIdHome) {
           $key = $userIdHome2."-".$userIdHome;
        } else {
           $key = $userIdHome."-".$userIdHome2;
        }
        
        if (!array_key_exists($key, $pointsArray)) {
          $pointsArray[$key] = array('pt' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'dc' => 0);
        }
        if ($scoreHome == $scoreAway) {
          $pointsArray[$key]['d'] = $pointsArray[$key]['d'] + 1;
        } elseif (($home == 1 && $scoreHome > $scoreAway) || ($home == 0 && $scoreHome < $scoreAway)) {
          $pointsArray[$key]['w'] = $pointsArray[$key]['w'] + 1;
        } else {
          $pointsArray[$key]['l'] = $pointsArray[$key]['l'] + 1;
        }
      }

      if ($userIdAway2 <> "") {
        if ($userIdAway2 < $userIdAway) {
           $key = $userIdAway2."-".$userIdAway;
        } else {
           $key = $userIdAway."-".$userIdAway2;
        }
        
        if (!array_key_exists($key, $pointsArray)) {
          $pointsArray[$key] = array('pt' => 0, 'w' => 0, 'd' => 0, 'l' => 0, 'dc' => 0);
        }
        if ($scoreHome == $scoreAway) {
          $pointsArray[$key]['d'] = $pointsArray[$key]['d'] + 1;
        } elseif (($home == 1 && $scoreHome > $scoreAway) || ($home == 0 && $scoreHome < $scoreAway)) {
          $pointsArray[$key]['l'] = $pointsArray[$key]['l'] + 1;
        } else {
          $pointsArray[$key]['w'] = $pointsArray[$key]['w'] + 1;
        }
      }
    }
  }
  
  foreach($pointsArray as $key => $score) {
    // only entries with 5+ games
    if (($score['w'] + $score['d'] + $score['l'] + $score['dc']) < 5) {
      unset($pointsArray[$key]);
    } else {
      $pointsArray[$key]['pt'] = GetSixserverPoints($score['w'],$score['d'],$score['l']+$score['dc']);
    }
  }
  arsort($pointsArray);
  return $pointsArray;
}

// gets the proper season awards image for 1st to 6th place for the profile
// the images are defined in variables.php  
function getSixImgForRank($rank, $align) {
	$appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'../variables.php');

	$img1 = '<img style="vertical-align:'.$align.';" src="'.$directory.'/gfx/';
	$img2 = '" />';

  if ($rank == 1) {
    $img = $img1.$gfx_rank1_six.$img2;
  } elseif ($rank == 2) {
    $img = $img1.$gfx_rank2_six.$img2;
  } elseif ($rank == 3) {
    $img = $img1.$gfx_rank3_six.$img2;
  } elseif ($rank == 4) {
    $img = $img1.$gfx_rank4_six.$img2;
  } elseif ($rank == 5) {
    $img = $img1.$gfx_rank5_six.$img2;
  } elseif ($rank == 6) {
    $img = $img1.$gfx_rank6_six.$img2;
  } else {
    $img = "";
  }
	return $img;
}

function getSixImgForPosAndSeasonArray($position, $seasonArray, $align) {
	$appRoot = realpath( dirname( __FILE__ ) ).'/';
  require($appRoot.'../variables.php');

	$result = "";
	if ($position == 1) {
		$posImg = $gfx_rank1_six;
	} elseif ($position == 2) {
		$posImg = $gfx_rank2_six;
	} elseif ($position == 3) {
		$posImg = $gfx_rank3_six;
	} elseif ($position == 4) {
		$posImg = $gfx_rank4_six;
	} elseif ($position == 5) {
		$posImg = $gfx_rank5_six;
	} elseif ($position == 6) {
		$posImg = $gfx_rank6_six;
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
	
	$result = '<img style="cursor:help; padding-right:2px;vertical-align:'.$align.';" title="Rank '.$position.' in Sixserver season'.$s.' '.$seasons.$times.'" 
				src="'.$directory.'/gfx/'.$posImg.'" />';	
	return $result;
}

?>