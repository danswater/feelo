
yamba.App.directive( "ybFeeds", [ "$compile", "$timeout", "UserServices","FeedServices", "HashtagServices","$document","$window","$state", "FavoServices",
									function( $compile, $timeout,UserServices, FeedServices, HashtagServices,$document,$window,$state, FavoServices ) {
	/***************** pattern *********************/
	var pattern = [	'w3','w2','w1',
					'w1','w2','w3',

					'w2','w1','w1',
					'w1','w1','w3',
					
					'w3','w2','w2',
					'w2','w2','w3',
					
					'w2','w1','w1',
					'w2','w3','w2',
					
					'w1','w1','w1',
					'w1','w3','w2',
					
					'w1','w1','w3',
					'w3','w2','w2',
					
					'w2','w2','w3',
					'w2','w1','w1',
					
					'w2','w3','w2',
					'w1','w1','w1',
					
					'w1','w3','w2',
					'w1','w1','w3',
					
					'w3','w2','w2',
					'w2','w2'];
	// var ctr = 0;
	var ctr = {
		mypost : 0,
		mylikepost : 0,
		activity : 0,
		featured : 0,
		hashtag : 0,
		trending : 0,
		favofeed : 0,		
	};

	var scroll = {
		mypost : false,
		mylikepost : false,
		activity : false,
		featured : false,
		hashtag : false,
		trending : false		
	};

	var buildTemplate = function( data, classes, current_length,request ) {
		var template = "", 
		getPattern = function( i ){
			return pattern[ i ];
		} ;
		getHashtagColor = function(image_color_data){
			var hashtag_color = null;
			if(angular.isObject(image_color_data)){
				hashtag_color = 'rgb(' + image_color_data.R + ',' + image_color_data.G + ',' + image_color_data.B + ')';
			}
			else{
				hashtag_color = 'rgb(31,32,33)';
			}
			return hashtag_color;
		}
		for( var i = ( data.length - current_length ); i < data.length; i++ ){
			var cls = getPattern( ctr[request] );
			var hashtag_background_color = getHashtagColor(data[i].Image_color);
			data[i]['hashtag_color'] = hashtag_background_color;
			data[i]['scope_ctr'] = i;
			template += '<figure class="item animated fadeIn ' + cls + '" yb-feed record="feeds[ ' + i + ' ]" view="' + cls + '" hashtag_color="'+ hashtag_background_color +'"></figure>';
			if(ctr[request] == 58){
				ctr[request] = 0;
			}
			else{
				ctr[request]++;
			}
		}
		// console.log("Counter Summary:"+ctr[request]);
		return template;
	}
	/***************** pattern *********************/
	var request = {

		mypost : function( filter, cb ){
			console.log(filter);
			UserServices.profile(
				{
					method : 'fetchByUsername',
		    		username : filter.username
				},
				function(response){
					filter = angular.extend( { 
						user_id : response.data.User.user_id
					}, filter );
					FeedServices.getMyPost( filter, function( response ) {
						cb( response.Posts );
					} )
				}
			);
		},

		mylikepost : function( filter, cb ){
			console.log(filter);
			UserServices.profile(
				{
					method : 'fetchByUsername',
		    		username : filter.username
				},
				function(response){
					filter = angular.extend( { 
						user_id : response.data.User.user_id
					}, filter );
					FeedServices.getMyLikePost( filter, function( response ) {
						cb( response.my_likes );
					} )
				}
			);
		},

		activity : function( filter, cb ) {
			FeedServices.getActivityFeeds( filter, function( response ) {
				cb( response.Activity_Feed )	
			} );
		},
	
		featured : function( filter, cb ) {
			FeedServices.getFeaturedFeeds( filter, function( response ) {
				cb( response.Activity_Feed );
			} );
		},

		hashtag : function( filter, cb ) {
			HashtagServices.getFeedsByHashtag( filter, function( response ) {
				cb( response.data );
			} )
		},

		trending : function( filter, cb ) {
			FeedServices.getFeedsTrending( filter, function( response ) {
				cb( response.Activity_Feed );
			} )
		},

		favofeed : function( filter, cb ) {
			FavoServices.fetchFavoPost( filter, function( response ) {
				cb( response.data.Favo_Feed );
			} )
		}

	}	

	/***************** directive *******************/
	return {
		restrict : "E",
		scope : {
			filter : "=",
			type : '@',
			callback : '&'
		},
		link : function( $scope, element, attr ){

			$scope.feeds = [];

			$scope.likePost = function(project_id){
				console.log(project_id);
			}

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

			var dFilter = angular.copy( $scope.filter )||{}, feedFigure = "", feedFigureTpl, feedTemplateCompile;
			dFilter = angular.extend( { 
				offset : 0
			}, dFilter );

			var fetchFeeds = function() {
				// console.log("Scope Type:"+$scope.type+" Ctr of Type:"+ctr[$scope.type]);
				if( dFilter.offset >= 100 || scroll[$scope.type] == true){
					scroll[$scope.type] = true;
					return;
				}

				if( typeof request[ $scope.type ] != "undefined" ){
					if(dFilter.offset == 0){
						ctr[$scope.type] = 0;
					}
					var spinner = new $window.Spinner(opts).spin(target);
					request[ $scope.type ]( dFilter,  function( responseFeeds ) {
						// console.log(responseFeeds);
						// console.log(responseFeeds.length);
						if(responseFeeds.length <= 9){
							scroll[$scope.type] = true;
							// console.log(scroll[$scope.type]);
						}

						$scope.feeds = $scope.feeds.merge( responseFeeds );

						feedFigureTpl = buildTemplate( $scope.feeds, FeedServices.classes, responseFeeds.length,$scope.type);

						feedTemplateCompile = $compile( feedFigureTpl )( $scope );

						element.append( feedTemplateCompile );

						$timeout( function() {
							$( ".masonry" ).masonry({
								columnWidth: 20,
								itemSelector: '.item'
							});
							$( ".masonry" ).masonry( 'reloadItems' );
							$( ".masonry" ).masonry( 'layout' );
							spinner.stop();
						}, 500 )

						// callback
						if( typeof $scope.callback == "function" ){
							$scope.callback( { feeds : $scope.feeds } );
						}

					} )

				}	

				dFilter.offset++;

			}

			fetchFeeds();

			$( window ).scroll( function( event ){
				var scrollBottom = $( document ).height() - $( window ).height() - $( window ).scrollTop();
				if( scrollBottom == 0 ){
					if($state.is('main.activity_feed') || $state.is('main.hashtag') || $state.is('main.featured') || $state.is('main.trending') || $state.is('main.profile') || $state.is('main.favo') ){
						fetchFeeds();
					}
				}
			} );

		}
	}

} ] )
