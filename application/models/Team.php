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
	public function removeTeam($team)	{		$this->db->trans_begin();				$this->db->query('DELETE FROM squads WHERE team_id = '.$team);		$this->db->query('DELETE FROM teams WHERE team_id = '.$team);						if ($this->db->trans_status() === FALSE)		{	        $this->db->trans_rollback();	        return false;		}		else		{	        $this->db->trans_commit();	        return true;		}	}
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
	}
}