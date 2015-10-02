yamba.App.factory( "FeedServices", [ "CookieServices", "BaseServices", function( CookieServices, BaseServices ) {

	return {

		classes : [ "w1", "w2", "w3" ],

		relatedPost : function( project_id, callback ){
			var filter = { project_id : project_id }
			BaseServices.post( "api-2/relatedpost", filter, callback );
		},

		getActivityFeeds : function( filter, callback ){
			var filter = angular.extend( { 
				offset : 0
			}, filter );
			BaseServices.post( "api/activityfeed", filter, callback )

		}, 

		getFeaturedFeeds : function( filter, callback ){
			var filter = angular.extend( { 
				offset : 0
			}, filter );
			BaseServices.post( "api/featured", filter, callback )
		},

		getFeedByProjectId : function( project_id, callback ){

			BaseServices.post( "api/specificprofilepost", {
				project_id : project_id	
			}, callback );

		},

		getFeedsByHashtag : function( filter, callback ){
			var filter = angular.extend( { 
				offset : 0,
				method : 'fetchByHashtagName'
			}, filter );
			BaseServices.post( "api-2/feedsearch", filter, callback )
		}, 

		getFeedsTrending : function( filter, callback ){
			var filter = angular.extend( { 
				offset : 0,
				method : 'fetchTrending',
				creation_date : 'today'
			}, filter );
			BaseServices.post( "api/explore", filter, callback )

		},

		getPostMedia : function( params, callback ){

			var filter = angular.extend( { 
				method : 'fetchEmbedded',
				project_id : 0	
			}, params)	
			BaseServices.post( "api-2/project", filter, callback )
		},

		getMyPost : function( params, callback ){

			var filter = angular.extend( { 
				offset  : 0	
			}, params)	
			// BaseServices.post( "api/myposts", filter, callback );
			BaseServices.post( "api/userprofileposts", filter, callback );

		},

		getMyLikePost : function( params, callback ){

			var filter = angular.extend( { 
				offset  : 0	
			}, params)	
			BaseServices.post( "api/getlikesposts", filter, callback );

		}


	}


} ] );

