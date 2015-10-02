yamba.App .controller( 'NewPostController', [ '$scope', "UserServices", "PostServices",
											function( $scope, UserServices, PostServices ) {
	
	$scope.current_user = UserServices.getLoginUser();
	$scope.fileReaderSupported = window.FileReader != null && (window.FileAPI == null || FileAPI.html5 != false);

	var resetVars = function(){	
		$scope.post = {
			privacy : "public",
			title : "",
			description : "",
			hashtags : ""
		}
		$scope.newPostError = '';	
		$scope.upload_type = '';
	}
	resetVars();

    $scope.closeModal = function(){
    	delete $scope.files;
		resetVars();
		$( "#newPostContainer" ).css( { 
			'display' : 'none'	
		} );	
	}

	$scope.showUploadForm = function( type ){
		$scope.upload_type = type;
		$( "#uploadMediaStep1" ).css( { 'display':'none' } )
		$( "#uploadMediaStep2" ).css( { 'display':'block' } )
		$( "#uploadMediaStep3" ).css( { 'display':'none' } )
	}


	$scope.nextNewPostForm = function(){
		if( typeof $scope.files == "undefined") return;
		$( "#uploadMediaStep1" ).css( { 'display':'none' } )
		$( "#uploadMediaStep2" ).css( { 'display':'none' } )
		$( "#uploadMediaStep3" ).css( { 'display':'block' } )
	}


	var refineHashtag = function( hashtags, addHash, addComma ){
		var hashtag = "";
		var hashtag_counts = hashtags.length;
		var tmp_hashtags = "";
		if( hashtag_counts > 3 ){
			hashtag_counts = 3
		}
	
		for( var i = 0; i < hashtag_counts; i++ ){
			hashtag = hashtags[ i ];
			// remove hashtag
			hashtag = hashtag.replace( /[!@#\$%\^\&*\)\(+=._,]+/g, "" );
			if( addHash == true ){
				hashtag = "#" + hashtag; // add hashtag in the first
			}
			tmp_hashtags += hashtag; 

			if( addComma == true ){
				tmp_hashtags += ",";
			}else{
				tmp_hashtags += " ";
			}
		}

		return tmp_hashtags;
	}

	$scope.hashtagCheker = function(){

		var hashtags =  $scope.post.hashtags.toString().match( /[\w#]+/g );
		$scope.post.hashtags = refineHashtag( hashtags, true, false );

	}


	$scope.newPostSubmit = function(){
		var params = {
			title : $scope.post.title,
			description : $scope.post.description,
			privacy : $scope.post.privacy,
			hashtags : ""
		};
		var hashtags =  $scope.post.hashtags.toString().match( /[\w#]+/g );
		params.hashtags = refineHashtag( hashtags, false, true );
		Notifier.show( "Uploading... Please wait...")
		PostServices.uplaodNewPost( $scope.files, "Filedata", params, function( response ) {

			if( typeof response.data != "undefined" ){
				$scope.closeModal();
			}
			else{
				$scope.newPostError = "Error";
			}
			Notifier.hide();
		} )		

	}


} ] );