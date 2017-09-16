<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

	public function __construct()
	{
        parent::__construct();    	
    }
	

	public function changePassword()
	{
		$post = json_decode(file_get_contents("php://input"));

		if(isset($post->opw))
		{
			$this->load->model("Utils");
			if($this->Utils->isValidPassword($post->opw))
			{
				if(isset($post->npw) && isset($post->cpw) && trim($post->npw) != '' && ($post->npw === $post->cpw))
				{
					echo json_encode($this->Utils->setNewPassword($post->npw));
				}
				else
				{
					echo json_encode(array('status'=>'NOTOK', 'msg'=>'New Password and Confirm New Password do not match.'));
				}				
			}
			else
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'Current Password does not match as per our records.'));
			}
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Current Password cannot be blank/empty'));
		}
		exit;
	}
	
	public function Profile()
	{
		$this->load->view("templates/logged_in", array('page'=>'profile'));
	}
	
	public function getProfileData()
	{
		$user = (int) $this->session->logged_user;
		if($user == 0)
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Your session has expired. Please logout and log back in.'));
			exit;
		}
		else
		{
			$this->db->from('members');
			$this->db->where('user_id', $user);
			$query = $this->db->get();
			if($query->num_rows() == 0)
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'User not found. Please logout and log back in.'));
				exit;
			}
			else if($query->num_rows() > 1)
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'Multiple rows found.'));
				exit;
			}
			else if($query->num_rows() == 1)
			{
				$row = $query->result()[0];
				$data = array(
					'username'	=> $row->username,
					'email'	=>	$row->email,
					'dob'	=>	($row->dob != '0000-00-00' ? $row->dob : null),
					'gender'	=>	$row->gender,
					'first_name'	=>	$row->first_name,
					'last_name'	=>	$row->last_name,
					'timezone'	=>	$row->timezone
				);
				echo json_encode(array('status'=>'OK', 'user'=>$data));
				exit;
			}
			else
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'Unknown error.'));
				exit;
			}
		}		
	}
}