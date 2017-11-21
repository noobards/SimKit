<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');  

class Match{

	private $match_id;
	public $CI;
	
	public $game_mode = 0;
	public $innings = "first";
	public $teams = array();
	public $win_score = 0;
	
	public $batting_team_id = 0; // the team that is batting first
	public $bowling_team_id = 0; // the team that is batting second
	public $batting_team_label = "";
	public $bowling_team_label = "";
	public $partnership_runs = 0;
	public $partnership_balls = 0;
	public $partnerships = array();
	
	public $innings_completed = false;
	public $innings_total = 0;
	public $innings_wickets = 0;
	public $innings_overs = "0.0";
	public $innings_balls_bowled = 0;
	public $innings_commentary = array();
	public $innings_fow = array();
	public $last_over_total = 0;
	public $game_stage = "PP1";	
	public $innings_noballs = 0;
	public $innings_wides = 0;
	public $innings_byes = 0;
	public $innings_legbyes = 0;	
	public $match_result = array();
	
	public $bowlers = array();
	public $balls_per_bowler = 0;
	public $currently_bowling_index = 0;
	public $part_timers_available = false;
	public $part_timers_count = 0;
	public $change_bowler_after_overs = 3;
	public $bowling_pair_1 = 0;
	public $bowling_pair_2 = 1;
	public $bowling_change_count = 0;
	public $get_new_bowling_pair = false;
	public $last_over_bowling_index = null;
	public $fielders = array();

	public $mode_of_dismissal = array("Caught","LBW","Caught","Bowled","Caught","Run Out", "Hit Wicket", "Caught","Caught","Caught","Caught","LBW","Caught","Bowled","Caught","Caught","Bowled","Caught","LBW","Caught","Caught","Caught");
	public $out_how = "";
	public $batsmen = array();
	public $striker = array();
	public $non_striker = array();
	public $next_batsman_index = 0;
	public $striker_index = 0;
	public $non_striker_index = 1;


	// commentary related
	public $dots = array();
	public $singles = array();
	public $twos = array();
	public $threes = array();
	public $fours = array();
	public $sixes = array();
	public $wides = array();
	public $noballs = array();
	public $notout = array();

	public $live = array();
	public $live_index = 0;

	public function __construct()
	{
		$this->CI = get_instance();
		$this->CI->load->model("Center");
		$this->CI->load->model("Team");

		$this->dots = $this->getCommentaryLines('0');
		$this->singles = $this->getCommentaryLines('1');
		$this->twos = $this->getCommentaryLines('2');
		$this->threes = $this->getCommentaryLines('3');
		$this->fours = $this->getCommentaryLines('4');
		$this->sixes = $this->getCommentaryLines('6');
		$this->wides = $this->getCommentaryLines('WD');
		$this->noballs = $this->getCommentaryLines('NB');
		$this->notout = $this->getCommentaryLines('NO');
	}
	
	

	public function getCommentaryLines($ball_result)
	{
		$data = array();
		$this->CI->db->select('line_text');
		$this->CI->db->from('commentary');
		$this->CI->db->where('ball_result', $ball_result);
		$q = $this->CI->db->get();
		if($q && $q->num_rows() > 0)
		{
			foreach($q->result() as $r)
			{
				$data[] = $r->line_text;
			}
		}
		return $data;
	}
		
	public function setMatchId($mid)
	{
		$this->match_id = $mid;
		$this->game_mode = $this->CI->Center->getGameMode($this->match_id);
		$this->teams = $this->CI->Center->getTeam1Team2($this->match_id);
	}

	public function setOpeningBatsman()
	{
		$this->batsmen = $this->CI->Center->getTeamBattingLineup($this->match_id, $this->batting_team_id);		
		$this->striker = $this->batsmen[$this->striker_index];
		$this->non_striker = $this->batsmen[$this->non_striker_index];
		$this->next_batsman_index = 2;
	}

	public function ballsToOvers($num)
	{
		if($num % 6 == 0)
		{
			return (floor($num/6) - 1).'.6';
		}
		else
		{
			return floor($num/6).'.'.floor($num % 6);
		}
	}

	/*
		* GET THE BOWLING OPTIONS FOR FIELDING TEAM
		* GENERATE DELIVERIES FOR EACH BOWLER
		* SET THE OPENING BATSMEN
		* SET THE OPENING BOWLER
	*/
	public function startInnings($innings)
	{
		$this->innings = $innings;
		if($innings == 'first')
		{
			$this->batting_team_id = $this->teams[0];
			$this->batting_team_label = $this->CI->Team->getTeamName($this->teams[0]);

			$this->bowling_team_id = $this->teams[1];
			$this->bowling_team_label = $this->CI->Team->getTeamName($this->teams[1]);
			$this->fielders = $this->CI->Center->getFielderNames($this->match_id, $this->teams[1]);			
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->bowling_team_id);
		}
		else if($innings == 'second')
		{

			$this->innings_completed = false;
			$this->innings_total = 0;
			$this->innings_wickets = 0;
			$this->innings_overs = "0.0";
			$this->innings_balls_bowled = 0;
			$this->innings_commentary = array();
			$this->innings_fow = array();
			$this->last_over_total = 0;
			$this->game_stage = "PP1";	
			$this->innings_noballs = 0;
			$this->innings_wides = 0;
			$this->innings_byes = 0;
			$this->innings_legbyes = 0;
			$this->partnership_runs = 0;
			$this->partnership_balls = 0;
			$this->partnerships = array();

			$this->striker_index = 0;
			$this->non_strike_index = 0;

			$this->batting_team_id = $this->teams[1];
			$this->batting_team_label = $this->CI->Team->getTeamName($this->teams[1]);

			$this->bowling_team_id = $this->teams[0];
			$this->bowling_team_label = $this->CI->Team->getTeamName($this->teams[0]);
			$this->currently_bowling_index = 0;
			$this->last_over_bowling_index = 1;
			$this->fielders = $this->CI->Center->getFielderNames($this->match_id, $this->teams[0]);			
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->bowling_team_id);

			$this->live_index = 0;
		}

		foreach($this->bowlers as $index=>$array)
		{
			$points = $array['rating_points'];
			$role = $array['player_type'];
			$this->balls_per_bowler = $this->game_mode == 1 ? 60 : 24;
			$this->generateDeliveries($index, $role, $points);	
		}

		$this->setOpeningBatsman();		
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
				$ball_result = "STOCK";
			}
			$this->bowlers[$this->currently_bowling_index]['last_ball_index'] = ((int) $this->bowlers[$this->currently_bowling_index]['last_ball_index'] + 1);

			$this->live[$this->innings][$this->live_index]['type_of_ball'] = $ball_result;
			
			if($ball_result === "WIDE" || $ball_result === "NOBALL")
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

				$this->partnership_balls += 1;
				$this->partnership_runs += 1;	

				$this->live[$this->innings][$this->live_index]['runs'] = 1;
				$this->live[$this->innings][$this->live_index]['outcome'] = $result;
			}
			else
			{	
				// is a legal delivery
				$this->bowlers[$this->currently_bowling_index]['legal_balls'] = ((int) $this->bowlers[$this->currently_bowling_index]['legal_balls'] + 1);
				$this->batsmen[$this->striker_index]['balls'] = (int) $this->batsmen[$this->striker_index]['balls'] + 1;
				$this->innings_balls_bowled = (int) $this->innings_balls_bowled + 1;
				$number_of_deliveries--;
				$this->partnership_balls += 1;

				$batsman_role = $this->striker['role'];
				$batsman_points = $this->batsmen[$this->striker_index]['bat'];
				$batsman_style = $this->batsmen[$this->striker_index]['mentality_id'];
				$bowler_role = $this->bowlers[$this->currently_bowling_index]['player_type'];
				$bowler_points = $this->bowlers[$this->currently_bowling_index]['rating_points'];

				if($ball_result === "WICKET")
				{
					$result = $this->wicketBallResult($bowler_role, $bowler_points, $batsman_points);
					if($result === "W")
					{
						$this->onWicketFall();						

						if($this->innings_wickets == 10)
						{
							$this->innings_completed = true;
							$this->addToCommentary($this->innings_balls_bowled, $result, $ball_result);							
							break;
						}
					}
					else
					{											
						$this->batsmen[$this->striker_index]['status'] = "NOTOUT";
					}
					$this->bowlers[$this->currently_bowling_index]['wicket_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['wicket_balls'] + 1;
				}
				else
				{
					$this->batsmen[$this->striker_index]['status'] = "NOTOUT";					

					if($ball_result === 'BAD')
					{
						$result = $this->badBallResult($batsman_points, $bowler_points, $batsman_role, $batsman_style);						
						$this->bowlers[$this->currently_bowling_index]['bad_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['bad_balls'] + 1;
					}
					else if($ball_result === 'GOOD')
					{
						$result = $this->goodBallResult($batsman_points, $bowler_points, $batsman_role, $batsman_style);						
						$this->bowlers[$this->currently_bowling_index]['good_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['good_balls'] + 1;
					}
					else if($ball_result === 'STOCK')
					{
						$result = $this->stockBallResult($batsman_points, $bowler_points, $batsman_role, $batsman_style);
						$this->bowlers[$this->currently_bowling_index]['stock_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['stock_balls'] + 1;
					}

					
					if($result === "UNDEFINED")
					{
						die("UNDEFINED ERROR: ".$ball_result." (".$result.")");
					}
					else if($result === "W")
					{
						$this->onWicketFall();						
						if($this->innings_wickets == 10)
						{
							$this->innings_completed = true;
							$this->addToCommentary($this->innings_balls_bowled, $result, $ball_result);							
							break;
						}						
					}
					else 
					{
						if($result == '4' || $result == '6')
						{
							if($result == '4')
							{
								$this->batsmen[$this->striker_index]['fours'] = ((int) $this->batsmen[$this->striker_index]['fours'] + 1);							
							}
							else if($result == '6')
							{							
								$this->batsmen[$this->striker_index]['sixes'] = ((int) $this->batsmen[$this->striker_index]['sixes'] + 1);							
							}
						}						

						$this->batsmen[$this->striker_index]['runs'] += $result;
						$this->bowlers[$this->currently_bowling_index]['runs'] += $result;
						$this->innings_total = ((int) $this->innings_total + (int) $result);
						$this->partnership_runs += (int) $result;
					}
					
				}				
				$check_for_over = true;

				$this->live[$this->innings][$this->live_index]['runs'] = ( ($result === "W" || $result === "NOTOUT") ? 0 : $result );
				$this->live[$this->innings][$this->live_index]['outcome'] = $result;
			}
			
			$this->live[$this->innings][$this->live_index]['batsman_id'] = $this->batsmen[$this->striker_index]['player_id'];
			$this->live[$this->innings][$this->live_index]['batsman_name'] = $this->batsmen[$this->striker_index]['name'];
			$this->live[$this->innings][$this->live_index]['bowler_id'] = $this->bowlers[$this->currently_bowling_index]['player_id'];
			$this->live[$this->innings][$this->live_index]['bowler_name'] = $this->bowlers[$this->currently_bowling_index]['name'];

			$this->addToCommentary($this->innings_balls_bowled, $result, $ball_result);

			// change strike if single or three taken and it's not end of over
			if($check_for_over)
			{
				if(($result == '1' || $result == '3') && $this->bowlers[$this->currently_bowling_index]['legal_balls'] % 6 != 0)
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
					// change strike if condition met (not out added because on last ball if wicket not taken, then too change strike)
					if($result == 'W' || $result == '0' || $result == '2' || $result == '4' || $result == '6' || $result == "NOT OUT")
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

			// checking if team batting second has achieved the target
			if($this->innings == 'second')
			{
				// match over
				if($this->win_score <= $this->innings_total)
				{
					$this->matchSummary("won");
					break;
				}
			}
			$this->live_index++;
		}

		// team did not get all out so get partnership details of last batting pair
		if($this->innings_wickets < 10)		
		{
			// partnership result
			$player1 = $this->non_striker;
			$player2 = $this->striker;
			$this->calculatePartnership($player1, $player2);
		}

		if($this->innings == "first")
		{
			$this->win_score = ($this->innings_total + 1);			
		}

		// match ended and it's a tie
		if($this->innings == 'second' && (($this->win_score - 1) == $this->innings_total))
		{
			$this->matchSummary("tie");
		}
		// match ended but team batting second didn't score the required runs
		else if($this->innings == 'second' && $this->win_score > $this->innings_total)
		{
			$this->matchSummary("lose");
		}
	}

	public function wicketBallResult($bowler_role, $bowler_points, $batsman_points)
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
		
		return $out[mt_rand(0, (count($out) - 1))];
	}

	public function goodBallResult($batsman_points, $bowler_points, $batsman_role, $batsman_style)
	{
		$result = "UNDEFINED";

		if($batsman_points >= $bowler_points)
		{
			if($batsman_style == '1')
			{	
				if($this->game_stage == 'PP1')
				{
					$possibility = array('1', '0', '1', '1', '2', '1', '0', '1', '2', '1');
				}	
				else if($this->game_stage == 'MO')
				{
					$possibility = array('1', '0', '1', '1', '1', '1', '1', '0', '1', '0');
				}
				else
				{
					$possibility = array('2', '1', '1', '0', '2', '1', '1', '2', '1', '2');
				}
			}
			else if($batsman_style == '2')
			{
				if($this->game_stage == 'PP1')
				{
					$possibility = array('1', '0', '1', '0', '1', '2', '0', '1', '1', '1');
				}
				else if($this->game_stage == 'MO')
				{
					$possibility = array('0', '1', '0', '1', '1', '0', '1', '1', '1', '0');
				}
				else
				{
					$possibility = array('1', '2', '1', '2', '1', '1', '0', '2', '1', '0');
				}
			}
			else
			{
				if($this->game_stage == 'PP1')
				{
					$possibility = array('0', '0', '1', '1', '0', '1', '0', '1', '1', '0');
				}
				else if($this->game_stage == 'MO')
				{
					$possibility = array('1', '1', '0', '1', '0', '0', '1', '0', '1', '0');
				}
				else
				{
					$possibility = array('1', '0', '1', '1', '1', '1', '0', '1', '1', '0');
				}
			}
		}
		else
		{
			if($batsman_style == '1')
			{	
				if($this->game_stage == 'PP1')
				{
					$possibility = array('1', '0', '0', '1', '0', '0', '1', '1', '1', '0');
				}	
				else if($this->game_stage == 'MO')
				{
					$possibility = array('0', '1', '0', '1', '0', '1', '0', '1', '0', '0');
				}
				else
				{
					$possibility = array('W', '0', '1', '1', '1', '2', '0', '2', '1', '0');
				}
			}
			else if($batsman_style == '2')
			{
				if($this->game_stage == 'PP1')
				{
					$possibility = array('1', '0', '0', '1', '0', '0', '0', '1', '1', '0');
				}
				else if($this->game_stage == 'MO')
				{
					$possibility = array('0', '1', '0', '1', '0', '1', '0', '1', '0', '1');
				}
				else
				{
					$possibility = array('1', '0', '0', '1', '1', '1', '0', '0', '1', '0');
				}
			}
			else
			{
				if($this->game_stage == 'PP1')
				{
					$possibility = array('0', '1', '1', '0', '0', '1', '0', '1', '0', '0');
				}
				else if($this->game_stage == 'MO')
				{
					$possibility = array('0', '1', '0', '1', '0', '1', '0', '1', '0', '1');
				}
				else
				{
					$possibility = array('0', 'W', '0', '1', '1', '2', '0', '1', '1', '0');
				}
			}				
		}

		shuffle($possibility);
		$result = array_pop($possibility);

		return $result;
	}

	public function badBallResult($batsman_points, $bowler_points, $batsman_role, $batsman_style)
	{
		$result = "UNDEFINED";
		if($batsman_points >= $bowler_points)
		{
			if($batsman_role == 1 || $batsman_role == 5 || $batsman_role == 3)
			{
				if($this->game_stage == 'PP1')
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('m');
					}
					else if($batsman_style == '2')
					{
						$result = $this->boundaryChance('m');
					}
					else
					{
						$result = $this->boundaryChance('l');
					}									
				}
				else if($this->game_stage == 'MO')
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('m');
					}
					else
					{
						$result = $this->boundaryChance('l');
					}
					
				}
				else
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('h');
					}
					else if($batsman_style == '2')
					{
						$result = $this->boundaryChance('m');
					}
					else
					{
						$result = $this->boundaryChance('l');
					}
				}
			}
			else if($batsman_role == 4)
			{
				if($this->game_stage == 'PP1')
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('l');
					}
					else
					{
						$result = $this->boundaryChance('l');
					}
				}
				else if($this->game_stage == 'MO')
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('m');
					}
					else
					{
						$result = $this->boundaryChance('l');	
					}					
				}
				else
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('m');
					}
					else
					{
						$result = $this->boundaryChance('l');
					}
				}
			}
			else
			{
				if($batsman_style == '1')
				{										
					$result = $this->boundaryChance('m');
				}
				else
				{
					$result = $this->boundaryChance('l');
				}
			}
		}
		else
		{
			if($batsman_role == 1 || $batsman_role == 5 || $batsman_role == 3)
			{
				if($this->game_stage == 'PP2')
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('m');
					}
					else if($batsman_style == '2')
					{
						$result = $this->boundaryChance('m');
					}
					else
					{
						$result = $this->boundaryChance('l');
					}
				}								
				else if($this->game_stage == 'PP1')
				{
					if($batsman_style == '1')
					{										
						$result = $this->boundaryChance('m');
					}									
					else
					{
						$result = $this->boundaryChance('l');
					}
				}
				else
				{
					$result = $this->boundaryChance('l');
				}
			}							
			else
			{
				$result = $this->boundaryChance('l');
			}
		}

		return $result;
	}

	public function stockBallResult($batsman_points, $bowler_points, $batsman_role, $batsman_style)
	{
		$result = "UNDEFINED";
		if($batsman_points >= $bowler_points)
		{
			if($batsman_role == 1 || $batsman_role == 5 || $batsman_role == 3)
			{
				if($this->game_stage == "PP1")
				{
					if($batsman_style == 1)
					{
						$result = $this->stockOutcome('two');
					}
					else if($batsman_style == 2)
					{
						$result = $this->stockOutcome('single');
					}
					else
					{
						$result = $this->stockOutcome('dot');
					}
				}
				else if($this->game_stage == "MO")
				{
					if($batsman_style == 1)
					{
						$result = $this->stockOutcome('single');
					}									
					else
					{
						$result = $this->stockOutcome('dot');
					}
				}
				else
				{
					if($batsman_style == 1)
					{
						$result = $this->stockOutcome('three');
					}
					else if($batsman_style == 2)
					{
						$result = $this->stockOutcome('two');
					}
					else
					{
						$result = $this->stockOutcome('dot');
					}
				}
			}							
			else
			{
				if($this->game_stage == "PP1")
				{
					if($batsman_style == 1)
					{
						$result = $this->stockOutcome('single');
					}									
					else
					{
						$result = $this->stockOutcome('dot');
					}
				}
				else if($this->game_stage == "MO")
				{
					$result = $this->stockOutcome('dot');									
				}
				else
				{
					if($batsman_style == 1)
					{
						$result = $this->stockOutcome('two');
					}
					else if($batsman_style == 2)
					{
						$result = $this->stockOutcome('single');
					}
					else
					{
						$result = $this->stockOutcome('dot');
					}
				}	
			}
		}
		else
		{
			if($batsman_role == 1 || $batsman_role == 5 || $batsman_role == 3)
			{
				if($this->game_stage == "PP2")
				{
					if($batsman_style == 1)
					{
						$result = $this->stockOutcome('single');
					}									
					else
					{
						$result = $this->stockOutcome('dot');
					}
				}
				else
				{
					$result = $this->stockOutcome('dot');
				}
			}
			else
			{
				$result = $this->stockOutcome('dot');
			}
		}

		return $result;
	}

	public function stockOutcome($outcome)
	{		
		if($outcome == 'three')
		{
			$possibility = array('1','0','4','1','4','3','1','1');
		}
		else if($outcome == 'two')
		{
			$possibility = array('0','2','3','1','4','1','1','0');
		}
		else if($outcome == 'single')
		{
			$possibility = array('1','0','1','1','0','1','0','1');
		}
		else
		{
			$possibility = array('0','1','0','1','1','1','1','0');
		}

		return $possibility[mt_rand(0, (count($possibility) - 1))];

	}

	public function onWicketFall()
	{
		// partnership result
		$player1 = $this->striker;
		$player2 = $this->non_striker;
		$this->calculatePartnership($player1, $player2);


		$this->out_how = $this->mode_of_dismissal[mt_rand(0, (count($this->mode_of_dismissal) - 1))];
		$this->innings_wickets = (int) $this->innings_wickets + 1;
		$this->innings_fow[] = $this->innings_wickets.'-'.$this->innings_total.' <strong>'.$this->batsmen[$this->striker_index]['name'].'</strong> ('.$this->ballsToOvers($this->innings_balls_bowled).')';

		$this->live[$this->innings][$this->live_index]['out_how'] = $this->out_how;
		
		if($this->out_how !== "Run Out")
		{
			$this->bowlers[$this->currently_bowling_index]['wickets'] = (int) $this->bowlers[$this->currently_bowling_index]['wickets'] + 1;	
		}
		else
		{
			$this->batsmen[$this->striker_index]['status'] = "Run out";
			$this->live[$this->innings][$this->live_index]['status'] = "Run out";
		}

		if($this->out_how == 'Bowled')
		{
			$this->batsmen[$this->striker_index]['status'] = "b. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
			$this->live[$this->innings][$this->live_index]['status'] = "b. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
		}
		else if($this->out_how == 'Hit Wicket')
		{
			$this->batsmen[$this->striker_index]['status'] = "hitwicket. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
			$this->live[$this->innings][$this->live_index]['status'] = "hitwicket. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
		}
		else if($this->out_how == 'LBW')
		{
			$this->batsmen[$this->striker_index]['status'] = "lbw ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
			$this->live[$this->innings][$this->live_index]['status'] = "lbw ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
		}
		else if($this->out_how == "Caught")
		{
			$caught_by = $this->getFielder();
			$this->batsmen[$this->striker_index]['status'] = "c. ".$this->shortName($caught_by)." b. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
			$this->live[$this->innings][$this->live_index]['status'] = "c. ".$this->shortName($caught_by)." b. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
		}
	}

	public function matchSummary($result)
	{
		// team batting secon won (in wickets)
		if($result == "won")
		{
			$this->match_result['team_id'] = $this->batting_team_id;
			$this->match_result['team_label'] = $this->batting_team_label;
			$this->match_result['margin'] = (10 - $this->innings_wickets).' wickets with '.($this->game_mode == 1 ? 300 - $this->innings_balls_bowled : 120 - $this->innings_balls_bowled).' balls to spare.';
			$this->match_result['is_tie'] = 'NO';
		}
		// team batting first won (in runs)
		else if($result == "lose")
		{
			$this->match_result['team_id'] = $this->bowling_team_id;
			$this->match_result['team_label'] = $this->bowling_team_label;
			$this->match_result['margin'] = ($this->win_score - $this->innings_total - 1).' runs.';
			$this->match_result['is_tie'] = 'NO';
		}
		// it's a tie
		else if($result == "tie")
		{
			$this->match_result['is_tie'] = 'YES';
			$this->match_result['margin'] = "The match has ended in a tie. There is nothing separating the two teams.";
		}
	}

	public function calculatePartnership($player1, $player2)
	{
		if($player1['batting_index'] > $player2['batting_index'])
		{
			$between = $this->shortName($player2['name']).'/'.$this->shortName($player1['name']);							
		}
		else
		{
			$between = $this->shortName($player1['name']).'/'.$this->shortName($player2['name']);
		}
		$this->partnerships[] = array('between'=>$between, 'runs'=>$this->partnership_runs, 'balls'=>$this->partnership_balls);
		$this->partnership_runs = 0;
		$this->partnership_balls = 0;
	}

	public function getFielder()
	{
		$wicket_taking_bowler = $this->bowlers[$this->currently_bowling_index]['name'];
		$picked_name = $this->fielders[mt_rand(0, (count($this->fielders) - 1))];
		while($picked_name == $wicket_taking_bowler){
			$picked_name = $this->fielders[mt_rand(0, (count($this->fielders) - 1))];
		}
		return $picked_name;
	}

	public function addToCommentary($balls_bowled, $result, $type_of_ball)
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

		// delivery description		
		if($result == '0' || $result == '1' || $result == '2' || $result == '3')
		{			
			if($result == '0')
			{
				$text = "no run, ".$this->dots[mt_rand(0, (count($this->dots) - 1))];
			}
			else if($result == '1')
			{
				$text = "1 run, ".$this->singles[mt_rand(0, (count($this->singles) - 1))];
			}
			else
			{
				$text = $result." runs, ";
				if($result == '2')
				{
					$text .= $this->twos[mt_rand(0, (count($this->twos) - 1))];
				}
				else if($result == '3')
				{
					$text .= $this->threes[mt_rand(0, (count($this->threes) - 1))];
				}
			}
		}
		else if($result == '4' || $result == '6')
		{
			if($result == '4')
			{
				$text = "<strong>FOUR</strong>, ";
				$text .= $this->fours[mt_rand(0, (count($this->fours) - 1))];
			}
			else if($result == '6')
			{
				$text = "<strong class='green'>SIX</strong>, ";
				$text .= $this->sixes[mt_rand(0, (count($this->sixes) - 1))];
			}
		}
		else if($result == 'W')
		{			
			$text = "<strong class='red'>WICKET</strong>";
		}
		else if($result === "WIDE" || $result === "NOBALL")
		{
			$text = $result.", ";
			if($result === "WIDE")
			{
				$text .= $this->wides[mt_rand(0, (count($this->wides) - 1))];
			}
			else
			{
				$text .= $this->noballs[mt_rand(0, (count($this->noballs) - 1))];
			}
		}
		else if($result === "NOTOUT")
		{
			$text = "no run, ";
			$text .= $this->notout[mt_rand(0, (count($this->notout) - 1))];
		}

		$comm = "<div class='clearfix comm_line'><div class='comm_over'><span class='over_number'>[".$this->innings_overs."]</span><span style='font-size:85%;' ng-show='data.debug'><br />".$type_of_ball."</span></div><div class='comm_desc'><em>".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']).'</em> to <em>'.$this->shortName($this->batsmen[$this->striker_index]['name']).'</em>, '.$text."</div></div>";

		$live_comm = "<div class='clearfix comm_line'><div class='comm_over'><span class='over_number'>[".$this->innings_overs."]</span></div><div class='comm_desc'><em>".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']).'</em> to <em>'.$this->shortName($this->batsmen[$this->striker_index]['name']).'</em>, '.$text."</div></div>";

		$this->innings_commentary[] = $comm;
		
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

			$overs = floor($balls_bowled/6);
			$crr = number_format($this->innings_total/$overs, 2);
			$overs_remaining = $this->game_mode == 1 ? (50 - $overs) : (20 - $overs);
			if($this->innings == 'second')
			{
				$no_of_runs_required = $this->win_score - $this->innings_total;				
				$rrr = " | To Win: ".$no_of_runs_required." runs | RRR: ".number_format($no_of_runs_required/($overs_remaining == 0 ? 1 : $overs_remaining), 2);
			}
			else
			{
				$projected = floor($crr*($this->game_mode == 1 ? 50 : 20));
				$rrr = " | Projected: ".$projected." runs";
			}

			$comm2 = "<div class='comm_end_of_over bold'>End of ".$this->ordinal($overs)." over</div><div class='bold comm_over_stat'><span class='over_stat1'>".$this->batting_team_label.": ".$this->innings_total.'/'.$this->innings_wickets.' | '.$runs.' runs | RR: '.$crr.$rrr.'</span><span class="over_stat2">'.$bowler_string.'</span></div>';
			$this->innings_commentary[] = $comm2;

			$comm .= $comm2;
			$live_comm .= $comm2;
		}

		$this->live[$this->innings][$this->live_index]['commentary'] = $live_comm;
	}

	public function shortName($str)
	{
		if(! $str)
		{
			return "";
		}

		$ex = explode(" ", $str);
		if(count($ex) == 1)
		{
			return $ex[0];
		}
		else
		{
			$fn = $ex[0];
			$ln = end($ex);
			return $fn[0].'.'.$ln;
		}		
	}

	public function boundaryChanceWithWicket($stronger)
	{
		if($stronger == 'bat') // batsman points is greater than bowler
		{
			$runs = array("1", "W", "4", "6", "2", "2", "1", "4", "1");
		}
		else
		{
			$runs = array("1", "W", "4", "6", "W", "2", "1", "4", "W");
		}		
		return $runs[array_rand($runs)];
	}

	public function boundaryChance($chance)
	{
		if(! $chance || ! in_array($chance, array('h', 'm', 'l')))
		{
			return mt_rand(0, 3);
		}

		if($chance == 'h')
		{
			if($this->game_mode == 1)
			{
				$runs = array('4','1','4','6','3','4','6','2');	
			}
			else
			{
				$runs = array('4','6','4','6','3','4','6','4');
			}
		}
		else if($chance == 'm')
		{
			if($this->game_mode == 1)
			{
				$runs = array('1','2','4','1','2','6','1','1');
			}
			else
			{
				$runs = array('6','2','4','1','2','6','2','1');
			}			
		}
		else if($chance == 'l')
		{
			if($this->game_mode == 1)
			{
				$runs = array('0','1','0','1','0','1','2','0','4','0','1','2');
			}
			else
			{
				$runs = array('2','2','0','4','0','1','2','0','4','0','1','2');
			}
			
		}
		return $runs[array_rand($runs)];
	}
	
	public function changeBowler()
	{		
		
		$bowling_options_number = count($this->bowlers);
		if($bowling_options_number > 5)
		{
			$this->part_timers_available = true;
			$this->part_timers_count = count($this->bowlers) - 5;
		}

		$overs_bowled = floor($this->innings_balls_bowled/6);
		$current_index = $this->currently_bowling_index;

		$per_bowler_allowed = $this->game_mode == 1 ? 60 : 24;
		$match_overs = $this->game_mode == 1 ? 50 : 20;

		// time to get a new pair of bowlers
		if($overs_bowled % ($this->change_bowler_after_overs*2) == 0)
		{
			if($this->change_bowler_after_overs == 3)
			{
				if($overs_bowled == 6)
				{
					$new_index = 2;
					$this->last_over_bowling_index = 3;
				}
				else if($overs_bowled == 12)
				{
					$new_index = 4;
					$this->last_over_bowling_index = 0;
				}
				else if($overs_bowled == 18)
				{
					if($this->game_mode == 1)
					{
						$new_index = 1;
					}
					else
					{
						$new_index = 4;
					}					
					$this->last_over_bowling_index = 2;
				}
				else if($overs_bowled == 24)
				{
					$new_index = 3;
					$this->last_over_bowling_index = 4;
				}
				else if($overs_bowled == 30)
				{
					$new_index = 0;
					$this->last_over_bowling_index = 1;
				}
				else if($overs_bowled == 36)
				{
					$new_index = 2;
					$this->last_over_bowling_index = 3;
				}
				else if($overs_bowled == 42)
				{
					$new_index = 4;
					$this->last_over_bowling_index = 0;
				}
				else if($overs_bowled == 48)
				{
					$new_index = 3;			
					$this->last_over_bowling_index = 4;
				}
			}
			else if($this->change_bowler_after_overs == 5)
			{
				if($overs_bowled == 10)
				{
					$new_index = 2;
					$this->last_over_bowling_index = 3;
				}
				else if($overs_bowled == 20)
				{
					$new_index = 4;
					$this->last_over_bowling_index = 0;
				}
				else if($overs_bowled == 30)
				{
					$new_index = 1;
					$this->last_over_bowling_index = 2;
				}
				else if($overs_bowled == 40)
				{
					$new_index = 3;
					$this->last_over_bowling_index = 4;
				}
				else if($overs_bowled == 50)
				{
					$new_index = 0;
					$this->last_over_bowling_index = 1;
				}
			}
		}
		else
		{
			if($this->last_over_bowling_index === null)
			{
				$new_index = 1;
			}
			else
			{
				$new_index = $this->last_over_bowling_index;	
			}

			$next_bolwer_balls_bowled = $this->bowlers[$new_index]['legal_balls'];			
			while($next_bolwer_balls_bowled == $per_bowler_allowed && $overs_bowled < $match_overs)
			{				
				$new_index = $new_index + 1;				
				$next_bolwer_balls_bowled = $this->bowlers[$new_index]['legal_balls'];
			}

			$this->last_over_bowling_index = $current_index;
		}

		
		$this->currently_bowling_index = $new_index;
		
		/*
		$new_index = (int) $current_index + 1;

		if($new_index <= 4)
		{			
			$this->currently_bowling_index = $new_index;
		}
		else
		{	
			$this->currently_bowling_index = 0;
		}		
		*/
		

		//echo '<p>End of over '.$overs_bowled.' (Last Bowler: '.$current_index.' | New Bolwer: '.$new_index.'</p>';
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
		
		if($this->game_mode == 1)
		{
			$balls_bank = range(1, 60);
		}
		else
		{
			$balls_bank = range(1, 24);
		}
		
		
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
				$deliveries[$i] = "STOCK";
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
		if($points >=0 && $points < 50)
		{

			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(0, 3);
				}
				else
				{
					$percent = mt_rand(3, 4);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(2, 7);
				}
				else
				{
					$percent = mt_rand(0, 5);
				}
			}
		}
		else if($points >= 50 && $points < 75)
		{

			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(4, 6);
				}
				else
				{
					$percent = mt_rand(3, 4);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(10, 15);
				}
				else
				{
					$percent = mt_rand(2, 5);
				}
			}
			
		}
		else if($points >= 75 && $points < 90)
		{
			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(5, 8);
				}
				else
				{
					$percent = mt_rand(4, 6);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(15, 20);
				}
				else
				{
					$percent = mt_rand(7, 10);
				}
			}
			
		}
		else
		{
			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(6, 10);
				}
				else
				{
					$percent = mt_rand(5, 8);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(20, 25);
				}
				else
				{
					$percent = mt_rand(10, 15);
				}
			}
			
		}

		return ($this->game_mode == 1 ? ceil($percent*60/100) : floor($percent*24/100));
	}

	public function extraDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 50)
		{
			if($role == '2')
			{
				$balls = 6;
			}
			else
			{
				$balls = 7;
			}			
		}
		else if($points >= 50 && $points < 75)
		{
			if($role == '2')
			{
				$balls = 4;
			}
			else
			{
				$balls = 5;
			}
		}
		else if($points >= 75 && $points < 90)
		{
			if($role == '2')
			{
				$balls = mt_rand(0, 3);
			}
			else
			{
				$balls = mt_rand(0, 4);
			}
		}
		else
		{
			if($role == '2')
			{
				$balls = mt_rand(0, 1);
			}			
			else
			{
				$balls = mt_rand(0, 2);
			}
		}

		return $balls;
	}

	public function badDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 50)
		{
			if($role == '2')
			{
				$percent = mt_rand(30, 40);
			}
			else
			{
				$percent = mt_rand(35, 45);
			}			
		}
		else if($points >= 50 && $points < 75)
		{
			if($role == '2')
			{
				$percent = mt_rand(20, 30);
			}
			else
			{
				$percent = mt_rand(25, 35);
			}
		}
		else if($points >= 75 && $points < 90)
		{
			if($role == '2')
			{
				$percent = mt_rand(10, 20);
			}
			else
			{
				$percent = mt_rand(15, 25);
			}
		}
		else
		{
			if($role == '2')
			{
				$percent = mt_rand(0, 10);
			}			
			else
			{
				$percent = mt_rand(5, 15);
			}
		}

		return ($this->game_mode == 1 ? ceil($percent*60/100) : ceil($percent*24/100));	
	}


	public function goodDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 50)
		{
			if($role == '2')
			{
				$percent = mt_rand(0, 5);
			}
			else
			{
				$percent = 0;
			}			
		}
		else if($points >= 50 && $points < 75)
		{
			if($role == '2')
			{
				$percent = mt_rand(5, 15);
			}
			else
			{
				$percent = mt_rand(0, 10);
			}
		}
		else if($points >= 75 && $points < 90)
		{
			if($role == '2')
			{
				$percent = mt_rand(15, 25);
			}
			else
			{
				$percent = mt_rand(10, 20);
			}
		}
		else
		{
			if($role == '2')
			{
				$percent = mt_rand(25, 35);
			}			
			else
			{
				$percent = mt_rand(20, 30);
			}
		}

		return ($this->game_mode == 1 ? floor($percent*60/100) : floor($percent*24/100));
	}

	public function ordinal($number) {
	    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	    if ((($number % 100) >= 11) && (($number%100) <= 13))
	        return $number. 'th';
	    else
	        return $number. $ends[$number % 10];
	}
}