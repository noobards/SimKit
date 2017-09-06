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

}