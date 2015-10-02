yamba.App.controller( "ActivityFeedController", [ "$scope", "$stateParams", "FeedServices", 
												function( $scope, $stateParams, FeedServices ) {
	$scope.$parent.App.title = "Activity Feed";

	$scope.activity_feed_filter = {};

	if( typeof $stateParams.box_id != "undefined" ){
		$scope.activity_feed_filter = {
			circle_id : $stateParams.box_id
		}
	}

} ] );