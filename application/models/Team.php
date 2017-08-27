<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Team extends CI_Model {
	public function hasTeamPermission($team)
	{		$user = (int) $this->session->logged_user;
		$this->db->from("teams");
		$this->db->where(array('owner'=>$user, 'team_id'=>$team));
		$query = $this->db->get();
		return ($query->num_rows() == 1 ? "YES" : "NO");
	}		public function getTeamName($team)	{		$this->db->select('team_name');		$this->db->from("teams");		$this->db->where(array('team_id'=>$team));		$query = $this->db->get();		foreach($query->result() as $row)		{			return $row->team_name;		}	}
	public function removeTeam($team)	{		$this->db->trans_begin();				$this->db->query('DELETE FROM squads WHERE team_id = '.$team);		$this->db->query('DELETE FROM match_center WHERE (team1 = '.$team.' || team2 = '.$team.')');		$this->db->query('DELETE FROM teams WHERE team_id = '.$team);						if ($this->db->trans_status() === FALSE)		{	        $this->db->trans_rollback();	        return false;		}		else		{	        $this->db->trans_commit();	        return true;		}	}
	public function getTeamObject($tid)
	{	
		$this->db->from("teams t");
		$this->db->join("team_types tt", "tt.team_type_id = t.team_type", "left");
		$this->db->where(array("t.team_id"=>$tid));
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$rows = array();
			foreach($query->result() as $row)
			{
				return $row;
			}
		}
		return false;
	}		public function getTeamPlayers($tid)	{		$team_name = $this->getTeamName($tid);		$this->db->select('player_id');		$this->db->from('squads');		$this->db->where('team_id', $tid);		$query = $this->db->get();		$data = array();		if($query->num_rows() > 0)		{			$players = array();			foreach($query->result() as $row)			{				$players[] = $row->player_id;			}						foreach($players as $pid)			{								$this->db->select("p.player_id, p.first_name, p.last_name, p.age, pt.type_name, pt.type_icon");				$this->db->from('players p');				$this->db->join('player_types pt', 'pt.player_type_id = p.player_type', 'left');				$this->db->where('player_id', $pid);				$query = $this->db->get();				if($query->num_rows() > 0)				{					foreach($query->result() as $row)					{												$data[$team_name][] = array(														'player_name'	=>	$row->first_name.' '.$row->last_name,							'player_age'	=>	$row->age,							'player_type'	=>	$row->type_name,							'icon'			=>	$row->type_icon,							'player_id'		=>	$row->player_id						);											}				}							}		}				return $data;	}
}