yamba.App
.controller('CollectionsController', ['$scope','$stateParams','UserServices','FollowServices','profile_cover','FavoServices',function($scope,$stateParams,UserServices,FollowServices,profile_cover,FavoServices){
	$scope.user_data = {};
	$scope.random_cover = profile_cover.cover;
	console.log(profile_cover.cover);
	$scope.favos = new FavoServices.fetchFavoUser;
	$scope.follow = [
		my_profile = false,
		follow_status = ''
	];
	
	UserServices.profile(
		{
			method : 'fetchByUsername',
    		username : $stateParams.username
		},
		function(response){
			var data = response.data.User;
			
			var my_profile = false;

			$scope.user_data = data;

			if($scope.$parent.App.user.user_id == data.user_id){
				my_profile =  true;
				$scope.follow.my_profile = my_profile;
				$scope.follow.follow_status = '';
			}
			else{
				FollowServices.checkFollowStatus(
					$scope.user_data.user_id,
					function(response){
						if(profile_cover.id != data.user_id){
							profile_cover.cover = null;
							profile_cover.id = 0;
						}
						var response = response.data[ 0 ].Follow;
                		var status = FollowServices.followStatus( response.is_followed, response.pending_approval );
                		$scope.follow.my_profile = my_profile;
						$scope.follow.follow_status = status;
					}
				);
			}

			UserServices.profile(
				{
					method : 'fetchPosts',
				    offset : 0,
				    user_id : data.user_id
				},
				function(response){
					if(profile_cover.cover == null){
						var new_cover = response.data.cover_photo;
						profile_cover.cover = new_cover;
						profile_cover.id = data.user_id;
						$scope.random_cover = new_cover;
					} else {
						$scope.random_cover = profile_cover.cover;
					}
				}
			);
		}
	);
}]);