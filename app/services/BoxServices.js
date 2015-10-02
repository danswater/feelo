yamba.App.factory( "BoxServices", [ "CookieServices", "BaseServices", function( CookieServices, BaseServices ) {

	return {
		boxes : [],

		getBox : function( callback ){
			var self = this;
			if( this.boxes.length != 0 ){
				callback( this.boxes );
			}else{
				BaseServices.post( 'api/box', {
					method : 'get'
				}, function( response ) {
					self.boxes = response;
					callback( response );
				} )
			}
		},

		getUserBox : function( params, callback ){
			var filter = angular.extend( {
				method : 'getUsers',
				circle_id : 0 
			}, params || {} )

			BaseServices.post( "api/box", filter, callback );
		},

		editBox : function( params, callback ){
			var filter = angular.extend( {
				method : 'editBox',
				circle_id : 0, 
				title : ""
			}, params || {} )

			BaseServices.post( "api/box", filter, callback );
		},	

		addRemoveUserInBox : function( params, callback ){
			var filter = angular.extend( {
				method : 'addRemoveBox',
				circle_id : 0,
				user_id : 0 
			}, params || {} )

			BaseServices.post( "api/box", filter, callback );
		},

		addUserToBox : function( circle_id, user_id, callback ){
			BaseServices.post( "api/box", {
				circle_id : circle_id,
				user_id : user_id,
				method : "addRemoveBox"
			}, callback )
		}

	}

} ] );