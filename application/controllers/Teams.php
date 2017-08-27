<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Teams extends CI_Controller {
	
	public function __construct()
	{
        parent::__construct();
    	if(! isset($this->session->logged_user))
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be logged in to view that page.'));
			redirect("../");
		} 	
    }

	public function index()
	{				
		$this->load->view('templates/logged_in', array('page'=>'teams'));
	}

	public function getTeamTypes()
	{		
		$this->db->from("team_types");
		$this->db->order_by("team_type_id", "ASC");
		$query = $this->db->get();
		$types = array();
		if($query->num_rows() > 0)
		{
			foreach ($query->result() as $row)
			{
			    $types[] = array('id'=>$row->team_type_id, 'name'=>$row->type_name);
			}
		}
		echo json_encode($types);
	}

	public function Edit($team_id)
	{
		$logged_in_user = (int) $this->session->logged_user;
		if($logged_in_user > 0)
		{			
			$this->load->model("Team", "team_model");
			$this->load->model("Utils", "utils");
			$is_owner = $this->team_model->hasTeamPermission($team_id);
			$team = $this->team_model->getTeamObject($team_id);
			$team_type_options = $this->utils->getTeamTypes();
			$this->load->view('templates/logged_in', array('page'=>'edit_team', 'is_owner'=>$is_owner, 'team'=>$team, 'options'=>$team_type_options));
		}
		else
		{
			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to login to view that page'));
			redirect("../");
		}		
	}

	public function UpdateTeam()
	{
		$post = json_decode(file_get_contents("php://input"));
		$this->load->model("Team");
		$hasPermission = $this->Team->hasTeamPermission($post->tid);
		if($hasPermission == "YES")
		{
			$data = array(
				'team_name'		=>	$post->tn,
				'team_type'		=>	$post->tt,
				'updated_time'	=>	date("Y-m-d H:i:s")
			);
			$this->db->where('team_id', $post->tid);
			if($this->db->update("teams", $data))
			{
				$this->session->set_flashdata('flash', array('status'=>'OK', 'msg'=>'Team has been updated successfully.'));
				echo json_encode(array('status'=>'OK'));			
			}
			else
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>$this->db->error()['message']));					
			}
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'You do not have permission to edit this team.'));			
		}
		exit;
	}

	public function save()
	{
		$logged_in_user = $this->session->logged_user;
		$_POST = json_decode(file_get_contents("php://input"), true);
		$data = array(
			'team_name'		=>	$_POST['tn'],
			'team_type'		=>	$_POST['tt'],
			'owner'			=>	$logged_in_user
		);
		echo json_encode(array($this->db->insert('teams', $data)));
		exit;
	}

	public function getTeamList()
	{
		$logged_in_user = $this->session->logged_user;
		$this->db->select('count(s.player_id) as nop, t.team_id, t.team_name, tt.type_name, t.created_time');
		$this->db->from("teams t");
		$this->db->join("team_types tt", "tt.team_type_id=t.team_type", "left");
		$this->db->join("squads s", "t.team_id=s.team_id", "left");
		$this->db->group_by("t.team_name");
		$this->db->order_by("t.created_time", "DESC");
		$this->db->where(array('t.owner'=>$logged_in_user));
		$query = $this->db->get();
		$my_teams = array();
		if($query->num_rows() != 0)
		{			
			foreach ($query->result() as $row)
			{
			    $my_teams[] = array('team_id'=>$row->team_id, 'name'=>$row->team_name, 'type'=>$row->type_name, 'nop'=>$row->nop, 'created'=>date('m/d/y h:i a', strtotime($row->created_time)));
			}
		}
		echo json_encode($my_teams);
		exit;
	}


	// list of players that are not already in the selected team
	public function getAvailablePlayers()
	{
		$post = json_decode(file_get_contents("php://input"), true);
		$team_id = $post['tid'];
		
		$logged_in_user = $this->session->logged_user;
		$query = $this->db->query("SELECT 
			`p`.`player_id`, `p`.`first_name`, `p`.`last_name`, `p`.`gender`, `pt`.`type_name`, `pt`.`type_icon`, `c`.`country_name`
			FROM `players` `p`
			LEFT JOIN `player_types` `pt` ON `pt`.`player_type_id`=`p`.`player_type`
			LEFT JOIN `countries` `c` ON `c`.`country_id`=`p`.`country`
			WHERE `p`.`owner` = $logged_in_user AND p.player_id NOT IN (SELECT `player_id` from `squads` WHERE `team_id` = $team_id)
			ORDER BY `p`.`first_name` ASC");
		$my_players = array();
		if($query->num_rows() != 0)
		{			
			foreach ($query->result() as $row)
			{
			    $my_players[] = array('id'=>$row->player_id, 'name'=>$row->first_name.' '.$row->last_name, 'gender'=>$row->gender, 'type'=>$row->type_name, 'icon'=>$row->type_icon, 'country'=>$row->country_name);
			}
		}
		echo json_encode($my_players);
	}

	public function addToTeam()
	{
		$post = json_decode(file_get_contents("php://input"), true);
		$team_id = (int) $post['tid'];
		
		if(is_array($post['players']['selected']) && count($post['players']['selected']) > 0)
		{
			$players = [];
			foreach($post['players']['selected'] as $player_id => $is_selected)
			{
				if($is_selected === true)
				{
					$players[] = $player_id;
				}
			}

			if(count($players) > 0)
			{
				// get the count of players in squad to know the batting position of the next player being inserted
				$this->db->select('batting_position');
				$this->db->from('squads');
				$this->db->where(array('team_id'=>$team_id));
				$query = $this->db->get();
				if($query->num_rows() == 0)
				{
					$position = 1;
				}
				else
				{
					$position = ($query->num_rows() + 1);
				}
				foreach($players as $pid)
				{
					try
					{
						$data = array(
							'player_id'			=> $pid,
							'team_id'			=> $team_id,
							'is_captain'		=> 0,
							'batting_position'	=> $position
						);
						$this->db->insert('squads', $data);
						$position++;
					}
					catch (Exception $e)
					{
						echo json_encode(array('status'=>'NOTOK', 'msg'=>'Insertion query failed ('.$e->getMessage().').'));
						exit;
					}
					
				}
				$this->session->set_flashdata('flash', count($players).' player(s) have been added to the team.');
				echo json_encode(array('status'=>'OK'));
				exit;
			}
			else
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>'No player was selected.'));
				exit;
			}
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Input is not in correct format.'));
			exit;
		}
	}		public function removeTeam()	{		$post = json_decode(file_get_contents("php://input"));		$team_id = (int) $post->tid;		if($team_id == 0)		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Team ID not found.'));			exit;		}				if((int) $this->session->logged_user == 0)		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>'You are not logged in. Please logout and log back in again.'));			exit;		}				$this->load->model("Team");		if($this->Team->hasTeamPermission($team_id))		{			$team_name = $this->Team->getTeamName($team_id);			if($this->Team->removeTeam($team_id))			{				$this->session->set_flashdata('flash', $team_name.' has been removed successfully.');				echo json_encode(array('status'=>'OK'));				exit;			}			else			{				echo json_encode(array('status'=>'NOTOK', 'msg'=>'Could not process the action. Please contact the developer.'));				exit;			}		}		else		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>'You do not have permission to modify the team.'));			exit;		}			}

	public function getTeamPlayers()
	{
		$post = json_decode(file_get_contents("php://input"));
		$this->load->model("Team");
		if($this->Team->hasTeamPermission($post->tid))
		{
			$this->db->select("p.player_id, p.first_name, p.last_name, p.gender, c.country_name");
			$this->db->from("players p");			
			$this->db->join("countries c", "c.country_id = p.country", "left");
			$this->db->join("squads s", "s.player_id = p.player_id", "left");
			$this->db->where(array('p.owner'=>$this->session->logged_user, 's.team_id'=>$post->tid));
			$this->db->order_by("s.batting_position", "ASC");
			$query = $this->db->get();			$players = array();
			if($query->num_rows() > 0)
			{
				foreach($query->result() as $row)
				{
					$players[] = array('id'=>$row->player_id, 'name'=>$row->first_name.' '.$row->last_name, 'gender'=>$row->gender, 'country'=>$row->country_name);
				}				
			}			echo json_encode(array('status'=>'OK', 'players'=>$players));			exit;
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'You do not have permission to view/modify the team.'));
			exit;
		}
	}

	public function removeFromSquad()
	{
		$post = json_decode(file_get_contents("php://input"));				if(! isset($post->selectedPlayers) || count($post->selectedPlayers) == 0)		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Nothing to remoe.'));			exit;		}
		$this->load->model("Team");
		if($this->Team->hasTeamPermission($post->tid))
		{
			$deleted_rows = 0;
			$to_delete_rows = 0;
			foreach($post->selectedPlayers as $pid=>$status)
			{
				if($status == 1)
				{
					$this->db->where(array('player_id'=>$pid, 'team_id'=>$post->tid));
   					$this->db->delete('squads');
   					$deleted_rows += $this->db->affected_rows();
   					$to_delete_rows += 1;
				}
			}
			if($deleted_rows == $to_delete_rows)
			{
				$this->session->set_flashdata('flash', array('status'=>'OK', 'msg'=>$deleted_rows.' player(s) removed from squad.'));
				echo json_encode(array('status'=>'OK', 'deleted'=>$deleted_rows));
			}
			else
			{
				echo json_encode(array('status'=>'NOTOK', 'msg'=>$deleted_rows.'/'.$to_delete_rows.' row(s) deleted.'));
			}
			exit;
		}
		else
		{
			echo json_encode(array('status'=>'NOTOK', 'msg'=>'You do not have permission to view/modify the team.'));
			exit;
		}
	}
}