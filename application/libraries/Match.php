<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');  

class Match{

	private $match_id;
	public $CI;
	
	public $game_mode = 0;
	public $innings = 1;
	
	public $batting_team_id = 0; // the team that is batting first
	public $bowling_team_id = 0; // the team that is batting second
	
	public $innings_completed = false;
	public $innings_total = 0;
	public $innings_wickets = 0;
	public $innings_overs = "0.0";
	public $innings_balls_bowled = 0;
	
	public $innings_noballs = 0;
	public $innings_wides = 0;
	public $innings_byes = 0;
	public $innings_legbyes = 0;
	
	public $bowlers = array();
	public $balls_per_bowler = 0;
	public $currently_bowling_index = 0;
	public $current_bowler = array();
	public $part_timers_available = false;
	public $part_timers_count = 0;
	public $change_bowler_after_overs = 3;
	
	public $batsmen = array();
	public $striker = array();
	public $non_striker = array();
	
	
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
		$this->batting_team_id = $teams[0];
		$this->bowling_team_id = $teams[1];
	}

	/*
		* GET THE BOWLING OPTIONS FOR FIELDING TEAM
		* GENERATE DELIVERIES FOR EACH BOWLER
		* SET THE OPENING BATSMEN
		* SET THE OPENING BOWLER
	*/
	public function startInnings($innings)
	{
		if($innings == 'first')
		{
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->bowling_team_id);
		}
		else if($innings == 'second')
		{
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->batting_team_id);
		}

		foreach($this->bowlers as $index=>$array)
		{
			$points = $array['rating_points'];
			$role = $array['player_type'];
			$this->balls_per_bowler = 60;
			$this->generateDeliveries($index, $role, $points);	
		}

		$this->setOpeningBatsman();
		$this->current_bowler = $this->bowlers[$this->currently_bowling_index];	
		$this->Simulate();
	}

	public function Simulate()
	{
		$number_of_deliveries = ($this->game_mode == 1 ? 100 : 120);
		
		while($number_of_deliveries > 0)
		{
			if(! $this->innings_completed)
			{
				
				$ball_result = $this->current_bowler['deliveries'][($this->current_bowler['last_ball_index'] == 0 ? 1 : ($this->current_bowler['last_ball_index'] + 1))];		
				if($ball_result == "WIDE" || $ball_result == "NOBALL")
				{					
					if($ball_result == "WIDE")
					{
						$this->innings_wides += 1;						
					}
					else if($ball_result == "NOBALL")
					{
						$this->innings_noballs += 1;
					}
					$this->innings_total += 1;
				}
				else
				{					
					$this->current_bowler['legal_balls'] += 1;
					if($ball_result == "WICKET")
					{
						$this->innings_wickets += 1;
						$this->current_bowler['wickets'] += 1;
						if($this->innings_wickets == 10)
						{
							$this->innings_completed = true;
						}
					}
					else
					{

					}

					// is a legal delivery, so decrease total ball count	
					$this->innings_balls_bowled += 1;				
					$number_of_deliveries--;
				}
				
				$this->current_bowler['last_ball_index'] += 1;

				// over complete
				if($this->current_bowler['legal_balls'] > 0 && $this->current_bowler['legal_balls'] % 6 == 0)
				{
					$this->innings_overs = floor($this->innings_balls_bowled/6).".".floor($this->innings_balls_bowled % 6);
					$this->changeBowler();
				}
				else
				{
					$this->innings_overs = floor($this->innings_balls_bowled/6).".".floor($this->innings_balls_bowled % 6);
				}
			}			
		}
		//echo $this->innings_total.'/'.$this->innings_wickets;
		//echo '<br />';
		//echo $this->innings_overs;
	}

	public function changeBowler()
	{
		$bowling_options_number = count($this->bowlers);
		if($bowling_options_number > 5)
		{
			$this->part_timers_available = true;
			$this->part_timers_count = count($this->bowlers) - 5;
		}

		$current_index = $this->currently_bowling_index;
		$new_index = $current_index + 1;
		if($new_index <= 4)
		{
			$this->current_bowler = $this->bowlers[$new_index];
			$this->currently_bowling_index = $new_index;
		}
		else
		{			
			$this->current_bowler = $this->bowlers[0];
			$this->currently_bowling_index = 0;
		}
	}

	public function setOpeningBatsman()
	{
		$this->batsmen = $this->CI->Center->getTeamBattingLineup($this->match_id, $this->batting_team_id);		
		$this->striker = $this->batsmen[0];
		$this->non_striker = $this->batsmen[1];
	}
	
	public function getMatchId()
	{
		return $this->match_id;
	}
	
	public function generateDeliveries($index, $role, $points)
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
					$this->bowlers[$index]['wides'] += 1;
				}
				else if($result == "NOBALL")
				{	
					$this->bowlers[$index]['noballs'] += 1;
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
		$this->bowlers[$index]['deliveries'] = $deliveries;		
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
}