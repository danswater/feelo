yamba.App.factory( "LikeServices", [ "CookieServices", "BaseServices", function( CookieServices, BaseServices ) {

	return {

		likeUnliked : function( project_id, callback ){
			BaseServices.post( "/api/like", {
				project_id : project_id,
				type : 'post'
			}, callback )
		}

	}

} ] );