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
<?php echo link_tag("assets/css/stream.css?v=1"); ?>
<?php echo link_tag("assets/css/responsive.css?v=1"); ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/angular.1.6.4.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/app.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/functions.js"></script>
</head>
<body ng-controller="template">
	<div class="header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-2 col-sm-3 col-xs-12">
					<div class="site_logo">
						<i class="fa fa-bars fa-3x mobile_menu_bars" style="color:#fff;">&nbsp;</i>
						<?php echo img(array('src'=>'assets/images/site_logo_white.png', 'alt'=> 'SimKit')); ?>
					</div>
				</div>
				<div class="col-md-10 col-sm-9 hidden-xs">
					<div class="main_menu">					
						<ul class="clearfix">						
							
						</ul>
					</div>
				</div>				
			</div>			
		</div>
	</div>
	<div id="mobile_menu" class="is_collapsed">
		
	</div>
	<div class="container-fluid">	