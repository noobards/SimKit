	<div class="row site-breadcrumbs">
		<div class="col-xs-12">
			<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> Profile
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-6 col-md-offset-3" ng-controller="profile">			
			<?php

			if($this->session->flashdata('flash'))
			{

				$flash = $this->session->flashdata('flash');
				if($flash['status'] == 'OK')
				{	?>
					<div class="row">
						<div class="col-md-12">
							<div class="alert alert-info" style="margin-bottom: 0;">
								<i class="fa fa-check">&nbsp;</i><?= $flash['msg'];	?>
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
			<div class="box">
				<div class="box-title">
					<div class="box-main-text">My Profile</div>
					<div class="box-helping-text">Modify your preferences/settings.</div>
				</div>
				<div class="box-body box-body-max">
					<form class="form-horizontal" id="save_preferences_form" name="save_preferences_form" onsubmit="return false;" autocomplete="off" novalidate>
						<div class="bot10">
							<small>Fields marked with <span class="red">*</span> are mandatory and cannot be left blank.</small>
						</div>
						<div class="form-group">
							<label for="un" class="control-label col-md-6">Username:</label>
							<div class="col-md-6">
								<input type="text" id="un" disabled="disabled" ng-model="data.un" ng-required="false" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="em" class="control-label col-md-6">Email Address:</label>
							<div class="col-md-6">
								<input type="text" id="em" disabled="disabled" ng-model="data.em" ng-required="false" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="fn" class="control-label col-md-6">First Name: <span class="red">*</span></label>
							<div class="col-md-6">
								<input type="text" id="fn" ng-model="data.fn" ng-required="true" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="ln" class="control-label col-md-6">Last Name: <span class="red">*</span></label>
							<div class="col-md-6">
								<input type="text" id="ln" ng-model="data.ln" ng-required="true" class="form-control" />
							</div>
						</div>
						<div class="form-group hide">
							<label for="dob" class="control-label col-md-6">Date of Birth: </label>
							<div class="col-md-6">
								<input type="text" id="dob" ng-model="data.dob" ng-required="false" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label for="gen" class="control-label col-md-6">Gender:</label>
							<div class="col-md-6">
								<select id="gen" ng-model="data.gender" ng-required="false" class="form-control">
									<option value="">Choose One</option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="tz" class="control-label col-md-6">Timezone: <span class="red">*</span></label>
							<div class="col-md-6">
								<select id="tz" ng-model="data.tz" ng-required="true" class="form-control">
									<option value="">Select One</option>				
									<option value="Pacific/Midway">(UTC-11:00) American Samoa</option>				
									<option value="Pacific/Honolulu">(UTC-10:00) Hawaii</option>				
									<option value="Pacific/Marquesas">(UTC-09:30) Marquesas Islands</option>								
									<option value="America/Anchorage">(UTC-09:00) Alaska</option>								
									<option value="America/Los_Angeles">(UTC-08:00) Pacific Time (US &amp; Canada)</option>								
									<option value="America/Santa_Isabel">(UTC-08:00) Baja California</option>								
									<option value="America/Tijuana">(UTC-08:00) Tijuana</option>								
									<option value="America/Denver">(UTC-07:00) Mountain Time (US &amp; Canada)</option>								
									<option value="America/Chihuahua">(UTC-07:00) Chihuahua, La Paz, Mazatlan</option>								
									<option value="America/Phoenix">(UTC-07:00) Arizona</option>								
									<option value="America/Chicago">(UTC-06:00) Central Time (US &amp; Canada)</option>								
									<option value="America/Belize">(UTC-06:00) Saskatchewan, Central America</option>								
									<option value="America/Mexico_City">(UTC-06:00) Guadalajara, Mexico City, Monterrey</option>								
									<option value="Pacific/Easter">(UTC-06:00) Easter Island</option>								
									<option value="America/New_York">(UTC-05:00) Eastern Time (US &amp; Canada)</option>								
									<option value="America/Havana">(UTC-05:00) Cuba</option>								
									<option value="America/Bogota">(UTC-05:00) Bogota, Lima, Quito</option>								
									<option value="America/Caracas">(UTC-04:30) Caracas</option>								
									<option value="America/Halifax">(UTC-04:00) Atlantic Time (Canada)</option>								
									<option value="America/Goose_Bay">(UTC-04:00) Atlantic Time (Goose Bay)</option>								
									<option value="America/Asuncion">(UTC-04:00) Asuncion</option>								
									<option value="America/Santiago">(UTC-04:00) Santiago</option>								
									<option value="America/Cuiaba">(UTC-04:00) Cuiaba</option>								
									<option value="America/La_Paz">(UTC-04:00) Georgetown, La Paz, Manaus, San Juan</option>								
									<option value="America/St_Johns">(UTC-03:30) Newfoundland</option>								
									<option value="America/Argentina/Buenos_Aires">(UTC-03:00) Buenos Aires</option>								
									<option value="America/Argentina/San_Luis">(UTC-03:00) San Luis</option>								
									<option value="America/Argentina/Mendoza">(UTC-03:00) Argentina, Cayenne, Fortaleza</option>								
									<option value="Atlantic/Stanley">(UTC-03:00) Falkland Islands</option>								
									<option value="America/Godthab">(UTC-03:00) Greenland</option>								
									<option value="America/Montevideo">(UTC-03:00) Montevideo</option>								
									<option value="America/Sao_Paulo">(UTC-03:00) Brasilia</option>								
									<option value="America/Miquelon">(UTC-03:00) Saint Pierre and Miquelon</option>								
									<option value="America/Noronha">(UTC-02:00) Mid-Atlantic</option>								
									<option value="Atlantic/Cape_Verde">(UTC-01:00) Cape Verde Is.</option>								
									<option value="Atlantic/Azores">(UTC-01:00) Azores</option>								
									<option value="Europe/London">(UTC) Dublin, Edinburgh, Lisbon, London</option>								
									<option value="Africa/Casablanca">(UTC) Casablanca</option>								
									<option value="Atlantic/Reykjavik">(UTC) Monrovia, Reykjavik</option>								
									<option value="Europe/Amsterdam">(UTC+01:00) Central European Time</option>								
									<option value="Africa/Algiers">(UTC+01:00) West Central Africa</option>								
									<option value="Africa/Windhoek">(UTC+01:00) Windhoek</option>								
									<option value="Africa/Tunis">(UTC+01:00) Tunis</option>								
									<option value="Europe/Athens">(UTC+02:00) Eastern European Time</option>								
									<option value="Africa/Johannesburg">(UTC+02:00) South Africa Standard Time</option>								
									<option value="Europe/Kaliningrad">(UTC+02:00) Kaliningrad</option>								
									<option value="Asia/Amman">(UTC+02:00) Amman</option>								
									<option value="Asia/Beirut">(UTC+02:00) Beirut</option>								
									<option value="Africa/Cairo">(UTC+02:00) Cairo</option>								
									<option value="Asia/Jerusalem">(UTC+02:00) Jerusalem</option>								
									<option value="Asia/Gaza">(UTC+02:00) Gaza</option>								
									<option value="Asia/Damascus">(UTC+02:00) Syria</option>								
									<option value="Europe/Moscow">(UTC+03:00) Moscow, St. Petersburg, Volgograd</option>								
									<option value="Europe/Minsk">(UTC+03:00) Minsk</option>								
									<option value="Africa/Nairobi">(UTC+03:00) Nairobi, Baghdad, Kuwait, Qatar, Riyadh</option>								
									<option value="Asia/Tehran">(UTC+03:30) Tehran</option>								
									<option value="Asia/Dubai">(UTC+04:00) Abu Dhabi, Muscat, Tbilisi</option>								
									<option value="Asia/Yerevan">(UTC+04:00) Yerevan</option>								
									<option value="Asia/Baku">(UTC+04:00) Baku</option>								
									<option value="Indian/Mauritius">(UTC+04:00) Mauritius</option>								
									<option value="Asia/Kabul">(UTC+04:30) Kabul</option>								
									<option value="Asia/Yekaterinburg">(UTC+05:00) Ekaterinburg</option>								
									<option value="Asia/Tashkent">(UTC+05:00) Tashkent, Karachi</option>								
									<option value="Asia/Kolkata">(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>								
									<option value="Asia/Kathmandu">(UTC+05:45) Kathmandu</option>								
									<option value="Asia/Novosibirsk">(UTC+06:00) Novosibirsk</option>								
									<option value="Asia/Dhaka">(UTC+06:00) Astana, Dhaka</option>								
									<option value="Asia/Almaty">(UTC+06:00) Almaty, Bishkek, Qyzylorda</option>								
									<option value="Asia/Rangoon">(UTC+06:30) Yangon (Rangoon)</option>								
									<option value="Asia/Krasnoyarsk">(UTC+07:00) Krasnoyarsk</option>								
									<option value="Asia/Bangkok">(UTC+07:00) Bangkok, Hanoi, Jakarta</option>								
									<option value="Asia/Irkutsk">(UTC+08:00) Irkutsk</option>								
									<option value="Asia/Hong_Kong">(UTC+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>								
									<option value="Asia/Singapore">(UTC+08:00) Kuala Lumpur, Singapore</option>								
									<option value="Australia/Perth">(UTC+08:00) Perth</option>								
									<option value="Asia/Yakutsk">(UTC+09:00) Yakutsk</option>								
									<option value="Asia/Tokyo">(UTC+09:00) Osaka, Sapporo, Tokyo</option>								
									<option value="Asia/Seoul">(UTC+09:00) Seoul</option>								
									<option value="Australia/Adelaide">(UTC+09:30) Adelaide</option>								
									<option value="Australia/Darwin">(UTC+09:30) Darwin</option>								
									<option value="Asia/Vladivostok">(UTC+10:00) Vladivostok</option>								
									<option value="Asia/Magadan">(UTC+10:00) Magadan</option>								
									<option value="Australia/Brisbane">(UTC+10:00) Brisbane, Guam</option>								
									<option value="Australia/Sydney">(UTC+10:00) Sydney, Melbourne, Hobart</option>								
									<option value="Pacific/Noumea">(UTC+11:00) Solomon Is., New Caledonia</option>								
									<option value="Pacific/Norfolk">(UTC+11:30) Norfolk Island</option>								
									<option value="Asia/Anadyr">(UTC+12:00) Anadyr, Kamchatka</option>								
									<option value="Pacific/Auckland">(UTC+12:00) Auckland, Wellington</option>								
									<option value="Pacific/Fiji">(UTC+12:00) Fiji</option>								
									<option value="Pacific/Chatham">(UTC+12:45) Chatham Islands</option>								
									<option value="Pacific/Tongatapu">(UTC+13:00) Nuku'alofa</option>								
									<option value="Pacific/Apia">(UTC+13:00) Apia, Samoa</option>								
									<option value="Pacific/Kiritimati">(UTC+14:00) Kiritimati</option>
								</select>
							</div>
						</div>
						<div class="form-group text-center">												
							<button class="btn btn-primary" ng-disabled="save_preferences_form.$invalid" ng-click="savePreferences($event)">Save Preferences</button>							
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pages/profile.js"></script>