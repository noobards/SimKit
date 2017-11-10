<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Community extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
    	if(! isset($this->session->logged_user))
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be logged in to view that page.'));
			redirect("Login");
		}
		$this->load->model('Community_Model');
    }

	public function index()
	{
		$this->load->model("Utils");
		$team_count = $this->Utils->communityTeamCount();
		$player_count = $this->Utils->communityPlayerCount();		
		$this->load->view('templates/logged_in', array('page'=>'community', 'team_count'=>$team_count, 'player_count'=>$player_count));
	}

	public function fetchPlayers()
	{		
		$players = array();
		$players['created'] = $this->Community_Model->fetchManuallyCreatedPlayers();
		$players['downloaded'] = $this->Community_Model->downloadedButChangedPlayers();

		
		echo json_encode(array('status'=>'OK', 'players'=>$players));
		exit;
	}

	public function getPlayer()
	{
		$post = json_decode(file_get_contents("php://input"));
		$this->load->model('Community_Model');
		echo json_encode($this->Community_Model->playerData($post->pid));
	}

	public function downloadPlayers()
	{
		$post = json_decode(file_get_contents("php://input"));

		if(count($post) > 0)
		{
			$this->load->model('Utils');
			$total_to_download = count($post);			
			$downloaded = 0;
			$failed = array();
			date_default_timezone_set("UTC");
			foreach($post as $obj)
			{
				// complete this function
				/*
				Array
				(
				    [0] => stdClass Object
				        (
				            [pid] => 41
				            [name] => Roman Mitchell
				            [author] => admin
				        )

				    [1] => stdClass Object
				        (
				            [pid] => 66
				            [name] => Tomas Ward
				            [author] => admin
				        )

				    [2] => stdClass Object
				        (
				            [pid] => 52
				            [name] => Ruslan Sanchez
				            [author] => admin
				        )

				)
				*/
				$source_owner = $this->Utils->getOwnerId($obj->pid);
				$owner = (int) $this->session->logged_user;
						

				$this->db->from('players');
				$this->db->where('player_id', $obj->pid);
				$query = $this->db->get();
				if($query->num_rows() == 1)
				{
					$time = date("Y-m-d H:i:s");
					$row = $query->result()[0];
					$data = array(
						'first_name'	=>	$row->first_name,
						'last_name'		=>	$row->last_name,
						'nick_name'		=>	$row->nick_name,
						'age'			=>	$row->age,
						'gender'		=>	$row->gender,
						'country'		=>	$row->country,
						'player_type'	=>	$row->player_type,
						'mentality'		=>	$row->mentality,
						'batting_hand'	=>	$row->batting_hand,
						'bowling_hand'	=>	($row->bowling_hand ? $row->bowling_hand : null),
						'bowler_type'	=>	($row->bowler_type ? $row->bowler_type : null),
						'test'			=>	$row->test,
						'odi'			=>	$row->odi,
						't20'			=>	$row->t20,
						'batting_rp'	=>	$row->batting_rp,
						'bowling_rp'	=>	$row->bowling_rp,
						'fielding_rp'	=>	$row->fielding_rp,
						'owner'			=>	$owner,
						'source_owner'	=>	$source_owner,
						'is_downloaded'	=>	'1',
						'source_player'	=>	$obj->pid,
						'created_time'	=>	$time,
						'updated_time'	=>	$time
					);

					if($this->db->insert('players', $data))
					{
						$downloaded++;
					}
					else
					{
						$failed[] = $obj->name;
					}
				}
				else
				{
					$failed[] = $obj->name;
				}
			}
			if($total_to_download == $downloaded)
			{
				$this->session->set_flashdata('flash', '<i class="fa fa-check">&nbsp;</i><strong>'.$total_to_download.'</strong> players successfully downloaded');
				echo json_encode(array('status'=>'OK'));
			}
			else
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>($total_to_download - $downloaded).' players could not be downloaded ('.implode(', ', $failed).')'));
			}
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Nothing to download.'));
		}
	}

	public function getDownloadList()
	{
		$post = json_decode(file_get_contents("php://input"));
		$this->db->select('m.username');
		$this->db->from('players p');
		$this->db->join('members m', 'm.user_id = p.owner', 'left');
		$this->db->where('p.source_player', $post->player_id);
		$query = $this->db->get();
		if($query->num_rows() > 0)
		{
			$owners = array();
			foreach($query->result() as $row)
			{
				$owners[] = $row->username;
			}
			echo json_encode(array('status'=>'OK', 'list'=>$owners));
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'No downloaders found.'));
		}
	}

}