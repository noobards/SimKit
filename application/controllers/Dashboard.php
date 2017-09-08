<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Dashboard extends CI_Controller {

	

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

		$my_player_count = (int) $this->Utils->myPlayerCount();

		$my_team_count = (int) $this->Utils->myTeamCount();

		$this->load->view('templates/logged_in', array('page'=>'dashboard', 'my_player_count'=>$my_player_count, 'my_team_count'=>$my_team_count));		

	}

	public function NewPassword()
	{
		$this->load->view('templates/logged_in', array('page'=>'new_password'));
	}

}