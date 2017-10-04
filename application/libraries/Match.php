<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');  

class Match{

	public $match_id;
	public $CI;
	public $innings = 1;
	public $team1 = 0; // the team that is batting first
	public $team2 = 0; // the team that is batting second
	public $fist_innings_completed = false;
	public $team1_wickets = 0;
	public $team2_wickets = 0;
	
	public function Match()
	{
		$this->CI = get_instance();
		$this->CI->load->model("Center");
	}
	
	public function getInnings()
	{
		return $this->innings;
	}
	
	public function setInnings($number)
	{
		$this->innings = $number;
	}
	
	public function setMatchId($mid)
	{
		$this->match_id = $mid;
		$teams = $this->CI->Center->getTeam1Team2($this->match_id);
		$this->team1 = $teams[0];
		$this->team2 = $teams[1];
	}
	
	public function getMatchId()
	{
		return $this->match_id;
	}
	
	
}