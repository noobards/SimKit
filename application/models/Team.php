<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Team extends CI_Model {
	public function hasTeamPermission($user, $team)
	{
		$this->db->from("teams");
		$this->db->where(array('owner'=>$user, 'team_id'=>$team));
		$query = $this->db->get();
		return ($query->num_rows() == 1 ? "YES" : "NO");
	}

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