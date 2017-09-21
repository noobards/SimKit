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
    }

	public function index()
	{
		$this->load->model("Utils");
		$team_count = count($this->Utils->communityTeamList());
		$player_count = count($this->Utils->communityPlayerList());
		$this->load->view('templates/logged_in', array('page'=>'community', 'team_count'=>$team_count, 'player_count'=>$player_count));
	}

	public function fetchPlayers()
	{
		$user = (int) $this->session->logged_user;
		$this->db->select('p.player_id, p.first_name, p.last_name, p.updated_time, m.username, p.is_downloaded');
		$this->db->from('players p');
		$this->db->join('members m', 'm.user_id = p.owner', 'left');
		$this->db->where(array('p.owner !=' => $user));
		$this->db->order_by('p.updated_time', 'DESC');
		$query = $this->db->get();
		$players = array();
		if($query->num_rows() > 0)
		{
			foreach($query->result() as $row)
			{
				$time = new DateTime($row->updated_time, new DateTimeZone('UTC'));
				$time->setTimeZone(new DateTimeZone($this->session->timezone));
				$players[] = array(
								'pid'		=>	$row->player_id,
								'name'		=>	$row->first_name.' '.$row->last_name,
								'author'	=>	$row->username,
								'time'		=>	$time->format('M j @ h:i a'),
								'download'	=>	$row->is_downloaded
							);
			}
		}
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
			}
		}
	}

}