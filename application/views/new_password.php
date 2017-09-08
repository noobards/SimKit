<div class="row site-breadcrumbs">
	<div class="col-xs-12">
		<span class="bradcrumbs-static-text">You're here</span> <i class="fa fa-chevron-right">&nbsp;</i> <a href="<?php echo base_url(); ?>">Dashboard</a> <i class="fa fa-chevron-right">&nbsp;</i> Change Password
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<div class="box">
			<div class="box-title">
				<div class="box-main-text">Change Password</div>
				<div class="box-helping-text">Choose a new password if you're bored of the current one.</div>
			</div>
			<div class="box-body" ng-controller="changePassword">

				<div ng-show="showErrorAlert" class="alert alert-danger text-center"><i class="fa fa-exclamation-triangle">&nbsp;</i>{{response}}</div>
				<div ng-show="showSuccessAlert" class="alert alert-success text-center"><i class="fa fa-check-square">&nbsp;</i>{{response}}</div>
				
				<form class="form-horizontal" name="change_password" onsubmit="return false;" autocomplete="off" novalidate>
					<div class="bot10">
						<small>Fields marked with <span class="red">*</span> are mandatory and cannot be left blank.</small>
					</div>
					<div class="form-group">
						<label for="opw" class="control-label col-md-6">Current Password: <span class="red">*</span></label>
						<div class="col-md-6">
							<input type="password" id="opw" ng-model="data.opw" ng-required="true" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label for="npw" class="control-label col-md-6">New Password: <span class="red">*</span></label>
						<div class="col-md-6">
							<input type="password" id="npw" ng-model="data.npw" ng-required="true" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label for="cpw" class="control-label col-md-6">Confirm New Password: <span class="red">*</span></label>
						<div class="col-md-6">
							<input type="password" id="cpw" ng-model="data.cpw" ng-required="true" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-6 col-md-offset-6">
							<button class="btn btn-primary" ng-disabled="change_password.$invalid" ng-click="changePassword($event)">Change Password</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
simkit.app.controller("changePassword", function($scope, $http){
	$scope.data = {};
	$scope.response = null;
	$scope.showErrorAlert = false;
	$scope.showSuccessAlert = false;

	$scope.changePassword = function(e){
		$scope.showErrorAlert = false;
		$scope.showSuccessAlert = false;
		var button = jQuery(e.target);
		var button_text = button.text();
		if($scope.data.npw && ($scope.data.npw == $scope.data.cpw))
		{
			if($scope.data.opw)
			{
				$http({
				  method: 'POST',
				  data:$scope.data,
				  url: simkit.baseUrl+'Account/changePassword'
				}).then(function successCallback(response) {
					if(response.statusText == "OK")
					{			
						if(response.data.status == "OK")
						{
							$scope.data = {};
							$scope.showSuccessAlert = true;
						}
						else
						{	
							$scope.showErrorAlert = true;
						}
						$scope.response = response.data.msg;				
					}
					else
					{
						$scope.response = response.statusText;				
						$scope.showErrorAlert = true;
					}
					button.removeAttr('disabled').html(button_text);
				}, function errorCallback(response) {
					button.removeAttr('disabled').html(button_text);
					$scope.response = response.statusText;
					$scope.showErrorAlert = true;
				});
			}
			else
			{
				$scope.showErrorAlert = true;
				$scope.response = "Current password cannot be blank.";
				jQuery('#opw').focus();
			}
		}
		else
		{
			$scope.showErrorAlert = true;
			$scope.response = "New Password and Confirm New Password do not match.";
			jQuery('#npw').focus();
		}
	};
});
</script>