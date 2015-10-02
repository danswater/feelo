yamba.App.controller( "TrendingController", [ "$scope", "$timeout", "$stateParams", function( $scope, $timeout, $stateParams ) {
	$scope.$parent.App.title = "Trending";

	$scope.trending = {
		filter : {
			creation_date : $stateParams.creation_date
		}
	}

	
} ] );