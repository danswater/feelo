yamba.App .controller('LeftMenuController', ['$scope', "BoxServices",
											function( $scope, BoxServices ) {

	$scope.boxes = [];

	$scope.$watch( "$parent.Constant.boxes", function( ){
		
		if( $scope.$parent.Constant.boxes.length == 0 ) return;
		$scope.boxes = $scope.$parent.Constant.boxes;

	} )	


}]);