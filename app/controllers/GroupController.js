yamba.App.controller( "GroupController", [ "$scope", "$stateParams", "$state", "BoxServices",
									function( $scope, $stateParams, $state, BoxServices ) {
	
	if( $scope.$parent.Constant.boxes.length == 0 ){
		$state.go( 'main.activity_feed' );
		return;
	}

	var group_index = $scope.$parent.Constant.boxes.getIndexByKeyVal( 'circle_id', $stateParams.box_id)
	$scope.$parent.App.title = "Groups";

	$scope.box_users = []
	$scope.edit_group = false;
	$scope.current_box = {};
	$scope.box_name = $scope.$parent.Constant.boxes[ group_index ].title;


	BoxServices.getUserBox( {
		circle_id : $stateParams.box_id	
	}, function( response ) {
		$scope.box_users = response.data;
	} )

	$scope.saveGroupName = function(){
		BoxServices.editBox( {
			circle_id : $stateParams.box_id,
			title : $scope.box_name
		}, function( response ) {
			$scope.edit_group = false;
			$scope.$parent.Constant.boxes[ group_index ].title = response.data.title;
		} )
	}


	$scope.addRemoveUserInBox = function( user_id, index ){

		if( !confirm( "Are you sure you want to delete this?") ) return;

		BoxServices.addRemoveUserInBox( {
			circle_id : $stateParams.box_id,
			user_id : user_id	
		}, function( response ) {
			if( response.data.message == "Removed"){
				$scope.box_users.splice( index, 1 );
			}

		} )

	}

} ] );