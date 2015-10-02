yamba.App.factory( "SearchServices", [ "CookieServices", "BaseServices", function( CookieServices, BaseServices ) {

	return {

		searchHashtag : function( filter, callback ){

			filter = angular.extend( {
				method : "fetchHashtag",
				offset : 0
			}, filter )
			BaseServices.post( "api-2/search", filter, callback)

		},

		searchFeed : function( filter, callback ){
			
			filter = angular.extend( {
				method : "fetchMedia",
				offset : 0
			}, filter )
			BaseServices.post( "api/search", filter, callback)

		},

		searchUser : function( filter, callback ){
			
			filter = angular.extend( {
				method : "fetchUser",
				offset : 0
			}, filter )
			BaseServices.post( "api/search", filter, callback)

		},

		searchFavo : function( filter, callback ){
			
			filter = angular.extend( {
				method : "fetchFavo",
				offset : 0
			}, filter )
			BaseServices.post( "api/search", filter, callback)

		},

	}


} ] );