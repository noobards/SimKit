<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MatchCenter extends CI_Controller {
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
		$this->load->view('templates/logged_in', array('page'=>'match_center'));
	}
}