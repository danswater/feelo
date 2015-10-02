yamba.App.factory( "HashtagServices", [ "CookieServices", "BaseServices",'UserServices','$window','$timeout','$stateParams', function( CookieServices, BaseServices,UserServices, $window, $timeout,$stateParams ) {
	var Hashtag = function() {
	    this.items = [];
	    this.busy = false;
	    this.after = 0;
  	};

  	Hashtag.prototype.nextPage = function(filter) {
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
			   		"/api-2/hashtag", 
			   		{
						method : 'fetchFollowedHashtag',
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

		followUnfollowHashtag : function( data, successCb, errorCb ){
			var filter = angular.extend( {
				method : "followHashtag"
			}, data );
			BaseServices.post( "api/hashtag", filter, successCb, errorCb );
		},

		getPostHashtag : function( data, successCb, errorCb ){
			BaseServices.post( "api/hashtagposts", data, successCb, errorCb );
		},

		fetchHashtag : function( data, successCb, errorCb ){
			data.method = "fetchFollowedHashtag";
			BaseServices.post( "api/hashtagposts", data, successCb, errorCb );
		},

		getFeedsByHashtag : function( filter, callback ){
			var filter = angular.extend( { 
				offset : 0,
				method : 'fetchByHashtagName'
			}, filter );
			BaseServices.post( "api-2/feedsearch", filter, callback );
		},

		getHashtagInfo: function(text,callback){
			var filter = {
				method : 'fetchByHashtagname',
    			text : text,

			};
			BaseServices.post( "api-2/hashtag", filter, callback );
		} ,

	}

} ] );