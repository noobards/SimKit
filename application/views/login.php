<div ng-controller="login">

<form name="login_form" action="<?php echo site_url("Login/doLogin") ?>" method="post" ng-submit="doLogin($event)"  novalidate autocomplete="off">

	<div class="row single">

		<div class="col-md-4 col-md-offset-4">

			<div class="boxed">

				<?php

					if($this->session->flashdata('flash'))

					{

						$flash = $this->session->flashdata('flash');

						if($flash['status'] == 'OK')

						{	?>

							<div class="row">

								<div class="col-md-12">									

									<div class="alert alert-success"><i class="fa fa-check">&nbsp;</i><?php echo $flash['msg']; ?></div>

								</div>

							</div>

					<?php

						}

						else

						{	?>

							<div class="row">

								<div class="col-md-12">

									<div class="alert alert-danger"><i class="fa fa-exclamation-triangle">&nbsp;</i><?php echo $flash['msg']; ?></div>

								</div>

							</div>

					<?php		

						}		

					}

				?>

				<div class="row">

					<div class="col-md-12 text-center">

						<img src="<?php echo base_url(); ?>assets/images/site_logo.png" alt="SimKit" />

					</div>

				</div>



				<div class="row top20">

					<div class="col-md-12">

						<div class="subheading"><strong>Welcome, </strong> Please login...</div>

						<div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-user">&nbsp;</span>

				                </div>

				                <input type="text" ng-required="true" name="un" ng-model="data.un" class="form-control" placeholder="Username" autofocus="true">

				            </div>

			            </div>

			            <div class="form-group">

				            <div class="input-group">

				                <div class="input-group-addon">

				                    <span class="fa fa-lock">&nbsp;</span>

				                </div>

				                <input type="password" name="pw" ng-required="true" ng-model="data.pw" class="form-control" placeholder="Password">

				            </div>

			            </div>

			        </div>

			    </div>



			    <div class="row">			    	

			    	<div class="col-xs-6">

			    			<a href="<?php echo site_url(); ?>/Login/ForgotPassword">Forgot your password?</a>

			    	</div>

			    	<div class="col-xs-6 text-right">

			    		<a href="<?php echo site_url(); ?>/Login/NewAccount">Create an account</a>

			    	</div>

			    </div>



	    		<div class="row top10">

	    			<div class="col-md-12">

	    				<button type="submit" ng-disabled="login_form.$invalid" class="btn btn-lg btn-block btn-success btn-login">Login</button>

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

</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/login.js"></script>