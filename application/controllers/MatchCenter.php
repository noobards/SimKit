<?phpdefined('BASEPATH') OR exit('No direct script access allowed');class MatchCenter extends CI_Controller {	public function __construct()	{        parent::__construct();    	if(! isset($this->session->logged_user))		{			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be logged in to view that page.'));			redirect("Login");		}		$this->load->model("Center");		$this->load->model("Team");    }	public function index()	{				$this->load->view('templates/logged_in', array('page'=>'match_center'));	}		public function getCompetingTeamPlayers()	{		$post = json_decode(file_get_contents("php://input"));				if($post->home == $post->away)		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>"Home and Away team cannot be same."));			exit;		}		else		{			if($this->Team->hasTeamPermission($post->home) && $this->Team->hasTeamPermission($post->away))			{							$teams = array();				$teams[0] = (int) $post->home;				$teams[1] = (int) $post->away;				$data = $this->Center->getMatchTeamPlayers($teams);				echo json_encode(array('status'=>'OK', 'db'=>$data));				exit;			}			else			{				echo json_encode(array('status'=>'NOTOK', 'msg'=>"You do not have permissions to access either one or both the teams."));				exit;			}		}	}		public function getMyTeams()	{				$teams = $this->Center->fetchTeams();				echo json_encode(array('teams'=>$teams));	}			public function savedMatchList()	{		$data = $this->Center->fetchInProgressMatches();		echo json_encode(array('status'=>'OK', 'matches'=>$data));	}}