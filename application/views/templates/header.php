<!DOCTYPE html>
<html lang="en-us" ng-app="SimKit">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SimKit</title>
<link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>assets/images/favicon.ico?v=1" />
<?php echo link_tag("assets/css/bootstrap.min.css"); ?>
<?php echo link_tag("assets/css/bootstrap.theme.min.css"); ?>
<?php echo link_tag("assets/css/font_awesome.min.css"); ?>
<?php echo link_tag("assets/css/style.css?v=1"); ?>
<?php echo link_tag("assets/css/responsive.css?v=1"); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/angular.1.6.4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/app.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/functions.js"></script>
</head>
<body>
	<div class="header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3 col-sm-3 col-xs-8">
					<div class="site_logo">
						<i class="fa fa-bars fa-3x mobile_menu_bars" style="color:#fff;">&nbsp;</i>
						<a href="<?php echo site_url(); ?>/Dashboard"><?php echo img(array('src'=>'assets/images/site_logo_white.png', 'alt'=> 'SimKit')); ?></a>
					</div>
				</div>
				<div class="col-md-6 col-sm-8 hidden-xs">
					<div class="main_menu">					
						<ul class="clearfix">						
							<li><a href="<?php echo site_url(); ?>/Dashboard" class="<?php echo ($page == 'dashboard' ? 'menu_active' : 'menu_inactive'); ?>"><i class="fa fa-dashboard">&nbsp;</i>Dashboard</a></li>
							<li><a href="<?php echo site_url(); ?>/Players" class="<?php echo (($page == 'players' || $page == 'confirm_file_upload' || $page == 'list_players' || $page == 'edit_player') ? 'menu_active' : 'menu_inactive'); ?>"><i class="fa fa-users">&nbsp;</i>Players</a></li>
							<li><a href="<?php echo site_url(); ?>/Teams" class="<?php echo (($page == 'teams' || $page == 'edit_team') ? 'menu_active' : 'menu_inactive'); ?>"><i class="fa fa-list-alt">&nbsp;</i>Teams</a></li>
							<?php if($this->session->is_admin == "1") {	?>
							<li><a href="<?php echo site_url(); ?>/Admin/SiteMembers" class="<?php echo ($page == 'admin/site_members' ? 'menu_active' : 'menu_inactive'); ?>"><i class="fa fa-user">&nbsp;</i>Site Members</a></li>
							<?php }	?>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-1 col-xs-4">
					<div class="power-off">
						<a href="<?php echo site_url(); ?>/Logout"><i class="fa fa-power-off">&nbsp;</i></a>
					</div>
				</div>
			</div>			
		</div>
	</div>
	<div id="mobile_menu" class="is_collapsed">
		
	</div>
	<div class="container-fluid">