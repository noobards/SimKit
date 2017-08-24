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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/angular.1.6.4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/app.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/functions.js"></script>
</head>
<body>
<div class="viewport">
	<div class="container-fluid">
		<div class="row split-container">
			<div class="left-column">				
				<div class="site-name">
					<h1>SMART AI</h1>
				</div>

				<div class="user-meta">
					<div class="text-center">
						<?php echo img(array('src'=>'assets/images/blank_profile_pic.png', 'alt'=> 'Profile Pic', 'class'=>'profile_pic')); ?>						
					</div>
					<div class="text-center top10">
						<span class="profile-user-name"><?php echo $this->session->username; ?></span>
					</div>
					<div class="text-center">
						<span class="grey-text">SmartAI <?php echo ($this->session->is_admin == "1" ? "Administrator" : "Manager"); ?></span>
					</div>
				</div>

				<div class="user-nav">
					<ul class="left-nav">
						<li><a href="<?php echo site_url(); ?>/Dashboard" class="<?php echo ($page == 'dashboard' ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-dashboard">&nbsp;</span><span class="nav-text">Dashboard</span></a></li>						
						<li><a href="<?php echo site_url(); ?>/Players" class="<?php echo (($page == 'players' || $page == 'confirm_file_upload' || $page == 'list_players' || $page == 'edit_players') ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-group">&nbsp;</span><span class="nav-text">Players</span></a></li>
						<li><a href="<?php echo site_url(); ?>/Teams" class="<?php echo (($page == 'teams' || $page == 'edit_team') ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-list-alt">&nbsp;</span><span class="nav-text">Teams</span></a></li>
						<?php if($this->session->is_admin == "1") {	?>
						<li><a href="<?php echo site_url(); ?>/Admin/SiteMembers" class="<?php echo ($page == 'site_members' ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-user">&nbsp;</span><span class="nav-text">Site Members</span></a></li>
						<?php }	?>
						<?php /*
						<li><a href="#"><span class="nav-icon fa fa-bar-chart">&nbsp;</span><span class="nav-text">Statistics</span></a></li>						
						<li><a href="#"><span class="nav-icon fa fa-cogs">&nbsp;</span><span class="nav-text">Preferences</span></a></li>
						<li><a href="#"><span class="nav-icon fa fa-commenting">&nbsp;</span><span class="nav-text">Contact Developer</span></a></li>
						*/ ?>
						<li><a href="<?php echo site_url(); ?>/Logout"><span class="nav-icon fa fa-power-off">&nbsp;</span><span class="nav-text">Log Off</span></a></li>
					</ul>
				</div>
			</div>
			<div class="right-column">
				<div class="top-bar">
					<div class="clearfix">
						<div class="col-xs-6">
							<i class="fa fa-bars fa-2x mobile-nav-trigger">&nbsp;</i>
						</div>
						<div class="col-xs-6 text-right">
							<a class="power-off" href="<?php echo site_url(); ?>/Logout"><i class="fa fa-power-off">&nbsp;</i></a>
						</div>
					</div>
					
					<div class="user-nav">
						<ul class="left-nav menu-collapsed" id="mobile-nav">
							<li><a href="<?php echo site_url(); ?>/Dashboard" class="<?php echo ($page == 'dashboard' ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-dashboard">&nbsp;</span><span class="nav-text">Dashboard</span></a></li>						
							<li><a href="<?php echo site_url(); ?>/Players" class="<?php echo (($page == 'players' || $page == 'confirm_file_upload' || $page == 'list_players' || $page == 'edit_players') ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-group">&nbsp;</span><span class="nav-text">Players</span></a></li>
							<li><a href="<?php echo site_url(); ?>/Teams" class="<?php echo (($page == 'teams' || $page == 'edit_team') ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-list-alt">&nbsp;</span><span class="nav-text">Teams</span></a></li>						
							<li><a href="<?php echo site_url(); ?>/Logout"><span class="nav-icon fa fa-power-off">&nbsp;</span><span class="nav-text">Log Off</span></a></li>
							<?php if($this->session->is_admin == "1") {	?>
							<li><a href="<?php echo site_url(); ?>/Admin/SiteMembers" class="<?php echo ($page == 'site_members' ? 'active-anchor' : 'non-active'); ?>"><span class="nav-icon fa fa-user">&nbsp;</span><span class="nav-text">Site Members</span></a></li>
							<?php }	?>
						</ul>
					</div>
				</div>

				<div class="container-fluid">