yamba.App.factory( "FollowServices", [ "CookieServices", "BaseServices",'$window','$timeout', function( CookieServices, BaseServices, $window, $timeout ) {

	return {

		followUnfollow : function( user_id, callback ){

			BaseServices.post( "/api/follow", {
				user_id : user_id
			}, callback )

		},

		checkFollowStatus : function( user_id, callback ){

			BaseServices.post( "/api/follow", {
				method : "checkFollowStatus",
				subject_id : user_id
			}, callback )

		},

		fetchFollowers : function( data, callback ){

			BaseServices.post( "/api/follow", {
				method : data.filter,
				subject_id : data.user_id,
				offset : data.offset
			}, callback )

		},

		confirmFollowRequest : function( subject_id, callback ){

			BaseServices.post( "api/follow", {
				method : 'confirm',
				subject_id : subject_id
			}, callback );

		},

		ignoreFollowRequest : function( subject_id, callback ){

			BaseServices.post( "api/follow", {
				method : 'ignore',
				subject_id : subject_id
			}, callback );

		},

		// fetchFollowers : Follow,

		followStatus : function( is_followed, pending ){
			is_followed = parseInt( is_followed )
			pending = parseInt( pending );

			if( is_followed == 0 && pending == 0 ){
				return "Follow";
			}
			else if( is_followed == 0 && pending == 1 ){
				return "For Approval";
			}
			else if( is_followed == 1 && pending == 0 ){
				return "Following";	
			}
			else{
				return "Follow status not found.";
			}

		} 


	}

} ] );