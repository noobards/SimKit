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

}