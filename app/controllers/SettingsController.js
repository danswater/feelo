yamba.App.controller( "SettingsController", [ "$scope", "$timeout", "SettingsServices", "UserServices", "StaticDataServices",
											function( $scope, $timeout, SettingsServices, UserServices, StaticDataServices ) {
	$scope.$parent.App.title = "Settings";

	/***** variables *****/
	$scope.current_user = UserServices.getLoginUser();

	console.log( $scope.current_user );

	$scope.settings = {
		timezones : StaticDataServices.getTimezone()
	};
	
	$scope.input_disabled = {
		email : true,
		username : true,
		displayname : true,
		password : true,
		timezone : true,
	};

	$scope.pass = {
		password: "",
		new_password: "",
		confirm_password: ""
	};

	$scope.errors = {
		email : "",
		username : "",
		displayname : "",
		password : "",
		timezone : "",
		locale : ""
	};

	/***** variables *****/


	$scope.updateGeneralInfo = function( field, name ){
		var params = {};
		params[ field ] = $scope.current_user[ field ];
		$scope.errors[ field ] = "";

		console.log( params[ field ] );

		if( typeof params[ field ] == "object" ){
			params[ field ] = params[ field ][ name ];
			//$scope.current_user[ field ] = params[ field ];
		}

		SettingsServices.updateGeneralInfo( params, function( response ) {

			if( response.success ){
				$scope.input_disabled[ field ] = true;
			}
			else{
				$scope.errors[ field ] = response.errors[ 0 ];
			}

		} )
	}


	$scope.changePassword = function(){

		$scope.errors.password = "";

		SettingsServices.updateGeneralInfo( {
			password : $scope.pass.password,
			new_password : $scope.pass.new_password,
			confirm_password : $scope.pass.confirm_password
		}, function( response ) {

			if( response.success ){
				$scope.input_disabled.password = true;
			}
			else{
				$scope.errors.password = response.errors[ 0 ];
			}

		} )

	}



	
} ] );