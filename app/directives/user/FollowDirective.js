yamba.App
.directive( "followInfo", function(){

    var directive = {
    	restrict : "EA",
        templateUrl: 'app/views/directives/user/follow-info.php',
        scope: {
        	data : '=',
        	method : '='
        },
        link : function( $scope, element, attr ){
        	$scope.follow = function(id){
        		console.log(id);
        	}

        	$scope.unfollow = function(id){
                console.log(id);
        	}
        }
    };
    return directive;
});

// yamba.App
// .directive( "followInfo", ["BaseServices",'$compile',function(BaseServices,$compile){
// 	var buildTemplate = function( data, current_length,request ) {
// 		template = '';
// 		for( var i = ( data.length - current_length ); i < data.length; i++ ){
// 			template +='<li>';
// 		    template +=    '<a ui-sref="main.profile({ username:'+ data.User.username +'})">';
// 		    template +=	     	'<img class="userImage circle" src="http://yamba.rocks/services/{{'+ data.User.storage_path +'}}" />';
// 		    template +=		'</a>';
// 		    template +=    '<h3>';
// 		    template +=			'<a ui-sref="main.profile({ username:'+ data.User.username +'})" class="followUsername">{{'+ data.User.username +'}}</a>';
// 		    template +=    '</h3>';
// 		    template +='</li>';
// 		}
// 		return template;
// 	}
//     var directive = {
//     	restrict : "EA",
//         // templateUrl: 'app/views/directives/user/follow-info.php',
//         scope: {
//         	filter : '=',
//         	user: '='
//         },
//         link : function( $scope, element, attr ){
//         	$scope.feeds = [];

//         	var opts = {
// 				lines: 13,
// 				length: 0,
// 				width: 4,
// 				radius: 51,
// 				corners: 1,
// 				rotate: 52,
// 				direction: 1,
// 				color: '#000',
// 				speed: 1.3,
// 				trail: 25,
// 				shadow: false,
// 				hwaccel: false,
// 				className: 'spinner',
// 				zIndex: 2e9,
// 				top: '50%',
// 				left: '50%'
// 			};

// 			var target = document.getElementById('thespinner');

// 			var dFilter = angular.copy( $scope.filter )||{}, feedFigure = "", feedFigureTpl, feedTemplateCompile;
// 			dFilter = angular.extend( { 
// 				offset : 0,
// 				user_id : angular.copy( $scope.user )
// 			}, dFilter );

// 			var fetchFeeds = function() {
// 				BaseServices.post( 
// 	   				"/api/follow",
// 	   				dFilter,
// 	   				function(responseFeeds) { 
// 	   					console.log(responseFeeds);
// 						$scope.feeds = $scope.feeds.merge( responseFeeds );

// 						feedFigureTpl = buildTemplate( $scope.feeds, responseFeeds.length,$scope.type);

// 						feedTemplateCompile = $compile( feedFigureTpl )( $scope );

// 						element.append( feedTemplateCompile );
// 	 				}
// 	 			);

// 				dFilter.offset++;
// 			}

// 			fetchFeeds();

// 			$( window ).scroll( function( event ){
// 				var scrollBottom = $( document ).height() - $( window ).height() - $( window ).scrollTop();
// 				if( scrollBottom == 0 ){
// 					fetchFeeds();
// 				}
// 			} );
//         }
//     };
//     return directive;
// }]);