<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');  

class Match{

	private $match_id;
	public $CI;
	
	public $game_mode = 0;
	public $innings = "first";
	public $teams = array();
	public $win_score = 0;
	
	public $batting_team_id = 0; // the team that is batting first
	public $bowling_team_id = 0; // the team that is batting second
	public $batting_team_label = "";
	public $bowling_team_label = "";
	public $partnership_runs = 0;
	public $partnership_balls = 0;
	public $partnerships = array();
	
	public $innings_completed = false;
	public $innings_total = 0;
	public $innings_wickets = 0;
	public $innings_overs = "0.0";
	public $innings_balls_bowled = 0;
	public $innings_commentary = array();
	public $innings_fow = array();
	public $last_over_total = 0;
	public $game_stage = "PP1";	
	public $innings_noballs = 0;
	public $innings_wides = 0;
	public $innings_byes = 0;
	public $innings_legbyes = 0;	
	public $match_result = array();
	
	public $bowlers = array();
	public $balls_per_bowler = 0;
	public $currently_bowling_index = 0;
	public $current_bowler = array();
	public $part_timers_available = false;
	public $part_timers_count = 0;
	public $change_bowler_after_overs = 3;
	public $fielders = array();

	public $mode_of_dismissal = array("LBW","Caught","Bowled","Run Out","LBW","Caught","Bowled","LBW","Caught","Bowled","LBW","Caught","Bowled","LBW","Caught","Bowled");
	public $out_how = "";
	public $batsmen = array();
	public $striker = array();
	public $non_striker = array();
	public $next_batsman_index = 0;
	public $striker_index = 0;
	public $non_striker_index = 1;


	// commentary related
	public $dots = array("back of a length, at the pads. Tucked to midwicket who is closes in.", "back of a length, shaped in at middle stump. Defended off the back foot into the leg side. The bounce is still in there.", "bending in at the thigh guard. Late to get his bat around and he awkwardly bunts it into the leg side.", "pushed to point with an open face.", "slower ball, full outside off stump. Looks to clatter it through the off side but he's forced to check his drive.", "defended to midwicket off the back foot.", "lovely line. Good length and angled into the corridor. Late on this forward push as it straightens and zips past the outside edge.", "length and into the pads. Looks to tickle this fine, but he can only get pad on it. The keeper collects it down leg side. Feeble appeal is turned down", "back of a length in the corridor, defended into the covers.", "<strong>narrowly misses the off stump</strong>. Good length, in at off stump and just flies through from above as the batsman offers a big gap between bat and pad on his push.", "full, whips against the angle, straight to midwicket.", "bit of theatrics from the batsman in his stance. But he can't tuck this past midwicket who hares to his right.", "another slower one. This one spits off a length outside off. Shoulder of the bat and into the off side as he fends in front.", "back of a length at off stump, pushes too hard as he looks to steal a single in the off side.", "short and wide, slashes hard at it. It bounces rapidly over the outside edge.", "back of a length in the corridor, pushed to point off the back foot.", "<strong>strikes him on the pads</strong> and he's on the back foot trying to shovel this into the leg side. Loud appeal. It hit him on the knee roll. Not given.", "good length, spins back in at off stump. Stretches forward and pokes it back. Extra cover dives to his left to field.", "good length at middle stump, reverse sweep, straight to point.", "good length at leg stump, tucked into the open square leg region off the back foot.", "full at middle stump, jammed out to midwicket.", "good length at off stump. Deflected off the inside half of the bat as he pushes forward at this.", "<strong>didn't pick it</strong>. On the back foot to this length ball at off stump.", "yorker at middle stump. Inside edge, onto pad and straight to mid-on via the bowler's fingertips.", "Dug in short. He backs away to try and slap this behind square on the off side, but the change in pace does him. Beaten.", "short one on the leg stump. Looks to force it off the back foot, awkwardly. It lands pitch side on the off side off the leading edge.", "walks across to the off side. Not sure whether to go forward or back. Stuck in between. Awkward poke and he's trapped on the pad. He was outside the line.", "length at middle stump, forward block.", "good length, outside off stump. Switches to a left-handed grip and tries to waft this over backward point. Cops it in the body.", "full outside off, down the track to drive through the off side. Mistimes it. Straight back at the bowler.", "good length at middle stump, spins into his mid-riff as he pushes forward.", "<strong>the bowler has dropped a return catch!</strong> It wasn't an easy chance. The batsman forays down the track, takes it on the full and drives it back to the bowler, sticks out his right hand but the ball does not stick. Not even on the rebound.", "pushed through flatter and faster, starts outside leg and raps the front pad. The lbw appeal is shot down.", "really full and wide outside off, the batsman backs away outside leg and flat-bats it back to the bowler.", "skidding into leg stump, beats the flick, and raps the pad.", "back of a length and outside off, punched on the up to extra cover.", "very full and the exaggerated angle makes things difficult for the batsman, flicked away through midwicket.");
	public $singles = array("back of a length at off stump, lifts off after pitching. Takes the thick outside edge but he was playing with soft hands.", "back of a length on the pads, skids in quickly. Nudged off the inside edge to short fine, via thigh guard", "slower ball, full and wide. Chases it with hard hands. Can only slice it to deep point.", "outside edge falls short of slip. Length ball from the corridor. Wafts at it on the up and gets a big edge that lands to the fielder's right.", "good length at middle stump, tapped wide of mid-on off the back foot.", "back of a length in the corridor, pulled off the toe-end to long-on.", "short and wide. Toe-ended cut to sweeper", "good length at leg stump, tucked into the open square leg region off the back foot.", "back of a length outside off, cut past backward point and to sweeper's right.", "slower ball, full at middle stump. Driven to sweeper with an open face.", "slower ball, short at middle stump. Looks to parry it past short fine but he's got a leading edge that lands in front of the fielder. He's too keen on the run out and is shaping to throw before gathering it. Fumbles and lets them through.", "makes room early. This is full on middle stump. He slices it to sweeper cover.", "full toss at leg stump, tickled straight to short fine.", "full at leg stump, flicked to deep midwicket.", "yorker just a shade outside off, jammed out through cover-point.", "just short of a good length and outside off, batsman chops it hard to deep point.", "shortish on middle, flat-batted right back to the bowler, who takes some pace off the ball. It rolls down to long-on.", "really, really full on the leg stump, the batsman squirts an inside edge to square leg.", "backs away outside leg, meets a short ball and crisply forces it to the sweeper.", "faster and flatter on off, shovel-pulled away to deep midwicket.", "flat dart on off, whipped out to deep midwicket.", "searches for the yorker, misses his length and offers up a full-toss on off, which is belted ram-rod straight down the ground, so much so it strikes the stumps at the non-striker's end.", "full-toss on off, punched down to long-on. The call from the batsman is for two but have to settle with just one.", "middle-stump yorker, dragged to long-on off the toe end.", "full and straight on middle, drilled down to long-off.", "full and wide outside off, scythed away through the covers.", "really, really full on off, not a perfect yorker, squeezed down to long-on. There is a mix-up between the batsmen. The throw from the deep, though, hits the striker on his back side.", "dug short and outside off, another slower ball, swatted to long-on.", "nails a yorker on middle, squirted away to short fine leg.");	
	public $twos = array("back of a length outside off, cut past backward point and to sweeper's right.", "back of a length into the pads. Whipped uppishly to deep square's left.", "pushed through square leg off the back foot.", "incredible shot. This is full, almost an overpitched one at middle. But he middles the shot. Gets it past square leg. Deep midwicket runs 20 yards to his left and slides to save the boundary.", "lands wide of mid-on who was backpedalling. Good length ball, slow and outside off. Looks to slice it over extra cover but it's off the toe and lobbed over the umpire.", "outside off, driven past extra cover and wide of long-off. Back for the second.", "full and sliding down leg, glanced easily to long leg.", "full on the pads, whipped away wide of deep midwicket. The batsman hustles back for the second.", "outside off, slashes and sends a thick outside edge past slip.", "full on off, possibly a slower ball, as the batsman swats it across the line to deep midwicket.", "angled in, but the line is too straight, allowing the batsman to tuck it away to the right of midwicket.", "good length - and not a half-volley - outside off, the batsman presses forward, drives on the up, and finds the gap at the covers.", "short ball outside off, think this one got stuck in the surface. The batsman shapes to pull but ends up short-arm flapping in the gap between midwicket and mid-on.", "banged short and outside off, rushes the batsman into a hook. He ends up miscuing it in the air, straight of mid-on.", "<strong>a misfield and he's way!</strong> A slip was on place for the batsman. This is back of a length and lifting away from outside off. He gets a thick outside edge and backward point lets it through.", "full toss outside off, driven to long-off's left. The fielder slides to his left and keeps it to two.", "nicely played. Doesn't lean in too much. This is still rising as he brings the bat down and taps it past extra cover on the front foot.", "back of a length into the pads, dabbed to fine leg with soft hands...and they come back for the second.", "punched wide of long-off off the back foot. And there won't be running issues with these two. Back for the second, easily.", "watches it on and thumps it gracefully past extra cover on the up. Sweeper cleans up.", "full at leg stump, flicked to fine leg's left.", "hares down the track and makes room outside leg, the bowler goes short on middle, scythed away towards deep point.", "too full and outside off, the batsman pushes it to the right of sweeper.");
	public $threes = array("shot. But superbly fielded. This was coming in at middle stump, handsomely pushed past the non-striker. It's running away to the long-on boundary but the fielder has hared across from mid-off. Puts in a huge dive to pull it back.", "<strong>in the air</strong> but he's safe. It's a slower ball, climbing on him from a length at off stump. He goes across the line but has to check his shot. Manages to chip it over midwicket.", "uses his feet, shorter on middle, punched to long-on. The fielder runs in and fumbles. The result is three.", "full and wide outside off, the batsman slices it in the air, the fielder races in from sweeper cover but the ball drops short of him.");
	public $fours = array("overpitched and outside off, just too full,and the batsman leans into a square-drive, drilling it to the left of backward point.", "full and angling in on off stump, the batsman does not have a big front-foot stride, but drives it between midwicket and mid-on.", "loose ball - short and wide outside off - the batsman extends his hands as far as he could and slashes a cut to the right of backward point, beats the man in the deep to his left.", "short and angled towards the corridor. Not so much room. But he goes for the cut. And he deposits it backward of point to beat third man's slide to the right.", "length at off stump again. He takes a small step down the track while getting the front leg out of the way to make room. Then he hits through the line, straight over the bowler.", "<strong>flies past the fielder's outstretched right hand</strong>. Ooooooo goes the crowd as they show the replay.", "do.not.bowl.short.to.him. A long-hop at middle stump, climbs into the like one. Slaps it flat towards the midwicket boundary.", "<strong>edged</strong> and four. Back of a length outside off, wafts at it away from his body and it flies wide of first slip.", "what a shot. Come to me, he says. I'm not going to hit you. The ball, dug into the pitch and rising in the corridor, is lured. It's coming into him, cutting out the angle for any shot. Then, at the last moment, the batsman springs a surprise. A bat, shaped like a ramp, deflects this over slip. Subtle, unlike his haircut.", "an aimless stab in the corridor from the batsman. A nothing shot. It's headed straight to the fielder but he sees the open face and starts moving to his right. Means he is already up as this flies low to his left. Would've taken it if he wasn't moving.", "full-toss on middle stump, the batsman sends it clattering over a dead-straight mid-on.", "short and down the leg side, Pace is your friend. Pace is your enemy. The batsman uses the pace to his advantage and helps it fine on the leg side for another boundary.", "slower, short ball outside off, from the back of the hand. The batsman reads the change-up. He manufactures all the pace and smacks it away past the right of midwicket.", "offers up a full-toss on middle, the batsman slog-sweeps it fiercely to the right of midwicket.", "back of a length and outside off, the batsman clears his front leg and clobbers it straight of mid-on who was inside the circle.", "hip-high short ball on the leg stump, the batsman sizes it up, picks it up with the wrists and launches it over short fine leg. Once-bounce four.", "anticipates the back-of-the-hand slower ball outside off, shuffles across off, takes a length ball from the stumps, and laps it away fine of the man in the deep.", "the batsman shuffled a long way across the off stump even before the bowler delivered the ball. Length ball outside off, paddled away stylishly to the fine-leg boundary.", "this is overpitched on off stump. His front leg's out of the way but the bat comes down nice and straight. Punched past mid-off.");
	public $sixes = array("Tossed up on middle, the batsman loves the flight. He gets under it and hoists it down the ground. The ball soars over long-off. Whoa! That hit the roof.", "on middle, the batsman unleases the smoothest of swings and sends the ball over the boundary.", "quicker and flatter on middle, it does not matter. The batsman still clears his front leg and clouts it straight of long-on who is in the deep now.", "Fetch that! The batsman thumps it over wide long-on. Clean bat swing. Beautiful to watch.", "clears his front leg and smites it over the wide long-on boundary.", "the batsman surges down the track, the bowler goes full and wide outside off. The batsman extends his hands and scythes it over extra cover for a six. Stunning shot.", "he doesn't need to run when he can launch it for sixes. This is vintage and the crowd soaks it in. He surges down the track, meets a length ball outside off and lifts it imperiously over long-off.", "good length at middle stump, not to the pitch with the slog sweep but he goes through. Gets under it, and lifts it over the midwicket boundary.", "oh, so clean! What a striker of the ball. This is in his slot. Nicely looped full at middle stump. A short stride to get to the pitch and clobbered flat into the sightscreen.", "uff! This is what they pay him for. Full one on leg stump. Slightly flat but he's down quickly. On one knee. Lifts it cleanly over the square leg boundary.", "ohhhhh yesss. Pushed through at leg stump. Down on one knee again. It's so close to him but he gets under and launches it over square leg.", "the batsman clears his front leg and clatters it into the sightscreen. Lofted straight down the ground.");
	public $wides = array("good length, sliding down leg side and past his attempted flick.", "full, floats down the leg side as the batsman falls over.", "wide of the crease and floated very wide outside off, well past the guideline, off-side wide.", "slower short ball outside off, past the wild swing, off-side wide.", "the direction is wrong from the bowler. He spears a back-of-a-length delivery down the leg side. Left alone.", "on a length outside off, goes away so very late, left alone. Past the tramline.", "short and down leg side, doesn't go after it.", "tempted to go after it outside off, but he decides against it. Wasn't very convincing but that was well above him and he gets away.");
	public $noballs = array("very big no-ball.", "the umpire checks the line and decides to call it.", "very marginal, tough call.", "is it on or over the line? The umpire thinks it is over.", "50-50 on the line but the umpire sides with the batting side.", "huge gap between the foot and the tram line. Guess the bowler missed his run-up.");
	public $notout = array("muffled appeal for an LBW, the umpire shakes his head.", "<strong>huge appeal</strong> for caught behind. The umpire stares and declares not out.", "is that a nick? The bowler appeals, but the keeper remains silent. Not out.", "<strong>struck on the pads</strong> and they all go up. The umpire says no.", "polite inquiry about the runout chance. No says the umpire.", "<strong>what's happened here?</strong> The bowler is claiming a wicket, the batsman has stood his ground. Not sure what they are appealing for. The umpire consults with square leg umpire and they decide it's not out.", "<strong>stumping chance</strong>, they have gone upstairs, not out comes back as the verdict.");	
	
	public function Match()
	{
		$this->CI = get_instance();
		$this->CI->load->model("Center");
		$this->CI->load->model("Team");
	}
		
	public function setMatchId($mid)
	{
		$this->match_id = $mid;
		$this->game_mode = $this->CI->Center->getGameMode($this->match_id);
		$this->teams = $this->CI->Center->getTeam1Team2($this->match_id);
	}

	public function setOpeningBatsman()
	{
		$this->batsmen = $this->CI->Center->getTeamBattingLineup($this->match_id, $this->batting_team_id);		
		$this->striker = $this->batsmen[$this->striker_index];
		$this->non_striker = $this->batsmen[$this->non_striker_index];
		$this->next_batsman_index = 2;
	}

	public function ballsToOvers($num)
	{
		if($num % 6 == 0)
		{
			return (floor($num/6) - 1).'.6';
		}
		else
		{
			return floor($num/6).'.'.floor($num % 6);
		}
	}

	/*
		* GET THE BOWLING OPTIONS FOR FIELDING TEAM
		* GENERATE DELIVERIES FOR EACH BOWLER
		* SET THE OPENING BATSMEN
		* SET THE OPENING BOWLER
	*/
	public function startInnings($innings)
	{
		$this->innings = $innings;
		if($innings == 'first')
		{
			$this->batting_team_id = $this->teams[0];
			$this->batting_team_label = $this->CI->Team->getTeamName($this->teams[0]);

			$this->bowling_team_id = $this->teams[1];
			$this->bowling_team_label = $this->CI->Team->getTeamName($this->teams[1]);
			$this->fielders = $this->CI->Center->getFielderNames($this->match_id, $this->teams[1]);			
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->bowling_team_id);
		}
		else if($innings == 'second')
		{

			$this->innings_completed = false;
			$this->innings_total = 0;
			$this->innings_wickets = 0;
			$this->innings_overs = "0.0";
			$this->innings_balls_bowled = 0;
			$this->innings_commentary = array();
			$this->innings_fow = array();
			$this->last_over_total = 0;
			$this->game_stage = "PP1";	
			$this->innings_noballs = 0;
			$this->innings_wides = 0;
			$this->innings_byes = 0;
			$this->innings_legbyes = 0;
			$this->partnership_runs = 0;
			$this->partnership_balls = 0;
			$this->partnerships = array();

			$this->striker_index = 0;
			$this->non_strike_index = 0;

			$this->batting_team_id = $this->teams[1];
			$this->batting_team_label = $this->CI->Team->getTeamName($this->teams[1]);

			$this->bowling_team_id = $this->teams[0];
			$this->bowling_team_label = $this->CI->Team->getTeamName($this->teams[0]);
			$this->fielders = $this->CI->Center->getFielderNames($this->match_id, $this->teams[0]);			
			$this->bowlers = $this->CI->Center->getBowlingOptions($this->match_id, $this->bowling_team_id);
		}

		foreach($this->bowlers as $index=>$array)
		{
			$points = $array['rating_points'];
			$role = $array['player_type'];
			$this->balls_per_bowler = 60;
			$this->generateDeliveries($index, $role, $points);	
		}

		$this->setOpeningBatsman();
		$this->current_bowler = $this->bowlers[$this->currently_bowling_index];	
		$this->Simulate();
	}

	public function Simulate()
	{		
		$number_of_deliveries = ($this->game_mode == 1 ? 300 : 120);		
		while($number_of_deliveries > 0)
		{
			if($this->game_mode == 1)
			{
				if($this->innings_balls_bowled <= 60)
				{
					$this->game_stage = "PP1";
				}
				else if($this->innings_balls_bowled > 240)
				{
					$this->game_stage = "PP2";
				}
				else
				{
					$this->game_stage = "MO";
				}
			}
			else
			{
				if($this->innings_balls_bowled <= 36)
				{
					$this->game_stage = "PP1";
				}
				else if($this->innings_balls_bowled > 90)
				{
					$this->game_stage = "PP2";
				}
				else
				{
					$this->game_stage = "MO";
				}
			}
			

			$last_delivery_index = (int) $this->bowlers[$this->currently_bowling_index]['last_ball_index'];
			$next_delivery_index = ($last_delivery_index + 1);
			if(isset($this->bowlers[$this->currently_bowling_index]['deliveries'][$next_delivery_index]))				
			{
				$ball_result = $this->bowlers[$this->currently_bowling_index]['deliveries'][$next_delivery_index];
			}
			else
			{
				$ball_result = "STOCK";
			}
			$this->bowlers[$this->currently_bowling_index]['last_ball_index'] = ((int) $this->bowlers[$this->currently_bowling_index]['last_ball_index'] + 1);
			
			if($ball_result == "WIDE" || $ball_result == "NOBALL")
			{
				if($ball_result == "WIDE")
				{
					$this->innings_wides = (int) $this->innings_wides + 1;
					$this->bowlers[$this->currently_bowling_index]['wides'] = (int) $this->bowlers[$this->currently_bowling_index]['wides'] + 1;
				}
				else if($ball_result == "NOBALL")
				{
					$this->innings_noballs = (int) $this->innings_noballs + 1;
					$this->bowlers[$this->currently_bowling_index]['noballs'] = (int) $this->bowlers[$this->currently_bowling_index]['noballs'] + 1;
				}
				$this->innings_total += 1;
				$this->bowlers[$this->currently_bowling_index]['runs'] = (int) $this->bowlers[$this->currently_bowling_index]['runs'] + 1;
				$result = $ball_result;
				$check_for_over = false;

				$this->partnership_balls += 1;
				$this->partnership_runs += 1;
			}
			else
			{	
				// is a legal delivery
				$this->bowlers[$this->currently_bowling_index]['legal_balls'] = ((int) $this->bowlers[$this->currently_bowling_index]['legal_balls'] + 1);
				$this->innings_balls_bowled = (int) $this->innings_balls_bowled + 1;
				$number_of_deliveries--;
				$this->partnership_balls += 1;

				$batsman_role = $this->striker['role'];
				$batsman_points = $this->batsmen[$this->striker_index]['bat'];
				$bowler_role = $this->bowlers[$this->currently_bowling_index]['player_type'];
				$bowler_points = $this->bowlers[$this->currently_bowling_index]['rating_points'];

				if($ball_result == "WICKET")
				{
					if($bowler_role == '2')
					{
						if($bowler_points >= $batsman_points)
						{
							$out = array("NOTOUT", "NOTOUT", "W", "NOTOUT", "W", "NOTOUT", "NOTOUT", "W", "NOTOUT", "W");
						}
						else
						{
							$out = array("NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "W", "NOTOUT", "W", "NOTOUT");
						}							
					}
					else if($bowler_role == '3')
					{
						if($bowler_points >= $batsman_points)
						{
							$out = array("NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT");
						}
						else
						{
							$out = array("NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "W", "NOTOUT");
						}
					}
					else if($bowler_role == '4')
					{
						if($bowler_points >= $batsman_points)
						{
							$out = array("NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "W", "NOTOUT");
						}
						else
						{
							$out = array("NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "W", "NOTOUT", "NOTOUT", "NOTOUT", "NOTOUT");
						}
					}
					$result = $out[mt_rand(0, (count($out) - 1))];

					if($result === "W")
					{

						// partnership result
						$player1 = $this->striker;
						$player2 = $this->non_striker;
						$this->calculatePartnership($player1, $player2);


						$this->out_how = $this->mode_of_dismissal[mt_rand(0, (count($this->mode_of_dismissal) - 1))];
						$this->innings_wickets = (int) $this->innings_wickets + 1;
						$this->innings_fow[] = $this->innings_wickets.'-'.$this->innings_total.' <strong>'.$this->batsmen[$this->striker_index]['name'].'</strong> ('.$this->ballsToOvers($this->innings_balls_bowled).')';
						
						if($this->out_how !== "Run Out")
						{
							$this->bowlers[$this->currently_bowling_index]['wickets'] = (int) $this->bowlers[$this->currently_bowling_index]['wickets'] + 1;	
						}
						else
						{
							$this->batsmen[$this->striker_index]['status'] = "Run Out";
						}

						if($this->out_how == 'Bowled')
						{
							$this->batsmen[$this->striker_index]['status'] = "b. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
						}
						else if($this->out_how == 'LBW')
						{
							$this->batsmen[$this->striker_index]['status'] = "lbw ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
						}
						else if($this->out_how == "Caught")
						{
							$caught_by = $this->getFielder();
							$this->batsmen[$this->striker_index]['status'] = "c. ".$this->shortName($caught_by)." b. ".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']);
						}
						
						if($this->innings_wickets == 10)
						{
							$this->innings_completed = true;
							$this->addToCommentary($this->innings_balls_bowled, $result, $ball_result);							
							break;
						}
					}
					else
					{											
						$this->batsmen[$this->striker_index]['status'] = "NOTOUT";
					}
					$this->batsmen[$this->striker_index]['balls'] = (int) $this->batsmen[$this->striker_index]['balls'] + 1;

					$this->bowlers[$this->currently_bowling_index]['wicket_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['wicket_balls'] + 1;
				}
				else
				{
					$this->batsmen[$this->striker_index]['status'] = "NOTOUT";
					$this->batsmen[$this->striker_index]['balls'] = (int) $this->batsmen[$this->striker_index]['balls'] + 1;

					// could be 0s,1s,2s,3s,4s,6s					

					/*
					1	=	PURE BATSMAN
					2	=	PURE BOWLER
					3	=	BATTING ALLROUNDER
					4	=	BOWLING ALLROUNDER
					5	=	WICKETKEEPER
					*/

					if($ball_result == 'BAD')
					{
						if($batsman_points >= $bowler_points)
						{
							if($batsman_role == 1 || $batsman_role == 5 || $batsman_role == 3)
							{
								if($this->game_stage == 'PP1')
								{
									$result = $this->boundaryChance('m');
								}
								else if($this->game_stage == 'MO')
								{
									$result = $this->boundaryChance('l');
								}
								else
								{
									$result = $this->boundaryChance('h');	
								}								
							}
							else if($batsman_role == 4)
							{
								$result = $this->boundaryChance('m');
							}
							else
							{
								$result = $this->boundaryChance('l');
							}
						}
						else
						{
							if($batsman_role == 1 || $batsman_role == 5 || $batsman_role == 3)
							{
								if($this->game_stage == 'PP2')
								{
									$result = $this->boundaryChance('m');
								}								
								else
								{
									$result = $this->boundaryChance('l');
								}								
							}							
							else
							{
								$result = $this->boundaryChance('l');
							}
						}
						$this->bowlers[$this->currently_bowling_index]['bad_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['bad_balls'] + 1;
					}
					else if($ball_result == 'GOOD')
					{
						if($batsman_points >= $bowler_points)
						{
							if($batsman_role == 1 || $batsman_role == 5)
							{
								$result = $this->conservativeApproach();
							}
							else if($batsman_role == 3)
							{
								$result = $this->defensiveApproach();
							}
							else
							{
								$result = $this->defensiveApproach();
							}
						}
						else
						{
							$result = $this->defensiveApproach();							
						}
						$this->bowlers[$this->currently_bowling_index]['good_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['good_balls'] + 1;
					}
					else if($ball_result == 'STOCK')
					{
						if($batsman_points >= $bowler_points)
						{
							if($batsman_role == 1 || $batsman_role == 5)
							{
								$result = $this->conservativeApproach();
							}
							else if($batsman_role == 3)
							{
								$result = $this->conservativeApproach();
							}
							else
							{
								$result = $this->defensiveApproach();
							}
						}
						else
						{
							if($batsman_role == 1 || $batsman_role == 5)
							{
								$result = $this->conservativeApproach();
							}
							else if($batsman_role == 3)
							{
								$result = $this->defensiveApproach();
							}
							else
							{
								$result = $this->defensiveApproach();
							}
						}

						$this->bowlers[$this->currently_bowling_index]['stock_balls'] = (int) $this->bowlers[$this->currently_bowling_index]['stock_balls'] + 1;
					}

					if($result == '4' || $result == '6')
					{
						
						if($result == '4')
						{
							$this->batsmen[$this->striker_index]['fours'] = ((int) $this->batsmen[$this->striker_index]['fours'] + 1);							
						}
						else if($result == '6')
						{							
							$this->batsmen[$this->striker_index]['sixes'] = ((int) $this->batsmen[$this->striker_index]['sixes'] + 1);							
						}
					}
					$this->batsmen[$this->striker_index]['runs'] += $result;
					$this->bowlers[$this->currently_bowling_index]['runs'] += $result;
					$this->innings_total = ((int) $this->innings_total + (int) $result);
					$this->partnership_runs += (int) $result;
				}
				$check_for_over = true;
			}
			
			
			$this->addToCommentary($this->innings_balls_bowled, $result, $ball_result);

			// change strike if single or three taken and it's not end of over
			if($check_for_over)
			{
				if(($result === 1 || $result === 3) && $this->bowlers[$this->currently_bowling_index]['legal_balls'] % 6 != 0)
				{				
					$non_strike_index = $this->non_striker['batting_index'];
					$this->striker_index = $non_strike_index;
					$temp = $this->striker;
					$this->striker = $this->non_striker;
					$this->non_striker = $temp;
					unset($temp);
				}
				else if($result === "W") // get new striker on the crease
				{
					$this->striker = $this->batsmen[$this->next_batsman_index];
					$this->next_batsman_index = (int) $this->next_batsman_index + 1;
					$this->striker_index = $this->striker['batting_index'];	
				}

				// over complete
				if($this->bowlers[$this->currently_bowling_index]['legal_balls'] > 0 && $this->bowlers[$this->currently_bowling_index]['legal_balls'] % 6 == 0)
				{
					// change strike if condition met (not out added because on last ball if wicket not taken, then too change strike)
					if($result === 'W' || $result === 0 || $result === 2 || $result === 4 || $result === 6 || $result === "NOT OUT")
					{
						$temp = $this->striker;
						$this->striker = $this->non_striker;
						$this->non_striker = $temp;
						unset($temp);
						$this->striker_index = $this->striker['batting_index'];	
					}

					$this->changeBowler();
				}
			}

			// checking if team batting second has achieved the target
			if($this->innings == 'second')
			{
				// match over
				if($this->win_score <= $this->innings_total)
				{
					$this->matchSummary("won");
					break;
				}
			}
			
		}

		// team did not get all out so get partnership details of last batting pair
		if($this->innings_wickets < 10)		
		{
			// partnership result
			$player1 = $this->non_striker;
			$player2 = $this->striker;
			$this->calculatePartnership($player1, $player2);
		}

		if($this->innings == "first")
		{
			$this->win_score = ($this->innings_total + 1);			
		}

		// match ended and it's a tie
		if($this->innings == 'second' && (($this->win_score - 1) == $this->innings_total))
		{
			$this->matchSummary("tie");
		}
		// match ended but team batting second didn't score the required runs
		else if($this->innings == 'second' && $this->win_score > $this->innings_total)
		{
			$this->matchSummary("lose");
		}				
	}

	public function matchSummary($result)
	{
		// team batting secon won (in wickets)
		if($result == "won")
		{
			$this->match_result['team_id'] = $this->batting_team_id;
			$this->match_result['team_label'] = $this->batting_team_label;
			$this->match_result['margin'] = (10 - $this->innings_wickets).' wickets with '.($this->game_mode == 1 ? 300 - $this->innings_balls_bowled : 120 - $this->innings_balls_bowled).' balls to spare.';
			$this->match_result['is_tie'] = 'NO';
		}
		// team batting first won (in runs)
		else if($result == "lose")
		{
			$this->match_result['team_id'] = $this->bowling_team_id;
			$this->match_result['team_label'] = $this->bowling_team_label;
			$this->match_result['margin'] = ($this->win_score - $this->innings_total - 1).' runs.';
			$this->match_result['is_tie'] = 'NO';
		}
		// it's a tie
		else if($result == "tie")
		{
			$this->match_result['is_tie'] = 'YES';
			$this->match_result['margin'] = "The match has ended in a tie. There is nothing separating the two teams.";
		}
	}

	public function calculatePartnership($player1, $player2)
	{
		if($player1['batting_index'] > $player2['batting_index'])
		{
			$between = $this->shortName($player2['name']).'/'.$this->shortName($player1['name']);							
		}
		else
		{
			$between = $this->shortName($player1['name']).'/'.$this->shortName($player2['name']);
		}
		$this->partnerships[] = array('between'=>$between, 'runs'=>$this->partnership_runs, 'balls'=>$this->partnership_balls);
		$this->partnership_runs = 0;
		$this->partnership_balls = 0;
	}

	public function getFielder()
	{
		$wicket_taking_bowler = $this->bowlers[$this->currently_bowling_index]['name'];
		$picked_name = $this->fielders[mt_rand(0, (count($this->fielders) - 1))];
		while($picked_name == $wicket_taking_bowler){
			$picked_name = $this->fielders[mt_rand(0, (count($this->fielders) - 1))];
		}
		return $picked_name;
	}

	public function addToCommentary($balls_bowled, $result, $type_of_ball)
	{
		if($balls_bowled % 6 == 0 && $result !== "WIDE" && $result !== "NOBALL")
		{
			$this->innings_overs = (floor($balls_bowled/6) - 1).".6";
			$over = true;
		}
		else
		{
			$this->innings_overs = floor($balls_bowled/6).".".floor($balls_bowled % 6);
			$over = false;
		}

		// delivery description		
		if($result == 0 || $result == 1 || $result == 2 || $result == 3)
		{			
			if($result == 0)
			{
				$text = "no run, ".$this->dots[mt_rand(0, (count($this->dots) - 1))];
			}
			else if($result == 1)
			{
				$text = "1 run, ".$this->singles[mt_rand(0, (count($this->singles) - 1))];
			}
			else
			{
				$text = $result." runs, ";
				if($result == 2)
				{
					$text .= $this->twos[mt_rand(0, (count($this->twos) - 1))];
				}
				else if($result == 3)
				{
					$text .= $this->threes[mt_rand(0, (count($this->threes) - 1))];
				}
			}
		}
		else if($result == 4 || $result == 6)
		{
			if($result == 4)
			{
				$text = "<strong>FOUR</strong>, ";
				$text .= $this->fours[mt_rand(0, (count($this->fours) - 1))];
			}
			else if($result == 6)
			{
				$text = "<strong class='green'>SIX</strong>, ";
				$text .= $this->sixes[mt_rand(0, (count($this->sixes) - 1))];
			}
		}
		else if($result === 'W')
		{
			$text = "<strong class='red'>WICKET</strong>";
		}
		else if($result === "WIDE" || $result === "NOBALL")
		{
			$text = $result.", ";
			if($result === "WIDE")
			{
				$text .= $this->wides[mt_rand(0, (count($this->wides) - 1))];
			}
			else
			{
				$text .= $this->noballs[mt_rand(0, (count($this->noballs) - 1))];
			}
		}
		else if($result === "NOTOUT")
		{
			$text = "no run, ";
			$text .= $this->notout[mt_rand(0, (count($this->notout) - 1))];
		}

		$this->innings_commentary[] = "<div class='clearfix comm_line'><div class='comm_over'>[".$this->innings_overs."]<span style='font-size:85%;' ng-show='data.debug'><br />".$type_of_ball."</span></div><div class='comm_desc'><em>".$this->shortName($this->bowlers[$this->currently_bowling_index]['name']).'</em> to <em>'.$this->shortName($this->batsmen[$this->striker_index]['name']).'</em>, '.$text."</div></div>";
		
		if($over)
		{
			if($this->innings_total - $this->last_over_total == 0)
			{
				$this->bowlers[$this->currently_bowling_index]['maidens'] = ( (int) $this->bowlers[$this->currently_bowling_index]['maidens'] + 1);
				// maiden
				$runs = 0;
				$this->last_over_total = $this->innings_total;
			}
			else
			{
				$runs = $this->innings_total - $this->last_over_total;
				$this->last_over_total = $this->innings_total;
			}

			$bowler_name = $this->bowlers[$this->currently_bowling_index]['name'];
			$bowler_balls = $this->bowlers[$this->currently_bowling_index]['legal_balls'];
			$bowler_runs = $this->bowlers[$this->currently_bowling_index]['runs'];
			$bowler_wickets = $this->bowlers[$this->currently_bowling_index]['wickets'];
			$bowler_maidens = $this->bowlers[$this->currently_bowling_index]['maidens'];
			$bowler_string = $bowler_name.str_repeat("&nbsp;", 8).floor($bowler_balls/6).'-'.$bowler_maidens.'-'.$bowler_runs.'-'.$bowler_wickets;

			$overs = floor($balls_bowled/6);
			$crr = number_format($this->innings_total/$overs, 2);
			$overs_remaining = $this->game_mode == 1 ? (50 - $overs) : (20 - $overs);
			if($this->innings == 'second')
			{
				$no_of_runs_required = $this->win_score - $this->innings_total;				
				$rrr = " | To Win: ".$no_of_runs_required." runs | RRR: ".number_format($no_of_runs_required/($overs_remaining == 0 ? 1 : $overs_remaining), 2);
			}
			else
			{
				$projected = floor($crr*($this->game_mode == 1 ? 50 : 20));
				$rrr = " | Projected: ".$projected." runs";
			}

			$this->innings_commentary[] = "<p class='comm_end_of_over bold'>End of ".$this->ordinal($overs)." over</p><p class='bold'>".$this->batting_team_label.": ".$this->innings_total.'/'.$this->innings_wickets.' | '.$runs.' runs | RR: '.$crr.$rrr.'<br />'.$bowler_string.'</p>';
		}
	}

	public function shortName($str)
	{
		if(! $str)
		{
			return "";
		}

		$ex = explode(" ", $str);
		if(count($ex) == 1)
		{
			return $ex[0];
		}
		else
		{
			$fn = $ex[0];
			$ln = end($ex);
			return $fn[0].'.'.$ln;
		}		
	}

	public function boundaryChance($chance)
	{
		if(! $chance || ! in_array($chance, array('h', 'm', 'l')))
		{
			return mt_rand(0, 3);
		}

		if($chance == 'h')
		{			
			$runs = array('4','0','4','6');
		}
		else if($chance == 'm')
		{
			$runs = array('1','2','4','1','4','6');
		}
		else if($chance == 'l')
		{
			$runs = array('0','1','0','1','2','0','2','0','4','0','6','0');
		}
		return $runs[array_rand($runs)];
	}

	public function aggresiveApproach()
	{
		if($this->game_stage == 'PP1')
		{			
			$input = array(1, 1, 0, 3, 0, 4, 1, 0, 1, 2);
		}
		else if($this->game_stage == 'MO')
		{			
			$input = array(1, 4, 0, 1, 6, 0, 1, 0, 1, 2);
		}
		else
		{			
			$input = array(0, 4, 6, 3, 2, 1, 6, 4, 2, 6);
		}
		return $input[rand(0, count($input) - 1)];
	}

	public function conservativeApproach()
	{
		if($this->game_stage == 'PP1')
		{			
			$input = array(2, 1, 0, 3, 1, 4, 1, 0, 1, 1);
		}
		else if($this->game_stage == 'MO')
		{			
			$input = array(0, 0, 1, 3, 0, 1, 0, 2, 0, 1);
		}
		else
		{			
			$input = array(2, 2, 4, 3, 1, 0, 3, 2, 0, 1);
		}
		return $input[rand(0, count($input) - 1)];
	}

	public function defensiveApproach()
	{
		if($this->game_stage == 'PP1')
		{			
			$input = array(1, 0, 0, 0, 1, 0, 1, 0, 1, 0);
		}
		else if($this->game_stage == 'MO')
		{			
			$input = array(1, 0, 0, 1, 0, 0, 1, 0, 0, 0);
		}
		else
		{			
			$input = array(1, 0, 1, 2, 1, 1, 0, 0, 1, 2);
		}
		return $input[rand(0, count($input) - 1)];
	}

	public function oneOrZero()
	{
		$input = array(0,0,1,0,1,0,1,0,0,1,0,0,1,0,1,0,0,1,0,0,0,0,1,0);
		return $input[rand(0, count($input) - 1)];
	}

	public function oneOrTwo()
	{
		$input = array(1,1,1,1,1,2,1,2,1,1,1,1,1,1,1,2,1,1,1,1,1,1,1,1,1,2);
		return $input[rand(0, count($input) - 1)];
	}

	public function zeroOneTwo()
	{
		$input = array(0,1,0,0,0,2,0,0,0,0,1,0,0,0,0,0,1,0,0,0,0,0,0,2,0,0,2);
		return $input[rand(0, count($input) - 1)];
	}

	public function oneTwoThree()
	{
		$input = array(1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3,1,2,3);
		return $input[rand(0, count($input) - 1)];
	}	

	public function fourOrSix()
	{
		$input = array(4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6,4,6);
		return $input[rand(0, count($input) - 1)];
	}

	public function changeBowler()
	{
		$bowling_options_number = count($this->bowlers);
		if($bowling_options_number > 5)
		{
			$this->part_timers_available = true;
			$this->part_timers_count = count($this->bowlers) - 5;
		}

		$overs_bowled = floor($this->innings_balls_bowled/6);
		$current_index = $this->currently_bowling_index;

		// time to get a new pair of bowlers bowler
		if(in_array($overs_bowled, array(6,12,18,24,30,36,42,48,54)))
		{			
			
		}
		else // continue with same pair
		{
			
		}

		
		$new_index = (int) $current_index + 1;

		if($new_index <= 4)
		{
			$this->current_bowler = $this->bowlers[$new_index];
			$this->currently_bowling_index = $new_index;
		}
		else
		{			
			$this->current_bowler = $this->bowlers[0];
			$this->currently_bowling_index = 0;
		}		
	}
	
	public function getMatchId()
	{
		return $this->match_id;
	}
	
	public function generateDeliveries($index, $role, $points)
	{
		$wicket_taking_ball_count = $this->wicketDeliveryCount($role, $points);
		$good_ball_count = $this->goodDeliveryCount($role, $points);
		$bad_ball_count = $this->badDeliveryCount($role, $points);
		$extra_ball_count = $this->extraDeliveryCount($role, $points);
		
		if($this->game_mode == 1)
		{
			$balls_bank = range(1, 60);
		}
		else
		{
			$balls_bank = range(1, 24);
		}
		
		
		$wicket_taking_balls = array();
		for($k = 1; $k <= $wicket_taking_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$wicket_taking_balls[] = array_pop($balls_bank);
		}

		$good_delivery_balls = array();
		for($k = 1; $k <= $good_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$good_delivery_balls[] = array_pop($balls_bank);
		}

		$bad_delivery_balls = array();
		for($k = 1; $k <= $bad_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$bad_delivery_balls[] = array_pop($balls_bank);
		}

		$extra_delivery_balls = array();		
		for($k = 1; $k <= $extra_ball_count; $k++)
		{
			shuffle($balls_bank);
    		$extra_delivery_balls[] = array_pop($balls_bank);
		}


		$deliveries = array();
		for($i = 1; $i <= $this->balls_per_bowler; $i++)
		{
			$extra_balls_to_be_bowled = false;
			if(in_array($i, $wicket_taking_balls))
			{
				$deliveries[$i] = "WICKET";
			}
			else if(in_array($i, $good_delivery_balls))
			{
				$deliveries[$i] = "GOOD";
			}
			else if(in_array($i, $bad_delivery_balls))
			{
				$deliveries[$i] = "BAD";
			}
			else if(in_array($i, $extra_delivery_balls))
			{
				$possibility = array("WIDE", "NOBALL","WIDE", "NOBALL","WIDE", "NOBALL","WIDE", "NOBALL","WIDE", "NOBALL");
				$result = $possibility[mt_rand(0, count($possibility) - 1)];				
				$deliveries[$i] = $result;
				$extra_balls_to_be_bowled = true;
			}
			else
			{				
				$deliveries[$i] = "STOCK";
			}
			if($extra_balls_to_be_bowled)
			{
				$this->balls_per_bowler++;
			}
		}
		$this->bowlers[$index]['deliveries'] = $deliveries;		
	}

	
	/* 
		* This function decides how many potential wicket taking deliveries will a bowler bowl
		* Adjust the values in this function if wickets are more frequent or less frequent
	*/
	public function wicketDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 50)
		{

			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(0, 3);
				}
				else
				{
					$percent = mt_rand(3, 4);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(10, 15);
				}
				else
				{
					$percent = mt_rand(5, 10);
				}
			}
		}
		else if($points >= 50 && $points < 75)
		{

			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(4, 6);
				}
				else
				{
					$percent = mt_rand(3, 4);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(15, 20);
				}
				else
				{
					$percent = mt_rand(10, 15);
				}
			}
			
		}
		else if($points >= 75 && $points < 90)
		{
			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(5, 8);
				}
				else
				{
					$percent = mt_rand(4, 6);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(20, 25);
				}
				else
				{
					$percent = mt_rand(15, 20);
				}
			}
			
		}
		else
		{
			if($this->game_mode == 1)
			{				
				if($role == '2')
				{
					$percent = mt_rand(6, 10);
				}
				else
				{
					$percent = mt_rand(5, 8);
				}
			}
			else
			{				
				if($role == '2')
				{
					$percent = mt_rand(25, 30);
				}
				else
				{
					$percent = mt_rand(20, 25);
				}
			}
			
		}

		return ($this->game_mode == 1 ? ceil($percent*60/100) : ceil($percent*24/100));
	}

	public function extraDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 50)
		{
			if($role == '2')
			{
				$balls = 6;
			}
			else
			{
				$balls = 7;
			}			
		}
		else if($points >= 50 && $points < 75)
		{
			if($role == '2')
			{
				$balls = 4;
			}
			else
			{
				$balls = 5;
			}
		}
		else if($points >= 75 && $points < 90)
		{
			if($role == '2')
			{
				$balls = mt_rand(0, 3);
			}
			else
			{
				$balls = mt_rand(0, 4);
			}
		}
		else
		{
			if($role == '2')
			{
				$balls = mt_rand(0, 1);
			}			
			else
			{
				$balls = mt_rand(0, 2);
			}
		}

		return $balls;
	}

	public function badDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 50)
		{
			if($role == '2')
			{
				$percent = mt_rand(30, 40);
			}
			else
			{
				$percent = mt_rand(35, 45);
			}			
		}
		else if($points >= 50 && $points < 75)
		{
			if($role == '2')
			{
				$percent = mt_rand(20, 30);
			}
			else
			{
				$percent = mt_rand(25, 35);
			}
		}
		else if($points >= 75 && $points < 90)
		{
			if($role == '2')
			{
				$percent = mt_rand(10, 20);
			}
			else
			{
				$percent = mt_rand(15, 25);
			}
		}
		else
		{
			if($role == '2')
			{
				$percent = mt_rand(0, 10);
			}			
			else
			{
				$percent = mt_rand(5, 15);
			}
		}

		return ($this->game_mode == 1 ? ceil($percent*60/100) : ceil($percent*24/100));	
	}


	public function goodDeliveryCount($role, $points)
	{
		if($points >=0 && $points < 50)
		{
			if($role == '2')
			{
				$percent = mt_rand(0, 5);
			}
			else
			{
				$percent = 0;
			}			
		}
		else if($points >= 50 && $points < 75)
		{
			if($role == '2')
			{
				$percent = mt_rand(5, 15);
			}
			else
			{
				$percent = mt_rand(0, 10);
			}
		}
		else if($points >= 75 && $points < 90)
		{
			if($role == '2')
			{
				$percent = mt_rand(15, 25);
			}
			else
			{
				$percent = mt_rand(10, 20);
			}
		}
		else
		{
			if($role == '2')
			{
				$percent = mt_rand(25, 35);
			}			
			else
			{
				$percent = mt_rand(20, 30);
			}
		}

		return ($this->game_mode == 1 ? floor($percent*60/100) : floor($percent*24/100));
	}

	public function ordinal($number) {
	    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
	    if ((($number % 100) >= 11) && (($number%100) <= 13))
	        return $number. 'th';
	    else
	        return $number. $ends[$number % 10];
	}
}