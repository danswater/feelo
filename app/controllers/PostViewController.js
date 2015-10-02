yamba.App.controller( "PostViewController", [ "$scope", "$stateParams", "$timeout", "FeedServices", "FavoServices", "CommentServices", "LikeServices", "FollowServices", "ModalService", "UserServices",
											function( $scope, $stateParams, $timeout, FeedServices, FavoServices, CommentServices, LikeServices, FollowServices, ModalService, UserServices ) {

	$scope.project_id = $stateParams.project_id;
	$scope.hasMoreComment = false;
	$scope.comments = [];
	$scope.post_data = {};
	$scope.follow = {};
	$scope.isOwner = true;

	$timeout( function( ) {
		 $( document ).scrollTop(0);	
	} ); // make sure it scroll to the top


	var owner = UserServices.getLoginUser();

	var showModalBox = function( user ){

		ModalService.showModal( {
	      	templateUrl: "app/views/modal/box.php",
	      	controller: "ModalBoxController",
	      	inputs : {
	      		user : user
	      	}
	    } ).then( function( modal ) {
	      modal.close.then( function( result ) {

	      } );
	    } );

	}

	var checkFollowStatus = function( user ){
		/*** check if owner ***/
		if( owner.user_id != user.user_id ){
			$scope.isOwner = false;
		}

		FollowServices.checkFollowStatus( user.user_id, function( response ) {
			$scope.follow = response.data[ 0 ].Follow;
			$scope.follow.status_string = FollowServices.followStatus( $scope.follow.is_followed, $scope.follow.pending_approval );
		} );
	}

	FeedServices.getFeedByProjectId( $scope.project_id, function( response ) {
		$scope.post_data = response.data.Posts[0];
		checkFollowStatus( response.data.Posts[ 0 ].User );
	} )

	/*************** author meta ******************/

	$scope.unfollowFollow = function(){
		FollowServices.followUnfollow( $scope.post_data.User.user_id, function( response ) {
			$scope.follow.status_string = FollowServices.followStatus( response.is_followed, response.pending_approval );	

			if( response.is_followed == 1 && response.pending_approval == 0 ){
				showModalBox( response.Users );	
			}

		} )
	}

	/*************** author meta ******************/


	/*************** media action ****************/

	$scope.likeUnliked = function() {
		LikeServices.likeUnliked( $stateParams.project_id, function( response ){
			if( response.data.islike ){
				$scope.post_data.is_liked = 1;
			}
			else{
				$scope.post_data.is_liked = 0;
			}
		} )
	}

	$scope.addCollection = function() {

		ModalService.showModal( {
	      	templateUrl: "app/views/modal/collecton.php",
	      	controller: "ModalCollectionController",
	      	inputs : {
	      		post : $scope.post_data
	      	}
	    } ).then( function( modal ) {
	      modal.close.then( function( result ) {

	      } );
	    } );
		
	}

	$scope.sharePost = function(){

		ModalService.showModal( {
	      	templateUrl: "app/views/modal/repost.php",
	      	controller: "ModalRepostController",
	      	inputs : {
	      		post : $scope.post_data
	      	}
	    } ).then( function( modal ) {
	      modal.close.then( function( result ) {

	      } );
	    } );

	}

	/*************** media action ****************/

	/*************** comment *********************/
	var current_page = 0; // paging start to zero
	var loadComments = function(){
		CommentServices.fetchComments( {
			project_id : $scope.project_id, 
			page : current_page }, function( response ) {
			var data = response.data;
			for( var i = 0; i < $scope.comments.length; i++ ){
				data.push( $scope.comments[ i ] );
			}

			$scope.comments = data;
			if( current_page >= response.total_pager ){
				$scope.hasMoreComment = false;
			}
			else{
				$scope.hasMoreComment = true;
			}
			current_page++;

		})
	}
	loadComments(); // init first comments
	$scope.loadMoreComment = function(){
		console.log( 'loading more comments' );
		loadComments();
	}


	var comment_form_processing = false;

	$scope.submitComment = function( form ){

		if( form.$valid && !comment_form_processing ){

			comment_form_processing = true;
			CommentServices.createComment( {

				project_id : $scope.project_id,
				body : $scope.form.comment.body

			}, function( response ) {

				$scope.form.comment.body = "";
				$scope.comments.push( response.data );
				console.log( ' -- newly added --', $scope.comments );
				comment_form_processing = false;

			} )

		}
	}
	/*************** comment *********************/


	/*************** related favos and projects *********************/
	FavoServices.relatedFavos( $scope.project_id, function( response ) {
		$scope.related_favos = response.RelatedFavo;
	} )

	FeedServices.relatedPost( $scope.project_id, function( response ) {
		$scope.related_projects = response.RelatedPost
	} )
	/*************** related favos and projects *********************/

} ] );