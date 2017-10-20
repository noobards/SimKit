<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/MatchCenter">Match Center</a> <i class="fa fa-chevron-right">&nbsp;</i> <?php echo $data['home_label'].'&nbsp;&nbsp;&nbsp;v/s&nbsp;&nbsp;&nbsp;'.$data['away_label']; ?>
	</div>
</div>
	
<?php
if($data['status'] == "OK")
{		
?>
<div ng-controller="beginMatch">
<div class="row">
	<?php
	$batsmen = $data['first_batsmen'];
	$bowlers = $data['first_bowlers'];	
	?>
	<div class="col-md-8">
		<div class="text-center">
			<div><strong><?php echo $data['match_length']; ?></strong> overs match</div>
			<div>at <strong><?php echo $data['ground']; ?></strong></div>
			<div>on <?php echo date('F d, Y'); ?></div>
		</div>
		<div class="alert alert-success mb0 text-center bold"><?php echo $data['first_batting_label']; ?> Batting Scorecard</div>
		<div class="table-mockup">
			<div class="thead">
				<div class="tr">
					<div class="th">Player</div>
					<div class="th">Status</div>
					<div class="th">Runs</div>					
					<div class="th">4/6</div>					
					<div class="th">SR</div>
				</div>
			</div>
			<div class="tbody">
				<?php foreach($batsmen as $ary)
				{	
					$css_class = ($ary['status'] == 'NOTOUT' ? 'highlight' : 'unhilight' );
					?>
				<div class="tr <?php echo $css_class; ?>">
					<div class="td"><?php echo $ary['name']; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : ( $ary['status'] == "NOTOUT" ? "" : $ary['status']); ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['runs'].' ('.$ary['balls'].')'; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['fours'].'/'.$ary['sixes']; ?></div>
					<div class="td text-center"><?php echo $ary['balls'] > 0 ? number_format(($ary['runs']*100/$ary['balls']), 2) : ""; ?></div>
				</div>
				<?php
				}				
			?>
				<div class="tr">
					<div class="td text-right">
						<strong>Total: <?php echo $data['first_total'].'/'.$data['first_wickets'].' in '.$data['first_overs'].' overs ('.$data['first_run_rate'].' rpo)'; ?></strong>
					</div>
				</div>
			</div>
		</div>

		<div class="alert alert-success mb0 text-center bold"><?php echo $data['first_bowling_label']; ?> Bowling Scorecard</div>
		<div class="table-mockup">
			<div class="thead">
				<div class="tr">
					<div class="th">Player</div>
					<div class="th">Overs</div>
					<div class="th">Maidens</div>
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
					<div class="td text-center"><?php echo $ary['maidens'] ?></div>
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
		<?php
			if(count($data['first_fow']) > 0)
			{
				echo '<div class="fow">';
				echo '<strong>FOW: </strong>';
				foreach($data['first_fow'] as $line)
				{
					echo '<span>'.$line.'</span>';
				}
				echo '</div>';
			}
		?>

		<div class="alert alert-success mb0 top10 text-center"><strong><?php echo $data['first_batting_label']; ?> Parnerships</strong></div>
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th>Players</th>
					<th>&nbsp;</th>
					<th>Runs (Balls)</th>
				</tr>
			</thead>
			<tbody>
				<?php					
					foreach($data['first_partnerships'] as $ary)
					{
						$percent = ceil($ary['runs']*100/$data['first_max_partnership']);
						echo '<tr>';
							echo '<td style="width:200px;">'.$ary['between'].'</td>';
							echo '<td style="padding-right:10px;"><div style="width:'.$percent.'%" class="partnership_bar"></div></td>';
							echo '<td style="width:100px;">'.$ary['runs'].' ('.$ary['balls'].')</td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<div class="alert alert-success mb0 text-center bold">Innings Commentary</div>
		<div class="commentary" style="height: 630px; overflow-y: scroll;">
			<?php foreach($data['first_commentary'] as $line)
			{
				echo $line;
			}
			?>
		</div>
	</div>
</div>


<!-- SECOND INNINGS 	-->
<div class="row top10">
	<div class="col-md-12">
		<div class="alert alert-info text-center">
			<strong><?php echo $data['second_batting_label']; ?></strong> require <strong><?php echo $data['to_win']; ?></strong> runs to win in <strong><?php echo $data['in_overs']; ?></strong> overs at <strong><?php echo $data['rrr']; ?> runs per over</strong>
		</div>

		<div class="text-center">
			<button class="btn btn-primary" ng-click="startSecondInnings($event)">Simulate 2nd Innings</button>
		</div>
	</div>
</div>
<div class="row" id="secondInnings" ng-show="data.showSecondInningsScorecard">
	<?php
	$batsmen = $data['second_batsmen'];
	$bowlers = $data['second_bowlers'];	
	?>
	<div class="col-md-8">		
		<div class="alert alert-success mb0 text-center bold"><?php echo $data['second_batting_label']; ?> Batting Scorecard</div>
		<div class="table-mockup">
			<div class="thead">
				<div class="tr">
					<div class="th">Player</div>
					<div class="th">Status</div>
					<div class="th">Runs</div>					
					<div class="th">4/6</div>					
					<div class="th">SR</div>
				</div>
			</div>
			<div class="tbody">
				<?php foreach($batsmen as $ary)
				{	
					$css_class = ($ary['status'] == 'NOTOUT' ? 'highlight' : 'unhilight' );
					?>
				<div class="tr <?php echo $css_class; ?>">
					<div class="td"><?php echo $ary['name']; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : ( $ary['status'] == "NOTOUT" ? "" : $ary['status']); ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['runs'].' ('.$ary['balls'].')'; ?></div>
					<div class="td text-center"><?php echo $ary['status'] == "DNB" ? "" : $ary['fours'].'/'.$ary['sixes']; ?></div>
					<div class="td text-center"><?php echo $ary['balls'] > 0 ? number_format(($ary['runs']*100/$ary['balls']), 2) : ""; ?></div>
				</div>
				<?php
				}				
			?>
				<div class="tr">
					<div class="td text-right">
						<strong>Total: <?php echo $data['second_total'].'/'.$data['second_wickets'].' in '.$data['second_overs'].' overs ('.$data['second_run_rate'].' rpo)'; ?></strong>
					</div>
				</div>
			</div>
		</div>

		<div class="alert alert-success mb0 text-center bold"><?php echo $data['second_bowling_label']; ?> Bowling Scorecard</div>
		<div class="table-mockup">
			<div class="thead">
				<div class="tr">
					<div class="th">Player</div>
					<div class="th">Overs</div>
					<div class="th">Maidens</div>
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
					<div class="td text-center"><?php echo $ary['maidens'] ?></div>
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
		<?php
			if(count($data['second_fow']) > 0)
			{
				echo '<div class="fow">';
				echo '<strong>FOW: </strong>';
				foreach($data['second_fow'] as $line)
				{
					echo '<span>'.$line.'</span>';
				}
				echo '</div>';
			}
		?>

		<div class="alert alert-success mb0 top10 text-center"><strong><?php echo $data['second_batting_label']; ?> Parnerships</strong></div>
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th>Players</th>
					<th>&nbsp;</th>
					<th>Runs (Balls)</th>
				</tr>
			</thead>
			<tbody>
				<?php					
					foreach($data['second_partnerships'] as $ary)
					{
						$percent = ceil($ary['runs']*100/$data['second_max_partnership']);
						echo '<tr>';
							echo '<td style="width:200px;">'.$ary['between'].'</td>';
							echo '<td style="padding-right:10px;"><div style="width:'.$percent.'%" class="partnership_bar"></div></td>';
							echo '<td style="width:100px;">'.$ary['runs'].' ('.$ary['balls'].')</td>';
						echo '</tr>';
					}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<div class="alert alert-success mb0 text-center bold">Innings Commentary</div>
		<div class="commentary" style="height: 630px; overflow-y: scroll;">
			<?php foreach($data['second_commentary'] as $line)
			{
				echo $line;
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