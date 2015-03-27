<?php

class TeamStat {
	var $player1;
	var $player2;
	var $ptsWon = 0;
	var $ptsLost = 0;
	var $won = 0;
	var $draw = 0;
	var $lost = 0;
	
	function TeamStat($name1, $name2) {
		$this->player1 = $name1;
		$this->player2 = $name2;
	}
	
	function addPtsWon($points) {
		$this->ptsWon = $this->ptsWon + $points;
	}
	
	function addPtsLost($points) {
		$this->ptsLost = $this->ptsLost + $points;
	}
	
	function isInTeam($name) {
		if ((strcmp($this->player1, $name) == 0) || (strcmp($this->player2, $name) == 0)) {
			return true;
		} else {
			return false;
		}
	}
	
	function isTeam($name1, $name2) {
		if ($this->isInTeam($name1) && $this->isInTeam($name2)) {
			return true;
		} else {
			return false;
		}
	}
	
	function getPtsDiff() {
		return $this->ptsWon-$this->ptsLost;
	}
	
	function compare($a, $b) {
	    $val1 = $a->getPtsDiff();
	    $val2 = $b->getPtsDiff();
	    if ($val1 == $val2) {
	       return 0;
	    } 
	    return ($val1 > $val2) ? -1 : 1;
	    
	}
}
?>