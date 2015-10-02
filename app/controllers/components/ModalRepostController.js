yamba.App .controller('ModalRepostController', ['$scope', 'post', 'close',
											function( $scope, post, close ) {
	$scope.display = true;

	$scope.post = post;

	$scope.repostPost = function(){
		alert( 'no repost api but after this is done.');
		$scope.close();
	}

	$scope.close = function() {
	    $scope.display = false;
		close();
	};

}]);