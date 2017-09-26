<style type="text/css">
.player-added{
	background-color: #FCF8E3 !important;
}
</style>
<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> Coummunity
	</div>
</div>

<?php
	if($this->session->flashdata('flash'))
	{
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-info">
					<?= $this->session->flashdata('flash');	?>
				</div>
			</div>
		</div>
<?php
	}
?>

<div class="row">
	<div class="col-sm-6">
		<div class="dashboard-stats-box">
			<div class="row">
				<div class="col-xs-3 stats-box-icon">
					<img src="<?php echo base_url(); ?>assets/images/icons/thumbsup.png" alt="Thumbs Up" class="dash-stats" />
				</div>
				<div class="col-xs-9 stats-box-text">
					<p class="stats-main-number"><?php echo $team_count; ?></p>
					<p class="stats-side-text">Team(s)</p>
					<p class="stats-meta-text">created by COMMUNITY</p>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="dashboard-stats-box">
			<div class="row">
				<div class="col-xs-3 stats-box-icon">
					<img src="<?php echo base_url(); ?>assets/images/icons/users.png" alt="Players" class="dash-stats" />
				</div>
				<div class="col-xs-9 stats-box-text">
					<p class="stats-main-number"><?php echo $player_count; ?></p>
					<p class="stats-side-text">Player(s)</p>
					<p class="stats-meta-text">created by COMMUNITY</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div ng-controller="communityPlayers">
<div class="modal fade" id="confirm" data-backdrop="static">
  <div class="modal-dialog modal-md">
  	<div class="modal-content">
	  	<div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">Confirm Download</h4>
	     </div>
	     <div class="modal-body">
	        <p ng-hide="data.cart.length == 0">Please confirm the players that you are about to download to your account. If you wish to cancel, simply click the "Cancel" button.</p>
	        <table class="table table-striped table-condensed">
	        	<thead>
	        		<tr class="alert-warning">
	        			<th>S.No</th>
	        			<th>Player Name</th>
	        			<th>Owner</th>
	        		</tr>
	        	</thead>
	        	<tbody>
	        		<tr ng-repeat="p in data.cart">
	        			<td>{{($index + 1)}}</td>
	        			<td>{{p.name}}</td>
	        			<td>{{p.author}}</td>
	        		</tr>
	        		<tr ng-show="data.cart.length == 0">
	        			<td colspan="3">No players present in queue</td>
	        		</tr>
	        	</tbody>
	        </table>
	    </div>
	    <div class="modal-footer">
	    	<button type="button" ng-hide="data.cart.length == 0" ng-click="downloadPlayers($event)" class="btn btn-success"><i class="fa fa-download">&nbsp;</i>Download</button>
	    	<button data-dismiss="modal" type="button" class="btn btn-default"><i class="fa fa-close">&nbsp;</i>Cancel</button>
	    </div>
    </div>
  </div>
</div>
<div class="modal fade" id="dl_list">
  <div class="modal-dialog modal-sm">
  	<div class="modal-content">
	  	<div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">List of Downloaders</h4>
	     </div>
	     <div class="modal-body">	        
	        <table class="table-condensed">	        	
	        	<tbody>
	        		<tr ng-repeat="o in data.dl_list">
	        			<td>{{(o)}}</td>
	        		</tr>	        		
	        	</tbody>
	        </table>
	    </div>
	    <div class="modal-footer">	    	
	    	<button data-dismiss="modal" type="button" class="btn btn-default"><i class="fa fa-close">&nbsp;</i>Cancel</button>
	    </div>
    </div>
  </div>
</div>



	<div class="row">
		<div class="col-md-7">
			<div class="box" style="margin-top: 10px;">
				<div class="box-title">
					<div class="box-main-text">Players</div>
					<div class="box-helping-text">List of players created by the community.</div>
				</div>
				<div class="box-body box-body-max" id="list">				
					<div class="row">
						<div class="col-md-12 vscroll" style="height: 400px;">	
							<div class="form-horizontal">						
								<div class="form-group">
									<label for="fil">Filter Keywords</label>
									<input type="text" id="fil" class="form-control" placeholder="Filter by Name, Role, Author or Country" ng-model="searchKW" />
								</div>
							</div>
							<div class="table-mockup">
								<div class="thead">
									<div class="tr">
										<div class="th">Name</div>
										<div class="th">Role</div>
										<div class="th">Author</div>
										<div class="th"># of Downloads</div>
										<div class="th">Actions</div>
									</div>
								</div>
								<div class="tbody">
									<div class="tr" ng-repeat="player in data.players | filter : searchKW as filtered">
										<div class="td"><a class="normal-anchor" ng-click="showDetail($event, player.pid)" href="#">{{player.name}}</a>&nbsp;<i ng-show="player.download == '1'" class="fa fa-download red" title="This player was downloaded">&nbsp;</i>&nbsp;<i ng-show="player.already == 'YES'" class="fa fa-check red" title="You downloaded this player">&nbsp;</i></div>
										<div class="td text-center"><img style="width: 20px; height: 20px; margin-top: 6px;" src="<?php echo base_url(); ?>assets/images/icons/{{player.icon}}" title="{{player.player_type}}" alt="{{player.player_type}}" /></div>
										<div class="td text-center">{{player.author}} <span style="font-size: 10px;" ng-if="player.source_owner">({{player.source_owner}})</span></div>
										<div class="td text-center"><a href="#" ng-show="player.download_count > 0" class="normal-anchor" ng-click="showDownloadList($event, player.pid)">{{player.download_count}}</a><span ng-show="player.download_count == 0">{{player.download_count}}</span></div>
										<div class="td text-center"><button style="padding-top: 3px; padding-bottom: 3px;" type="button" ng-disabled="player.already == 'YES'" data-author="{{player.author}}" data-pid="{{player.pid}}" ng-click="addToQueue($event)" class="btn btn-primary" data-name="{{player.name}}"><i class="fa fa-plus">&nbsp;</i>Add to Queue</button>&nbsp;<a data-pid="{{player.pid}}" ng-click="removeFromCart($event)" class="hide red" href="#">(Remove)</a></div>
									</div>
									<div class="tr" ng-if="data.players.length == 0">
										<div class="td">No players found.</div>
									</div>
								</div>
							</div>
						</div>						
					</div>
				</div>
			</div>
			<div class="row top10 bot10">
				<div class="col-md-12">
					<div class="text-center">
						<button data-toggle="modal" data-target="#confirm" type="button" ng-click="confirmDownloadPlayers($event)" class="btn btn-default"><strong><i class="fa fa-user">&nbsp;</i> {{data.cart.length}} players in queue <span ng-show="data.cart.length > 0"><br /><i class="fa fa-download">&nbsp;</i>Click to Download</span></strong></button>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-5 top10" id="player_placeholder" style="background-color: #fff;">
			<div class="alert alert-warning" ng-show="no_player_data">{{page_load_message}}</div>
			<div ng-hide="no_player_data">
				<div class="row">
					<div class="col-md-6">									
						<div class="bar_heading">Personal Information</div>
						<table class="table table-striped table-condensed">											
							<tbody>
								<tr>
									<td style="width: 150px;"><strong>First Name:</strong></td>
									<td>{{data.player.first_name}}</td>
								</tr>
								<tr>
									<td><strong>Last Name:</strong></td>
									<td>{{data.player.last_name}}</td>
								</tr>
								<tr ng-show="data.player.nick">
									<td><strong>Nickname:</strong></td>
									<td>{{data.player.nick}}</td>
								</tr>
								<tr>
									<td><strong>Age:</strong></td>
									<td>{{data.player.age}}</td>
								</tr>
								<tr>
									<td><strong>Gender:</strong></td>
									<td>{{data.player.gender}}</td>
								</tr>
								<tr>
									<td><strong>Country:</strong></td>
									<td>{{data.player.country}}</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-md-6">
						<div class="bar_heading">Attributes</div>
						<table class="table table-striped table-condensed">											
							<tbody>
								<tr>
									<td style="width: 150px;"><strong>Player Type:</strong></td>
									<td>{{data.player.type}}</td>
								</tr>
								<tr>
									<td><strong>Batting Hand:</strong></td>
									<td>{{data.player.bat_hand}}</td>
								</tr>
								<tr ng-show="data.player.type_id == '2' || data.player.type_id == '3' || data.player.type_id == '4'">
									<td><strong>Bowling Hand:</strong></td>
									<td>{{data.player.bowl_hand}}</td>
								</tr>
								<tr ng-show="data.player.type_id == '2' || data.player.type_id == '3' || data.player.type_id == '4'">
									<td><strong>Bowler Type:</strong></td>
									<td>{{data.player.bowl_type}}</td>
								</tr>
								<tr>
									<td><strong>Test:</strong></td>
									<td><i ng-if="data.player.test == '1'" class="fa green fa-check">&nbsp;</i><i ng-if="data.player.test == '0'" class="fa red fa-close">&nbsp;</i></td>
								</tr>
								<tr>
									<td><strong>ODI:</strong></td>
									<td><i ng-if="data.player.odi == '1'" class="fa green fa-check">&nbsp;</i><i ng-if="data.player.odi == '0'" class="fa red fa-close">&nbsp;</i></td>
								</tr>
								<tr>
									<td><strong>T20:</strong></td>
									<td><i ng-if="data.player.t20 == '1'" class="fa green fa-check">&nbsp;</i><i ng-if="data.player.t20 == '0'" class="fa red fa-close">&nbsp;</i></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-md-12">
						<div class="bar_heading">Author Information</div>
						<table class="table table-striped table-condensed">											
							<tbody>
								<tr ng-show="data.player.base_owner" class="alert-warning">
									<td><strong>Orginal Creator:</strong></td>
									<td>{{data.player.base_owner}}</td>
								</tr>
								<tr ng-show="data.player.downloaded == 1">
									<td><strong>Downloaded From:</strong></td>
									<td>{{data.player.source}}</td>
								</tr>
								<tr>
									<td style="width: 150px;"><strong><span ng-show="data.player.downloaded == 1">Downloaded By:</span><span ng-show="data.player.downloaded == 0">Created By:</span></strong></td>
									<td>{{data.player.owner}}</td>
								</tr>
								<tr>
									<td style="width: 150px;"><strong><span ng-show="data.player.downloaded == 0">Created</span><span ng-show="data.player.downloaded == 1">Downloaded</span> On:</strong></td>
									<td>{{data.player.created}}</td>
								</tr>								
							</tbody>
						</table>		
					</div>
				</div>
				<div class="row top10">
					<div class="col-md-12">
						<div class="bar_heading">Rating Points</div>
						<div class="row">	
							<div class="col-sm-5 top10">
								<div class="c100 p{{data.player.avg*10 | number : 0}} center">
									<span>{{data.player.avg | number : 2}}</span>
									<div class="slice">
										<div class="bar"></div>
										<div class="fill"></div>
									</div>
								</div>
							</div>
							<div class="col-sm-7">
								<table class='table top20'>
									<tbody>												
										<tr>
											<td style='width:30%; border-top: 0;'><strong>Batting</strong></td>
											<td style='width:70%; border-top: 0;'><div class='outer_bar'><div style='width:{{data.player.bat_pt}}%' class='inner_bar'></div><span class='bar_value'>{{data.player.bat_pt}}</span></div></td>
										</tr>
										<tr>
										<td>
											<strong>Bowling</strong></td>
											<td><div class='outer_bar'><div style='width:{{data.player.bowl_pt}}%' class='inner_bar'></div><span class='bar_value'>{{data.player.bowl_pt}}</span></div></td>
										</tr>
										<tr>
											<td><strong>Fielding</strong></td>
											<td><div class='outer_bar'><div style='width:{{data.player.field_pt}}%' class='inner_bar'></div><span class='bar_value'>{{data.player.field_pt}}</span></div></td>
										</tr>
									</tbody>
								</table>
							</div>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/community.js"></script>