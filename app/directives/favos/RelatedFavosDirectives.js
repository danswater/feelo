yamba.App.directive( "ybRelatedFavos", [ "$timeout", function( $timeout ) {

	return {
		restrict : "AC",
		scope : {
			favos : "="
		},
		templateUrl : "app/views/directives/favos/related-favos.php",
	}	

} ] );