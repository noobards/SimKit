<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Player extends CI_Model {
	public function comparePlayers($source, $current)
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

		$is_changed = false;
		foreach($columns as $col)
		{
			if(trim($source_player->$col) != trim($current_player->$col))
			{
				$is_changed = true;
				break;
			}
		}

		return $is_changed;
	}
}