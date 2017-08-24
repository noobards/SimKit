<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo site_url(); ?>/players">Players</a> <i class="fa fa-chevron-right">&nbsp;</i> Confirm Upload
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<p class="alert alert-danger"><i class="fa fa-exclamation-triangle">&nbsp;</i> You're about to insert the following players into the database. Please confirm the data</p>

		<div class="vscroll h400 pad10" style="background-color: #fff; border:1px solid #ccc;">
			<table class="table table-striped">
				<thead>
					<?php
					$table = $this->session->userdata('file_upload_data');
					foreach($table['header'] as $row_number => $header_array)
					{
						echo '<tr class="alert-success">';
							echo '<th>S.No</th>';
						foreach($header_array as $colname => $colvalue)
						{
							echo '<th>'.$colvalue.'</th>';
						}
						echo '</tr>';
					}
					?>
				</thead>
				<tbody>
					<?php					
					foreach($table['values'] as $row_number => $values_array)
					{
						if(isset($values_array['A']) && trim($values_array['A']) != '')						
						{
							echo '<tr>';
							echo '<td>'.($row_number - 1).'</td>';
							foreach($values_array as $colname => $colvalue)
							{
								echo '<td>'.$colvalue.'</td>';
							}
							echo '</tr>';
						}						
					}
					?>
				</tbody>
			</table>
		</div>
		<div class="text-center top20">
			<form action="<?php echo site_url(); ?>/Players/ImportPlayers" method="post">
				<button type="submit" onclick="this.innerHTML='Sending…';" class="btn btn-primary">Confirm</button>&nbsp;<a href="<?php echo site_url(); ?>/players" class="btn btn-danger">Cancel</a>
			</form>	
		</div>
	</div>
</div>