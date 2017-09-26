<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Community_Model extends CI_Model {
	public function playerData($pid)
	{
		if((int) $pid == 0)
		{
			return array('status'=>'NOTOK', 'msg'=>'Selected player ID not found.');
		}

		$this->db->select('*');
		$this->db->from("players p");
		$this->db->join("countries c", "c.country_id=p.country", "left");
        $this->db->join("player_types pt", "pt.player_type_id=p.player_type", "left");
        $this->db->join("bowler_types bt", "bt.bowler_type_id=p.bowler_type", "left");
		$this->db->where('p.player_id', $pid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$this->load->model('Utils');
			$avg = $this->Utils->playerRating($pid);
			$row = $query->result()[0];
			$data = array();
			$data['pid'] = $pid;
			$data['first_name'] = $row->first_name;
			$data['last_name'] = $row->last_name;
			$data['nick'] = $row->nick_name;
			$data['age'] = $row->age;
			$data['gender'] = $row->gender;
			$data['country'] = $row->country_name;
			$data['type'] = $row->type_name;
			$data['type_id'] = $row->player_type;
			$data['bat_hand'] = $row->batting_hand;
			if(in_array($row->player_type, array('2', '3', '4')))
			{
				$data['bowl_hand'] = $row->bowling_hand;
				$data['bowl_type'] = $row->bowler_type_name;
			}
			$data['test'] = $row->test;
			$data['odi'] = $row->odi;
			$data['t20'] = $row->t20;
			$data['bat_pt'] = $row->batting_rp;
			$data['bowl_pt'] = $row->bowling_rp;
			$data['field_pt'] = $row->fielding_rp;
			$data['downloaded'] = $row->is_downloaded;
			if($row->is_downloaded == '1')
			{
				$this->load->model('Player');
				// check if the source player is also a downloaded player
				$source_player_id = $row->source_player;
				while($this->Player->isPlayerDownloaded($source_player_id))
				{
					$source_player_id = $this->Player->getSourcePlayerId($source_player_id);
				}
				$base_owner = $this->Player->getPlayerOwnerId($source_player_id);
			}
			else
			{
				$base_owner = null;
			}
			if($base_owner)
			{
				$data['base_owner'] = $this->Utils->getPlayerOwner($base_owner);
			}
			$data['source'] = $this->Utils->getPlayerOwner($row->source_owner);
			$data['owner'] = $this->Utils->getPlayerOwner($row->owner);
			$data['created'] = $this->Utils->localTimeZone($row->created_time);
			$data['updated'] = $this->Utils->localTimeZone($row->updated_time);
			$data['avg'] = $avg;
			return array('status'=>'OK', 'player'=>$data);
		}
		else
		{
			return array('status'=>'NOTOK', 'msg'=>'Selected player not found in database.');
		}
	}

	public function isAlreadyDownloaded($pid)
	{
		$owner = (int) $this->session->logged_user;
		if($owner > 0)
		{
			$this->db->select('player_id');
			$this->db->from('players');
			$this->db->where(array('owner'=>$owner, 'source_player'=>$pid));
			$query = $this->db->get();
			if($query->num_rows() > 0)
			{
				return true;
			}
		}

		return false;
	}
}