yamba.App
.directive( "profileHeader", ["FollowServices",function(FollowServices){

    var directive = {
    	restrict : "EA",
        templateUrl: 'app/views/user/profile/profile-header.php',
        scope: {
        	data : '=',
        	cover: '=',
        	follow: '=',
            group: '='
        },
        link : function( $scope, element, attr ){
            $scope.followButtonFunction = function(user_id){
                FollowServices.followUnfollow( user_id, function( response ) {
                    var status = FollowServices.followStatus( response.is_followed, response.pending_approval );
                    angular.element(document.getElementById('follow_status')).html(status);
                } );
            }
        }
    };
    return directive;
}]);