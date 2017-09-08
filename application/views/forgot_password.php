<div class="row single" ng-controller="forgotPassword">
	<div class="col-md-4 col-md-offset-4">
		<div class="boxed">				
			<div class="row">
				<div class="col-md-12">
					<div ng-show="showErrorAlert" class="alert alert-danger"><i class="fa fa-exclamation-triangle">&nbsp;</i>{{response}}</div>
					<div ng-show="showSuccessAlert" class="alert alert-success"><i class="fa fa-check-square">&nbsp;</i>{{response}}</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					<img src="<?php echo base_url(); ?>assets/images/site_logo.png" alt="SimKit" />
				</div>
			</div>			
			<div class="row top30">
				<div class="col-md-12">
					<div class="subheading">Enter your email address.</div>
		            <div class="form-group">
			            <div class="input-group">
			                <div class="input-group-addon">
			                    <span class="fa fa-envelope">&nbsp;</span>
			                </div>
			                <input type="text" ng-model="data.em" class="form-control" placeholder="Email Address">
			            </div>
		            </div>
		        </div>
		    </div>
		    <div class="row">			    	
		    	<div class="col-xs-6">
		    		<a href="<?php echo site_url(); ?>/Login/NewAccount">Create New Account</a>
		    	</div>
		    	<div class="col-xs-6 text-right">
		    		Have an account? <a class="normal-anchor" href="<?php echo base_url(); ?>">Login</a>
		    	</div>
		    </div>
    		<div class="row top10">
    			<div class="col-md-12">
    				<button ng-click="forgotPassword($event)" class="btn btn-lg btn-block btn-success">Retrive Password</button>
    			</div>
    		</div>
    		<div class="row top10">    		
    			<div class="col-md-12">
    				<small>&copy; <?= date('Y'); ?> All Rights Reserved</small>
    			</div>
    		</div>    		
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/forgot_password.js"></script>