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
}