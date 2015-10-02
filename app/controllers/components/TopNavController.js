yamba.App .controller('TopNavController', ['$scope', '$state',
											function( $scope, $state ) {

	$scope.showLeftMenuCanvas = function(){
		var leftMenuCanvas = $( "#leftMenuCanvas" ), mainAppCanvas = $( "#mainAppCanvas" );

		if( leftMenuCanvas.hasClass( "leftMenuCanvasHidden" ) ){ // show
			leftMenuCanvas.removeClass( "leftMenuCanvasHidden" );
			mainAppCanvas.addClass( "mainAppCanvasVisible" );
		}
		else{ // hide
			leftMenuCanvas.addClass( "leftMenuCanvasHidden" );
			mainAppCanvas.removeClass( "mainAppCanvasVisible" );
		}

	}

	$scope.showTrendingModal = function(){
		$( "#trendingContainerForm" ).css( {
			display : 'block'
		} );
	}

	$scope.showSearchForm = function(){
		$( "#searchContainerForm" ).css( {
			'display' : 'block'
		} );

		$( "#searchFormInput" ).focus()
	}

	$scope.showNewPostModal = function(){
		
		$( "#uploadMediaStep3" ).css( { 'display':'none' } );
		$( "#uploadMediaStep2" ).css( { 'display':'none' } );
		$( "#uploadMediaStep1" ).css( { 'display':'block' } );
		$( "#newPostContainer" ).css( { 
			'display' : 'block'	
		} );	
	}

}]);