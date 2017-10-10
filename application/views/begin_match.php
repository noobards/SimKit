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
	<?php
	$batsmen = $data['batsmen'];
	$bowlers = $data['bowlers'];	
	?>
	<div class="col-md-12">
		<div class="table-mockup">
			<div class="thead">
				<div class="tr">
					<div class="th">Player</div>
					<div class="th">Status</div>
					<div class="th">Runs</div>
					<div class="th">Balls</div>
					<div class="th">Fours</div>
					<div class="th">Sixes</div>
					<div class="th">SR</div>
				</div>
			</div>
			<div class="tbody">
				<?php foreach($batsmen as $ary)
				{	?>
				<div class="tr">
					<div class="td"><?php echo $ary['name']; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['status']; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['runs']; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['balls']; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['fours']; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['sixes']; ?></div>
					<div class="td text-center"><?php echo $ary['balls'] > 0 ? number_format(($ary['runs']*100/$ary['balls']), 2) : ""; ?></div>
				</div>
				<?php
				}				
			?>
				<div class="tr">
					<div class="td text-right">
						<strong>Total: <?php echo $data['total'].'/'.$data['wickets'].' ('.$data['run_rate'].')'; ?></strong>
					</div>
				</div>
			</div>
		</div>

		<div class="table-mockup top10">
			<div class="thead">
				<div class="tr">
					<div class="th">Player</div>
					<div class="th">Overs</div>
					<div class="th">Runs</div>
					<div class="th">Wickets</div>
					<div class="th">Economy</div>					
				</div>
			</div>
			<div class="tbody">
				<?php foreach($bowlers as $ary)
				{
					if($ary['legal_balls'] > 0)
					{
					?>
				<div class="tr">
					<div class="td"><?php echo $ary['name']; ?></div>
					<div class="td text-center">
						<?php
						echo floor($ary['legal_balls']/6).'.'.floor($ary['legal_balls'] % 6);
						?>
					</div>
					<div class="td text-center"><?php echo $ary['runs'] ?></div>
					<div class="td text-center"><?php echo $ary['wickets']; ?></div>
					<div class="td text-center">
						<?php
							$econ = $ary['runs']*6/$ary['legal_balls'];
							echo number_format($econ, 2);
						?>
					</div>
				</div>
				<?php
					}
				}
			?>
			</div>
		</div>
	</div>
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