
	<div class="row site-breadcrumbs">
		<div class="col-xs-12">
			<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> Site Members
		</div>
	</div>

	<?php
	if($this->session->flashdata('flash'))
	{
		$flash = $this->session->flashdata('flash');
		if($flash['status'] == 'OK')
		{	?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-success" style="margin-bottom: 0;">
						<p>Summary of records: </p>
						<ul>
							<li><strong>Total records: </strong><?php echo $flash['total'] ; ?></li>
							<li><strong>Successfull inserts: </strong><?php echo $flash['inserted'] ; ?></li>
							<li><strong>Failed inserts: </strong><?php echo $flash['failed'] ; ?></li>
						</ul>										
					</div>
				</div>
			</div>
	<?php
		}
		else
		{	?>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger" style="margin-bottom: 0;"><i class="fa fa-exclamation-triangle">&nbsp;</i><?php echo $flash['msg']; ?></div>
				</div>
			</div>
	<?php		
		}		
	}
	?>

	<div class="row">
		<div class="col-md-12" ng-controller="listSiteMembers">
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">Site Member(s)</div>
					<div class="box-helping-text">List of registered members.</div>
				</div>
				<div class="box-body box-body-max">
					<table class="table table-bordered table-striped">
						<thead>
							<tr class="alert-success">
								<th>S.No</th>
								<th>Username</th>
								<th>Full Name</th>																								
								<th>Added On</th>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="member in data.site_members">
								<td>{{$index + 1}}</td>
								<td>{{member.un}}</td>
								<td>{{member.full_name}}</td>
								<td>{{member.time}}</td>								
							</tr>
							<tr ng-if="data.site_members.length == 0">
								<td colspan="4">No records found</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/site_members.js"></script>