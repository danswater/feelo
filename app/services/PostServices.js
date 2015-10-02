yamba.App.factory( "PostServices", [ "CookieServices", "BaseServices", function( CookieServices, BaseServices ) {

	return {


		myPost : function( offset, successCb, errorCb ){
			var data = {};
			data.offset = offset
			BaseServices.post( "api/myposts", data, successCb, errorCb );
		},

		getPostByProjectId : function( user_id, project_id, successCb, errorCb ){
			var data = {};
			data.user_id = user_id;
			data.project_id = project_id;
			BaseServices.post( "api/specificprofilepost", data, successCb, errorCb );

		},

		uplaodNewPost : function( file, filename, params, successCb ){
			var filter = angular.extend( {
				"method" : "create",
				"title" : "",
			    "description" : "",
			    "hashtags" : "",
			    "searchable" : 1,
			    "allow_downloadable_original" : 1,
			    "privacy" : "everyone",
			    "auth_comment" : "everyone" 
			}, params || {} );
			BaseServices.upload( "api-2/project", file, filename, filter, successCb );

		}




	}


} ] );