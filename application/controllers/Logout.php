<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
	
	public function index()
	{
		$this->session->unset_userdata('logged_user');		$this->session->unset_userdata('is_logged_in');
		$this->session->set_flashdata('flash', array('status'=>'OK', 'msg'=>'Logged out successfully.'));
		redirect("../");
	}
}