yamba.App.directive( "ybPostMedia", [ "$timeout", "$sce", "FeedServices", function( $timeout, $sce, FeedServices ) {


	return {
		restrict : "AC",
		scope : {
			project : "=",
		},
		templateUrl : "app/views/directives/post/post-media.php",
		link : function( $scope, element, attr ){
			// hide first
			$scope.render={
				image : false,
				embedded : false
			};

			$scope.embedded = [];

			var urlRegex = /(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])/gi;
			var createFrame = function( url ){
				return '<iframe width="560" height="315" src="'+ url +'" frameborder="0" allowfullscreen></iframe>';
			}
			var isFirefox = false;
			var browser = navigator.userAgent.toLowerCase();
			if( browser.indexOf( 'firefox' ) > -1 ) {
				isFirefox = true;
			}




			var fetchMedia = function() {
				FeedServices.getPostMedia( {
					project_id : $scope.project.project_id
				}, function( response ) {
					$scope.embed = "";
					var content = "", match ;
					for( var i = 0; i < response.data.Embedded.length; i++ ){
						content = response.data.Embedded[ i ];

						if( isFirefox ) {
							$scope.embed =  $sce.trustAsHtml( content );
						}
						else{ 
							match = content.toString().match( urlRegex );
							$scope.embed = $sce.trustAsHtml( createFrame( match[ 0 ] ) ); 	
						}
						break;
					}

				} )

			}	

			// just make sure the project is not undefined
			$scope.$watch( "project", function() {

				// if( typeof $scope.project != "undefined" ){

				if( angular.isObject($scope.project) ){

					if( angular.isObject($scope.project.Media) ){
						if( $scope.project.Media.type == "null" ){
							$scope.render.image = true;
						}
						else{
							$scope.render.embedded = true;
							fetchMedia();
						}
					}
				}

			} )

		}
	}	

} ] );