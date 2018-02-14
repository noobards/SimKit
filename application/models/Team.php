<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Team extends CI_Model {

	public function hasTeamPermission($team)
	{
		$user = (int) $this->session->logged_user;
		$this->db->from("teams");
		$this->db->where(array('owner'=>$user, 'team_id'=>$team));
		$query = $this->db->get();
		return ($query->num_rows() == 1 ? "YES" : "NO");
	}
	
	public function getTeamName($team)
	{
		$this->db->select('team_name');
		$this->db->from("teams");
		$this->db->where(array('team_id'=>$team));
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return $query->result()[0]->team_name;
		}
		else
		{
			return "Not defined";
		}
	}

	public function notInvolvedInMatch($tid)
	{
		$this->db->select('match_id');
		$this->db->from('match_center');
		$where = "stage != 3 AND (home = ".$tid." OR away = ".$tid." )";
		$this->db->where($where);	
		$q = $this->db->get();
		if($q->num_rows() > 0)
		{
			return false;
		}
		return true;
	}

	public function removeTeam($team)
	{
		$this->db->trans_begin();		
		$this->db->query('DELETE FROM squads WHERE team_id = '.$team);
		$this->db->query('DELETE FROM teams WHERE team_id = '.$team);
		
		
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

	public function getLogoPath($tid)
	{
		if((int) $tid == 0)
		{
			return "";
		}

		$this->db->select('logo');
		$this->db->from('teams');
		$this->db->where("team_id", $tid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$row = $query->result();
			return $row[0]->logo;
		}
		else
		{
			return "";
		}
	}
	
	public function getPitchLabel($pid)
	{
		if((int) $pid == 0)
		{
			return "Not defined";
		}
		
		$this->db->select('pitch_label');
		$this->db->from('pitch_types');
		$this->db->where('pitch_id', $pid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return $query->result()[0]->pitch_label;
		}
		else
		{
			return "Not defined";
		}
	}

	public function getMyTeams()
	{
		$logged_in_user = $this->session->logged_user;
		$this->db->select('count(s.player_id) as nop, t.team_id, t.team_name, tt.type_name, t.created_time, t.updated_time');
		$this->db->from("teams t");
		$this->db->join("team_types tt", "tt.team_type_id=t.team_type", "left");
		$this->db->join("squads s", "t.team_id=s.team_id", "left");
		$this->db->group_by("t.team_name");
		$this->db->order_by("t.updated_time", "DESC");
		$this->db->where(array('t.owner'=>$logged_in_user));
		$query = $this->db->get();
		$my_teams = array();
		if($query->num_rows() != 0)
		{	
			foreach ($query->result() as $row)
			{
				$created = new DateTime($row->created_time, new DateTimeZone("UTC"));
				$created->setTimeZone(new DateTimeZone($this->session->timezone));
				$updated = new DateTime($row->updated_time, new DateTimeZone("UTC"));
				$updated->setTimeZone(new DateTimeZone($this->session->timezone));
			    $my_teams[] = array('team_id'=>$row->team_id, 'name'=>$row->team_name, 'type'=>$row->type_name, 'nop'=>$row->nop, 'created'=>$created->format('m/d/y h:i a'), 'updated'=>$updated->format('m/d/y h:i a'));
			}
		}

		return $my_teams;
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
	
	public function getTeamPlayers($tid)
	{
		$team_name = $this->getTeamName($tid);
		$this->db->select('p.player_id, p.first_name, p.last_name, p.age, p.player_type, mt.mentality_id, mt.mentality_label, mt.mentality_icon, p.batting_rp, p.bowling_rp, p.fielding_rp, pt.type_name, pt.type_icon');
		$this->db->from('players p');
		$this->db->join('player_types pt', 'pt.player_type_id = p.player_type', 'left');
		$this->db->join('squads s', 's.player_id = p.player_id', 'left');
		$this->db->join('mentality_types mt', 'mt.mentality_id = p.mentality', 'left');
		$this->db->where(array('s.team_id'=>$tid));
		$this->db->order_by('p.first_name');
		$query = $this->db->get();
		$data = array();
		if($query->num_rows() > 0)
		{
			$data['name'] = $team_name;
			foreach($query->result() as $row)
			{
				$data['players'][] = array(
										'player_name'		=>	$row->first_name.' '.$row->last_name,
										'player_age'		=>	$row->age,
										'player_type_id'	=>	$row->player_type,
										'player_type'		=>	$row->type_name,
										'mentality_id'		=>	$row->mentality_id,
										'mentality'			=>	$row->mentality_label,
										'mentality_icon'	=>	$row->mentality_icon,
										'icon'				=>	$row->type_icon,
										'player_id'			=>	$row->player_id,
										'bat'				=>	$row->batting_rp,
										'bowl'				=>	$row->bowling_rp,
										'field'				=>	$row->fielding_rp												
									);
			}
		}
		
		return $data;
	}

	public function getTeamRating($tid)
	{
		if((int) $tid == 0)
		{
			return "0.0";
		}
		else
		{
			$this->db->select("s.player_id, p.batting_rp, p.bowling_rp, p.fielding_rp");
			$this->db->from("squads s");
			$this->db->join("players p", "p.player_id = s.player_id", "left");
			$this->db->where("s.team_id", $tid);
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				$players = array();
				foreach($query->result() as $row)
				{
					$players[] = (int) $row->batting_rp + (int) $row->fielding_rp + (int) $row->bowling_rp;
				}

				$total_players = count($players);
				$total_sum = array_sum($players);
				$avg = number_format($total_sum/$total_players, 2);
				return number_format($avg*10/300, 1);
			}
			else
			{
				return "0.0";
			}
		}
	}

}