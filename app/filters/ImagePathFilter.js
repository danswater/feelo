yamba.App.filter( "ybImagePath", function( ) {

	return function( image ){

		if( typeof image == "undefined" || image == null || image == 'null'){
			return ""; 	
		}
		if( /services/gi.test( image.toString() ) ){
			return "http://yamba.rocks/" + image;
		}
		else{
			return "http://yamba.rocks/services/" + image;
		}
	}

} );