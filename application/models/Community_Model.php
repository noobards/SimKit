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
        $this->db->join("mentality_types mt", "mt.mentality_id=p.mentality", "left");
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
			$data['mentality'] = $row->mentality_label;
			$data['mentality_id'] = $row->mentality_id;
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

	public function fetchManuallyCreatedPlayers()
	{
		$user = (int) $this->session->logged_user;
		$this->db->select('p.player_id, p.first_name, p.last_name, p.updated_time, m.username, p.player_type, mt.mentality_label, pt.type_name, pt.type_icon, c.country_name');
		$this->db->from('players p');
		$this->db->join('members m', 'm.user_id = p.owner', 'left');
		$this->db->join("player_types pt", "pt.player_type_id = p.player_type", 'left');
		$this->db->join("countries c", "c.country_id = p.country", "left");
		$this->db->join("mentality_types mt", "mt.mentality_id = p.mentality", "left");
		$this->db->where(array('p.owner !=' => $user, 'p.is_private'=>0, 'p.is_downloaded'=>'0'));
		$this->db->order_by('p.player_id', 'DESC');
		$query = $this->db->get();
		$players = array();		
		if($query->num_rows() > 0)
		{	
			$this->load->model('Player');
			foreach($query->result() as $row)
			{	
				$source_owner = null;							

				// check if the current player in loop has already been downloaded by the logged in user
				$already = $this->isAlreadyDownloaded($row->player_id);

				$time = new DateTime($row->updated_time, new DateTimeZone('UTC'));
				$time->setTimeZone(new DateTimeZone($this->session->timezone));

				$download_count = $this->Player->getDownloadCount($row->player_id);

				
				$players[] = array(
								'pid'				=>	$row->player_id,
								'name'				=>	$row->first_name.' '.$row->last_name,
								'author'			=>	$row->username,
								'time'				=>	$time->format('M j @ h:i a'),
								'download'			=>	'0',
								'already'			=>	$already ? 'YES' : 'NO',
								'source_owner'		=>	$source_owner,
								'download_count'	=>	$download_count,
								'player_type'		=>	$row->type_name,
								'mentality'			=>	$row->mentality_label,
								'icon'				=>	$row->type_icon,
								'country'			=>	$row->country_name
							);
			}

			// sorting based on download count
			$temp = array();
			foreach ($players as $key => $row)
			{
			    $temp[$key] = $row['download_count'];
			}
			array_multisort($temp, SORT_DESC, $players);
		}
		return $players;
	}

	public function downloadedButChangedPlayers()
	{
		$user = (int) $this->session->logged_user;
		$this->db->select('p.player_id, p.first_name, p.last_name, p.updated_time, m.username, p.source_owner, p.source_player, mt.mentality_label, p.player_type, pt.type_name, pt.type_icon, c.country_name');
		$this->db->from('players p');
		$this->db->join('members m', 'm.user_id = p.owner', 'left');
		$this->db->join("player_types pt", "pt.player_type_id = p.player_type", 'left');
		$this->db->join("countries c", "c.country_id = p.country", "left");
		$this->db->join("mentality_types mt", "mt.mentality_id = p.mentality", "left");
		$this->db->where(array('p.owner !=' => $user, 'is_private'=>0, 'is_downloaded'=>'1'));
		$this->db->order_by('p.player_id', 'DESC');
		$query = $this->db->get();
		$players = array();
		if($query->num_rows() > 0)
		{
			$this->load->model('Community_Model');
			$this->load->model('Utils');
			$this->load->model('Player');
			foreach($query->result() as $row)
			{	
				// if this player has same values as that of source player, do not show it on the page					
				if($this->Player->sameAsSourcePlayer($row->source_player, $row->player_id))
				{
					continue;
				}
				$source_owner = $this->Utils->getPlayerOwner($row->source_owner);
				
				

				// check if the current player in loop has already been downloaded by the logged in user
				$already = $this->Community_Model->isAlreadyDownloaded($row->player_id);

				$time = new DateTime($row->updated_time, new DateTimeZone('UTC'));
				$time->setTimeZone(new DateTimeZone($this->session->timezone));

				$download_count = $this->Player->getDownloadCount($row->player_id);

				
				$players[] = array(
								'pid'				=>	$row->player_id,
								'name'				=>	$row->first_name.' '.$row->last_name,
								'author'			=>	$row->username,
								'time'				=>	$time->format('M j @ h:i a'),
								'download'			=>	'1',
								'already'			=>	$already ? 'YES' : 'NO',
								'source_owner'		=>	$source_owner,
								'download_count'	=>	$download_count,
								'player_type'		=>	$row->type_name,
								'mentality'			=>	$row->mentality_label,
								'icon'				=>	$row->type_icon,
								'country'			=>	$row->country_name
							);
			}

			// sorting based on download count
			$temp = array();
			foreach ($players as $key => $row)
			{
			    $temp[$key] = $row['download_count'];
			}
			array_multisort($temp, SORT_DESC, $players);
		}

		return $players;
	}
}