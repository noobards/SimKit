<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>index.php/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> Squad Selection
	</div>
</div>

<?phpif($view['status'] == 'OK'){	?>
<div class="row" ng-controller="squadSelection">
	<div class="col-md-12">
		<div class="box">
			<div class="box-title">
				<div class="box-main-text">Squad Selection</div>
				<div class="box-helping-text">Choose your playing X1</div>
			</div>
			<div class="box-body box-body-max">				<?php					$squads = $this->data['squads'];					if(count($squads) > 0)					{						echo '<div class="row">';						foreach($squads as $team)						{							echo '<div class="col-md-6">';							foreach($team as $team_name => $array)							{								echo '<strong>'.$team_name.'</strong> (<span class="selection_number green">0 selected</span>)';								echo '<div class="table-mockup">';								echo '<div class="thead">';									echo '<div class="tr">';										echo '<div class="th">&nbsp;</div>';										echo '<div class="th">Name</div>';										echo '<div class="th">Type</div>';										echo '<div class="th">Age</div>';									echo '</div>';								echo '</div>';								echo '<div class="tbody">';								foreach($array as $meta)								{									echo '<div class="tr">';										echo '<div class="td text-center"><input ng-click="playerSelect($event)" type="checkbox" /></div>';										echo '<div class="td">'.$meta['player_name'].'</div>';										echo '<div class="td text-center"><img class="player_type_icon" src="'.base_url().'assets/images/icons/'.$meta['icon'].'" alt="'.$meta['player_type'].'" /></div>';										echo '<div class="td text-center">'.$meta['player_age'].'</div>';									echo '</div>';								}								echo '</div>';								echo '</div>';							}							echo '</div>';						}						echo '</div>';					}				?>
			</div>
		</div>
	</div>	
</div><script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/squad_selection.js"></script><?php} else {	?><div class="row">	<div class="col-md-12">		<div class="alert alert-danger"><i class="fa fa-exclamation-triangle">&nbsp;</i><?php echo $view['msg']; ?></div>	</div></div><?php}?>