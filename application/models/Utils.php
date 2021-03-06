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

	public function getSingleValue($data)
	{
		$this->db->select($data['column']);
		$this->db->from($data['table']);
		foreach($data['where'] as $col=>$val)
		{
			$this->db->where($col, $val);
		}		
		$q = $this->db->get();
		if($q->num_rows() == 1)
		{
			return $q->result()[0]->{$data['column']};
		}
	}

	public function playerRating($player_id)
	{
		if($player_id && (int) $player_id > 0)
		{
			$permission = $this->hasPlayerPermission($player_id);
			// no need to check permission since we are only getting rating, hence dummy condition
			if($permission == "YES" || $permission == "NO")
			{
				$this->db->select("batting_rp, bowling_rp, fielding_rp, player_type");
				$this->db->from('players');
				$this->db->where('player_id', $player_id);
				$query = $this->db->get();
				if($query->num_rows() == 1)
				{
					$row = $query->result()[0];
					$total_rp = $this->ratingCombo((int) $row->batting_rp, (int) $row->bowling_rp, (int) $row->fielding_rp, $row->player_type);
					if($total_rp > 0)
					{
						if($row->player_type == '3' || $row->player_type == '4')
						{
							return number_format(($total_rp*10/300), 2);
						}
						else
						{
							return number_format(($total_rp*10/200), 2);
						}						
					}					
				}
			}
		}
		return "0.00";
	}

	public function ratingCombo($bat, $bowl, $field, $type)
	{
		if($type == 1) // pure batsman
		{			
			return ( (100*$bat)/100  + (100*$field)/100 );
		}
		else if($type == 2) // pure bowler
		{			
			return ( (100*$bowl)/100 + (100*$field)/100 );
		}
		else if($type == 3) // batting allrounder
		{			
			return ( (100*$bat)/100  + (100*$bowl)/100 + (100*$field)/100 );	
		}
		else if($type == 4) // bowling allrounder
		{			
			return ( (100*$bat)/100  + (100*$bowl)/100 + (100*$field)/100 );
		}
		else if($type == 5) // wicket keeper
		{			
			return ( (100*$bat)/100  + (100*$field)/100 );
		}
		else
		{
			return 0;	
		}
	}

	public function getPlayerOwner($mid)
	{
		if((int) $mid == 0)
		{
			return "N/A";
		}

		$this->db->select('username');
		$this->db->from('members');
		$this->db->where('user_id', $mid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return $query->result()[0]->username;
		}
		else
		{
			return "Not Available";
		}
	}

	public function getOwnerId($pid)
	{
		if((int) $pid == 0)
		{
			return 0;
		}

		$this->db->select('owner');
		$this->db->from('players');
		$this->db->where('player_id', $pid);
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			return $query->result()[0]->owner;
		}
		else
		{
			return 0;
		}
	}

	public function localTimeZone($datetime, $format = null)
	{
		$dt = new DateTime($datetime, new DateTimeZone("UTC"));
		$dt->setTimeZone(new DateTimeZone($this->session->timezone ? $this->session->timezone : "Asia/Kolkata"));
		return $dt->format(($format ? $format : "M d @ h:i a"));
	}

	public function passwordReset($em)
	{
		$this->db->select('user_id, email, first_name, last_name, username');
		$this->db->from('members');
		$this->db->where('email', trim($em));
		$query = $this->db->get();
		if($query->num_rows() == 0)
		{
			return array('status'=>'NOTOK', 'msg'=>'Email was not found in our records.');
		}
		else if($query->num_rows() == 1)
		{
			$row = $query->result();
			$pass = $this->generatePassword();
			$this->load->library("EmailMailer");
			$un = $row[0]->username;
			$to = $row[0]->email;
			$name = $row[0]->first_name.' '.$row[0]->last_name;
			$subject = "New Password for SimKit";
			$body = "Your username is ".$un."<br /> Your new password is ".$pass."<br /> You can now use this password to login into the application";
			$email = $this->emailmailer->sendEmail($to, $name, $subject, $body);
			if($email['status'] == 'OK')
			{
				date_default_timezone_set("UTC");
				if($this->db->insert('emails', array('who'=>$un, 'who_id'=>$row[0]->user_id, 'sent_when'=>date("Y-m-d H:i:s"))))
				{
					// do nothing
				}
				$this->db->where("user_id", $row[0]->user_id);
				if($this->db->update("members", array('password'=>sha1($pass)) ))
				{
					return array('status'=>'OK', 'msg'=>"Password has been sent to ".$em.".");	
				}
				else
				{
					return array('status'=>'NOTOK', 'msg'=>$this->db->error()['message']);
				}
			}
			else
			{
				return $email;
			}
		}
		else
		{
			return array('status'=>'NOTOK', 'msg'=>'Multiple records found.');
		}
	}

	public function isValidPassword($pw)
	{
		$user = (int) $this->session->logged_user;
		if($user > 0)
		{
			$this->db->select("password");
			$this->db->from("members");
			$this->db->where("user_id", $user);
			$query = $this->db->get();
			if($query->num_rows() == 1)
			{
				$row = $query->result();
				$pass = $row[0]->password;
				if($pass === sha1($pw))
				{
					return true;
				}
			}
		}
		return false;
	}

	public function setNewPassword($pw)
	{
		$user = (int) $this->session->logged_user;
		if($user > 0)
		{
			if(trim($pw) != '')
			{
				$this->db->where("user_id", $user);
				if($this->db->update('members', array('password'=>sha1($pw))))
				{
					return array('status'=>'OK', 'msg'=>'Password has been changed successfully. It will take effect from your next login.');
				}
				else
				{
					return array('status'=>'NOTOK', 'msg'=>$this->db->error()['message']);
				}
			}
			else
			{
				return array('status'=>'NOTOK', 'msg'=>'New Password cannot be blank/empty.');
			}			
		}
		else
		{
			return array('status'=>'NOTOK', 'msg'=>'Your session has expired. Please logout and log back in.');
		}
	}

	public function generatePassword($len = 6)
	{
		$characters = '123456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $len; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public function communityTeamCount()
	{
		$data = array();
		$logged_in_user = (int) $this->session->logged_user;
		if($logged_in_user > 0)
		{
			$this->db->select("team_id");
			$this->db->from("teams");
			$this->db->where(array('owner != '=>$logged_in_user));			
			$query = $this->db->get();
			return $query->num_rows();			
		}
		return 0;
	}

	public function communityPlayerCount()
	{
		$data = array();
		$logged_in_user = (int) $this->session->logged_user;
		if($logged_in_user > 0)
		{
			$this->db->select('player_id');
			$this->db->from("players");			
			$this->db->where(array('owner != '=>$logged_in_user, 'is_downloaded'=>'0'));
			$query = $this->db->get();
			return $query->num_rows();
		}
		return 0;
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

	public function isNotPlayingMatch($pid)
	{
		$this->db->select('mid');
		$this->db->from('match_players');
		$this->db->where('pid', (int) $pid);
		$q = $this->db->get();
		if($q->num_rows() > 0)
		{
			$mids = array();
			foreach($q->result() as $r)
			{
				$mids[] = $r->mid;
			}

			// get match stage of the match
			foreach($mids as $mid)
			{
				$this->db->select("stage");
				$this->db->from("match_center");
				$this->db->where('match_id', $mid);
				$q = $this->db->get();
				if($q->num_rows() > 0)
				{
					foreach($q->result() as $r)
					{
						$stage = $r->stage;
						if($stage != 3) // still in progress
						{
							return false;
						}
					}
				}
			}
		}
		else
		{
			return true;
		}

		return true;
	}

	public function hasPlayerPermission($player_id)
	{
		$logged_in_user = (int) $this->session->logged_user;
		$this->db->select('player_id');
		$this->db->from("players");
		$this->db->where(array("owner"=>$logged_in_user, "player_id"=> (int) $player_id));
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
}