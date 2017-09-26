
	<div class="row site-breadcrumbs">
		<div class="col-xs-12">
			<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/Players">Players</a> <i class="fa fa-chevron-right">&nbsp;</i> My Players
		</div>
	</div>

	<?php
	if($this->session->flashdata('flash'))
	{
		?>
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-info"><?php echo $this->session->flashdata('flash'); ?></div>
			</div>
		</div>
	<?php
	}	
	?>
	<div ng-controller="listPlayers">
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-info"><i class="fa fa-exclamation-circle">&nbsp;</i>Click on a <strong>player name</strong> to edit his/her details. Alternatively, you can select the players that need to be removed by clicking on the <strong>checkbox</strong> next to their name.</div>
				<table class="filter-table">
					<tbody>
						<tr>
							<td><strong>Filter Keyword:</strong></td>
							<td><input type="text" ng-model="searchKW" class="form-control" /></td>
							<td><strong>Total listed:</strong> {{filtered.length}}</td>
							<td><button ng-show="showDeleteButton" class="btn btn-danger" ng-click="removeSelected($event)">Remove</button></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">				
				<div class="table-mockup">
					<div class="thead">
						<div class="tr">
							<div class="th"><input type="checkbox" ng-click="selectAll()" ng-model="check_all" /></div>
							<div class="th">Name</div>							
							<div class="th">Country</div>
							<div class="th">Role</div>
							<div class="th">Rating</div>
							<div class="th">Updated</div>
						</div>
					</div>
					<div class="tbody">
						<div class="tr" ng-repeat="player in my_players | filter : searchKW as filtered">
							<div class="td text-center"><input type="checkbox" ng-model="player.selected" class="nl-checkbox" ng-click="selectSingle($event)" value="{{player.id}}" /></div>
							<div class="td text-center"><a class="normal-anchor" href="Edit/{{player.id}}">{{player.name}}</a> <label class="label label-danger label-private-small" ng-show="player.is_private == 1">Private</label></div>							
							<div class="td text-center">{{player.country}}</div>
							<div class="td text-center"><img class="player_type_icon" src="<?php echo base_url(); ?>assets/images/icons/{{player.icon}}" title="{{player.player_type}}" alt="{{player.player_type}}" /></div>
							<div class="td">
								<div class='outer_bar'><div style="width:{{(player.avg*10)}}%" class='inner_bar'></div><span class='bar_value'>{{player.avg}}</span></div>
							</div>
							<div class="td text-center">{{player.updated}}</div>
						</div>
						<div class="tr" ng-if="my_players.length == 0"><div class="td text-center">No records found</div></div>
					</div>
				</div>				
			</div>
		</div>
	</div>		

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/list_players.js"></script>