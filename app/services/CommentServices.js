yamba.App.factory( "CommentServices", [ "CookieServices", "BaseServices", function( CookieServices, BaseServices ) {

	return {

		createComment : function( data, successCb, errorCb ){
			if( !angular.isObject( data ) )
				return;
			data.method = "createComment";
			BaseServices.post( "api-2/comment", data, successCb, errorCb )
		},

		fetchComments: function( data, successCb, errorCb ){
			if( !angular.isObject( data ) )
				return;
			data.method = "fetchComment";
			BaseServices.post( "api-2/comment", data, successCb, errorCb );
		},

		deleteComment : function( comment_id, successCb, errorCb ){
			if( angular.isUndefined( comment_id ) )	
				return;
			BaseServices.delete( "api/comment?comment_id=" + comment_id, successCb, errorCb );
		}

	}

} ] );