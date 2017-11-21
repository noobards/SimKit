<div ng-controller="beginMatch">

<div class="modal fade" id="editPlayerRatingModal">
  <div class="modal-dialog" style="width: 90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Edit the ratings</h4>
      </div>
      <div class="modal-body">
      	<div class="ajax-loading" style="width: 80%; margin: auto; height: 100px;" ng-hide="data.showPlayers">
      	</div>
        <div class="row" ng-show="data.showPlayers">
        	<div class="col-md-6">
        		<div class="alert alert-success text-center mb0"><strong>{{edit.home.name}}</strong></div>
        		<div class="table-mockup">
        			<div class="thead">
        				<div class="tr">
        					<div class="th">Name</div>
        					<div class="th">Bat</div>
        					<div class="th">Bowl</div>
        					<div class="th">Nature</div>
        				</div>        				
        			</div>
        			<div class="tbody">
    					<div class="tr" ng-repeat="p in edit.home.players">
    						<div class="td">{{p.name}}</div>
    						<div class="td"><input type="text" ng-model="save[p.id]['bat']" class="form-control" /></div>
    						<div class="td"><input type="text" ng-model="save[p.id]['bowl']" class="form-control" /></div>
    						<div class="td">
    							<select class="form-control" ng-model="save[p.id]['ment']">
    								<option value="1">Aggressive</option>
    								<option value="2">Moderate</option>
    								<option value="3">Defensive</option>
    							</select>
    						</div>
    					</div>
    				</div>
        		</div>
        	</div>
        	<div class="col-md-6">
        		<div class="alert alert-success text-center mb0"><strong>{{edit.away.name}}</strong></div>
        		<div class="table-mockup">
        			<div class="thead">
        				<div class="tr">
        					<div class="th">Name</div>
        					<div class="th">Bat</div>
        					<div class="th">Bowl</div>
        					<div class="th">Nature</div>
        				</div>        				
        			</div>
        			<div class="tbody">
    					<div class="tr" ng-repeat="p in edit.away.players">
    						<div class="td">{{p.name}}</div>
    						<div class="td"><input type="text" ng-model="save[p.id]['bat']" class="form-control" /></div>
    						<div class="td"><input type="text" ng-model="save[p.id]['bowl']" class="form-control" /></div>
    						<div class="td">
    							<select class="form-control" ng-model="save[p.id]['ment']">
    								<option value="1">Aggressive</option>
    								<option value="2">Moderate</option>
    								<option value="3">Defensive</option>
    							</select>
    						</div>
    					</div>
    				</div>
        		</div>
        	</div>
        </div>
      </div>
      <div class="modal-footer" style="text-align: center;">
        <button type="button" class="btn btn-default" ng-show="data.showPlayers" data-dismiss="modal">Close</button>
        <button type="button" ng-click="updateRatings($event)" ng-show="data.showPlayers" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="copyCommentary">
  <div class="modal-dialog" style="width: 90%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Copy the text</h4>
      </div>
      <div class="modal-body">      	
        <textarea style="font-family: MONOSPACE; overflow-x:auto;" class="form-control" id="clipboard" rows="12" ng-model="clipboard"></textarea>
      </div>
      <div class="modal-footer" style="text-align: center;">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>        
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row site-breadcrumbs">
	<div class="col-sm-10">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter/PreMatch/<?php echo $this->uri->segment(3); ?>">PreMatch</a> <i class="fa fa-chevron-right">&nbsp;</i> <?php echo $data['home_label'].'&nbsp;&nbsp;&nbsp;v/s&nbsp;&nbsp;&nbsp;'.$data['away_label']; ?>
	</div>
	<div class="col-sm-2 text-right">
		<?php
		if($data['status'] == "OK")
		{
			echo '<a data-mid="'.$data['mid'].'" style="font-size:80%; color:#333;" href="#" data-toggle="modal" data-target="#editPlayerRatingModal" ng-click="editRatings($event)">Edit Player Ratings</a> | ';
			echo '<a style="font-size:80%; color:#333;" href="#" ng-click="debug($event)">{{data.debug_text}}</a>';
		}?>
	</div>
</div>
	
<?php
if($data['status'] == "OK")
{		
?>

	<div class="row">
		<?php
		$batsmen = $data['first_batsmen'];
		$bowlers = $data['first_bowlers'];	
		?>
		<div class="col-md-8">
			<div class="text-center">
				<div><strong><?php echo $data['match_length']; ?></strong> overs match</div>
				<div>at <strong><?php echo $data['ground']; ?></strong></div>
				<div>on <?php echo date('F d, Y'); ?></div>
			</div>
			<div class="alert alert-success mb0 text-center bold"><?php echo $data['first_batting_label']; ?> Batting Scorecard</div>
			<div class="table-mockup">
				<div class="thead">
					<div class="tr">
						<div class="th">Player</div>
						<div class="th">Status</div>
						<div class="th">Runs</div>					
						<div class="th">4/6</div>					
						<div class="th">SR</div>
					</div>
				</div>
				<div class="tbody first-bat">
					<?php foreach($batsmen as $ary)
					{	
						$css_class = ($ary['status'] == 'NOTOUT' ? 'highlight' : 'unhilight' );
						?>
					<div class="tr <?php echo $css_class; ?>">
						<div class="td"><span class="for-comm"><?php echo $ary['name']; ?></span> <span ng-show="data.debug"><img class="role-icon" ng-src="<?php echo base_url().'/assets/images/icons/'.$ary['mentality_icon']; ?>" alt="<?php echo $ary['mentality']; ?>" title="<?php echo $ary['mentality']; ?>" />(<?php echo $ary['bat']; ?>)</span></div>
						<div class="td"><span class="for-comm"><?php echo $ary['status'] == "DNB" ? "" : ( $ary['status'] == "NOTOUT" ? "" : $ary['status']); ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['status'] == "DNB" ? "" : $ary['runs'].' ('.$ary['balls'].')'; ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['status'] == "DNB" ? "" : $ary['fours'].'/'.$ary['sixes']; ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['balls'] > 0 ? number_format(($ary['runs']*100/$ary['balls']), 2) : ""; ?></span></div>
					</div>
					<?php
					}				
				?>
					<div class="tr">
						<div class="td text-right">
							<strong><span class="for-comm">Total: <?php echo $data['first_total'].'/'.$data['first_wickets'].' in '.$data['first_overs'].' overs ('.$data['first_run_rate'].' rpo)'; ?></span></strong>
						</div>
					</div>
				</div>
			</div>

			<div class="alert alert-success mb0 text-center bold"><?php echo $data['first_bowling_label']; ?> Bowling Scorecard</div>
			<div class="table-mockup">
				<div class="thead">
					<div class="tr">
						<div class="th">Player</div>
						<div class="th">Overs</div>
						<div class="th">Maidens</div>
						<div class="th">Runs</div>
						<div class="th">Wickets</div>
						<div class="th">Economy</div>					
					</div>
				</div>
				<div class="tbody first-bowl">
					<?php foreach($bowlers as $ary)
					{
						if($ary['legal_balls'] > 0)
						{
						?>
					<div class="tr">
						<div class="td">
						<span class="for-comm"><?php echo $ary['name']; ?></span> <span ng-show="data.debug">(<?php echo $ary['rating_points']; ?>)</span>
							<div ng-show="data.debug.hide">
								<span style="font-size: 70%;"><strong>Ext:</strong> <?php echo $ary['noballs'] + $ary['wides']; ?> | <strong>GB:</strong> <?php echo $ary['good_balls']; ?> | <strong>BB:</strong> <?php echo $ary['bad_balls']; ?> | <strong>WB:</strong> <?php echo $ary['wicket_balls']; ?></span>
							</div>
						</div>
						<div class="td text-center">
						<span class="for-comm"><?php
							echo floor($ary['legal_balls']/6).'.'.floor($ary['legal_balls'] % 6);
							?></span>
						</div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['maidens'] ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['runs'] ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['wickets']; ?></span></div>
						<div class="td text-center">
						<span class="for-comm"><?php
								$econ = $ary['runs']*6/$ary['legal_balls'];
								echo number_format($econ, 2);
							?></span>
						</div>
					</div>
					<?php
						}
					}
				?>
				</div>
			</div>
			<?php
				if(count($data['first_fow']) > 0)
				{
					echo '<div class="fow">';
					echo '<strong>FOW: </strong>';
					foreach($data['first_fow'] as $line)
					{
						echo '<span>'.$line.'</span>';
					}
					echo '</div>';
				}
			?>

			<div class="alert alert-success mb0 top10 text-center"><strong><?php echo $data['first_batting_label']; ?> Parnerships</strong></div>
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>Players</th>
						<th>&nbsp;</th>
						<th>Runs (Balls)</th>
					</tr>
				</thead>
				<tbody>
					<?php					
						foreach($data['first_partnerships'] as $ary)
						{
							$percent = ceil($ary['runs']*100/$data['first_max_partnership']);
							echo '<tr>';
								echo '<td style="width:200px;">'.$ary['between'].'</td>';
								echo '<td style="padding-right:10px;"><div style="width:'.$percent.'%" class="partnership_bar"></div></td>';
								echo '<td style="width:100px;">'.$ary['runs'].' ('.$ary['balls'].')</td>';
							echo '</tr>';
						}
					?>
				</tbody>
			</table>
		</div>
		<div class="col-md-4">
			<div class="alert alert-success mb0 text-center bold">Innings Commentary</div>
			<div class="commentary 1st" style="height: 850px; overflow-y: scroll;">
				<?php foreach($data['first_commentary'] as $line)
				{
					echo $line;
				}
				?>
			</div>
		</div>
	</div>


	<!-- SECOND INNINGS 	-->
	<div class="row top10" style="padding-bottom: 50px;">
		<div class="col-md-12">
			<div class="alert alert-danger text-center">
				<h2 class="match_result_title"><strong><?php echo $data['second_batting_label']; ?></strong> require <strong><?php echo $data['to_win']; ?></strong> runs to win in <strong><?php echo $data['in_overs']; ?></strong> overs at <strong><?php echo $data['rrr']; ?> runs per over</strong></h2>
			</div>

			<div class="text-center">
				<button class="btn btn-primary" ng-click="startSecondInnings($event)">Simulate 2nd Innings</button>				
			</div>
		</div>
	</div>
	<div class="row" id="secondInnings" ng-show="data.showSecondInningsScorecard">
		<?php
		$batsmen = $data['second_batsmen'];
		$bowlers = $data['second_bowlers'];	
		?>
		<div class="col-md-8">		
			<div class="alert alert-success mb0 text-center bold"><?php echo $data['second_batting_label']; ?> Batting Scorecard</div>
			<div class="table-mockup">
				<div class="thead">
					<div class="tr">
						<div class="th">Player</div>
						<div class="th">Status</div>
						<div class="th">Runs</div>					
						<div class="th">4/6</div>					
						<div class="th">SR</div>
					</div>
				</div>
				<div class="tbody second-bat">
					<?php foreach($batsmen as $ary)
					{	
						$css_class = ($ary['status'] == 'NOTOUT' ? 'highlight' : 'unhilight' );
						?>
					<div class="tr <?php echo $css_class; ?>">
						<div class="td"><span class="for-comm"><?php echo $ary['name']; ?></span> <span ng-show="data.debug"><img class="role-icon" ng-src="<?php echo base_url().'/assets/images/icons/'.$ary['mentality_icon']; ?>" alt="<?php echo $ary['mentality']; ?>" title="<?php echo $ary['mentality']; ?>" />(<?php echo $ary['bat']; ?>)</span></div>
						<div class="td"><span class="for-comm"><?php echo $ary['status'] == "DNB" ? "" : ( $ary['status'] == "NOTOUT" ? "" : $ary['status']); ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['status'] == "DNB" ? "" : $ary['runs'].' ('.$ary['balls'].')'; ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['status'] == "DNB" ? "" : $ary['fours'].'/'.$ary['sixes']; ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['balls'] > 0 ? number_format(($ary['runs']*100/$ary['balls']), 2) : ""; ?></span></div>
					</div>
					<?php
					}				
				?>
					<div class="tr">
						<div class="td text-right">
							<strong><span class="for-comm">Total: <?php echo $data['second_total'].'/'.$data['second_wickets'].' in '.$data['second_overs'].' overs ('.$data['second_run_rate'].' rpo)'; ?></span></strong>
						</div>
					</div>
				</div>
			</div>

			<div class="alert alert-success mb0 text-center bold"><?php echo $data['second_bowling_label']; ?> Bowling Scorecard</div>
			<div class="table-mockup">
				<div class="thead">
					<div class="tr">
						<div class="th">Player</div>
						<div class="th">Overs</div>
						<div class="th">Maidens</div>
						<div class="th">Runs</div>
						<div class="th">Wickets</div>
						<div class="th">Economy</div>					
					</div>
				</div>
				<div class="tbody second-bowl">
					<?php foreach($bowlers as $ary)
					{
						if($ary['legal_balls'] > 0)
						{
						?>
					<div class="tr">
						<div class="td">
							<span class="for-comm"><?php echo $ary['name']; ?></span> <span ng-show="data.debug">(<?php echo $ary['rating_points']; ?>)</span>
							<div ng-show="data.debug.hide">
								<span style="font-size: 70%;"><strong>Ext:</strong> <?php echo $ary['noballs'] + $ary['wides']; ?> | <strong>GB:</strong> <?php echo $ary['good_balls']; ?> | <strong>BB:</strong> <?php echo $ary['bad_balls']; ?> | <strong>WB:</strong> <?php echo $ary['wicket_balls']; ?></span>
							</div>
						</div>
						<div class="td text-center">
						<span class="for-comm"><?php
							echo floor($ary['legal_balls']/6).'.'.floor($ary['legal_balls'] % 6);
							?></span>
						</div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['maidens'] ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['runs'] ?></span></div>
						<div class="td text-center"><span class="for-comm"><?php echo $ary['wickets']; ?></span></div>
						<div class="td text-center">
						<span class="for-comm"><?php
								$econ = $ary['runs']*6/$ary['legal_balls'];
								echo number_format($econ, 2);
							?></span>
						</div>
					</div>
					<?php
						}
					}
				?>
				</div>
			</div>
			<?php
				if(count($data['second_fow']) > 0)
				{
					echo '<div class="fow">';
					echo '<strong>FOW: </strong>';
					foreach($data['second_fow'] as $line)
					{
						echo '<span>'.$line.'</span>';
					}
					echo '</div>';
				}
			?>

			<div class="alert alert-success mb0 top10 text-center"><strong><?php echo $data['second_batting_label']; ?> Parnerships</strong></div>
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>Players</th>
						<th>&nbsp;</th>
						<th>Runs (Balls)</th>
					</tr>
				</thead>
				<tbody>
					<?php					
						foreach($data['second_partnerships'] as $ary)
						{
							$percent = ceil($ary['runs']*100/$data['second_max_partnership']);
							echo '<tr>';
								echo '<td style="width:200px;">'.$ary['between'].'</td>';
								echo '<td style="padding-right:10px;"><div style="width:'.$percent.'%" class="partnership_bar"></div></td>';
								echo '<td style="width:100px;">'.$ary['runs'].' ('.$ary['balls'].')</td>';
							echo '</tr>';
						}
					?>
				</tbody>
			</table>
		</div>
		<div class="col-md-4">
			<div class="alert alert-success mb0 text-center bold">Innings Commentary</div>
			<div class="commentary 2nd" style="height: 850px; overflow-y: scroll;">
				<?php foreach($data['second_commentary'] as $line)
				{
					echo $line;
				}
				?>
			</div>
		</div>
	</div>

	<div class="row top10" ng-show="data.showSecondInningsScorecard">
		<div class="col-md-12">
			<div class="alert alert-danger text-center">
				<h2 class="match_result_title comm_result"><?php
						if($data['result']['is_tie'] == 'YES')
						{
							echo $data['result']['margin'];
						}
						else
						{
							echo '<strong>'.$data['result']['team_label'].'</strong> have won by <strong>'.$data['result']['margin'].'</strong>';
						}
					?>	
				</h2>
			</div>
		</div>

		<div class="col-md-12 text-center" style="padding-bottom: 30px;">
			<button ng-click="resimulate($event)" class="btn btn-primary">Resimulate Match</button>&nbsp;<button class="btn btn-warning" ng-click="copyScorecard($event)">Copy Scorecard</button><br /><br />
			<button class="btn btn-danger" ng-click="copyCommentary($event, '1st')">Copy 1st Innings Commentary</button>&nbsp;<button class="btn btn-danger" ng-click="copyCommentary($event, '2nd')">Copy 2nd Innings Commentary</button>			
		</div>
	</div>	
</div>
<?php	
}
else
{	?>
	<div class="row">
		<div class="col-md-12 text-center">
			<div class="alert alert-danger"><i class="fa fa-warning">&nbsp;</i><?php echo $data['msg']; ?></div>
		</div>
	</div>
<?php	
}
?>


	



<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/begin_match.js?v=2"></script>