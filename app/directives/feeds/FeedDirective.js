yamba.App.directive( "ybFeed",['LikeServices','ModalService',function(LikeServices,ModalService) {

	return {
		restrict : "CA",
		scope : {
			data : "=record",
			view : "@"
		},
		templateUrl : function( element, attr ){ 
			return "app/views/directives/post/feed-" + attr.view + ".php";
		},
		link : function( $scope, element, attr ){

			$scope.addCollection = function( data ) {
				ModalService.showModal( {
			      	templateUrl: "app/views/modal/collecton.php",
			      	controller: "ModalCollectionController",
			      	inputs : {
			      		post : data
			      	}
			    } ).then( function( modal ) {
			      modal.close.then( function( result ) {

			      } );
			    } );
				
			}


			$scope.likeFeed = function(data){
				LikeServices.likeUnliked( data.project_id, function( response ){
					var scope_ctr = data.scope_ctr;
					var queryElement = document.querySelectorAll('article');
					var target = angular.element(queryElement[scope_ctr]).find('.itemLikes');
					var count = target.text();
					if( response.data.islike ){
						target.addClass('liked instantAnimation pulse');
						$scope.$parent.feeds[data.scope_ctr]['like_count']+=1;
						$scope.$parent.feeds[data.scope_ctr]['like_count_int']+=1;
					}
					else{
						target.removeClass('liked');
						$scope.$parent.feeds[data.scope_ctr]['like_count']-=1;
						$scope.$parent.feeds[data.scope_ctr]['like_count_int']-=1;
					}
				} );
			};

			$scope.commentModal = function(project_id,scope_ctr){
				ModalService.showModal({
					templateUrl: "app/views/directives/post/comments.php",
					controller: "CommentController",
					inputs: {
					    project_id: project_id,
					    scope_ctr: scope_ctr
					}
				}).then(function(modal) {
					modal.close.then(function(result) {
					    // $scope.customResult = "All good!";
					});
	    		});
			};



			$scope.deleteFeed = function(project_id){
				console.log(project_id);
				// $route.reload();
			};

			$scope.feed_options = function(userInfo){
				ModalService.showModal( {
			      	templateUrl: "app/views/modal/feed-options.php",
			      	controller: "FeedOptionsController",
			      	inputs : {
			      		user : userInfo
			      	}
			    } ).then( function( modal ) {
			      modal.close.then( function( result ) {

			      } );
			    } );
			}
		}
	} 

} ]);
