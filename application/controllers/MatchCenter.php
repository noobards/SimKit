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
		}		$this->load->model("Center");
    }

	public function index()
	{		
		$this->load->view('templates/logged_in', array('page'=>'match_center'));
	}		public function SquadSelection($match_id = null)	{		$view = array();				$permission = $this->Center->hasMatchPermission($match_id);		if((bool) $permission === true)		{			$match_status = $this->Center->getMatchStatus($match_id);			if($match_status == 1)			{								$this->data['squads'] = $this->Center->getMatchTeamPlayers($match_id);								$view['status'] = 'OK';			}			else			{				$view['status'] = 'NOTOK';				$view['msg'] = 'This match has already been completed.';			}		}		else		{			$view['status'] = 'NOTOK';			$view['msg'] = 'You do not have permission to edit/view this match.';		}		$this->load->view('templates/logged_in', array('page'=>'squad_selection', 'view'=>$view));	}		public function getMyTeams()	{				$teams = $this->Center->fetchTeams();				echo json_encode(array('teams'=>$teams));	}		public function saveStepOne()	{		$post = json_decode(file_get_contents("php://input"));				$result = $this->Center->stepOne($post->selected_teams, $post->match_type);		if($result['status'] == 'OK')		{			echo json_encode(array('status'=>'OK', 'id'=>$result['id']));		}		else		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>$result['msg']));		}		exit;	}		public function savedMatchList()	{		$data = $this->Center->fetchInProgressMatches();		echo json_encode(array('status'=>'OK', 'matches'=>$data));	}
}