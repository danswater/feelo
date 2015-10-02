yamba.App .controller('TrendingModalController', ['$scope', '$state', "FeedServices",
											function( $scope, $state, FeedServices ) {

	$scope.current_filter = 'today';
	$scope.loadingTrend = false;
	$scope.trending = [];
	var trendOffset = 0;

	$scope.closeTrendingModal = function() {
		$( "#trendingContainerForm" ).css( {
			display : 'none'
		} );
	}

	$scope.changeTrending = function( filter ){
		$scope.trending = [];
		trendOffset = 0;
		showTrending( { 
			creation_date : filter
		} );
	} 


	$scope.showMoreTrending = function(){
		trendOffset++;
		showTrending( $scope.current_filter, true );
	}

	/**
	 * @param
	 *	String filter |today|week|month|
	 */
	showTrending = function( filter, append ){
		append = !!append;
		var param = angular.extend( {
			creation_date : 'today',
			offset : trendOffset
		}, filter );

		$scope.current_filter = param.creation_date;
		$scope.loadingTrend = true;
		FeedServices.getFeedsTrending( param, function( response ) {
			if( append ){
				$scope.trending.merge( response.Activity_Feed )
			}else{
				$scope.trending = response.Activity_Feed;
			}
			$scope.loadingTrend = false;
		} )
	}

	
	// init
	showTrending( { 
		creation_date : 'today'
	} );


}]);