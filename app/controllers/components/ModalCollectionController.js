yamba.App .controller('ModalCollectionController', ['$scope', "$timeout", "FavoServices", "post", 
											function( $scope, $timeout, FavoServices, post ) {
	$scope.display = true;
	$scope.collections = [];
	$scope.fileReaderSupported = window.FileReader != null && (window.FileAPI == null || FileAPI.html5 != false);

	FavoServices.fetchFavoList({}, function( response ){
		$scope.collections = response.data;
	} )

	$scope.generateThumb = function(file) {
		if (file != null) {
			if ($scope.fileReaderSupported && file.type.indexOf('image') > -1) {
				$timeout(function() {
					var fileReader = new FileReader();
					fileReader.readAsDataURL(file);
					fileReader.onload = function(e) {
						$timeout(function() {
							file.dataUrl = e.target.result;
						});
					}
				});
			}
		}
	};

	$scope.addCollection = function( form ){
		
		Notifier.show( "Uploading.. Please wait." );
		if( form.$valid ){
			FavoServices.createFavo( $scope.collection.file, "Filedata", {
				title : $scope.collection.name,
				privacy : $scope.collection.privacy,
				category : ''
			}, function( response ) {
				$scope.selected_collection = response.data.favcircle_id;
				$scope.addProject();

				Notifier.hide();
				$scope.close();
			} )
		}

	}

	$scope.addProject = function() {
		FavoServices.addProjectToFavo( {
			project_id : post.project_id,
			favcircle_id : $scope.selected_collection
		}, function() {
			$scope.close();
		} )
	}
	

	$scope.close = function() {
	    $scope.display = false;
		close();
	};

}]);