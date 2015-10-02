yamba.App.controller( "HashtagController", [ "$scope", "$timeout", '$stateParams','HashtagServices', function( $scope, $timeout, $stateParams,HashtagServices ) {
	$scope.$parent.App.title = "Hashtag";
	$scope.hashtag = $stateParams.hashtag;
	
	$scope.filterHashtag = {
		text  : $stateParams.hashtag
	};

	$scope.hashtag_info = null

	$scope.headerBg = '';
	$scope.afterFinish = function( feeds ){
		// get first image for header background
		if( typeof feeds[ 0 ] != "undefined" ){
			$scope.headerBg = feeds[ 0 ].Media.storage_path
		}
	}

	$scope.hashtagFollow = function(id){
		data = {
			tag_id : id
		};
		HashtagServices.followUnfollowHashtag(
			data,
			function(response){
				// console.log(response);
				var target = document.getElementById("follow-hashtag");
				var message = "Follow";
				if(response.data.message == "Followed"){
					var message = "Unfollow";
				}
				angular.element(target).html(message);
			},
			function(){}
		);
	}

	HashtagServices.getHashtagInfo(
		$stateParams.hashtag,
		function(response){
			$scope.hashtag_info = response.data.Hashtag;
			if(response.data.Hashtag.is_followed == 0){
				$scope.hashtag_info.message = "Follow";
			}
			else{
				$scope.hashtag_info.message = "Unfollow";
			}
		}
	);

	
} ] );