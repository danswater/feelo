yamba.App.filter( "ybMediaType", function( ) {

	return function( type ){

		if( type == "direct" || type == "youtube" ){
			return "Video"
		}
		else if( type == "null" ){
			return "Image"
		}
		else{
			return "Type Not Found"
		}

	}

} );