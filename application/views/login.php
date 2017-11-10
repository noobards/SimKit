<div ng-controller="login">

<form name="login_form" action="<?php echo site_url("Login/doLogin") ?>" method="post" ng-submit="doLogin($event)"  novalidate autocomplete="off">

	<div class="row single">

		<div class="col-md-6 col-md-offset-3">

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
	    			<div class="col-xs-6">						
	    				<small>&copy; <?= date('Y'); ?> All Rights Reserved</small>
	    			</div>
					<div class="col-xs-6 text-right">
						<a ng-click="showCredits($event)" href="#">Big thanks to...</a>
					</div>
	    		</div>	

				<div class="row">
					<div class="col-md-12">
						<div class="credits" style="display:none;">
							<h5>A big thank you (<strong>in no particular order</strong>) to the following users:</h5>
							<ul>								
								<li><a href="https://www.planetcricket.org/forums/members/thanksrudolph.88834/" target="_blank">ThanksRudolph</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/pinch-hitter.129787/" target="_blank">Pinch hitter</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/yash.163359/" target="_blank">Yash.</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/rudi.141305/" target="_blank">Rudi</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/presidentevil.140946/" target="_blank">PresidentEvil</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/rebel2k17.63148/" target="_blank">Rebel2k17</a></li>								
								<li><a href="https://www.planetcricket.org/forums/members/zwarrior.164901/" target="_blank">zwarrior</a></li>								
								<li><a href="https://www.planetcricket.org/forums/members/alib.149648/" target="_blank">AliB</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/cerealkiller.158570/" target="_blank">CerealKiller</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/freedom.40138/" target="_blank">Freedom</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/field-marshal.147301/" target="_blank">Field Marshal</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/xtremegamer.170673/" target="_blank">XtremeGamer.</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/xeergdef.187281/" target="_blank">Xeergdef</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/naman-thakur.170104/" target="_blank">Naman Thakur</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/hawkaussie.75674/" target="_blank">HawkAussie</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/na-maloom-afraad.110291/" target="_blank">Na Maloom Afraad</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/sam.151099/" target="_blank">Sam.</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/swacker.95981/" target="_blank">swacker</a></li>
								<li><a href="https://www.planetcricket.org/forums/members/akshay.171234/" target="_blank">Akshay.</a></li>
							</ul>
							
							<p>for the continued encouragement and valuable inputs that went into making this simulator. It couldn't have been done without you guys!</p>
						</div>
					</div>
				</div>

	        </div>

	    </div>

	</div>

</form>

</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/login.js"></script>