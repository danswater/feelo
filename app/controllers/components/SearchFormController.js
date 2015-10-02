yamba.App .controller('SearchFormController', ['$scope', '$state',
											function( $scope, $state ) {

	$scope.searchKeyword = function( form ){

		if( form.$valid ){

			$scope.closeSearchModal();			

			$state.go( 'main.search', {
				keyword : $scope.search.keyword 
			} );

		}
	}

	$scope.closeSearchModal = function(){
		$( "#searchContainerForm" ).css( { 
			'display' : 'none'
		} )	
	}


}]);