
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
				<p>Click on a player name to edit his/her details.</p>
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
				<div class="pad10 white-bg">					
					<table class="table table-striped">
						<thead>
							<tr class="alert-warning">
								<th><input type="checkbox" ng-click="selectAll()" ng-model="check_all" /></th>
								<th>S.No</th>
								<th>Player Name</th>						
								<th>Age</th>
								<th>Gender</th>
								<th>Country</th>
								<th>Player Type</th>								
								<th>Added On</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-if="my_players.length > 0" ng-repeat="player in my_players | filter : searchKW as filtered">
								<td><input type="checkbox" ng-model="player.selected" class="nl-checkbox" ng-click="selectSingle($event)" value="{{player.id}}" /></td>
								<td>{{$index + 1}}</td>
								<td><a class="normal-anchor" href="Edit/{{player.id}}">{{player.name}}</a></td>
								<td>{{player.age}}</td>
								<td>{{player.gender}}</td>
								<td>{{player.country}}</td>
								<td><img class="player_type_icon" src="<?php echo base_url(); ?>assets/images/icons/{{player.icon}}" title="{{player.player_type}}" alt="{{player.player_type}}" /></td>								
								<td>{{player.created}}</td>
							</tr>
							<tr ng-if="filtered.length == 0">
								<td colspan="8">No records found</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>		

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/list_players.js"></script>