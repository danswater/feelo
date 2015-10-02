yamba.App.controller( "AppController", [ "$scope", "$state", "UserServices", "BoxServices", 
									function( $scope, $state, UserServices, BoxServices ) {

	$scope.App = {
		title : "Welcome test",
		user : {}
	};

	// for global variable
	$scope.Constant = {
		boxes : []	
	}

	BoxServices.getBox( function( response ) {
		$scope.Constant.boxes = response.Box;
	} )



	if( UserServices.isLogin() ){
		$scope.App.user = UserServices.getLoginUser();
	}

	$scope.logout = function( ){
		UserServices.logout();
		$state.go('login');
	}

	



} ] );