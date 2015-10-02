yamba.App.directive( "ybRelatedPost", [ "$timeout", function( $timeout ) {


	return {
		restrict : "AC",
		scope : {
			projects : "="
		},
		templateUrl : "app/views/directives/post/related-post.php",
	}	


} ] );