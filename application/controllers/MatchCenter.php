<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class MatchCenter extends CI_Controller {
	
	public function __construct()
	{
        parent::__construct();
    	if(! isset($this->session->logged_user))
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be logged in to view that page.'));
			redirect("Login");
		}
		$this->load->model("Center");
		$this->load->model("Team");
    }


    public function comment()
    {
    	$lines = array();
	

		foreach($lines as $line)
		{
			$this->db->insert('commentary', $line);

		}
	}

	public function SelectTeams()
	{
		$tour_id = (int) $this->uri->segment(4);
		$data['status'] = 'NOTOK';

		if($tour_id == 0)
		{
			$data['msg'] = 'Invalid tournament. Please go back and try again.';
		}
		else
		{
			if($this->Center->isTournamentOwner($tour_id))
			{
				$data['status'] = 'OK';
				$data['teams'] = $this->Team->getMyTeams();
			}
			else
			{
				$data['msg'] = 'You do not have permissions to view/edit this tournament. The tournament ID is '.$tour_id;
			}
		}

		$this->load->view('templates/logged_in', array('page'=>'select_teams', 'data'=>$data));
	}
	
	public function setTournamentName()
	{
		$data = array('status'=>'NOTOK');

		$post = json_decode(file_get_contents("php://input"));
		$errors = array();

		if(! isset($post->t_name) || trim($post->t_name) == '')
		{
			$errors[] = "Tournament Name is required.";
		}

		if(! isset($post->t_not) || (int) $post->t_not == 0)
		{
			$errors[] = "Number of Teams is required.";
		}

		if(! isset($post->t_type) || ! in_array($post->t_type, array('ODI', 'T20', 'CUSTOM')))
		{
			$errors[] = 'Tournament Type is required.';
		}

		if(! isset($post->t_noo) || (int) $post->t_noo == 0)
		{
			$errors[] = 'Number of overs cannot be empty.';
		}

		if(count($errors) == 0)
		{
			if($this->Center->isValidTournamentName($post->t_name))
			{
				if($post->t_noo < 25)
				{
					$pp = (30*( (int) $post->t_noo)/100);
					$do = (75*( (int) $post->t_noo)/100);
				}
				else
				{
					$pp = (20*( (int) $post->t_noo)/100);
					$do = (80*( (int) $post->t_noo)/100);
				}

				date_default_timezone_set("UTC");
				$created = date("Y-m-d H:i:s");

				$insert = array(
					'tournament_name'	=>	$post->t_name,
					'number_of_teams'	=>	$post->t_not,
					'tournament_type'	=>	$post->t_type,
					'number_of_overs'	=>	$post->t_noo,
					'powerplay'			=>	$pp,
					'death'				=>	$do,
					'owner'				=>	$this->session->logged_user,
					'status'			=>	1,
					'created_on'		=> $created,
					'updated_on'		=> $created
				);

				if($this->db->insert('tournament', $insert))
				{
					$data['status'] = 'OK';
					$data['id']	= $this->db->insert_id();
				}
				else
				{
					$data['msg'] = $this->db->error()['message'];					
				}
			}
			else
			{
				$data['msg'] = 'A tournament with the same name already exists.';
			}
		}
		else
		{
			$data['msg'] = implode("\n", $errors);
		}
		echo json_encode($data);
		exit;
	}


	public function index()
	{		
		$this->load->view('templates/logged_in', array('page'=>'match_center'));
	}

	public function Single()
	{
		$pending_matches = $this->Center->getPendingMatches();
		$this->load->view('templates/logged_in', array('page'=>'single_match', 'pending'=>$pending_matches));
	}

	public function Tournament()
	{
		$this->load->view('templates/logged_in', array('page'=>'tournament_home'));
	}

	public function deleteMatch()
	{
		$post = json_decode(file_get_contents("php://input"));
		$mid = (int) $post->mid;
		if($mid > 0)
		{
			if($this->Center->hasMatchPermission($mid))
			{
				$this->db->trans_begin();
				$this->db->delete('match_players', array('mid' => $mid));
				$this->db->delete('match_center', array('match_id'=> $mid));
				if ($this->db->trans_status() === FALSE)
				{
					$data['msg'] = $this->db->error()['message'];
			        $this->db->trans_rollback();
			        $data['status'] = 'NOTOK';					
				}
				else
				{
			        $this->db->trans_commit();
			        $data['status'] = 'OK';
				}
				
			}
			else
			{
				$data['status'] = 'NOTOK';
				$data['msg'] = 'You do not have permissions to delete this match.';
			}
		}
		else
		{
			$data['status'] = 'NOTOK';
			$data['msg'] = 'Match not found.';
		}
		echo json_encode($data);
	}
	
	public function PreMatch($mid = null)
	{		
		$data = array();
		if((int) $mid == 0)
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "Invalid match. Please go back and try again.";			
		}
		else
		{
			if($this->Center->hasMatchPermission($mid))
			{
				if($this->Center->hasMatchFinished($mid))
				{
					$data['status'] = "NOTOK";
					$data['msg'] = "This match has already been simulated. Please choose a different match.";
				}
				else
				{
					$data['status'] = "OK";
					$data['msg'] = "";
					
					$extra = $this->Center->getMatchDetails($mid);
					$data['home'] = $extra['home'];
					$data['away'] = $extra['away'];
					$data['home_label'] = $extra['home_label'];
					$data['away_label'] = $extra['away_label'];
					$data['ground'] = $extra['ground'];
					$data['pitch'] = $extra['pitch'];
					$data['overs'] = $extra['overs'];
					$data['stage'] = $extra['stage'];
					$data['decision'] = $extra['decision'];
					$data['toss_won_by'] = $extra['won_by'];					
					$data['match'] = $mid;
				}				
			}
			else
			{
				$data['status'] = "NOTOK";
				$data['msg'] = "You do not have permissions to simulate this match.";
			}			
		}
		$this->load->view('templates/logged_in', array('page'=>'prematch', 'data'=>$data));
	}

	public function Live()
	{
		$qs = $this->uri->uri_to_assoc(3, array('mid', 'delay'));
		
		$mid = (int) $qs['mid'];
		$delay = (int) $qs['delay'];

		$data['home_label'] = "TBA";
		$data['away_label'] = "TBA";
		if((int) $mid == 0)
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "Invalid match. Please go back and try again.";			
		}
		else if($this->Center->hasMatchFinished($mid))
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "The match has already been simulated. Please choose a different match.";
		}
		else
		{			
			if($this->Center->hasMatchPermission($mid))
			{
				$match_details = $this->Center->getMatchDetails($mid);
				$data['mid'] = $match_details['mid'];
				$data['home_label'] = $match_details['home_label'];
				$data['away_label'] = $match_details['away_label'];
				if((int) $match_details['toss'] > 0 && trim($match_details['decision'] != ''))
				{
					$data['match_length'] = $match_details['overs'];
					$data['ground'] = $match_details['ground'];
					

					$data['mid'] = $mid;
					$data['delay'] = $delay;

					$data['status'] = "OK";					
				}
				else
				{
					$data['status'] = "NOTOK";
					$data['msg'] = "Toss has not been completed for this match. Click <strong><a href='../PreMatch/".$mid."' style='text-decoration:underline;'>here</a></strong> to do that.";
				}
				
			}
			else
			{
				$data['status'] = "NOTOK";
				$data['msg'] = "You do not have permissions to simulate this match.";
			}
		}
		$this->load->view('templates/logged_in', array('page'=>'live', 'data'=>$data));
	}

	public function liveMatch()
	{
		$post = json_decode(file_get_contents("php://input"));

		if((int) $post->mid == 0)
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "Invalid match. Please go back and try again.";			
		}
		else if($this->Center->hasMatchFinished($post->mid))
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "The match has already been simulated. Please choose a different match.";
		}
		else
		{			
			if($this->Center->hasMatchPermission($post->mid))
			{
				$match_details = $this->Center->getMatchDetails($post->mid);
				if((int) $match_details['toss'] > 0 && trim($match_details['decision'] != ''))
				{
					$this->load->library("Match");
					$match = new Match();
					$match->stream = ($post->stream == 'y' ? true : false);
					$match->setMatchId($post->mid);
					
					$match->startInnings("first");
					$data['first_batsmen'] = $match->batsmen;
					$data['first_bowlers'] = $match->bowlers;
					$data['first_total'] = $match->innings_total;
					$data['first_wickets'] = $match->innings_wickets;
					$data['first_overs'] = $this->roundOvers($match->ballsToOvers($match->innings_balls_bowled));
					$data['first_run_rate'] = number_format($match->innings_total*6/$match->innings_balls_bowled, 2);
					$data['first_commentary'] = $match->innings_commentary;
					$data['first_fow'] = $match->innings_fow;
					$data['first_batting_label'] = $match->batting_team_label;
					$data['first_bowling_label'] = $match->bowling_team_label;
					$data['first_partnerships'] = $match->partnerships;
					$data['first_max_partnership'] = max( array_map(function( $row ){ return $row['runs']; }, $match->partnerships) );
					$data['to_win'] = $match->win_score;				
					$data['in_overs'] = $match_details['overs'];
					$data['rrr'] = number_format(($data['to_win']/($data['in_overs']*6))*6, 2);


					$match->startInnings("second");
					$data['second_batsmen'] = $match->batsmen;
					$data['second_bowlers'] = $match->bowlers;
					$data['second_total'] = $match->innings_total;
					$data['second_wickets'] = $match->innings_wickets;
					$data['second_overs'] = $this->roundOvers($match->ballsToOvers($match->innings_balls_bowled));
					$data['second_run_rate'] = number_format($match->innings_total*6/$match->innings_balls_bowled, 2);
					$data['second_commentary'] = $match->innings_commentary;
					$data['second_fow'] = $match->innings_fow;
					$data['second_batting_label'] = $match->batting_team_label;
					$data['second_bowling_label'] = $match->bowling_team_label;
					$data['second_partnerships'] = $match->partnerships;
					$data['second_max_partnership'] = max( array_map(function( $row ){ return $row['runs']; }, $match->partnerships) );

					$data['result'] = $match->match_result;

					$data['live'] = $match->live;
					$data['status'] = 'OK';
				}
				else
				{
					$data['status'] = "NOTOK";
					$data['msg'] = "Toss has not been completed for this match. Click <strong><a href='../PreMatch/".$mid."' style='text-decoration:underline;'>here</a></strong> to do that.";
				}
				
			}
			else
			{
				$data['status'] = "NOTOK";
				$data['msg'] = "You do not have permissions to simulate this match.";
			}
		}

		

		echo json_encode($data);
	}

	public function BeginMatch($mid = null)
	{
		$data['home_label'] = "TBA";
		$data['away_label'] = "TBA";
		if((int) $mid == 0)
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "Invalid match. Please go back and try again.";			
		}
		else if($this->Center->hasMatchFinished($mid))
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "The match has already been simulated. Please choose a different match.";
		}
		else
		{			
			if($this->Center->hasMatchPermission($mid))
			{
				$match_details = $this->Center->getMatchDetails($mid);
				$data['mid'] = $match_details['mid'];
				$data['home_label'] = $match_details['home_label'];
				$data['away_label'] = $match_details['away_label'];
				if((int) $match_details['toss'] > 0 && trim($match_details['decision'] != ''))
				{
					$data['match_length'] = $match_details['overs'];
					$data['ground'] = $match_details['ground'];
					

					$data['status'] = "OK";
					$this->load->library("Match");
					$match = new Match();
					$match->setMatchId($mid);
					
					$match->startInnings("first");
					$data['first_batsmen'] = $match->batsmen;
					$data['first_bowlers'] = $match->bowlers;
					$data['first_total'] = $match->innings_total;
					$data['first_wickets'] = $match->innings_wickets;
					$data['first_overs'] = $this->roundOvers($match->ballsToOvers($match->innings_balls_bowled));
					$data['first_run_rate'] = number_format($match->innings_total*6/$match->innings_balls_bowled, 2);
					$data['first_commentary'] = $match->innings_commentary;
					$data['first_fow'] = $match->innings_fow;
					$data['first_batting_label'] = $match->batting_team_label;
					$data['first_bowling_label'] = $match->bowling_team_label;
					$data['first_partnerships'] = $match->partnerships;
					$data['first_max_partnership'] = max( array_map(function( $row ){ return $row['runs']; }, $match->partnerships) );
					$data['to_win'] = $match->win_score;				
					$data['in_overs'] = $match_details['overs'];
					$data['rrr'] = number_format(($data['to_win']/($data['in_overs']*6))*6, 2);


					$match->startInnings("second");
					$data['second_batsmen'] = $match->batsmen;
					$data['second_bowlers'] = $match->bowlers;
					$data['second_total'] = $match->innings_total;
					$data['second_wickets'] = $match->innings_wickets;
					$data['second_overs'] = $this->roundOvers($match->ballsToOvers($match->innings_balls_bowled));
					$data['second_run_rate'] = number_format($match->innings_total*6/$match->innings_balls_bowled, 2);
					$data['second_commentary'] = $match->innings_commentary;
					$data['second_fow'] = $match->innings_fow;
					$data['second_batting_label'] = $match->batting_team_label;
					$data['second_bowling_label'] = $match->bowling_team_label;
					$data['second_partnerships'] = $match->partnerships;
					$data['second_max_partnership'] = max( array_map(function( $row ){ return $row['runs']; }, $match->partnerships) );

					$data['result'] = $match->match_result;
					
				}
				else
				{
					$data['status'] = "NOTOK";
					$data['msg'] = "Toss has not been completed for this match. Click <strong><a href='../PreMatch/".$mid."' style='text-decoration:underline;'>here</a></strong> to do that.";
				}
				
			}
			else
			{
				$data['status'] = "NOTOK";
				$data['msg'] = "You do not have permissions to simulate this match.";
			}
		}
		$this->load->view('templates/logged_in', array('page'=>'begin_match', 'data'=>$data));
	}

	public function getPlayerRatingsForEditPurpose()
	{
		$post = json_decode(file_get_contents("php://input"));
		$mid = (int) $post->mid;
		if($this->Center->hasMatchPermission($mid))
		{
			$teams = $this->Center->getTeam1Team2($mid);
			if(count($teams) == 2)
			{
				$home_team_id = $teams[0];
				$away_team_id = $teams[1];
				$home_team_label = $this->Team->getTeamName($home_team_id);
				$away_team_label = $this->Team->getTeamName($away_team_id);


				// home team
				$this->db->select('p.player_id, CONCAT(p.first_name," ",p.last_name) AS full_name, p.batting_rp, p.bowling_rp, p.mentality');
				$this->db->from('players p');
				$this->db->join('match_players mp', 'mp.pid = p.player_id', 'left');
				$this->db->where('mp.mid',$mid);
				$this->db->where('mp.tid',$home_team_id);
				$this->db->order_by('pos', 'ASC');
				$q = $this->db->get();
				$players = array();
				if($q)
				{
					if($q->num_rows() > 0)
					{
						foreach($q->result() as $r)
						{
							$players['home']['players'][] = array('id'=>$r->player_id, 'name'=>$r->full_name, 'bat'=>$r->batting_rp, 'ball'=>$r->bowling_rp, 'ment'=>$r->mentality);
						}
						$players['home']['label'] = $home_team_label;
					}


					// away team
					$this->db->select('p.player_id, CONCAT(p.first_name," ",p.last_name) AS full_name, p.batting_rp, p.bowling_rp, p.mentality');
					$this->db->from('players p');
					$this->db->join('match_players mp', 'mp.pid = p.player_id', 'left');
					$this->db->where('mp.mid',$mid);
					$this->db->where('mp.tid',$away_team_id);
					$this->db->order_by('pos', 'ASC');
					$q = $this->db->get();				
					if($q)
					{
						if($q->num_rows() > 0)
						{
							foreach($q->result() as $r)
							{
								$players['away']['players'][] = array('id'=>$r->player_id, 'name'=>$r->full_name, 'bat'=>$r->batting_rp, 'ball'=>$r->bowling_rp, 'ment'=>$r->mentality);
							}
							$players['away']['label'] = $away_team_label;
						}
						$data['status'] = 'OK';
						$data['players'] = $players;
					}
					else
					{
						$data['status'] = 'NOTOK';
						$data['msg'] = $this->db->error()['message'];
					}
				}
				else
				{
					$data['status'] = 'NOTOK';
					$data['msg'] = $this->db->error()['message'];
				}
			}
			else
			{
				$data['status'] = 'NOTOK';
				$data['msg'] = "Competing teams could not be fetched.";
			}
		}
		else
		{
			$data['status'] = 'NOTOK';
			$data['msg'] = "You do not have permission to edit/modify/view this match.";
		}
		echo json_encode($data);
		exit;
	}

	public function updatePlayerRatings()
	{
		$post = json_decode(file_get_contents("php://input"));
		
		if(count(get_object_vars($post)) == 22)
		{
			$error = array();
			$players_updated = 0;
			date_default_timezone_set("UTC");
			foreach($post as $pid => $meta)
			{
				$pid = (int) $pid;
				$bat = (int) $meta->bat;
				$ball = (int) $meta->bowl;
				$ment = (int) $meta->ment;
				$this->load->model("Utils");

				if($this->Utils->hasPlayerPermission($pid) == "YES")
				{
					$update = array(
						'batting_rp'	=>	$bat,
						'bowling_rp'	=>	$ball,
						'mentality'		=>	$ment,
						'updated_time'	=>	date("Y-m-d H:i:s")
					);
					
					$this->db->where('player_id', $pid);					
					if($this->db->update('players', $update))
					{
						$players_updated++;
					}
					else
					{						
						$error[] = array('name'=>$meta->name, 'reason'=>$this->db->error()['message']);
					}					
				}
				else
				{
					$error[] = array('name'=>$meta->name, 'reason'=>'Permission Denied');
				}
			}
			if(count($error) > 0)
			{
				$data['status'] = 'ERROR';
				$data['error'] = $error;
			}
			else
			{
				$data['status'] = 'OK';
			}
		}
		else
		{
			$data['status'] = 'NOTOK';
			$data['msg'] = 'Attempted to update 22 players but found '.count(get_object_vars($post)).' players';
		}

		echo json_encode($data);
		exit;
	}

	public function roundOvers($str)
	{
		if(! $str)
			return $str;

		list($over, $balls) = explode('.', $str);
		if($balls == 6)
		{
			return ($over + 1).'.0';
		}
		else
		{
			return $str;
		}
	}

	public function pre($data)
	{
		echo '<pre>';
		print_r($data);
		echo '<pre>';
	}

	public function finalizeMatchSettings()
	{
		$post = json_decode(file_get_contents("php://input"));
		$mid = (int) $post->match_id;

		if($mid == 0)
		{			
			$data['status'] = "NOTOK";
			$data['msg'] = "Invalid match ID.";			
		}
		else if(! $this->Center->hasMatchPermission($mid))
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "You do not have permissions to simulate this match.";
		}
		else if($this->Center->hasMatchFinished($mid))
		{
			$data['status'] = "NOTOK";
			$data['msg'] = "The match has already been simulated. Please choose a different match.";
		}
		else
		{
			if(count($post->home_order) > 0 && count($post->away_order) > 0)
			{
				
				if(count(get_object_vars($post->home_order)) >= 5 && count(get_object_vars($post->away_order)) >= 5)
				{
					$this->db->trans_begin();
				
					$this->db->where('match_id', $mid);
					$this->db->update("match_center", array('toss'=>$post->toss_win_id, 'decision'=>$post->toss_decision, 'stage'=>2));
					
					foreach($post->home_order as $pid => $order)
					{
						$this->db->where(array('mid'=>$mid, 'pid'=>$pid));
						$this->db->update('match_players', array('bowl_pos'=>$order));
					}

					foreach($post->away_order as $pid => $order)
					{
						$this->db->where(array('mid'=>$mid, 'pid'=>$pid));
						$this->db->update('match_players', array('bowl_pos'=>$order));
					}

					if ($this->db->trans_status() === FALSE)
					{
				        $this->db->trans_rollback();
				        $data['status'] = 'NOTOK';
				        $data['msg'] = "Bowling order could not be saved. Transaction failed.";		        
					}
					else
					{
				        $this->db->trans_commit();
				        $data['status'] = "OK";
						$data['match_id'] = $mid;
					}
				}
				else
				{
					$data['status'] = "NOTOK";
					$data['msg'] = "Each team should have a minimum of 5 players who can bowl. Current numbers are: ".count(get_object_vars($post->home_order))." and ".count(get_object_vars($post->away_order));
				}				
				
			}
			else
			{
				$data['status'] = "NOTOK";
				$data['msg'] = "Either the home or away bowling order is not set.";
			}
		}
		echo json_encode($data);
		exit;
	}
	
	public function getCompetingTeamPlayers()
	{
		$post = json_decode(file_get_contents("php://input"));
		
		if($post->home == $post->away)
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>"Home and Away team cannot be same."));
			exit;
		}
		else
		{
			if($this->Team->hasTeamPermission($post->home) && $this->Team->hasTeamPermission($post->away))
			{			
				$teams = array();
				$teams[0] = (int) $post->home;
				$teams[1] = (int) $post->away;
				$data = $this->Center->getMatchTeamPlayers($teams);
				echo json_encode(array('status'=>'OK', 'db'=>$data));
				exit;
			}
			else
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>"You do not have permissions to access either one or both the teams."));
				exit;
			}
		}
	}
	
	public function initializeData()
	{
		$match_types = $this->Center->getMatchTypes();
		$pitch_types = $this->Center->getPitchTypes();
		$teams = $this->Center->fetchTeams();
		echo json_encode(array('teams'=>$teams, 'match_types'=>$match_types, 'pitch_types'=>$pitch_types));
	}
	
	public function setMatch()
	{
		$post = json_decode(file_get_contents("php://input"));
		$user = (int) $this->session->logged_user;
		if($user == 0)
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Your session has expired. Please log out and log back in.'));
			exit;
		}
		


		// check if there are at least 5 bowlers
		$bowler_count = (int) $this->countBowlersInTeam($post->home_eleven);
		if($bowler_count < 5)	
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Home team needs to have at least 5 bowlers. Currently, there are only '.$bowler_count.' players who can bowl.'));
			exit;
		}
		$bowler_count = (int) $this->countBowlersInTeam($post->away_eleven);
		if($bowler_count < 5)	
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Away team needs to have at least 5 bowlers. Currently, there are only '.$bowler_count.' players who can bowl.'));
			exit;
		}

		// check if exactly 11 players have been selected
		if(count($post->home_eleven) != 11 || count($post->away_eleven) != 11)
		{
			if(count($post->home_eleven) != 11)
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'Home team needs to have exactly 11 players.'));
				exit;
			}
			
			if(count($post->away_eleven) != 11)
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'Away team needs to have exactly 11 players.'));
				exit;
			}
		}
		
		
		date_default_timezone_set("UTC");
		$data = array(
			'match_type'	=>	$post->m_type->Id,
			'ground'		=>	$post->ground,
			'pitch'			=>	$post->p_type->Id,
			'overs'			=>	($post->m_type->Id == 1 ? 50 : 20),
			'home'			=>	$post->home,
			'away'			=>	$post->away,
			'owner'			=>	$user,
			'status'		=> 	1,
			'stage'			=>	1,
			'created_on'	=>	date("Y-m-d H:i:s")
		);
		
		$this->db->trans_begin();
		$this->db->insert('match_center', $data);
		$match_id = $this->db->insert_id();
		
		foreach($post->home_eleven as $index=>$player)
		{
			$pos = (int) ($index + 1);
			$home = array(
				'mid'			=>	$match_id,
				'pid'			=>	$player->pid,
				'tid'			=>	$post->home,
				'team'			=>	'home',
				'pos'			=>	$pos,
				'is_captain'	=>	0,
				'can_bowl'		=>	( ($player->icon == 'ball.png' || $player->icon == 'allrounder.png') ? 1 : 0 ),
				'is_keeper'		=>	($player->icon == 'keeper.png' ? 1 : 0)
			);
			
			$this->db->insert('match_players', $home);
		}
		
		foreach($post->away_eleven as $index=>$player)
		{
			$pos = (int) ($index + 1);
			$away = array(
				'mid'			=>	$match_id,
				'pid'			=>	$player->pid,
				'tid'			=>	$post->away,
				'team'			=>	'away',
				'pos'			=>	$pos,
				'is_captain'	=>	0,
				'can_bowl'		=>	( ($player->icon == 'ball.png' || $player->icon == 'allrounder.png') ? 1 : 0 ),
				'is_keeper'		=>	($player->icon == 'keeper.png' ? 1 : 0)
			);
			
			$this->db->insert('match_players', $away);
		}
		
		
		if ($this->db->trans_status() === FALSE)
		{
	        $this->db->trans_rollback();
	        echo json_encode(array('status'=>'NOTOK', 'msg'=>'Transaction failed'));
			exit;
		}
		else
		{
	        $this->db->trans_commit();
	        echo json_encode(array('status'=>'OK', 'match_id'=>$match_id));
			exit;
		}
	}

	public function countBowlersInTeam($array)
	{
		$cnt = 0;
		foreach($array as $index=>$player)
		{
			if(in_array($player->role_id,array('2', '3', '4')))
			{
				$cnt++;
			}
		}
		return $cnt;
	}
	
	public function coinToss()
	{	
		$post = json_decode(file_get_contents("php://input"));		
		if($this->Center->hasMatchPermission($post->match_id))
		{
			$toss = array($post->home_label, $post->away_label,$post->home_label, $post->away_label,$post->home_label, $post->away_label,$post->home_label, $post->away_label);
			$toss_won_by  = $toss[array_rand($toss)];
			$decisions = array("Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl");
			$decided_to = $decisions[array_rand($decisions)];
			
			$keypair = array();
			$keypair[$post->home_label] = $post->home;
			$keypair[$post->away_label] = $post->away;
			
			$home_bowlers = $this->Center->getBowlers($post->match_id, $post->home, 'home');
			$away_bowlers = $this->Center->getBowlers($post->match_id, $post->away, 'away');
			
			echo json_encode(array('status'=>'OK', 'toss'=>$toss_won_by, 'decided_to'=>$decided_to, 'toss_won_id'=>$keypair[$toss_won_by], 'home_bowlers'=>$home_bowlers, 'away_bowlers'=>$away_bowlers));
			exit;
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'You do not have permissions to simulate this match.'));
			exit;
		}		
	}
}