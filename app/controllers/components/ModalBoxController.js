yamba.App .controller('ModalBoxController', ['$scope', 'user', 'close', "BoxServices",
											function( $scope, user, close, BoxServices ) {
	$scope.display = true;
	$scope.user = user;
	$scope.boxes = [];

	BoxServices.getBox( function( response ) {
		$scope.boxes = response.Box.chunk( 3 );
	} )

	$scope.addBox = function( circle_id ){
		BoxServices.addUserToBox( circle_id, $scope.user.user_id, function(){ } );
	}

	$scope.close = function() {
	    $scope.display = false;
		close();
	};

}]);