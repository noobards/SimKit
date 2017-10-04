<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> Begin Match
	</div>
</div>
	
<?php
if($data['status'] == "OK")
{		
?>

<div class="row" ng-controller="beginMatch">
</div>



<?php	
}
else
{	?>
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-danger"><i class="fa fa-warning">&nbsp;</i><?php echo $data['msg']; ?></div>
		</div>
	</div>
<?php	
}
?>


	



<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/begin_match.js"></script>