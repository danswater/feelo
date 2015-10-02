yamba.App.directive( "ybNotificationMessageBuilder", [ "$timeout", "FollowServices",
									function( $timeout, FollowServices ) {


	return {
		restrict : "AC",
		scope : {
			layout : '@',
			message : '=',
		},
		templateUrl : function( $scope, element, attr ) {
			return "app/views/directives/notifications/layout.php";
		},
		link : function( $scope, element, attr ){

			$scope.confirmFollowRequest = function( confirm, user_id ){

				if( confirm == true ){
					FollowServices.confirmFollowRequest( user_id, function() { 

					} );
				}
				else{
					FollowServices.ignoreFollowRequest( user_id, function() { 

					} );
				}
				
				$( element ).remove();
			}

		}
	}	

} ] );