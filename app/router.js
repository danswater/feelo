yamba.App.config( function( $locationProvider, $stateProvider, $urlRouterProvider ) { 

 	$urlRouterProvider.otherwise('/login');


 	$stateProvider

 		.state( 'login', {
 			url : "/login",
 			templateUrl : 'app/views/user/login.php',
 			controller: 'LoginController'
 		} )

            .state( 'register', {
                  url : "/register",
                  templateUrl : 'app/views/user/register.php',
                  controller: 'RegisterController'
            } )


 		/**************** main template *********************/
 		.state( "main", {
 			url : "/main",
 			templateUrl : 'app/views/layouts/main-template.php'
 		} )

            .state( "main.group", {
                  url : "/group/:box_id",
                  templateUrl : 'app/views/groups/main.php',
                  controller : 'GroupController'
            } )

 		.state( "main.search", {
 			url : "/search/:keyword",
 			templateUrl : 'app/views/search/search.php',
 			controller : 'SearchController'
 		} )

            .state( "main.upload", {
                  url : "/main/upload",
                  templateUrl : 'app/views/post/sample-post.php'
            } )

 		.state( "main.favo", {
 			url : "/favo/:favo_id",
 			templateUrl : 'app/views/favo/list.php',
 			controller : 'FavoController'
 		} )

 		.state( "main.activity_feed", {
 			url : "/activity-feed",
 			templateUrl: 'app/views/post/activity-feed.php',
                  controller: 'ActivityFeedController'	
 		} )

            .state( "main.activity_feed_box", {
                  url : "/activity-feed/:box_id",
                  templateUrl: 'app/views/post/activity-feed.php',
                  controller: 'ActivityFeedController'      
            } )

 		.state( "main.hashtag", {
 			url : "/hashtag/:hashtag",
 			templateUrl: 'app/views/post/hashtag.php',
                  controller: 'HashtagController'	
 		} )

 		.state( "main.settings", {
 			url : "/settings",
 			templateUrl: 'app/views/settings/main.php',
                  controller: 'SettingsController'	
 		} )

            .state( "main.notification", {
                  url : "/notification/:type",
                  templateUrl: 'app/views/notification/main.php',
                  controller: 'NotificationController'    
            } )

 		.state( "main.featured", {
 			url : "/featured",
 			templateUrl: 'app/views/post/featured.php',
                  controller: 'FeaturedController'	
 		} )

 		.state( "main.trending", {
 			url : "/trending/:creation_date",
 			templateUrl: 'app/views/post/trending.php',
                  controller: 'TrendingController'	
 		} )

 		.state( "main.post", {
 			// url: "/post/:user_id/:project_id",
 			url: "/post/:project_id",
 			templateUrl: 'app/views/post/view.php',
                  controller: 'PostViewController'
 		} )

 		.state( "main.profile", {
 			url: "/profile/:username",
 			templateUrl: 'app/views/user/profile.php',
                  controller: 'ProfileController'
 		} )

 		.state( "main.followers", {
 			url: "/profile/:username/followers",
 			templateUrl: 'app/views/user/follow/follow-page.php',
                  controller: 'FollowerController'
 		} )

 		.state( "main.following", {
 			url: "/profile/:username/following",
 			templateUrl: 'app/views/user/follow/follow-page.php',
                  controller: 'FollowingController'
 		} )

            .state( "main.hashtags", {
                  url: "/profile/:username/hashtag",
                  templateUrl: 'app/views/user/hashtag/hashtags.php',
                  controller: 'ProfileHashtagController'
            } )

 		.state( "main.collections", {
 			url: "/profile/:username/collections",
 			templateUrl: 'app/views/user/collection/collection-page.php',
                  controller: 'CollectionsController'
 		} )

 		.state( "main.likes", {
 			url: "/profile/:username/likes",
 			templateUrl: 'app/views/user/likes/likes-page.php',
                  controller: 'LikesController'
 		} );


	$locationProvider.hashPrefix( "!" );
} );
