<div class="row site-breadcrumbs">

	<div class="col-xs-12">

		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> Match Center

	</div>

</div>

<div ng-controller="matchCenter">
	<div class="row">
		<div class="col-sm-6">
			<div class="action_box">
				<div class="action_box_heading">Single</div>
				<div class="action_box_content">
					Create or resume single matches. Ideal for situations where you just want to check things out and simulate a match without having to go through all the pre-requisites of a tournament mode.
				</div>
				<div class="action_box_footer">
					<a class="btn btn-primary" href="MatchCenter/Single">Single Mode</a>
				</div>
			</div>
		</div>

		<div class="col-sm-6">
			<div class="action_box">
				<div class="action_box_heading">Tournament</div>
				<div class="action_box_content">
					Create or resume a tournament. This is the big thing. You will find continuinty in this mode. Pick up from where you left off the last time or start a new tournament from scratch.
				</div>
				<div class="action_box_footer">
					<a class="btn btn-primary" href="MatchCenter/Tournament">Tournament Mode</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/match_center.js"></script>