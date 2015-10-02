yamba.App.factory( "UserServices", [ "CookieServices", "BaseServices","$http","$q", function( CookieServices, BaseServices,$http,$q ) {

	return {

		getLoginUser : function() {
			return BaseServices.getSession( BaseServices._UserCookie );
		},

		login : function( username, password, successCb, errorCb ){
			var userKey = BaseServices._UserCookie;

			BaseServices.post( "api/auth", {
				identity : username,
				password : password
			}, function( response ) {

				if( typeof response.error == "undefined" && typeof response.User != "undefined" ){

					var data = angular.copy( response.User );
					data.token = response.token;

					CookieServices.put( userKey, data );
					successCb( response );

				} // it means success
				else{
					errorCb( response );	
				}
			}, errorCb )
		},

		register : function( data, successCb, errorCb ){
			if( !angular.isObject( data ) )
				return;	
			data.method = "add";
			BaseServices.post( "api/register", data, successCb, errorCb );
		},

		isLogin : function(){
			return BaseServices.isLogin();
		},

		// so far no callback yet
		logout : function() {
			CookieServices.clean();
		},

		profile: function(data,successCb){
			BaseServices.post( "api-2/user", data, successCb);
		},

		profile2: function(username){
			var deferred = $q.defer();
			$http.post(
				"api-2/user", 
				$.param({
					method : 'fetchByUsername',
	    			username : username
				})
			).success(function(response) {
            	deferred.resolve(response);
	        }).error(function(reason) {
	            // logger.error('XHR Failed for retrievePaginateUsers.' + error.data);
	            deferred.reject(reason);
	        });
	        return deferred.promise;
		}




	}

} ] )