yamba.App.controller( "FavoController", [ "$scope","$timeout", "$stateParams", "FavoServices", 
										function( $scope, $timeout, $stateParams, FavoServices ) {
	$scope.$parent.App.title = "Favo";
	$scope.favo = {};

	$scope.favoFilter = {
		favcircle_id : $stateParams.favo_id
	}

	FavoServices.fetchSpecificFavo( {
		favcircle_id : $stateParams.favo_id
	}, function( response ) {
		$scope.favo = response.data.Favo;
	} )

} ] );