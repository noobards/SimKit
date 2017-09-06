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
			if((int) $_POST['batting_rp'] > 60 || (int) $_POST['bowling_rp'] > 60 || (int) $_POST['fielding_rp'] > 60)
			{
				if((int) $_POST['batting_rp'] > 60)
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'Batting rating points cannot be greater than 60.'));
				}
				else if((int) $_POST['bowling_rp'] > 60)
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'Bowling rating points cannot be greater than 60.'));
				}
				else if((int) $_POST['fielding_rp'] > 60)
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'Fielding rating points cannot be greater than 60.'));
				}
			}
			else
			{
				if( ( (int) $_POST['batting_rp'] + (int) $_POST['bowling_rp'] + (int) $_POST['fielding_rp'] > 120 ) )
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'The sum of all three (batting, bowling, fielding) rating points cannot be greater than 120.'));
				}
				else
				{
					$data = array(
						'first_name'	=>	$_POST['fn'],
						'last_name'		=>	$_POST['ln'],
						'nick_name'		=>	(isset($_POST['nick']) ? $_POST['nick'] : ""),
						'age'			=>	$_POST['age'],
						'gender'		=>	$_POST['gender'],
						'country'		=>	$_POST['country'],
						'player_type'	=>	$_POST['player_type'],
						'bowler_type'	=>	(isset($_POST['bowler_type']) ? $_POST['bowler_type'] : null),
						'batting_hand'	=>	$_POST['bat_hand'],
						'bowling_hand'	=>	(isset($_POST['bowl_hand']) ? $_POST['bowl_hand'] : null),
						'test'			=>	(isset($_POST['speciality']['test']) ? ($_POST['speciality']['test'] ? true: false) : false),
						'odi'			=>	(isset($_POST['speciality']['odi']) ? ($_POST['speciality']['odi'] ? true: false) : false),
						't20'			=>	(isset($_POST['speciality']['t20']) ? ($_POST['speciality']['t20'] ? true: false) : false),
						'batting_rp'	=>	$_POST['batting_rp'],
						'bowling_rp'	=>	$_POST['bowling_rp'],
						'fielding_rp'	=>	$_POST['fielding_rp'],
						'owner'			=>	$logged_in_user
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

	public function FileUpload()
	{
		$this->load->library('Excel');
		try {
		    $inputFile = $_FILES['file_input']['tmp_name'];
    		$objPHPExcel = PHPExcel_IOFactory::load($inputFile);
    		$cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

    		foreach ($cell_collection as $cell) {
			    $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
			    $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
			    $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();
			    //header will/should be in row 1 only. of course this can be modified to suit your need.
			    if ($row == 1) {
			        $header[$row][$column] = $data_value;
			    } else {			    	
			        $arr_data[$row][$column] = $data_value;
			    }
			}
			//send the data in an array format
			$data['header'] = $header;
			$data['values'] = $arr_data;
			$this->session->set_userdata("file_upload_data",$data);			
			redirect("players/ConfirmFileUpload");
		} catch(Exception $e) {
		    die($e->getMessage());
		}
	}

	public function ConfirmFileUpload()
	{
		$this->load->view('templates/logged_in', array('page'=>'confirm_file_upload'));
	}

	public function ImportPlayers()
	{		
		if($this->session->logged_user > 0)
		{
			$table = $this->session->userdata('file_upload_data');
			if(count($table) > 0)
			{				
				$this->load->model("Utils");			
				$insert_count = 0;
				$failed_count = 0;
				$total_count = 0;
				foreach($table['values'] as $row_number => $values_array)
				{
					if(isset($values_array['A']) && trim($values_array['A']) != '')						
					{
						$total_count++;
						$first_name = $values_array['A'];
						$last_name = $values_array['B'];
						$age = $values_array['C'];
						$gender = $values_array['D'];
						$country = (int) $this->Utils->getMenuID("countries", "country_id", array("country_name"=>trim($values_array['E'])));
						$player_type = (int) $this->Utils->getMenuID("player_types", "player_type_id", array("type_name"=>trim($values_array['F'])));
						$batting_hand = $values_array['G'];
						if($player_type == 2 || $player_type == 3 || $player_type == 4)
						{
							$bowling_hand = $values_array['H'];
							$bowler_type = (int) $this->Utils->getMenuID("bowler_types", "bowler_type_id", array("bowler_type_name"=>trim($values_array['I'])));
						}

						$test = (strtoupper(trim($values_array['J'])) == "YES" ? 1 : 0);
						$odi = (strtoupper(trim($values_array['K'])) == "YES" ? 1 : 0);
						$t20 = (strtoupper(trim($values_array['L'])) == "YES" ? 1 : 0);

						$data = array(
							'first_name'	=>	$first_name,
							'last_name'		=>	$last_name,
							'age'			=>	$age,
							'gender'		=>	$gender,
							'country'		=>	$country,
							'player_type'	=>	$player_type,
							'batting_hand'	=>	$batting_hand,																
							'test'			=>	$test,
							'odi'			=>	$odi,
							't20'			=>	$t20,
							'owner'			=>	$this->session->logged_user
						);

						if($player_type == 2 || $player_type == 3 || $player_type == 4)
						{
							$data['bowling_hand'] = $bowling_hand;
							$data['bowler_type'] = $bowler_type;							
						}
						else
						{
							$data['bowling_hand'] = null;
							$data['bowler_type'] = null;
						}

						
						if($this->db->insert('players', $data))
						{
							$insert_count++;
						}
						else
						{
							echo $this->db->error()['message'];							
							$failed_count++;
						}						
					}
				}
				$this->session->set_flashdata('flash', array('total'=>$total_count, 'failed'=>$failed_count, 'inserted'=>$insert_count, 'status'=>'OK'));				
				
			}
			else
			{
				$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'Upload session data not found.'));
			}
		}
		else
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'Session expired. Please logout and log back in.'));
			redirect("../");
		}
		redirect("Players");
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
		$this->db->order_by("p.created_time", "DESC");
		$this->db->limit(5);
		$this->db->where(array('p.owner'=>$logged_in_user));
		$query = $this->db->get();
		$players = array();
		if($query->num_rows() != 0)
		{
			foreach ($query->result() as $row)
			{
			    $players[] = array('name'=>$row->first_name.' '.$row->last_name, 'gender'=>$row->gender, 'country'=>$row->country_name, 'type'=>$row->type_name, 'icon'=>$row->type_icon, 'created'=>date('m/d/y h:i a', strtotime($row->created_time)));
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
		$this->db->order_by("p.created_time", "DESC");		
		$this->db->where(array('p.owner'=>$logged_in_user));
		$query = $this->db->get();
		$players = array();
		if($query->num_rows() != 0)
		{
			$this->load->model("Utils");
			foreach ($query->result() as $row)
			{
				$avg = $this->Utils->playerRating($row->player_id);
			    $players[] = array('id'=>$row->player_id, 'name'=>$row->first_name.' '.$row->last_name, 'age'=>$row->age, 'gender'=>$row->gender, 'country'=>$row->country_name, 'player_type'=>$row->type_name, 'icon'=>$row->type_icon, 'test'=>($row->test == 1 ? 'YES':'NO'), 'odi'=>($row->odi == 1 ? 'YES':'NO'), 't20'=>($row->t20 == 1 ? 'YES':'NO'), 'avg'=>$avg, 'created'=>date('m/d/y h:i a', strtotime($row->created_time)));
			}
		}
		echo json_encode($players);
		exit;
	}

	public function removeSeletedPlayers()
	{
		$this->load->model("Utils");
		$post = json_decode(file_get_contents("php://input"), true);		
		$to_delete = count($post);		
		if($to_delete > 0)
		{
			$total_deleted = 0;
			foreach($post as $index => $array)
			{
				$player_id = $array['id'];
				
				// check if logged in user has player permission
				if($this->Utils->hasPlayerPermission($player_id) == "YES")
				{
					if($this->Utils->removePlayer($player_id))
					{
						$total_deleted++;
					}
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
				$this->session->set_flashdata('flash', ($to_delete - $total_deleted).' players could not be deleted due to permission issues or system failure.');
				echo json_encode(array('status'=>'NOTOK', 'redirect'=>'ListPlayers'));
				exit;
			}
		}
		else
		{
			$this->session->set_flashdata('flash', 'Nothing to remove.');
			echo json_encode(array('status'=>'NOTOK', 'redirect'=>'ListPlayers'));
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
			    $player[] = array('id'=>$row->player_id, 'first_name'=>$row->first_name, 'last_name'=>$row->last_name, 'nick_name'=>$row->nick_name, 'age'=>$row->age, 'gender'=>$row->gender, 'country'=>$row->country, 'player_type'=>$row->player_type, 'bowler_type'=>$row->bowler_type, 'batting_hand'=>$row->batting_hand, 'bowling_hand'=>$row->bowling_hand, 'test'=>$row->test, 'odi'=>$row->odi, 't20'=>$row->t20, 'batting_rp'=>$row->batting_rp, 'bowling_rp'=>$row->bowling_rp, 'fielding_rp'=>$row->fielding_rp);
			}
		}
		echo json_encode($player);
		exit;
	}

	public function UpdatePlayerData()
	{

		$post = json_decode(file_get_contents("php://input"));
		$data = array(
			'first_name'	=>	$post->fn,
			'last_name'		=>	$post->ln,
			'nick_name'		=>	$post->nick,
			'age'			=>	$post->age,
			'gender'		=>	$post->gender,
			'country'		=>	$post->country,
			'player_type'	=>	$post->player_type,
			'batting_hand'	=>	$post->bat_hand,
			'test'			=>	($post->speciality->test ? 1 : 0),
			'odi'			=>	($post->speciality->odi ? 1 : 0),
			't20'			=>	($post->speciality->t20 ? 1 : 0),
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
			$this->session->set_flashdata('flash', $post->fn.' '.$post->ln.' has been updated successfully.');
			echo json_encode(array('status'=>'OK'));
		}		
		exit;
		
	}
}