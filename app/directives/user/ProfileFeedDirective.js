yamba.App
.directive( "profileFeed", ["FollowServices",function(FollowServices){

    var directive = {
    	restrict : "EA",
        templateUrl: 'app/views/user/profile/profile-feeds.php',
        scope: {
        	filter : '=',
        },
    };
    return directive;
}]);