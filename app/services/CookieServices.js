yamba.App.factory( "CookieServices", [ "$cookies", "$cookieStore", function( $cookies, $cookieStore ) {

	return {

		_concatSessionId : function( key ){
			return typeof session_id == "undefined" ? key : session_id + '_' + key;
		},

		get : function( key ) {
			key = this._concatSessionId( key );
			return $cookieStore.get( key );
		},

		put : function( key, value ){
			key = this._concatSessionId( key );
			$cookieStore.put( key, value );
		},

		remove : function( key ){
			key = this._concatSessionId( key );
			$cookieStore.remove( key );	
		},

		clean : function(){
			angular.forEach( $cookies, function ( v, k ) {
			    $cookieStore.remove( k );
			} );
		}

	}

} ] )