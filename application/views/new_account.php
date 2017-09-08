<form name="register_form" onsubmit="return false;" novalidate autocomplete="off">

	<div class="row single" ng-controller="register">

		<div class="col-md-4 col-md-offset-4">

			<div class="boxed">				

				<div class="row">

					<div class="col-md-12 text-center">

						<img src="<?php echo base_url(); ?>assets/images/site_logo.png" alt="SimKit" />

					</div>

				</div>



				<div class="row top30">

					<div class="col-md-12">

						<div class="subheading">You can register using the form below.</div>

						<p><strong>Account Details</strong></p>

						<div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-user">&nbsp;</span>

				                </div>

				                <input type="text" ng-required="true" ng-model="data.un" class="form-control" placeholder="Username">

				            </div>

			            </div>

			            <div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-lock">&nbsp;</span>

				                </div>

				                <input type="password" ng-required="true" ng-model="data.pw" class="form-control" placeholder="Password">

				            </div>

			            </div>

			            <div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-rotate-left">&nbsp;</span>

				                </div>

				                <input type="password" ng-required="true" style="padding-left:8px;" ng-model="data.cpw" class="form-control" placeholder="Confirm Password">

				            </div>

			            </div>

			            <p><strong>Personal Details</strong></p>

			            <div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-hand-o-up">&nbsp;</span>

				                </div>

				                <input type="text" ng-required="true" ng-model="data.fn" class="form-control" placeholder="First Name">

				            </div>

			            </div>

			            <div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-hand-peace-o">&nbsp;</span>

				                </div>

				                <input type="text" ng-required="true" ng-model="data.ln" class="form-control" placeholder="Last Name">

				            </div>

			            </div>

			            <div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-envelope">&nbsp;</span>

				                </div>

				                <input type="text" ng-required="true" ng-model="data.em" class="form-control" placeholder="Email Address">

				            </div>

			            </div>

			            <div class="alert alert-warning">
			            	<i class="fa fa-info-circle">&nbsp;</i> Please ensure you use a valid email address. In case of <strong>"Forgot your password?"</strong>, the new password will be sent to <strong>this email address</strong>.
			            </div>

			        </div>

			    </div>



			    <div class="row">			    	

			    	<div class="col-xs-6">

			    			<a href="<?php echo site_url(); ?>/Login/ForgotPassword">Forgot your password?</a>

			    	</div>

			    	<div class="col-xs-6 text-right">

			    		Have an account? <a class="normal-anchor" href="<?php echo base_url(); ?>">Login</a>

			    	</div>

			    </div>



	    		<div class="row top10">

	    			<div class="col-md-12">

	    				<button ng-disabled="register_form.$invalid" ng-click="createAccount($event)" class="btn btn-lg btn-block btn-success">Create Account</button>

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

</form>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/create_account.js"></script>