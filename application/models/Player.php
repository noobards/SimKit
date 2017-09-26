<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Player extends CI_Model {
	public function sameAsSourcePlayer($source, $current)
	{
		$this->db->from('players');
		$this->db->where('player_id', $source);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$source_player = $query->result()[0];			
		}
		else
		{
			return false;
		}

		$this->db->from('players');
		$this->db->where('player_id', $current);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$current_player = $query->result()[0];			
		}
		else
		{
			return false;
		}

		$columns = array('first_name', 'last_name', 'nick_name', 'age', 'gender', 'country', 'player_type', 'batting_hand', 'bowling_hand', 'bowler_type', 'test', 'odi', 't20', 'batting_rp', 'bowling_rp', 'fielding_rp');

		$not_changed = true;
		foreach($columns as $col)
		{	
			if(trim($source_player->{$col}) != trim($current_player->{$col}))
			{					
				$not_changed = false;
				break;
			}
		}

		return $not_changed;
	}

	public function isPlayerDownloaded($pid)
	{
		$this->db->select('is_downloaded');
		$this->db->from('players');
		$this->db->where('player_id', $pid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$row = $query->result()[0];
			if($row->is_downloaded == '1')
			{
				return true;
			}
		}
		return false;
	}

	public function getSourcePlayerId($pid)
	{
		$this->db->select('source_player');
		$this->db->from('players');
		$this->db->where('player_id', $pid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$row = $query->result()[0];
			return $row->source_player;
		}
		return 0;
	}

	public function getPlayerOwnerId($pid)
	{
		$this->db->select('owner');
		$this->db->from('players');
		$this->db->where('player_id', $pid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$row = $query->result()[0];
			return $row->owner;
		}
		return 0;
	}

	public function getDownloadCount($pid)
	{
		$this->db->select('player_id');
		$this->db->from('players');
		$this->db->where(array('source_player'=>$pid));
		$query = $this->db->get();
		return $query->num_rows();
	}
}