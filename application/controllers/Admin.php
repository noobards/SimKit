<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Admin extends CI_Controller {

	

	public function __construct()

	{

        parent::__construct();

    	if(! isset($this->session->logged_user))

		{

			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be logged in to view that page.'));

			redirect("../");

		}



		if($this->session->is_admin != '1')

		{

			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be an administrator to view that page.'));

			redirect("../");

		}

    }



	public function SiteMembers()

	{

		$this->load->view("templates/logged_in", array('page'=>'admin/site_members'));

	}



	public function getSiteMembers()

	{

		$this->db->select("*");

		$this->db->from("members");

		$this->db->order_by("last_login", "DESC");

		$result = $this->db->get();



		$data = array();

		if($result->num_rows() > 0)

		{

			foreach ($result->result() as $row)

			{
				$last = new DateTime($row->last_login, new DateTimeZone("UTC"));
				$last->setTimeZone(new DateTimeZone($this->session->timezone));

				$created = new DateTime($row->created_time, new DateTimeZone("UTC"));
				$created->setTimeZone(new DateTimeZone($this->session->timezone));
				$data[] = array(

					'id'			=> $row->user_id,

					'first_name'	=> $row->first_name,

					'last_name'		=> $row->last_name,

					'full_name'		=> $row->first_name.' '.$row->last_name,

					'un'			=> $row->username,

					'is_admin'		=> $row->is_admin,

					'is_active'		=> $row->is_active,

					'last_login'	=> $last->format("F d, Y (h:i a)"),

					'time'			=> $created->format("F d, Y (h:i a)")

				);

			}			

		}

		echo json_encode($data);

	}

}