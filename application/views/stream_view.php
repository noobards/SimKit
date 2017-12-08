<div ng-controller="stream">
<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> Live Stream
	</div>
</div>

<?php 
if($status == 'OK')
{ ?>
	<div class="row">
		<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 text-center">
			<div class="alert alert-success">
				<div class="stream_competing_teams"><strong><?php echo $home_label; ?></strong> v/s <strong><?php echo $away_label; ?></strong></div>
				<div class="stream_overs_and_ground"><?php echo $match_center->overs; ?> overs, <?php echo $match_center->ground; ?> - <?php echo date("M j, Y"); ?></div>
				<div class="stream_toss_decision"><strong><?php echo $toss_won_by; ?></strong> have won the toss and elected to <strong><?php echo $toss_decision; ?></strong> first</div>
			</div>
		</div>
	</div>
<?php
}
else
{	?>
<div class="row hasError">
	<div class="col-md-12">
		<div class="alert alert-danger"><?php echo $msg; ?></div>
	</div>
</div>
<?php
} 
?>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/stream_view.js?v=1"></script>