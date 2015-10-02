yamba.App.factory( "SettingsServices", [ "CookieServices", "BaseServices", 
										function( CookieServices, BaseServices ) {

	return {

		updateGeneralInfo : function( params, callback ){

			var filter = angular.extend( {
				method : "changeGeneralInfo",
			}, params );
			BaseServices.post( "api-2/settings", filter, callback )

		}



	}

} ] );