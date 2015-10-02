yamba.App
.controller('CommentController', ['$scope','close','project_id','scope_ctr','CommentServices', function($scope, close,project_id,scope_ctr, CommentServices) {

  	$scope.display = true;
	$scope.hasMoreComment = false;
	$scope.comments = [];

	var current_page = 0; // paging start to zero
	var loadComments = function(){
		CommentServices.fetchComments( {
			project_id : project_id, 
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

				project_id : project_id,
				body : $scope.form.comment.body

			}, function( response ) {

				$scope.form.comment.body = "";
				$scope.comments.push( response.data );
				var queryElement = document.querySelectorAll('article');
				var target = angular.element(queryElement[scope_ctr]).find('.itemComments');
				var count =  $scope.comments.length;
				console.log(count);
				target.html(count);
				console.log( ' -- newly added --', $scope.comments );
				comment_form_processing = false;

			} )

		}
	}

	$scope.close = function() {
	    $scope.display = false;
	 	close();
	};

}]);