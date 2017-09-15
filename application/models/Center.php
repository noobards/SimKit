<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Center extends CI_Model {
	public function fetchTeams()
	{
		$this->db->select("team_id, team_name");
		$this->db->from("teams");
		$this->db->where("owner", $this->session->logged_user);
		$query = $this->db->get();
		$teams = array();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$nop = $this->getPlayerCount($row->team_id);
				$teams[] = array(
								'team_id'		=> $row->team_id,
								'team_name'		=> $row->team_name,
								'player_count'	=> $nop
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
	
	public function stepOne($selected_teams, $match_type, $match_title = '')
	{
		$data = array();
		if(is_array($selected_teams) && count($selected_teams) == 2)
		{
			$team1 = $selected_teams[0];
			$team2 = $selected_teams[1];
			date_default_timezone_set("UTC");
			$insert = array(
				'match_type'	=>	$match_type,
				'owner'			=>	$this->session->logged_user,
				'match_title'	=>	$match_title,
				'team1'			=>	$team1,
				'team2'			=>	$team2,
				'match_stage'	=>	1,
				'match_status'	=>	1,
				'created_on'	=> date("Y-m-d H:i:s"),
				'updated_on'	=> date("Y-m-d H:i:s")
			);
			if($this->db->insert('match_center', $insert))
			{
				$data['status'] = "OK";
				$data['id']	= $this->db->insert_id();
			}
			else
			{
				$data['status'] = "NOTOK";
				$data['msg']	= $this->db->error()['message'];				
			}
		}
		else
		{
			$data['status'] = 'NOTOK';
			$data['msg'] = 'Selected teams not found';
		}
		return $data;
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
	
	public function getMatchStatus($id)
	{
		$this->db->select('match_status');
		$this->db->from('match_center');
		$this->db->where(array('match_id'=>$id));
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			return $row->match_status;
		}
	}
	
	public function fetchInProgressMatches()
	{
		$data = array();
		$this->db->select('m.match_id, m.match_title, m.updated_on, s.stage_label, t1.team_name as team1, t2.team_name as team2');
		$this->db->from('match_center m');
		$this->db->join('match_stages s', 's.stage_id = m.match_stage', 'left');
		$this->db->join('teams t1', 't1.team_id = m.team1', 'left');
		$this->db->join('teams t2', 't2.team_id = m.team2', 'left');
		$this->db->where(array('m.owner' => $this->session->logged_user, 'm.match_status' => 1));
		$this->db->order_by('m.updated_on', 'DESC');
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
		}
		return $data;
	}
	
	public function getCompetingTeams($mid)
	{
		$teams = array();
		$this->db->select('team1, team2');
		$this->db->from('match_center');
		$this->db->where('match_id', $mid);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$row = $query->result();
			$teams[0] = $row[0]->team1;
			$teams[1] = $row[0]->team2;
		}		
		return $teams;
	}
	
	public function getMatchTeamPlayers($mid)
	{
		$teams = $this->getCompetingTeams($mid);
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
}