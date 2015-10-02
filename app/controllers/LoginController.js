yamba.App.controller( "LoginController", [ "$scope", "$timeout", "$location", "UserServices", function( $scope, $timeout, $location, UserServices ) {
	$scope.$parent.App.title = "Login";

	if( UserServices.isLogin() ){
		$location.path( "/main/activity-feed" );
	}

	$scope.authenticate = function( form ){
		if( form.$valid ){
			UserServices.login( $scope.username, $scope.password, function( response ) {
				$scope.$parent.App.user = response.User;
				// console.log(response);
				$location.path( "/main/activity-feed" );
			}, function( response ) {
				$scope.errorMsg = response.error[ 0 ];
			} )
		}
	}
	
} ] );