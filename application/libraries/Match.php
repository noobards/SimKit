<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');  

class Match{

	public $match_id;
	public $CI;
	public $innings = 1;
	public $batting_team = 0; // the team that is batting first
	public $bowling_team = 0; // the team that is batting second
	public $fist_innings_completed = false;
	public $team1_wickets = 0;
	public $team2_wickets = 0;
	public $bowlers = [];
	public $balls_per_bowler = 0;
	public $game_mode = 0;
	
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
		$this->game_mode = $this->CI->Center->getGameMode($this->match_id);
		$teams = $this->CI->Center->getTeam1Team2($this->match_id);
		$this->batting_team = $teams[0];
		$this->bowling_team = $teams[1];
	}

	public function startInnings($innings)
	{
		if($innings == 'first')
		{
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->bowling_team);
		}
		else if($innings == 'second')
		{
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->batting_team);
		}

		foreach($this->bowlers as $pid=>$attr)
		{
			$points = $attr['rating_points'];
			$role = $attr['player_type'];
			$this->balls_per_bowler = 60;
			$this->generateDeliveries($pid, $role, $points);			
		}
	}
	
	public function getMatchId()
	{
		return $this->match_id;
	}
	
	public function generateDeliveries($pid, $role, $points)
	{
		$wicket_taking_ball_count = $this->wicketDeliveryCount($role, $points);
		$good_ball_count = $this->goodDeliveryCount($role, $points);
		$bad_ball_count = $this->badDeliveryCount($role, $points);
		$extra_ball_count = $this->extraDeliveryCount($role, $points);
		

		$balls_bank = range(1, 60);

		
		
		$wicket_taking_balls = array();
		for($k = 1; $k <= $wicket_taking_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$wicket_taking_balls[] = array_pop($balls_bank);
		}

		$good_delivery_balls = array();
		for($k = 1; $k <= $good_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$good_delivery_balls[] = array_pop($balls_bank);
		}

		$bad_delivery_balls = array();
		for($k = 1; $k <= $bad_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$bad_delivery_balls[] = array_pop($balls_bank);
		}

		$extra_delivery_balls = array();
		for($k = 1; $k <= $extra_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$extra_delivery_balls[] = array_pop($balls_bank);
		}


		$deliveries = array();
		for($i = 1; $i <= $this->balls_per_bowler; $i++)
		{
			$extra_balls_to_be_bowled = false;
			if(in_array($i, $wicket_taking_balls))
			{
				$deliveries[$i] = "WICKET";
			}
			else if(in_array($i, $good_delivery_balls))
			{
				$deliveries[$i] = "GOOD";
			}
			else if(in_array($i, $bad_delivery_balls))
			{
				$deliveries[$i] = "BAD";
			}
			else if(in_array($i, $extra_delivery_balls))
			{
				$possibility = array("WIDE", "NOBALL","WIDE", "NOBALL","WIDE", "NOBALL","WIDE", "NOBALL","WIDE", "NOBALL");
				$result = $possibility[mt_rand(0, count($possibility) - 1)];
				if($result == 'WIDE')
				{
					$this->bowlers[$pid]['wides'] += 1;
				}
				else if($result == "NOBALL")
				{	
					$this->bowlers[$pid]['noballs'] += 1;
				}
				$deliveries[$i] = $result;
				$extra_balls_to_be_bowled = true;
			}
			else
			{				
				$deliveries[$i] = "AVERAGE";
			}
			if($extra_balls_to_be_bowled)
			{
				$this->balls_per_bowler++;
			}
		}
		$this->bowlers[$pid]['deliveries'] = $deliveries;		
	}

	public function extraDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 20)
		{
			if($role == '2')
			{
				return 4;
			}
			else
			{
				return 5;
			}			
		}
		else if($points >= 20 && $points < 40)
		{
			if($role == '2')
			{
				return 2;
			}
			else
			{
				return 3;
			}
		}
		else if($points >= 40 && $points < 60)
		{
			if($role == '2')
			{
				$possibility = array(0,1,2,0,1,2);
				return $possibility[mt_rand(0, count($possibility) - 1)];
			}				
			else
			{
				$possibility = array(0,1,2,3,0,1,2,3);
				return $possibility[mt_rand(0, count($possibility) - 1)];
			}
		}
		else if($points >= 60 && $points < 80)
		{
			if($role == '2')
			{
				return mt_rand(0,1);
			}
			else if($role == '4')
			{
				$possibility = array(0,1,0,1);
				return $possibility[mt_rand(0, count($possibility) - 1)];
			}
			else
			{
				$possibility = array(0,1,2);
				return $possibility[mt_rand(0, count($possibility) - 1)];
			}
		}
		else if($points >= 80 && $points < 90)
		{
			if($role == '2')
			{
				$possibility = array(0,1,0,1);
				return $possibility[mt_rand(0, count($possibility) - 1)];
			}			
			else
			{
				return mt_rand(0,1);
			}
		}
		else
		{
			if($role == '2')
			{
				return 0;
			}			
			else
			{
				$possibility = array(0,1,0,1);
				return $possibility[mt_rand(0, count($possibility) - 1)];
			}
		}		
	}

	public function badDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 20)
		{
			if($role == '2')
			{
				return 9;
			}
			else
			{
				return 10;
			}			
		}
		else if($points >= 20 && $points < 40)
		{
			if($role == '2')
			{
				return 7;
			}
			else
			{
				return 8;	
			}
		}
		else if($points >= 40 && $points < 60)
		{
			if($role == '2')
			{
				return 6;
			}
			else if($role == '4')
			{
				return 7;
			}			
			else
			{
				return 8;
			}
		}
		else if($points >= 60 && $points < 80)
		{
			if($role == '2')
			{
				return 5;
			}
			else if($role == '4')
			{
				return 6;
			}
			else
			{
				return 7;
			}
		}
		else if($points >= 80 && $points < 90)
		{
			if($role == '2')
			{
				return 4;
			}
			else if($role == '4')
			{
				return 5;
			}
			else
			{
				return 6;
			}
		}
		else
		{
			if($role == '2')
			{
				return 3;
			}
			else if($role == '4')
			{
				return 4;
			}
			else
			{
				return 5;
			}
		}		
	}


	public function goodDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 20)
		{
			if($role == '2')
			{
				return 3;
			}
			else
			{
				return 1;
			}			
		}
		else if($points >= 20 && $points < 40)
		{
			if($role == '2')
			{
				return 5;
			}
			else
			{
				return 3;	
			}
		}
		else if($points >= 40 && $points < 60)
		{
			if($role == '2')
			{
				return 7;
			}
			else if($role == '4')
			{
				return 5;
			}			
			else
			{
				return 3;
			}
		}
		else if($points >= 60 && $points < 80)
		{
			if($role == '2')
			{
				return 9;
			}
			else if($role == '4')
			{
				return 7;
			}
			else
			{
				return 5;
			}
		}
		else if($points >= 80 && $points < 90)
		{
			if($role == '2')
			{
				return 11;
			}
			else if($role == '4')
			{
				return 9;
			}
			else
			{
				return 7;
			}
		}
		else
		{
			if($role == '2')
			{
				return 13;
			}
			else if($role == '4')
			{
				return 11;
			}
			else
			{
				return 9;
			}
		}		
	}
	
	/* 
		* This function decides how many potential wicket taking deliveries will a bowler bowl
		* Adjust the values in this function if wickets are more frequent or less frequent
	*/
	public function wicketDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 20)
		{
			if($role == '2')
			{
				return 1;
			}
			else
			{
				return 0;
			}			
		}
		else if($points >= 20 && $points < 40)
		{
			if($role == '2')
			{
				return 2;
			}
			else
			{
				return 1;	
			}
		}
		else if($points >= 40 && $points < 60)
		{
			if($role == '2')
			{
				return 3;
			}
			else if($role == '4')
			{
				return 2;
			}			
			else
			{
				return 1;
			}
		}
		else if($points >= 60 && $points < 80)
		{
			if($role == '2')
			{
				return 4;
			}
			else if($role == '4')
			{
				return 3;
			}
			else
			{
				return 2;
			}
		}
		else if($points >= 80 && $points < 90)
		{
			if($role == '2')
			{
				return 5;
			}
			else if($role == '4')
			{
				return 4;
			}
			else
			{
				return 3;
			}
		}
		else
		{
			if($role == '2')
			{
				return 6;
			}
			else if($role == '4')
			{
				return 5;
			}
			else
			{
				return 4;
			}
		}
	}
}