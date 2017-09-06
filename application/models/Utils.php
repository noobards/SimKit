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

	public function playerRating($player_id)
	{
		if($player_id && (int) $player_id > 0)
		{
			$permission = $this->hasPlayerPermission($player_id);
			if($permission == "YES")
			{
				$this->db->select("batting_rp, bowling_rp, fielding_rp");
				$this->db->from('players');
				$this->db->where('player_id', $player_id);
				$query = $this->db->get();
				if($query->num_rows() == 1)
				{
					foreach($query->result() as $row)
					{
						$total_rp = (int) $row->batting_rp + (int) $row->bowling_rp + (int) $row->fielding_rp;
						if($total_rp > 0)
						{							
							return number_format(($total_rp*10/300), 2);
						}
					}
				}
			}
		}
		return "0.00";
	}

	public function communityTeamList()
	{
		$data = array();
		$logged_in_user = (int) $this->session->logged_user;
		if($logged_in_user > 0)
		{
			$this->db->select("t.team_id, t.team_name, tt.type_name");
			$this->db->from("teams t");
			$this->db->join("team_types tt", "tt.team_type_id = t.team_type", "left");
			$this->db->where(array('t.owner != '=>$logged_in_user));
			$this->db->order_by("t.updated_time", "DESC");
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					$data[] = $row;
				}
			}
		}
		return $data;
	}

	public function communityPlayerList()
	{
		$data = array();
		$logged_in_user = (int) $this->session->logged_user;
		if($logged_in_user > 0)
		{
			$this->db->select('*');
			$this->db->from("players p");
			$this->db->join("countries c", "c.country_id=p.country", "left");
	        $this->db->join("player_types pt", "pt.player_type_id=p.player_type", "left");
	        $this->db->join("bowler_types bt", "bt.bowler_type_id=p.bowler_type", "left");
			$this->db->order_by("p.updated_time", "DESC");
			$this->db->where(array('p.owner != '=>$logged_in_user));
			$query = $this->db->get();			
			if($query->num_rows() > 0)
			{				
				foreach ($query->result() as $row)
				{					
				    $data[] = $row;
				}
			}
		}
		return $data;
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