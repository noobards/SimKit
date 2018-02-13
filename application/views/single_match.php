<div class="row site-breadcrumbs">

	<div class="col-xs-12">

		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> Single Match

	</div>

</div>

<div ng-controller="singleMatch">
	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Matches in Progress</div>
					<div class="box-helping-text">Continue from where you left off</div>
				</div>
				<div class="box-body box-body-max">
					<?php if(count($pending) > 0) {	?>
					<div class="row">
						<div class="col-md-6">							
								<div>Click on the "Title" to resume a match.</div>							
						</div>
						<div class="col-md-6">
							<div class="text-right">
								<i class="fa fa-balance-scale">&nbsp;</i>Coin Toss&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-gavel">&nbsp;</i>Simulate
							</div>
						</div>
					</div>	
					<?php }	?>
					<div class="table-mockup">
						<div class="thead">
							<div class="tr">
								<div class="th">Title</div>
								<div class="th">Type</div>
								<div class="th">Ground</div>
								<div class="th">Next Step</div>
								<div class="th">Created On</div>
							</div>
						</div>
						<div class="tbody">
						<?php if(count($pending) > 0) {
							foreach($pending as $arr)
							{
								echo '<div class="tr hover-action">';								
								echo '<div class="td text-center"><i data-mid="'.$arr['mid'].'" title="Delete this match" ng-click="deleteMatch($event)" class="fa fa-close red cursor hover-show">&nbsp;</i>&nbsp;<a class="normal-anchor" href="PreMatch/'.$arr['mid'].'"><strong>'.$arr['home'].'</strong> v/s <strong>'.$arr['away'].'</strong></a></div>';
								echo '<div class="td text-center">'.$arr['type'].'</div>';
								echo '<div class="td text-center">'.$arr['ground'].'</div>';
								echo '<div class="td text-center">'.($arr['stage'] == '1' ? '<i class="fa fa-balance-scale" title="Coin Toss">&nbsp;</i>' : '<i class="fa fa-gavel" title="Simulate">&nbsp;</i>').'</div>';
								echo '<div class="td text-center">'.$arr['time'].'</div>';
								echo '</div>';
							}
						} else {
							echo '<div class="td text-center">No pending matches found.</div>';
						}
						?>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Create New Match</div>
					<div class="box-helping-text">Choose teams and make them compete against each other</div>
				</div>
				<div class="box-body box-body-max">
					<div class="alert alert-danger">
						<h4 class="mb0"><i class="fa fa-warning">&nbsp;</i>If you do not see your team listed below, make sure the missing team has at least <strong>11 players</strong> (including minimum <strong>5 players who can bowl</strong> and <strong>1 wicket keeper</strong>) in the squad to choose from.</h4>
					</div>
					<div class="panel-group simkit-panel" id="config_match">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<span>Select Teams</span>
								</h4>
							</div>
							<div class="panel-collapse collapse in" id="panel_1">
								<div class="panel-body">
									<div ng-if="data.teams.length > 0">
										<div class="row">
											<div class="col-xs-5">
												<div class="text-center alert alert-info" style="margin-bottom: 0;"><strong>Home Team</strong></div>
												<div class="team_select_box">											
													<div ng-click="moveUp($event, 'home')" class="team_arrow arrow_up"><i class="fa green fa-chevron-up fa-3x">&nbsp;</i></div>
													<div class="list_of_teams">
														<div data-tid="{{team.team_id}}" data-team="{{team.team_name}}" ng-class="$index == 0 ? 'team_active' : null" class="team" data-index="{{($index + 1)}}" ng-repeat="team in data.teams">
															<div class="small_team_logo">
																<img ng-src="{{team.logo}}" alt="{{team.team_name}}" />														
															</div>
															<div class="team_name alert-warning"><span title="{{team.team_name}}">{{team.team_name}}</span></div>
															<div class="team_score red"><i class="fa fa-star">&nbsp;</i>{{team.rating}}</div>
														</div>
													</div>
													<div ng-click="moveDown($event, 'home')" class="team_arrow arrow_down arrow_disabled"><i class="fa green fa-chevron-down fa-3x">&nbsp;</i></div>
												</div>
											</div>
											<div class="col-xs-2">
												<span class="vs vs_big vs_middle">VS</span>
											</div>
											<div class="col-xs-5">
												<div class="text-center alert alert-info" style="margin-bottom: 0;"><strong>Away Team</strong></div>
												<div class="team_select_box">
													<div ng-click="moveUp($event, 'away')" class="team_arrow arrow_up"><i class="fa green fa-chevron-up fa-3x">&nbsp;</i></div>
													<div class="list_of_teams">
														<div data-tid="{{team.team_id}}" data-team="{{team.team_name}}" ng-class="$index == 0 ? 'team_active' : null" class="team" data-index="{{($index + 1)}}" ng-repeat="team in data.teams">
															<div class="small_team_logo">
																<img ng-src="{{team.logo}}" alt="{{team.team_name}}" />
															</div>
															<div class="team_name alert-warning"><span title="{{team.team_name}}">{{team.team_name}}</span></div>
															<div class="team_score red"><i class="fa fa-star">&nbsp;</i>{{team.rating}}</div>
														</div>
													</div>
													<div ng-click="moveDown($event, 'away')" class="team_arrow arrow_down arrow_disabled"><i class="fa green fa-chevron-down fa-3x">&nbsp;</i></div>
												</div>
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-12">
												<div class="text-center">
													<button class="btn btn-primary" data-current-panel="panel_1" ng-disabled="data.home == data.away" ng-click="goNext($event, 'panel_2')">Next <i class="fa fa-arrow-down">&nbsp;</i></button>
												</div>
											</div>
										</div>
									</div>
									<div ng-if="data.teams.length == 0">
										<div class="alert alert-info text-center"><i class="fa fa-exclamation-triangle">&nbsp;</i>You have not created any teams.</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<span>Select Playing XI</span>
								</h4>
							</div>
							<div class="panel-collapse collapse"  id="panel_2">
								<div class="panel-body">
									<div class="row">
										<div class="col-md-6 bot10" ng-repeat="obj in data.team_players">
											<div class="alert alert-info text-center mb0"><strong>{{obj.name}}</strong></div>																						
											<div class="text-right sel-count">
												<a href="#" ng-click="randomSelection($event, ($index == 0 ? 'home' : 'away'))"><strong><i class="fa fa-random">&nbsp;</i>Random Selection</strong></a> | 
												<span class="selection_number bold">0 selected</span>
											</div>
																			
											<div class="table-mockup" data-mode="{{($index == 0 ? 'home' : 'away')}}">
												<div class="thead">
													<div class="tr">
														<div class="th" style="width: 30%;">Select</div>
														<div class="th" style="width: 70%;">Name</div>													
													</div>
												</div>
												<div class="tbody">
													<div class="tr" ng-repeat="p in obj.players">
														<div class="td text-center" style="width: 30%;"><input class="select-player-cb" id="home_cb_{{$index}}" ng-click="playerSelect($event)" data-name="{{p.player_name}}" data-icon="{{p.icon}}" data-role_id="{{p.player_type_id}}" data-role="{{p.player_type}}" data-pid="{{p.player_id}}" data-ment_label="{{p.mentality}}" data-ment_icon="{{p.mentality_icon}}" type="checkbox" /></div>
														<div class="td" style="width: 70%;"><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.icon}}" alt="{{p.player_type}}" title="{{p.player_type}}" /><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.mentality_icon}}" alt="{{p.mentality}}" title="{{p.mentality}}" />{{p.player_name}}</div>									
													</div>
												</div>
											</div>
										</div>
									</div>								
									<div class="text-center">
										<button class="btn btn-default" data-current-panel="panel_2" ng-click="goPrev($event, 'panel_1')"><i class="fa fa-arrow-up">&nbsp;</i>Previous</button>
										<button class="btn btn-primary" data-current-panel="panel_2" ng-disabled="! data.players_selected" ng-click="goNext($event, 'panel_3')">Next <i class="fa fa-arrow-down">&nbsp;</i></button>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<span>Choose Batting Order</span>
								</h4>
							</div>
							<div class="panel-collapse collapse"  id="panel_3">
								<div class="panel-body">
									<div class="text-center alert alert-success" style="margin-bottom:7px;"><strong>Drag and drop the players to set the batting order of the team.</strong></div>
									<div class="row">
										<div class="col-sm-6">
											<div class="alert alert-info text-center mb0">{{data.home_label}}</div>
											<div class="table-mockup">
												<div class="tbody" id="sortHomeBattingOrder">
													<div class="tr handle" ng-repeat="p in data.home_eleven">
														<div class="td"><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.icon}}" alt="{{p.type}}" title="{{p.type}}" /><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.ment_icon}}" alt="{{p.ment_label}}" title="{{p.ment_label}}" />{{p.name}}</div>														
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="alert alert-info text-center mb0">{{data.away_label}}</div>
											<div class="table-mockup">
												<div class="tbody" id="sortAwayBattingOrder">
													<div class="tr handle" ng-repeat="p in data.away_eleven">
														<div class="td"><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.icon}}" alt="{{p.type}}" title="{{p.type}}" /><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.ment_icon}}" alt="{{p.ment_label}}" title="{{p.ment_label}}" />{{p.name}}</div>														
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="text-center top10">
										<button class="btn btn-default" data-current-panel="panel_3" ng-click="goPrev($event, 'panel_2')"><i class="fa fa-arrow-up">&nbsp;</i>Previous</button>
										<button class="btn btn-primary" data-current-panel="panel_3" ng-click="goNext($event, 'panel_4')">Next <i class="fa fa-arrow-down">&nbsp;</i></button>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<span>Playing Conditions</span>
								</h4>
							</div>
							<div class="panel-collapse collapse"  id="panel_4">
								<div class="panel-body">
									<div class="row">
										<div class="col-md-6 col-md-offset-3">
											<div class="form-horizontal">
												<div class="form-group">
													<label for="ground" class="col-md-3 control-label">Ground Name: <span class="red">*</span></label>
													<div class="col-md-9">
														<input type="text" id="ground" ng-model="data.ground" class="form-control" />
													</div>
												</div>
												<div class="form-group">
													<label for="pitch" class="col-md-3 control-label">Pitch Type: <span class="red">*</span></label>
													<div class="col-md-9">
														<select id="pitch" ng-model="data.p_type" class="form-control" ng-options="pt.label for pt in data.pitch_types track by pt.Id">
														</select>
													</div>
												</div>
												<div class="form-group">
													<label for="m_type" class="col-md-3 control-label">Match Type: <span class="red">*</span></label>
													<div class="col-md-9">
														<select id="m_type" ng-model="data.m_type" class="form-control" ng-options="mt.label for mt in data.match_types track by mt.Id">
														</select>
													</div>
												</div>
												<div class="form-group hide">
													<label for="overs" class="col-md-3 control-label">Number of Overs: <span class="red">*</span></label>
													<div class="col-md-9">
														<input type="text" id="overs" class="form-control" />
													</div>
												</div>
												<div class="form-group">
													<label for="comm" class="col-md-3 control-label">Commentator: <span class="red">*</span></label>
													<div class="col-md-9">
														<select id="comm" ng-model="data.comm" class="form-control" ng-options="comm.label for comm in data.commentators track by comm.Id">
														</select>
														<span class="extra-info">Work on commentary feature to commence soon. For now, only system generated content is available.</span>
													</div>
												</div>
											</div>
											<div class="text-center">
												<button class="btn btn-default" data-current-panel="panel_4" ng-click="goPrev($event, 'panel_3')"><i class="fa fa-arrow-up">&nbsp;</i>Previous</button>
												<button class="btn btn-primary" data-current-panel="panel_4" ng-click="goNext($event, 'panel_5')">Next <i class="fa fa-arrow-down">&nbsp;</i></button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<span>Confirm Selection</span>
								</h4>
							</div>
							<div class="panel-collapse collapse"  id="panel_5">
								<div class="panel-body" id="final_panel">	
									<div class="row">
										<div class="col-md-6 col-md-offset-3">
											<div class="row">
												<div class="col-sm-6">
													<div class="alert alert-warning text-center mb0"><strong>{{data.home_label}}</strong></div>
													<div class="table-mockup">
														<div class="tbody">
															<div class="tr" ng-repeat="p in data.home_eleven">
																<div class="td"><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.icon}}" alt="{{p.type}}" title="{{p.type}}" /><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.ment_icon}}" alt="{{p.ment_label}}" title="{{p.ment_label}}" />{{p.name}}</div>													
															</div>
														</div>
													</div>
												</div>
												<div class="col-sm-6">
													<div class="alert alert-warning text-center mb0"><strong>{{data.away_label}}</strong></div>
													<div class="table-mockup">
														<div class="tbody">
															<div class="tr" ng-repeat="p in data.away_eleven">
																<div class="td"><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.icon}}" alt="{{p.type}}" title="{{p.type}}" /><img class="role-icon" ng-src="<?php echo base_url(); ?>assets/images/icons/{{p.ment_icon}}" alt="{{p.ment_label}}" title="{{p.ment_label}}" />{{p.name}}</div>													
															</div>
														</div>
													</div>
												</div>
											</div>
											<table class="table table-striped table-condensed top10">
												<thead>
													<tr class="alert alert-warning">
														<th colspan="2">Match Conditions</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td style="width:35%;"><strong>Ground:</strong></td>
														<td>{{data.ground}}</td>
													</tr>
													<tr>
														<td><strong>Pitch:</strong></td>
														<td>{{data.p_type.label}}</td>
													</tr>
													<tr>
														<td><strong>Mode:</strong></td>
														<td>{{data.m_type.label}}</td>
													</tr>
													<tr>
														<td><strong>Commentator:</strong></td>
														<td>{{data.comm.label}}</td>
													</tr>
													<tr>
														<td><strong>Overs:</strong></td>
														<td>{{data.overs}}</td>
													</tr>
												</tbody>
											</table>
										</div>	
									</div>
									<div class="text-center">
										<button class="btn btn-default" data-current-panel="panel_5" ng-click="goPrev($event, 'panel_4')"><i class="fa fa-arrow-up">&nbsp;</i>Previous</button>
										<button class="btn btn-danger" ng-click="setMatch($event)">Continue <i class="fa fa-arrow-right">&nbsp;</i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>	
		</div>	
	</div>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugins/sortable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/single_match.js"></script>