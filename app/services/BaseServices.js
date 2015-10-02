yamba.App.factory( "BaseServices", [ "$http", "$location", "Upload", "CookieServices", 
									function( $http, $location, Upload, CookieServices ) {

	return {

		_UserCookie : "USER",

		getSession : function( key ){
			return CookieServices.get( key );
		},


		_getToken : function(){
			var _user = CookieServices.get( this._UserCookie );
			var token = "";
			if( typeof _user != "undefined" && typeof _user.token != "undefined" ){
				token = _user.token;
			}

			return token;

		},


		isLogin : function(){
			var _user = CookieServices.get( this._UserCookie );

			if( typeof _user != "undefined" && _user.token != "undefined" && _user.id != "undefined" ){
				return true;
			}
			return false;
		},


		_request : function( url, method, data, successCb, errorCb ){
			//url = encodeURIComponent( url );
			var token = this._getToken();
			data = angular.extend( {
				token : token,	
			}, data );

			$http( {
				url : services_url + url,
				method : method,
				data :  $.param( data ),
				headers : {
					'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8'
				}
			} ).success( function( response ) {

				if( typeof response.error != "undefined" && ( response.error == "Unauthorized 101" || response.error == "Invalid token" ) ){
					// redirect to login 
					$location.path( "login" );
				}
				else{
					successCb( response );
				}
			}, function( ) {
				if( typeof errorCb == "function" ){
					errorCb();
				}
			} );

		},

		_appendDataToUrl : function( url, data ){
			var paramStr = "";
			for( var key in data ){
				if( paramStr != "" )
					paramStr += "&";
				paramStr += key + "=" + data[ key ];
			}
			url += "?token=" + this._getToken() + "&" + paramStr;
			return url;
		},

		put : function( url, data, successCb, errorCb ){
			url = this._appendDataToUrl( url, data );
			this._request( url, "PUT", data, successCb, errorCb );
		},

		get : function( url, successCb, errorCb ){
			this._request( url, "GET", null, successCb, errorCb );
		},

		post : function( url, data, successCb, errorCb ){
			this._request( url, "POST", data, successCb, errorCb );
		},

		delete : function( url, successCb, errorCb ){
			this._request( url, "DELETE", null, successCb, errorCb );
		},

		upload : function( url, file, filename, params, successCb, errorCb ){
			//url = encodeURIComponent( url );
			var token = this._getToken();
			data = angular.extend( {
				token : token,	
			}, params );

			Upload.upload( {
				url : services_url + url,
				//url : 'http://localhost/ngyamba/filepost.php',
				fields : data,
				file : file,
				fileFormDataName : filename
			} )
			.success( function ( data, status, headers, config ) {
				successCb( data, status, headers, config )
            });
		}

	}

} ] )