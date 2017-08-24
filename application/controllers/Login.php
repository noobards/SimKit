<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	
	public function index()
	{				if(isset($this->session->is_logged_in) && $this->session->is_logged_in === true)		{			redirect("Dashboard");			exit;		}
		$this->load->view('templates/logged_out', array('page'=>'login'));
	}

	public function doLogin()
	{
		$this->db->select('user_id, username, is_admin, first_name, last_name');
		$this->db->from("members");
		$this->db->where(array("username"=>$this->input->post('un'), "password"=>sha1($this->input->post('pw'))));
		$query = $this->db->get();
		if($query->num_rows() == 1)
		{
			$row = $query->result();
			$user_id = $row[0]->user_id;
			$this->session->logged_user = $user_id;			$this->session->is_logged_in = true;
			$this->session->username = $row[0]->username;
			$this->session->is_admin = $row[0]->is_admin;
			$this->session->first_name = $row[0]->first_name;
			$this->session->last_name = $row[0]->last_name;
			$this->session->full_name = $row[0]->first_name.' '.$row[0]->first_name;
			redirect("Dashboard");
			exit;			
		}
		else if($query->num_rows() > 1)
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'Multiple accounts found.'));			
			redirect("Login");
			exit;
		}
		else
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'Invalid credentials. Please try again.'));			
			redirect("Login");
			exit;
		}		
	}

	public function NewAccount()
	{
		$this->load->view('templates/logged_out', array('page'=>'new_account'));
	}

	public function createAccount()
	{
		$post = json_decode(file_get_contents("php://input"));
		$this->load->model("Utils");
		if($this->Utils->isNewMemberAccount($post->un, $post->em))
		{
			$data = array(
				'username'		=> $post->un,
				'password'		=> sha1($post->pw),
				'first_name'	=> $post->fn,
				'last_name'		=> $post->ln,
				'email'			=> $post->em
			);

			if($this->db->insert("members", $data))
			{
				$this->session->set_flashdata('flash', array('status'=>'OK', 'msg'=>'Account created successfully.'));
				echo json_encode(array("status"=>"OK"));
			}
			else
			{
				echo json_encode(array("status"=>"NOTOK", "msg"=>$this->db->error()['message']));
			}
		}
		else
		{
			echo json_encode(array("status"=>"NOTOK", "msg"=>"Account with same credentials already exists."));
		}

		
		exit;
	}
}