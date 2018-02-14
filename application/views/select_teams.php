<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter/Tournament">Tournament</a> <i class="fa fa-chevron-right">&nbsp;</i> Select Teams
	</div>
</div>

<div ng-controller="selectTeams">
<?php
if($data['status'] == 'OK')
{
	if(count($data['teams']) > 0)	
	{
		foreach($data['teams'] as $team)
		{
			echo '<h3>'.$team['name'].' ('.$team['nop'].')</h3>';
		}
	}
	else
	{	?>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger">You have not created any teams. Click <a href="<?php echo site_url().'/Teams'; ?>"><strong>here</strong></a> to create one.</div>
		</div>
	</div>
	<?php
	}
}
else
{	?>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger"><?= $data['msg']; ?></div>
		</div>
	</div>	
<?php	
}
?>	
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/select_teams.js"></script>