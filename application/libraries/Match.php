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
	public $innings_commentary = array();
	public $last_over_total = 0;
	public $game_stage = "PP";
	
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
	public $next_batsman_index = 0;
	public $striker_index = 0;
	public $non_striker_index = 1;
	
	
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

	public function setOpeningBatsman()
	{
		$this->batsmen = $this->CI->Center->getTeamBattingLineup($this->match_id, $this->batting_team_id);		
		$this->striker = $this->batsmen[$this->striker_index];
		$this->non_striker = $this->batsmen[$this->non_striker_index];
		$this->next_batsman_index = 2;
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
		$number_of_deliveries = ($this->game_mode == 1 ? 300 : 120);
		while($number_of_deliveries > 0)
		{
			if($this->game_mode == 1)
			{
				if($this->innings_balls_bowled <= 60)
				{
					$this->game_stage = "PP1";
				}
				else if($this->innings_balls_bowled > 240)
				{
					$this->game_stage = "PP2";
				}
				else
				{
					$this->game_stage = "MO";
				}
			}
			else
			{
				if($this->innings_balls_bowled <= 36)
				{
					$this->game_stage = "PP1";
				}
				else if($this->innings_balls_bowled > 90)
				{
					$this->game_stage = "PP2";
				}
				else
				{
					$this->game_stage = "MO";
				}
			}
			

			$last_delivery_index = (int) $this->bowlers[$this->currently_bowling_index]['last_ball_index'];
			$next_delivery_index = ($last_delivery_index + 1);
			if(isset($this->bowlers[$this->currently_bowling_index]['deliveries'][$next_delivery_index]))				
			{
				$ball_result = $this->bowlers[$this->currently_bowling_index]['deliveries'][$next_delivery_index];
			}
			else
			{
				$ball_result = "AVERAGE";
			}
			$this->bowlers[$this->currently_bowling_index]['last_ball_index'] = ((int) $this->bowlers[$this->currently_bowling_index]['last_ball_index'] + 1);
			
			if($ball_result == "WIDE" || $ball_result == "NOBALL")
			{
				if($ball_result == "WIDE")
				{
					$this->innings_wides = (int) $this->innings_wides + 1;
					$this->bowlers[$this->currently_bowling_index]['wides'] = (int) $this->bowlers[$this->currently_bowling_index]['wides'] + 1;
				}
				else if($ball_result == "NOBALL")
				{
					$this->innings_noballs = (int) $this->innings_noballs + 1;
					$this->bowlers[$this->currently_bowling_index]['noballs'] = (int) $this->bowlers[$this->currently_bowling_index]['noballs'] + 1;
				}
				$this->innings_total += 1;
				$this->bowlers[$this->currently_bowling_index]['runs'] = (int) $this->bowlers[$this->currently_bowling_index]['runs'] + 1;
				$result = $ball_result;
				$check_for_over = false;
			}
			else
			{	
				// is a legal delivery
				$this->bowlers[$this->currently_bowling_index]['legal_balls'] = ((int) $this->bowlers[$this->currently_bowling_index]['legal_balls'] + 1);
				$this->innings_balls_bowled += 1;
				$number_of_deliveries--;

				$batsman_role = $this->striker['role'];
				$batsman_points = $this->striker['bat'];
				$bowler_role = $this->bowlers[$this->currently_bowling_index]['player_type'];
				$bowler_points = $this->bowlers[$this->currently_bowling_index]['rating_points'];

				if($ball_result == "WICKET")
				{
					if($bowler_role == '2')
					{
						if($bowler_points >= $batsman_points)
						{
							$out = array("NOTOUT", "NOTOUT", "W", "NOTOUT", "W", "NOTOUT", "NOTOUT", "W", "NOTOUT", "W");
						}
						else
						{
							$out = array("NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "W", "NOTOUT", "W", "NOTOUT");
						}							
					}
					else if($bowler_role == '3')
					{
						if($bowler_points >= $batsman_points)
						{
							$out = array("NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT");
						}
						else
						{
							$out = array("NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "W", "NOTOUT");
						}
					}
					else if($bowler_role == '4')
					{
						if($bowler_points >= $batsman_points)
						{
							$out = array("NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "W", "NOTOUT");
						}
						else
						{
							$out = array("NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT");
						}
					}
					$result = $out[mt_rand(0, (count($out) - 1))];

					if($result === "W")
					{	
						$this->innings_wickets = (int) $this->innings_wickets + 1;
						$this->bowlers[$this->currently_bowling_index]['wickets'] = (int) $this->bowlers[$this->currently_bowling_index]['wickets'] + 1;
						$this->batsmen[$this->striker_index]['status'] = "OUT";
						if($this->innings_wickets == 10)
						{
							$this->innings_completed = true;
							$this->addToCommentary($this->innings_balls_bowled, $result);							
							break;
						}
					}
					else
					{											
						$this->batsmen[$this->striker_index]['status'] = "NOTOUT";
					}
					$this->batsmen[$this->striker_index]['balls'] = (int) $this->batsmen[$this->striker_index]['balls'] + 1;					
				}
				else
				{
					$this->batsmen[$this->striker_index]['status'] = "NOTOUT";
					$this->batsmen[$this->striker_index]['balls'] = (int) $this->batsmen[$this->striker_index]['balls'] + 1;

					// could be 0s,1s,2s,3s,4s,6s					

					/*
					1	=	PURE BATSMAN
					2	=	PURE BOWLER
					3	=	BATTING ALLROUNDER
					4	=	BOWLING ALLROUNDER
					5	=	WICKETKEEPER
					*/

					if($ball_result == 'BAD')
					{
						if($batsman_points >= $bowler_points)
						{
							if($batsman_role == 1 || $batsman_role == 5)
							{
								$result = $this->aggresiveApproach();
							}
							else if($batsman_role == 3)
							{
								$result = $this->conservativeApproach();
							}
							else
							{
								$result = $this->defensiveApproach();
							}
						}
						else
						{
							if($batsman_role == 1 || $batsman_role == 5)
							{
								$result = $this->conservativeApproach();
							}
							else if($batsman_role == 3)
							{
								$result = $this->defensiveApproach();
							}
							else
							{
								$result = $this->defensiveApproach();
							}
						}
					}
					else if($ball_result == 'GOOD')
					{
						if($this->game_stage == "PP1")
						{
							if($batsman_role == 1)
							{
								$result = $this->conservativeApproach();
							}
							else if($batsman_role == 2)
							{
								$result = 0;
							}
							else if($batsman_role == 3)
							{
								$result = $this->oneOrZero();
							}
							else if($batsman_role == 4)
							{
								$result = 0;
							}
							else if($batsman_role == 5)
							{
								$result = $this->conservativeApproach();
							}
						}
						else
						{
							if($batsman_role == 1)
							{
								$result = $this->defensiveApproach();
							}
							else if($batsman_role == 2)
							{
								$result = 0;
							}
							else if($batsman_role == 3)
							{
								$result = $this->defensiveApproach();
							}
							else if($batsman_role == 4)
							{
								$result = 0;
							}
							else if($batsman_role == 5)
							{
								$result = $this->defensiveApproach();
							}
						}
					}
					else if($ball_result == 'AVERAGE')
					{
						if($this->game_stage == "PP1")
						{
							if($batsman_role == 1)
							{
								$result = $this->aggresiveApproach();
							}
							else if($batsman_role == 2)
							{
								$result = $this->oneOrZero();
							}
							else if($batsman_role == 3)
							{
								$result = $this->aggresiveApproach();
							}
							else if($batsman_role == 4)
							{
								$result = $this->oneOrZero();
							}
							else if($batsman_role == 5)
							{
								$result = $this->aggresiveApproach();
							}
						}
						else
						{
							if($batsman_role == 1)
							{
								$result = $this->conservativeApproach();
							}
							else if($batsman_role == 2)
							{
								$result = 0;
							}
							else if($batsman_role == 3)
							{
								$result = $this->oneOrZero();
							}
							else if($batsman_role == 4)
							{
								$result = 0;
							}
							else if($batsman_role == 5)
							{
								$result = $this->conservativeApproach();
							}
						}
					}

					if($result === 4 || $result === 6)
					{
						if($result === 4)
						{
							$this->batsmen[$this->striker_index]['fours'] = ((int) $this->batsmen[$this->striker_index]['fours'] + 1);
						}
						else if($result === 6)
						{							
							$this->batsmen[$this->striker_index]['sixes'] = ((int) $this->batsmen[$this->striker_index]['sixes'] + 1);
						}							
					}
					$this->batsmen[$this->striker_index]['runs'] += $result;
					$this->bowlers[$this->currently_bowling_index]['runs'] += $result;
					$this->innings_total = ((int) $this->innings_total + (int) $result);
				}
				$check_for_over = true;
			}
			
			// last ball of over, so use 1.6 instead of 2.0
			/*
			if($this->innings_balls_bowled % 6 == 0)
			{
				$this->innings_overs = (floor($this->innings_balls_bowled/6) - 1).".6";
			}
			else
			{
				$this->innings_overs = floor($this->innings_balls_bowled/6).".".floor($this->innings_balls_bowled % 6);	
			}
			*/
			$this->addToCommentary($this->innings_balls_bowled, $result);

			// change strike if single or three taken and it's not end of over
			if($check_for_over)
			{
				if(($result === 1 || $result === 3) && $this->bowlers[$this->currently_bowling_index]['legal_balls'] % 6 != 0)
				{				
					$non_strike_index = $this->non_striker['batting_index'];
					$this->striker_index = $non_strike_index;
					$temp = $this->striker;
					$this->striker = $this->non_striker;
					$this->non_striker = $temp;
					unset($temp);
				}
				else if($result === "W") // get new striker on the crease
				{
					$this->striker = $this->batsmen[$this->next_batsman_index];
					$this->next_batsman_index = (int) $this->next_batsman_index + 1;
					$this->striker_index = $this->striker['batting_index'];	
				}

				// over complete
				if($this->bowlers[$this->currently_bowling_index]['legal_balls'] > 0 && $this->bowlers[$this->currently_bowling_index]['legal_balls'] % 6 == 0)
				{
					// change strike if condition met
					if($result === 'W' || $result === 0 || $result === 2 || $result === 4 || $result === 6)				
					{
						$temp = $this->striker;
						$this->striker = $this->non_striker;
						$this->non_striker = $temp;
						unset($temp);
						$this->striker_index = $this->striker['batting_index'];	
					}

					$this->changeBowler();
				}
			}
			
		}

		/*
		foreach($this->bowlers as $index=>$ary)
		{
			echo '<h3>'.$ary['name'].'</h3>';						
			echo '<div>Runs: '.$ary['runs'].'</div>';
			echo '<div>Wides: '.$ary['wides'].'</div>';
			echo '<div>Noballs: '.$ary['noballs'].'</div>';
			echo '<div>Wickets: '.$ary['wickets'].'</div>';
			echo '<hr />';
		}
		

		foreach($this->batsmen as $index=>$ary)
		{
			if($ary['runs'] > 0)
			{
				echo '<h3>'.$ary['name'].'</h3>';						
				echo '<div>Runs: '.$ary['runs'].'</div>';
				echo '<div>Balls: '.$ary['balls'].'</div>';
				echo '<div>Fours: '.$ary['fours'].'</div>';
				echo '<div>Sixes: '.$ary['sixes'].'</div>';
				echo '<div>Status: '.$ary['status'].'</div>';
				echo '<hr />';
			}			
		}
		*/
		
	}

	public function addToCommentary($balls_bowled, $result)
	{
		if($balls_bowled % 6 == 0 && $result !== "WIDE" && $result !== "NOBALL")
		{
			$this->innings_overs = (floor($balls_bowled/6) - 1).".6";
			$over = true;
		}
		else
		{
			$this->innings_overs = floor($balls_bowled/6).".".floor($balls_bowled % 6);
			$over = false;
		}
		$this->innings_commentary[] = "[".$this->innings_overs."] <em>".$this->bowlers[$this->currently_bowling_index]['name'].'</em> to <em>'.$this->batsmen[$this->striker_index]['name'].'</em> ('.$result.')';
		
		if($over)
		{
			if($this->innings_total - $this->last_over_total == 0)
			{
				$this->bowlers[$this->currently_bowling_index]['maidens'] = ( (int) $this->bowlers[$this->currently_bowling_index]['maidens'] + 1);
				// maiden
				$runs = 0;
				$this->last_over_total = $this->innings_total;
			}
			else
			{
				$runs = $this->innings_total - $this->last_over_total;
				$this->last_over_total = $this->innings_total;
			}

			$bowler_name = $this->bowlers[$this->currently_bowling_index]['name'];
			$bowler_balls = $this->bowlers[$this->currently_bowling_index]['legal_balls'];
			$bowler_runs = $this->bowlers[$this->currently_bowling_index]['runs'];
			$bowler_wickets = $this->bowlers[$this->currently_bowling_index]['wickets'];
			$bowler_maidens = $this->bowlers[$this->currently_bowling_index]['maidens'];
			$bowler_string = $bowler_name.str_repeat("&nbsp;", 8).floor($bowler_balls/6).'-'.$bowler_maidens.'-'.$bowler_runs.'-'.$bowler_wickets;

			$this->innings_commentary[] = "<p class='comm_end_of_over'><strong>End of over ".floor($balls_bowled/6).'</strong></p><p>Score: <strong>'.$this->innings_total.'/'.$this->innings_wickets.'</strong> | '.$runs.' runs <br />'.$bowler_string.'</p>';
		}
	}


	public function aggresiveApproach()
	{
		if($this->game_stage == 'PP1')
		{			
			$input = array(1, 4, 6, 3, 2, 4, 1, 1, 3, 2);
		}
		else if($this->game_stage == 'MO')
		{			
			$input = array(1, 4, 0, 1, 6, 0, 3, 2, 1, 2);
		}
		else
		{			
			$input = array(3, 4, 6, 3, 2, 4, 6, 4, 3, 6);
		}
		return $input[rand(0, count($input) - 1)];
	}

	public function conservativeApproach()
	{
		if($this->game_stage == 'PP1')
		{			
			$input = array(2, 1, 0, 3, 1, 4, 1, 2, 1, 1);
		}
		else if($this->game_stage == 'MO')
		{			
			$input = array(1, 0, 1, 3, 1, 1, 1, 2, 0, 1);
		}
		else
		{			
			$input = array(2, 1, 4, 3, 1, 4, 3, 2, 0, 1);
		}
		return $input[rand(0, count($input) - 1)];
	}

	public function defensiveApproach()
	{
		if($this->game_stage == 'PP1')
		{			
			$input = array(1, 0, 1, 2, 1, 0, 1, 0, 1, 2);
		}
		else if($this->game_stage == 'MO')
		{			
			$input = array(1, 0, 1, 1, 1, 0, 1, 0, 1, 0);
		}
		else
		{			
			$input = array(1, 2, 1, 2, 1, 1, 2, 0, 1, 2);
		}
		return $input[rand(0, count($input) - 1)];
	}

	public function oneOrZero()
	{
		$input = array(0,0,1,0,1,0,1,0,0,1,0,0,1,0,1,0,0,1,0,0,0,0,1,0);
		return $input[rand(0, count($input) - 1)];
	}

	public function oneOrTwo()
	{
		$input = array(1,1,1,1,1,2,1,2,1,1,1,1,1,1,1,2,1,1,1,1,1,1,1,1,1,2);
		return $input[rand(0, count($input) - 1)];
	}

	public function zeroOneTwo()
	{
		$input = array(0,1,0,0,0,2,0,0,0,0,1,0,0,0,0,0,1,0,0,0,0,0,0,2,0,0,2);
		return $input[rand(0, count($input) - 1)];
	}

	public function oneTwoThree()
	{
		$input = array(1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3);
		return $input[rand(0, count($input) - 1)];
	}	

	public function fourOrSix()
	{
		$input = array(4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6);
		return $input[rand(0, count($input) - 1)];
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
		$new_index = (int) $current_index + 1;

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