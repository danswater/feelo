yamba.App.factory( "FavoServices", [ "CookieServices", "BaseServices",'UserServices','$window','$timeout','$stateParams', function( CookieServices, BaseServices,UserServices,$window, $timeout,$stateParams ) {

	var FavoUser = function() {
	    this.items = [];
	    this.busy = false;
	    this.after = 0;
  	};

  	FavoUser.prototype.nextPage = function() {
  		var opts = {
			lines: 13,
			length: 0,
			width: 4,
			radius: 51,
			corners: 1,
			rotate: 52,
			direction: 1,
			color: '#000',
			speed: 1.3,
			trail: 25,
			shadow: false,
			hwaccel: false,
			className: 'spinner',
			zIndex: 2e9,
			top: '50%',
			left: '50%'
		};
		var target = document.getElementById('thespinner');

	    if (this.busy) return;
	    this.busy = true;
	    
	    var spinner = new $window.Spinner(opts).spin();
		target.appendChild(spinner.el);
		var user_id = 0;
		console.log( $stateParams.username);

		UserServices.profile(
			{
				method : 'fetchByUsername',
	    		username : $stateParams.username
			},
			function(response){
				console.log(response);
				user_id = response.data.User.user_id;
				
				BaseServices.post( 
			   		"/api/favo", 
			   		{
						method : 'fetchUserFavo',
						user_id : response.data.User.user_id,
						offset : this.after
					},
					function(data) {
				      	var items = data.data;
				      	var items_count = items.length;
				      	console.log(items.length);
				      	for (var i = 0; i < items_count; i++) {
				        	this.items.push(items[i]);
				      	}
				      	this.after++;
				      	
				      	this.busy = false;
				      	if(items_count < 10) {
				      		this.busy = true;
				      	}
			    	}.bind(this)
			    );
			}.bind(this)
	    );

      	$timeout( function() {
	    	spinner.stop();
	    }, 500 );
  	};

	return {

		relatedFavos : function( project_id, successCb ){
			var filter = { project_id : project_id }
			BaseServices.post( "api-2/relatedfavo", filter, successCb );
		},

		fetchFavoList : function( params, successCb ){
			var filter = angular.extend( {
				method : "fetch"
			}, params || {} );
			BaseServices.post( "api/favo", filter, successCb );
		},

		addProjectToFavo : function( params, successCb ){
			var filter = angular.extend( {
				method : "addToFavo"
			}, params || {} );
			BaseServices.post( "api/favo", filter, successCb );
		},

		createFavo : function( file, filename, params, successCb ){
			var filter = angular.extend( {
				method : "create"
			}, params || {} );
			BaseServices.upload( "api/favo", file, filename, filter, successCb );
		},

		fetchSpecificFavo : function( params, successCb ){
			var filter = angular.extend( {
				method : "fetchAFavo",
			}, params || {} );
			BaseServices.post( "api/favo", filter, successCb );
		},

		fetchFavoPost : function( params, successCb ){
			var filter = angular.extend( {
				method : "fetchPosts",
				offset : '0'
			}, params || {} );
			BaseServices.post( "api/favo", filter, successCb );
		},

		fetchFavoUser: FavoUser
	}

} ] );