<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StreamModel extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function isValidKey($key)
	{
		$data = array();
		$this->db->select('*');
		$this->db->from('live_stream');
		$this->db->where('url_key', $key);
		$q = $this->db->get();
		if($q)
		{
			if($q->num_rows() == 0)
			{
				$data['status'] = 'NOTOK';
				$data['msg'] = 'Stream key not found in records.';
			}
			else if($q->num_rows > 1)
			{
				$data['status'] = 'NOTOK';
				$data['msg'] = 'Multiple records found with the same key.';
			}
			else
			{
				$data['status'] = 'OK';
				$r = $q->result()[0];
				$data['result'] = $r;
			}
		}
		else
		{
			$data['status'] = 'NOTOK';
			$data['msg'] = $this->db->error()['message'];
		}

		return $data;
	}

	public function doesMatchExists($mid)
	{
		$data = array();
		$this->db->select('*');
		$this->db->from('match_center');
		$this->db->where('match_id', $mid);
		$q = $this->db->get();
		if($q)
		{
			if($q->num_rows() == 0)
			{
				$data['status'] = 'NOTOK';
				$data['msg'] = 'Match not found in records.';
			}
			else if($q->num_rows > 1)
			{
				$data['status'] = 'NOTOK';
				$data['msg'] = 'Multiple records found with the same match ID.';
			}
			else
			{
				$data['status'] = 'OK';
				$r = $q->result()[0];
				$data['result'] = $r;
			}
		}
		else
		{
			$data['status'] = 'NOTOK';
			$data['msg'] = $this->db->error()['message'];
		}

		return $data;
	}
}