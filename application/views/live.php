<div ng-controller="liveMatch">

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
  <input type="hidden" id="onload_mid" value="<?php echo $data['mid']; ?>" />
  <input type="hidden" id="onload_delay" value="<?php echo $data['delay']; ?>" />
	<div class="row">		
		<div class="col-md-8">
			<div class="text-center">
				<div><strong><?php echo $data['match_length']; ?></strong> overs match</div>
				<div>at <strong><?php echo $data['ground']; ?></strong></div>
				<div>on <?php echo date('F d, Y'); ?></div>
			</div>
			<div class="alert alert-success mb0 text-center bold">{{live.first_batting_label}} Batting Scorecard</div>
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
					<div class="tr" data-id="{{bat.player_id}}" ng-repeat="bat in live.first_batting_order">
						<div class="td"><span class="for-comm">{{bat.name}}</span> <span ng-show="data.debug"><img class="role-icon" ng-src="<?php echo base_url(); ?>/assets/images/icons/{{bat.mentality_icon}}" alt="{{bat.mentality}}" title="{{bat.mentality}}" />({{bat.bat}})</span></div>
						<div class="td"><span class="for-comm">{{live[bat.player_id].status}}</span></div>
						<div class="td text-center"><span class="for-comm">{{live[bat.player_id].runsballs}}</span></div>
						<div class="td text-center"><span class="for-comm">{{live[bat.player_id].boundaries}}</span></div>
						<div class="td text-center"><span class="for-comm">{{live[bat.player_id].sr}}</span></div>
					</div>
					<div class="tr">
						<div class="td text-right">
							<strong><span class="for-comm">Total: {{live.first_innings.total}}/{{live.first_innings.wickets}} in {{live.first_innings.overs}} ({{live.first_innings.run_rate}})</span></strong>
						</div>
					</div>
				</div>
			</div>

			<div class="alert alert-success mb0 text-center bold">{{live.first_bowling_label}} Bowling Scorecard</div>
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
					 <div class="tr" data-id="{{bowl.player_id}}" ng-show="$index <= 4" ng-repeat="bowl in live.first_bowling_order">
              <div class="td"><span class="for-comm">{{bowl.name}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].overs_bowled}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].maidens}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].runs_conceded}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].wickets_taken}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].econ}}</span></div>
           </div>
				</div>
			</div>

      <div class="fow">
          <strong>FOW: </strong> <span style="margin-right: 15px;" ng-repeat="fw in live.first_innings.fow">{{fw}}</span>
      </div>

      <div ng-show="live.first_innings_completed" class="row top10" style="padding-bottom: 50px;">
        <div class="col-md-12">

          <div class="alert alert-danger text-center">
            <h2 class="match_result_title"><strong>{{live.first_bowling_label}}</strong> require <strong>{{live.to_win}}</strong> runs to win in <strong>{{live.in_overs}}</strong> overs at <strong>{{live.rrr}}</strong> runs per over</h2>
          </div>

          <div class="text-center">
            <button class="btn btn-primary" ng-click="startSecondInnings($event)">Simulate 2nd Innings</button>
          </div>
        </div>
      </div>
			
		</div>
		<div class="col-md-4">
			<div class="alert alert-success mb0 text-center bold">Innings Commentary</div>
			<div class="commentary 1st" style="height: 850px; overflow-y: scroll;">
				
			</div>
		</div>
	</div>


	<!-- SECOND INNINGS 	-->
	<div class="row" ng-show="data.showSecondInningsScorecard" id="secondInnings">    
    <div class="col-md-8">      
      <div class="alert alert-success mb0 text-center bold">{{live.second_batting_label}} Batting Scorecard</div>
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
          <div class="tr" data-id="{{bat.player_id}}" ng-repeat="bat in live.second_batting_order">
            <div class="td"><span class="for-comm">{{bat.name}}</span> <span ng-show="data.debug"><img class="role-icon" ng-src="<?php echo base_url(); ?>/assets/images/icons/{{bat.mentality_icon}}" alt="{{bat.mentality}}" title="{{bat.mentality}}" />({{bat.bat}})</span></div>
            <div class="td"><span class="for-comm">{{live[bat.player_id].status}}</span></div>
            <div class="td text-center"><span class="for-comm">{{live[bat.player_id].runsballs}}</span></div>
            <div class="td text-center"><span class="for-comm">{{live[bat.player_id].boundaries}}</span></div>
            <div class="td text-center"><span class="for-comm">{{live[bat.player_id].sr}}</span></div>
          </div>
          <div class="tr">
            <div class="td text-right">
              <strong><span class="for-comm">Total: {{live.second_innings.total}}/{{live.second_innings.wickets}} in {{live.second_innings.overs}} ({{live.second_innings.run_rate}})</span></strong>
            </div>
          </div>
        </div>
      </div>

      <div class="alert alert-success mb0 text-center bold">{{live.second_bowling_label}} Bowling Scorecard</div>
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
           <div class="tr" data-id="{{bowl.player_id}}" ng-show="$index <= 4" ng-repeat="bowl in live.second_bowling_order">
              <div class="td"><span class="for-comm">{{bowl.name}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].overs_bowled}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].maidens}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].runs_conceded}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].wickets_taken}}</span></div>
              <div class="td text-center"><span class="for-comm">{{live[bowl.player_id].econ}}</span></div>
           </div>
        </div>
      </div>

      <div class="fow">
          <strong>FOW: </strong> <span style="margin-right: 15px;" ng-repeat="fw in live.second_innings.fow">{{fw}}</span>
      </div>

      <div ng-show="live.second_innings_completed" class="row top10" style="padding-bottom: 50px;">
        <div class="col-md-12">

          <div class="alert alert-danger text-center">
            <h2 class="match_result_title comm_result"><strong>{{live.winning_team_label}}</strong> have won by <strong>{{live.winning_margin}}</strong></h2>
          </div>

          <div class="text-center">
              <button ng-click="resimulate($event)" class="btn btn-primary">Resimulate Match</button>&nbsp;<button class="btn btn-warning" ng-click="copyScorecard($event)">Copy Scorecard</button><br /><br />
              <button class="btn btn-danger" ng-click="copyCommentary($event, '1st')">Copy 1st Innings Commentary</button>&nbsp;<button class="btn btn-danger" ng-click="copyCommentary($event, '2nd')">Copy 2nd Innings Commentary</button>
          </div>
        </div>
      </div>
      
    </div>
    <div class="col-md-4">
      <div class="alert alert-success mb0 text-center bold">Innings Commentary</div>
      <div class="commentary 2nd" style="height: 850px; overflow-y: scroll;">
        
      </div>
    </div>
  </div>	
</div>
<?php	
}
else
{	?>
	<div class="row">
		<div class="col-md-12 text-center">
			<div class="alert alert-danger not-ok"><i class="fa fa-warning">&nbsp;</i><?php echo $data['msg']; ?></div>
		</div>
	</div>
<?php	
}
?>


	



<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/live.js"></script>