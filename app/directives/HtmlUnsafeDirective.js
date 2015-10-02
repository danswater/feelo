yamba.App.directive( "ybHtmlUnsafe", [ "$sce", function( $sce ) {

	return {
		restrict : "AC",
		scope : {
			ybHtmlUnsafe: '='
		},
		template: "<div ng-bind-html='trustedHtml'></div>",
		link : function( $scope, elem, attr ){

			$scope.updateHtml = function() {
				$scope.trustedHtml = $sce.trustAsHtml( $scope.ybHtmlUnsafe ); 
			}
			$scope.$watch( 'ybHtmlUnsafe', function() {

				if( typeof $scope.ybHtmlUnsafe != "undefined" ){
					$scope.updateHtml();
				}

			} );

		}
	}	

} ] );