yamba.App.controller( "SearchController", [ "$scope", "$timeout", "$stateParams", "SearchServices",
											function( $scope, $timeout, $stateParams, SearchServices ) {
	$scope.$parent.App.title = "Search | " + $stateParams.keyword;
	$scope.search = {
		hashtags : [],
		feeds : [],
		users : [],
		favos : []
	};

	/************* saerch hashtag ***********/
	SearchServices.searchHashtag( {
		keyword : $stateParams.keyword
	}, function( response ) {
		$scope.search.hashtags = response.Hashtag;
	} );




	/************* saerch media ***********/
	SearchServices.searchFeed( {
		keyword : $stateParams.keyword
	}, function( response ) {
		$scope.search.feeds = response.data;
	} );




	/************* saerch user ***********/
	SearchServices.searchUser( {
		keyword : $stateParams.keyword
	}, function( response ) {
		$scope.search.users = response.data;
	} );



	/************* saerch collection ***********/
	SearchServices.searchFavo( {
		keyword : $stateParams.keyword
	}, function( response ) {
		$scope.search.favos = response.data;
	} );



} ] );