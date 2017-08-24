<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utils extends CI_Model {
	public function getTeamTypes()
	{
		$this->db->from("team_types");
		$this->db->order_by("team_type_id", "ASC");
		$query = $this->db->get();
		$options = array();
		if($query->num_rows() > 0)
		{
			
			foreach($query->result() as $row)
			{
				$options[$row->team_type_id] = $row->type_name;
			}
		}
		return $options;
	}

	public function myPlayerCount()
	{
		$logged_in_user = (int) $this->session->logged_user;
		$this->db->select("player_id");
		$this->db->from("players");
		$this->db->where(array("owner"=>$logged_in_user));
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function myTeamCount()
	{
		$logged_in_user = (int) $this->session->logged_user;
		$this->db->select("team_id");
		$this->db->from("teams");
		$this->db->where(array("owner"=>$logged_in_user));
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function isNewMemberAccount($un, $em)
	{
		if(strlen(trim($un)) > 0 && strlen(trim($em)) > 0)
		{
			$this->db->select("user_id");
			$this->db->from("members");
			$this->db->where(array("email"=>$em));
			$query = $this->db->get();			
			if($query->num_rows() > 0)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
		
	}

	public function getMenuValues($table, $order_by, $order)
	{
		$this->db->from($table);
		$this->db->order_by($order_by, $order);
		$query = $this->db->get();
		$data = array();
		if($query->num_rows() > 0)
		{			
			foreach($query->result() as $row)
			{
				$data[] = $row;
			}
		}
		return $data;
	}
	
	public function getMenuID($table, $col, $where)
	{
		$this->db->select($col);
		$this->db->from($table);
		$this->db->where($where);
		$query = $this->db->get();			
		if($query->num_rows() == 1)
		{
			$row = $query->result();			
			return $row[0]->{$col};
		}
		else
		{
			return 0;
		}
	}

	public function hasPlayerPermission($player_id)
	{
		$logged_in_user = (int) $this->session->logged_user;
		$this->db->select('player_id');
		$this->db->from("players");
		$this->db->where(array("owner"=>$logged_in_user, "player_id"=>$player_id));
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return "YES";
		}
		else
		{
			return "NO";
		}
	}

	public function removePlayer($player_id)
	{
		$this->db->trans_begin();
		$this->db->query('DELETE FROM players WHERE player_id = '.$player_id);
		$this->db->query('DELETE FROM squads WHERE player_id = '.$player_id);
		
		
		if ($this->db->trans_status() === FALSE)
		{
	        $this->db->trans_rollback();
	        return false;
		}
		else
		{
	        $this->db->trans_commit();
	        return true;
		}
	}

	public function squadPlayers()
	{
		
	}
}