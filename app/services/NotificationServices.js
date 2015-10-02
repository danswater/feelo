yamba.App.factory( "NotificationServices", [ "CookieServices", "BaseServices", 
											function( CookieServices, BaseServices ) {

	return {

		generalNotification : function( params, successCb ){
			var filter = angular.extend( {
				method : "general",
				offset : 0,
				limit : 30
			}, params || {} );
			BaseServices.post( "api/notification", filter, successCb );
		},

		requestNotification : function( params, successCb ){
			var filter = angular.extend( {
				method : "request",
				offset : 0,
				limit : 30
			}, params || {} );
			BaseServices.post( "api/notification", filter, successCb );
		}


	}

} ] );