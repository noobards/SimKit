<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Players extends CI_Controller {
	
	public function __construct()
	{
        parent::__construct();
    	if(! isset($this->session->logged_user))
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be logged in to view that page.'));
			redirect("../");
		} 	
    }

	public function index()
	{
		$this->load->view('templates/logged_in', array('page'=>'players'));
	}

	public function dropdown_values()
	{
		$this->db->from("countries");
		$this->db->order_by("country_name", "ASC");
		$query = $this->db->get();
		$countries = array();
		foreach ($query->result() as $row)
		{
		    $countries[] = array('id'=>$row->country_id, 'name'=>$row->country_name);
		}

		$this->db->from("player_types");
		$this->db->order_by("player_type_id", "ASC");
		$query = $this->db->get();
		$player_types = array();
		foreach ($query->result() as $row)
		{
		    $player_types[] = array('id'=>$row->player_type_id, 'name'=>$row->type_name);
		}

		$this->db->from("bowler_types");
		$this->db->order_by("bowler_type_id", "ASC");
		$query = $this->db->get();
		$bowler_types = array();
		foreach ($query->result() as $row)
		{
		    $bowler_types[] = array('id'=>$row->bowler_type_id, 'name'=>$row->bowler_type_name);
		}
		
		echo json_encode(array('countries'=>$countries, 'player_types'=>$player_types, 'bowler_types'=>$bowler_types));
		exit;
	}

	public function save()
	{
		$logged_in_user = (int) $this->session->logged_user;
		if($logged_in_user > 0)
		{
			$_POST = json_decode(file_get_contents('php://input'), true);
			if((int) $_POST['batting_rp'] > 100 || (int) $_POST['bowling_rp'] > 100 || (int) $_POST['fielding_rp'] > 100)
			{
				if((int) $_POST['batting_rp'] > 100)
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'Batting rating points cannot be greater than 100.'));
				}
				else if((int) $_POST['bowling_rp'] > 100)
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'Bowling rating points cannot be greater than 100.'));
				}
				else if((int) $_POST['fielding_rp'] > 100)
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'Fielding rating points cannot be greater than 100.'));
				}
			}
			else
			{
				if( ( (int) $_POST['batting_rp'] + (int) $_POST['bowling_rp'] + (int) $_POST['fielding_rp'] > 300 ) )
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'The sum of all three (batting, bowling, fielding) rating points cannot be greater than 300.'));
				}
				else
				{
					date_default_timezone_set("UTC");
					$time = date("Y-m-d H:i:s");
					$data = array(
						'first_name'	=>	$_POST['fn'],
						'last_name'		=>	$_POST['ln'],
						'nick_name'		=>	(isset($_POST['nick']) ? $_POST['nick'] : ""),
						'age'			=>	(isset($_POST['age']) ? $_POST['age'] : 0),
						'gender'		=>	$_POST['gender'],
						'country'		=>	$_POST['country'],
						'player_type'	=>	$_POST['player_type'],
						'mentality'		=>	$_POST['mentality'],
						'bowler_type'	=>	(isset($_POST['bowler_type']) ? $_POST['bowler_type'] : null),
						'batting_hand'	=>	$_POST['bat_hand'],
						'bowling_hand'	=>	(isset($_POST['bowl_hand']) ? $_POST['bowl_hand'] : null),
						'test'			=>	(isset($_POST['speciality']['test']) ? ($_POST['speciality']['test'] ? true: false) : false),
						'odi'			=>	(isset($_POST['speciality']['odi']) ? ($_POST['speciality']['odi'] ? true: false) : false),
						't20'			=>	(isset($_POST['speciality']['t20']) ? ($_POST['speciality']['t20'] ? true: false) : false),
						'batting_rp'	=>	$_POST['batting_rp'],
						'bowling_rp'	=>	$_POST['bowling_rp'],
						'fielding_rp'	=>	$_POST['fielding_rp'],
						'is_private'	=>	(isset($_POST['is_private']) && $_POST['is_private'] == '1') ? 1 : 0,
						'owner'			=>	$logged_in_user,
						'created_time'	=>  $time,
						'updated_time'	=>  $time
					);

					if($this->db->insert('players', $data))
					{
						$this->session->set_flashdata('flash', array('msg'=>'<i class="fa fa-check">&nbsp;</i><strong>'.$_POST['fn'].' '.$_POST['ln']. '</strong> has been added successfully.', 'status'=>'OK'));
						echo json_encode(array('status'=>'OK'));
					}
					else
					{
						echo json_encode(array('status'=>'NOTOK', 'msg'=>$this->db->error()['message']));
					}
				}
			}			
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Session has expired. Please logout and login again.'));			
		}				
		exit;
	}


	public function ListPlayers()
	{
		$this->load->view('templates/logged_in', array('page'=>'list_players'));
	}


	public function getPlayerList()
	{
		$logged_in_user = $this->session->logged_user;
		$this->db->select('*');
		$this->db->from("players p");
		$this->db->join("countries c", "c.country_id=p.country", "left");
		$this->db->join("player_types pt", "pt.player_type_id=p.player_type", "left");
		$this->db->join("mentality_types mt", "mt.mentality_id=p.mentality", "left");
		$this->db->order_by("p.created_time", "DESC");
		$this->db->limit(5);
		$this->db->where(array('p.owner'=>$logged_in_user));
		$query = $this->db->get();
		$players = array();
		if($query->num_rows() != 0)
		{

			foreach ($query->result() as $row)
			{
				$date = new DateTime($row->created_time, new DateTimeZone('UTC'));
				$date->setTimeZone(new DateTimeZone($this->session->timezone));
			    $players[] = array('name'=>$row->first_name.' '.$row->last_name, 'gender'=>$row->gender, 'country'=>$row->country_name, 'type'=>$row->type_name, 'icon'=>$row->type_icon, 'ment_label'=>$row->mentality_label, 'ment_icon'=>$row->mentality_icon, 'is_private'=>$row->is_private, 'created'=>$date->format('M d, g:i a'));
			}
		}
		echo json_encode($players);
		exit;		
	}

	public function getMyPlayers()
	{		
		$logged_in_user = $this->session->logged_user;
		$this->db->select('*');
		$this->db->from("players p");
		$this->db->join("countries c", "c.country_id=p.country", "left");
        $this->db->join("player_types pt", "pt.player_type_id=p.player_type", "left");
		$this->db->join("bowler_types bt", "bt.bowler_type_id=p.bowler_type", "left");
		$this->db->join("mentality_types mt", "mt.mentality_id=p.mentality", "left");
		$this->db->order_by("p.updated_time", "DESC");		
		$this->db->where(array('p.owner'=>$logged_in_user));
		$query = $this->db->get();
		$players = array();
		if($query->num_rows() != 0)
		{
			$this->load->model("Utils");
			foreach ($query->result() as $row)
			{
				$created = new DateTime($row->created_time, new DateTimeZone("UTC"));
				$created->setTimeZone(new DateTimeZone($this->session->timezone));

				$updated = new DateTime($row->updated_time, new DateTimeZone("UTC"));
				$updated->setTimeZone(new DateTimeZone($this->session->timezone));
				$avg = $this->Utils->playerRating($row->player_id);
			    $players[] = array('id'=>$row->player_id, 'name'=>$row->first_name.' '.$row->last_name, 'age'=>$row->age, 'gender'=>$row->gender, 'country'=>$row->country_name, 'player_type'=>$row->type_name, 'icon'=>$row->type_icon, 'ment_label'=>$row->mentality_label, 'ment_icon'=>$row->mentality_icon, 'test'=>($row->test == 1 ? 'YES':'NO'), 'odi'=>($row->odi == 1 ? 'YES':'NO'), 't20'=>($row->t20 == 1 ? 'YES':'NO'), 'avg'=>$avg, 'is_private'=>$row->is_private, 'updated'=>$updated->format("M d @ h:i a"), 'created'=>$created->format('M d @ h:i a'));
			}
		}
		echo json_encode($players);
		exit;
	}

	public function removeSeletedPlayers()
	{
		try
		{
			$this->load->model("Utils");
			$post = json_decode(file_get_contents("php://input"), true);		
			$to_delete = count($post);		
			$error = array();
			if($to_delete > 0)
			{
				$total_deleted = 0;
				foreach($post as $index => $array)
				{
					$player_id = (int) $array['id'];
					
					// check if logged in user has player permission
					if($this->Utils->hasPlayerPermission($player_id) == "YES")
					{
						// check if this player is currently not playing a match
						if($this->Utils->isNotPlayingMatch($player_id))
						{						
							if($this->Utils->removePlayer($player_id))
							{
								$total_deleted++;
							}
							else
							{
								$error[] = array('name'=>$array['name'], 'id'=>$player_id, 'reason'=>'Transaction failed');	
							}						
						}
						else
						{
							$error[] = array('name'=>$array['name'], 'id'=>$player_id, 'reason'=>'Currently playing a match');	
						}					
					}
					else
					{
						$error[] = array('name'=>$array['name'], 'id'=>$player_id, 'reason'=>'Either "Already removed" or "No permissions"');
					}
				}

				if($total_deleted == $to_delete)
				{
					$this->session->set_flashdata('flash', $total_deleted.' player(s) removed successfully.');
					echo json_encode(array('status'=>'OK', 'redirect'=>'ListPlayers'));
					exit;
				}
				else
				{				
					echo json_encode(array('status'=>'ERROR', 'pending'=>($to_delete - $total_deleted), 'error'=>$error));
					exit;
				}
			}
			else
			{			
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'Nothing to remove'));
				exit;			
			}
		}
		catch (Exception $err)
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>$err->getMessage()));
			exit;
		}
	}

	public function Edit($player_id)
	{		
		$this->load->model("Utils");
		$has_permission = $this->Utils->hasPlayerPermission($player_id);
		$this->load->view("templates/logged_in", array('page'=>'edit_player', 'has_permission'=>$has_permission, 'pid'=>$player_id));
	}

	public function GetPlayerData()
	{
		$post = json_decode(file_get_contents("php://input"), true);
		$pid = (int) $post['pid'];
		$logged_in_user = (int) $this->session->logged_user;

		$this->db->select('*');
		$this->db->from("players");			
		$this->db->where(array('owner'=>$logged_in_user, 'player_id'=>$pid));
		$query = $this->db->get();
		$player = array();
		if($query->num_rows() != 0)
		{
			foreach ($query->result() as $row)
			{
			    $player[] = array('id'=>$row->player_id, 'first_name'=>$row->first_name, 'last_name'=>$row->last_name, 'nick_name'=>$row->nick_name, 'age'=>$row->age, 'gender'=>$row->gender, 'country'=>$row->country, 'player_type'=>$row->player_type, 'mentality'=>$row->mentality, 'bowler_type'=>$row->bowler_type, 'batting_hand'=>$row->batting_hand, 'bowling_hand'=>$row->bowling_hand, 'test'=>$row->test, 'odi'=>$row->odi, 't20'=>$row->t20, 'is_private'=>$row->is_private, 'batting_rp'=>$row->batting_rp, 'bowling_rp'=>$row->bowling_rp, 'fielding_rp'=>$row->fielding_rp);
			}
		}
		echo json_encode($player);
		exit;
	}

	public function UpdatePlayerData()
	{

		$post = json_decode(file_get_contents("php://input"));
		date_default_timezone_set("UTC");
		$data = array(
			'first_name'	=>	$post->fn,
			'last_name'		=>	$post->ln,
			'nick_name'		=>	$post->nick,
			'age'			=>	$post->age,
			'gender'		=>	$post->gender,
			'country'		=>	$post->country,
			'player_type'	=>	$post->player_type,
			'mentality'		=>	$post->mentality,
			'batting_hand'	=>	$post->bat_hand,
			'test'			=>	($post->speciality->test ? 1 : 0),
			'odi'			=>	($post->speciality->odi ? 1 : 0),
			't20'			=>	($post->speciality->t20 ? 1 : 0),
			'batting_rp'	=>	$post->batting_rp,
			'bowling_rp'	=>	$post->bowling_rp,
			'fielding_rp'	=>	$post->fielding_rp,
			'is_private'	=>	(isset($post->is_private) && $post->is_private == '1') ? 1 : 0,
			'updated_time'	=>	date("Y-m-d H:i:s")
		);
		if(in_array($post->player_type, array(2,3,4)))
		{
			$data['bowling_hand'] = $post->bowl_hand;
			$data['bowler_type'] = $post->bowler_type;
		}
		else
		{
			$data['bowling_hand'] = null;
			$data['bowler_type'] = null;
		}
		$this->db->where('player_id',$post->player_id);
		if(! $this->db->update('players',$data))
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>$this->db->error()['message']));			
		}
		else
		{
			$this->session->set_flashdata('flash', '<i class="fa fa-check">&nbsp;</i>'.$post->fn.' '.$post->ln.' has been updated successfully.');
			echo json_encode(array('status'=>'OK'));
		}		
		exit;
		
	}
}