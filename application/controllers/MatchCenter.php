<?phpdefined('BASEPATH') OR exit('No direct script access allowed');class MatchCenter extends CI_Controller {		public function __construct()	{        parent::__construct();    	if(! isset($this->session->logged_user))		{			$this->session->set_flashdata('flash', array('status'=>'NOTOK', 'msg'=>'You need to be logged in to view that page.'));			redirect("Login");		}		$this->load->model("Center");		$this->load->model("Team");    }	public function index()	{				$this->load->view('templates/logged_in', array('page'=>'match_center'));	}		public function PreMatch($mid)	{				$data = array();		if((int) $mid == 0)		{			$data['status'] = "NOTOK";			$data['msg'] = "Invalid match. Please go back and try again.";					}		else		{			if($this->Center->hasMatchPermission($mid))			{				$data['status'] = "OK";				$data['msg'] = "";								$extra = $this->Center->getMatchDetails($mid);				$data['home'] = $extra['home'];				$data['away'] = $extra['away'];				$data['home_label'] = $extra['home_label'];				$data['away_label'] = $extra['away_label'];				$data['ground'] = $extra['ground'];				$data['pitch'] = $extra['pitch'];				$data['overs'] = $extra['overs'];				$data['match'] = $mid;			}			else			{				$data['status'] = "NOTOK";				$data['msg'] = "You do not have permissions to simulate this match.";			}					}		$this->load->view('templates/logged_in', array('page'=>'prematch', 'data'=>$data));	}		public function getCompetingTeamPlayers()	{		$post = json_decode(file_get_contents("php://input"));				if($post->home == $post->away)		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>"Home and Away team cannot be same."));			exit;		}		else		{			if($this->Team->hasTeamPermission($post->home) && $this->Team->hasTeamPermission($post->away))			{							$teams = array();				$teams[0] = (int) $post->home;				$teams[1] = (int) $post->away;				$data = $this->Center->getMatchTeamPlayers($teams);				echo json_encode(array('status'=>'OK', 'db'=>$data));				exit;			}			else			{				echo json_encode(array('status'=>'NOTOK', 'msg'=>"You do not have permissions to access either one or both the teams."));				exit;			}		}	}		public function initializeData()	{		$match_types = $this->Center->getMatchTypes();		$pitch_types = $this->Center->getPitchTypes();		$teams = $this->Center->fetchTeams();		echo json_encode(array('teams'=>$teams, 'match_types'=>$match_types, 'pitch_types'=>$pitch_types));	}		public function setMatch()	{		$post = json_decode(file_get_contents("php://input"));		$user = (int) $this->session->logged_user;		if($user == 0)		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>'Your session has expired. Please log out and log back in.'));			exit;		}		date_default_timezone_set("UTC");		$data = array(			'match_type'	=>	$post->m_type->Id,			'ground'		=>	$post->ground,			'pitch'			=>	$post->p_type->Id,			'overs'			=>	$post->overs,			'home'			=>	$post->home,			'away'			=>	$post->away,			'owner'			=>	$user,			'created_on'	=>	date("Y-m-d H:i:s")					);								$this->db->trans_begin();		$this->db->insert('match_center', $data);		$match_id = $this->db->insert_id();				foreach($post->home_eleven as $index=>$player)		{			$pos = (int) ($index + 1);			$home = array(				'mid'		=>	$match_id,				'pid'		=>	$player->pid,				'team'			=>	'home',				'pos'			=>	$pos,				'is_captain'	=>	0,				'can_bowl'		=>	( ($player->icon == 'ball.png' || $player->icon == 'allrounder.png') ? 1 : 0 ),				'is_keeper'		=>	($player->icon == 'keeper.png' ? 1 : 0)			);						$this->db->insert('match_players', $home);		}				foreach($post->away_eleven as $index=>$player)		{			$pos = (int) ($index + 1);			$away = array(				'mid'		=>	$match_id,				'pid'		=>	$player->pid,				'team'			=>	'away',				'pos'			=>	$pos,				'is_captain'	=>	0,				'can_bowl'		=>	( ($player->icon == 'ball.png' || $player->icon == 'allrounder.png') ? 1 : 0 ),				'is_keeper'		=>	($player->icon == 'keeper.png' ? 1 : 0)			);						$this->db->insert('match_players', $away);		}						if ($this->db->trans_status() === FALSE)		{	        $this->db->trans_rollback();	        echo json_encode(array('status'=>'NOTOK', 'msg'=>'Transaction failed'));			exit;		}		else		{	        $this->db->trans_commit();	        echo json_encode(array('status'=>'OK', 'match_id'=>$match_id));			exit;		}	}		public function coinToss()	{			$post = json_decode(file_get_contents("php://input"));				if($this->Center->hasMatchPermission($post->match_id))		{			$toss = array($post->home_label, $post->away_label,$post->home_label, $post->away_label,$post->home_label, $post->away_label,$post->home_label, $post->away_label);			$toss_won_by  = $toss[array_rand($toss)];			$decisions = array("Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl", "Bat", "Bowl");			$decided_to = $decisions[array_rand($decisions)];						$keypair = array();			$keypair[$post->home_label] = $post->home;			$keypair[$post->away_label] = $post->away;						$home_bowlers = $this->Center->getBowlers($post->match_id, $post->home, 'home');			$away_bowlers = $this->Center->getBowlers($post->match_id, $post->away, 'away');						echo json_encode(array('status'=>'OK', 'toss'=>$toss_won_by, 'decided_to'=>$decided_to, 'toss_won_id'=>$keypair[$toss_won_by], 'home_bowlers'=>$home_bowlers, 'away_bowlers'=>$away_bowlers));			exit;		}		else		{			echo json_encode(array('status'=>'NOTOK', 'msg'=>'You do not have permissions to simulate this match.'));			exit;		}			}}