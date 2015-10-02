yamba.App.controller( "NotificationController", [ "$scope", "$state", "$timeout", "$stateParams", "NotificationServices", 
											function( $scope, $state, $timeout, $stateParams, NotificationServices ) {

	$scope.$parent.App.title = "Notification";
	$scope.notifications = [];
	var currentOffset = 1;

	$scope.notification_type = $stateParams.type;

	var getNotification = function( type ){

		if( type == "request" ) {

			NotificationServices.requestNotification( {
				offset : currentOffset
			}, function( response ) {
				$scope.notifications.merge( response.yamba );
			} )

		}
		else{
			
			NotificationServices.generalNotification( {
				offset : currentOffset
			}, function( response ) {
				$scope.notifications.merge( response.yamba );
			} )

		}

	}	

	getNotification( $stateParams.type );

	$( window ).scroll( function( event ){
		var scrollBottom = $( document ).height() - $( window ).height() - $( window ).scrollTop();
		if( scrollBottom == 0 ){	

			if( $state.is('main.notification') ) {
				currentOffset++;
				getNotification( $stateParams.type );
			}

		}
	} );


	
} ] );