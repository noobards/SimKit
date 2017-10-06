<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Center extends CI_Model {
	public function fetchTeams()
	{
		$this->db->select("team_id, team_name, logo");
		$this->db->from("teams");
		$this->db->where("owner", $this->session->logged_user);
		$query = $this->db->get();
		$teams = array();
		if($query->num_rows() > 0)
		{
			$this->load->model("Team");
			foreach($query->result() as $row)
			{
				$nop = $this->getPlayerCount($row->team_id);
				if(trim($row->logo) != '')
				{
					$file = FCPATH.'assets'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'user_'.$this->session->logged_user.DIRECTORY_SEPARATOR.'teams'.DIRECTORY_SEPARATOR.$row->logo;					
					if(file_exists($file))
					{						
						$file = base_url().'assets/images/uploads/user_'.$this->session->logged_user.'/teams/'.$row->logo;
					}
					else
					{
						$file = base_url().'assets/images/no_team_logo.png';
					}
				}
				else
				{
					$file = base_url().'assets/images/no_team_logo.png';
				}

				$rating = $this->Team->getTeamRating($row->team_id);
				$teams[] = array(
								'team_id'		=> $row->team_id,
								'team_name'		=> $row->team_name,
								'player_count'	=> $nop,
								'logo'			=> $file,
								'rating'		=> $rating
							);
			}
		}
		return $teams;
	}
	
	public function getPlayerCount($tid)
	{
		$this->db->select("player_id");
		$this->db->from("squads");
		$this->db->where("team_id", $tid);
		$query = $this->db->get();		
		return $query->num_rows();
	}
	
	public function hasMatchPermission($id)
	{
		$this->db->select('match_id');
		$this->db->from('match_center');
		$this->db->where(array('match_id'=>$id, 'owner'=>$this->session->logged_user));
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return true;
		}
		return false;
	}

	public function hasMatchFinished($id)
	{
		$this->db->select('status');
		$this->db->from('match_center');
		$this->db->where(array('match_id'=>$id, 'owner'=>$this->session->logged_user));
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$r = $query->result()[0];
			if($r->status == '0')
			{
				return false;
			}
		}
		return true;
	}
	
	
	public function getMatchTeamPlayers($teams)
	{		
		$data = array();
		if(count($teams) > 0)
		{
			$this->load->model("Team");			
			foreach($teams as $tid)
			{				
				$data[] = $this->Team->getTeamPlayers($tid);
			}
		}		
		return $data;
	}
	
	public function getMatchTypes()
	{
		$this->db->from("match_types");
		$this->db->order_by("type_id", "ASC");
		$query = $this->db->get();
		$types = array();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$types[] = array('label'=>$row->type_label, 'Id'=>$row->type_id);
			}
		}
		return $types;
	}
	
	public function getPitchTypes()
	{
		$this->db->from("pitch_types");
		$this->db->order_by("pitch_id", "ASC");
		$query = $this->db->get();
		$types = array();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$types[] = array('label'=>$row->pitch_label, 'Id'=>$row->pitch_id);
			}
		}
		return $types;
	}
	
	public function getMatchDetails($mid)
	{
		$this->db->select('home, away, overs, ground, pitch');
		$this->db->from('match_center');
		$this->db->where(array('match_id'=>$mid));
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$data = array();
			$r = $query->result()[0];
			$data['home'] = $r->home;
			$data['away'] = $r->away;
			$data['overs'] = $r->overs;
			$data['ground'] = $r->ground;
			$this->load->model("Team");
			$data['pitch'] = $this->Team->getPitchLabel($r->pitch);
			$data['home_label'] = $this->Team->getTeamName($r->home);
			$data['away_label'] = $this->Team->getTeamName($r->away);
			return $data;
		}
		else
		{
			return array();
		}
	}
	
	public function getBowlers($mid, $tid, $what)
	{
		$this->db->select('p.player_id, CONCAT(p.first_name," ",p.last_name) AS name, p.player_type, pt.type_name, pt.type_icon');
		$this->db->from('players p');
		$this->db->join('player_types pt', 'pt.player_type_id = p.player_type', 'left');
		$this->db->join('match_players mp', 'mp.pid = p.player_id', 'left');
		$this->db->where(array('mp.mid'=>$mid, 'mp.team'=>$what, 'mp.can_bowl'=>1));
		$this->db->order_by('mp.pos', 'ASC');		
		$q = $this->db->get();
		$data = array();
		if($q->num_rows() > 0)
		{
			foreach($q->result() as $r)
			{
				$data[] = array(
					'name'	=>	$r->name,
					'player_id'	=>	$r->player_id,
					'role'	=>	$r->type_name,
					'icon'	=>	$r->type_icon,
					'role_id'	=>	$r->player_type
				);
			}
		}
		return $data;
	}
	
	public function getTeam1Team2($mid)
	{
		$this->db->select("home, away, toss, decision");
		$this->db->from("match_center");
		$this->db->where("match_id", $mid);
		$q = $this->db->get();
		$teams = array();
		$teams[0] = 0;
		$teams[1] = 0;
		if($q->num_rows() == 1)
		{
			$r = $q->result()[0];
			$toss_won_by = $r->toss;
			$decision = $r->decision;
			if($decision == "Bat")
			{
				if($toss_won_by == $r->home)
				{
					$teams[0] = $r->home;
					$teams[1] = $r->away;
				}
				else if($toss_won_by == $r->away)
				{
					$teams[0] = $r->away;
					$teams[1] = $r->home;
				}				
			}
			else
			{
				// decided to field
				if($toss_won_by == $r->home)
				{
					$teams[0] = $r->away;
					$teams[1] = $r->home;
				}
				else if($toss_won_by == $r->away)
				{
					$teams[0] = $r->home;
					$teams[1] = $r->away;
				}
			}
		}
		return $teams;
	}

	public function matchDuration($mid)
	{
		$overs = 0;
		$this->load->model("Utils");
		$overs = $this->Utils->getSingleValue(array('table'=>'match_center', 'column'=>'overs', 'where'=>array('match_id'=>$mid)));
		return $overs;
	}

	public function getGameMode($mid)
	{
		$mode = 0;
		$this->load->model("Utils");
		$mode = $this->Utils->getSingleValue(array('table'=>'match_center', 'column'=>'match_type', 'where'=>array('match_id'=>$mid)));
		return $mode;
	}

	public function getBowlingOptions($mid, $tid)
	{
		$this->db->select('p.player_id, CONCAT(p.first_name," ", p.last_name) AS name, p.player_type, p.bowler_type, p.bowling_rp');
		$this->db->from('players p');
		$this->db->join('match_players mp', 'mp.pid = p.player_id', 'left');
		$this->db->join('bowler_types bt', 'bt.bowler_type_id = p.bowler_type', 'left');
		$this->db->where('mp.mid', $mid);
		$this->db->where('mp.tid', $tid);
		$this->db->where('mp.can_bowl', 1);
		$this->db->order_by('mp.bowl_pos', 'ASC');
		$q = $this->db->get();
		$data = array();
		if($q->num_rows() > 0)
		{			
			foreach($q->result() as $r)
			{
				$data[$r->player_id] = array(
									'name'	=>	$r->name,									
									'player_type'	=>	$r->player_type,
									'bowler_type'	=>	$r->bowler_type,
									'rating_points'	=>	$r->bowling_rp,
									'bowls_bowled'	=>	0,
									'wickets'	=>	0,
									'wides'	=> 	0,
									'noballs'	=> 0,
									'maidens'	=> 0,
									'runs'	=>	0
								);
			}

		}
		return $data;
	}
}