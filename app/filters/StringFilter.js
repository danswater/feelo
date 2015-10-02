String.prototype.ucfirst = function() {
  str = '' + this;
  var f = str.charAt(0)
    .toUpperCase();
  return f + str.substr(1);
}

yamba.App.filter( "ybStringLimit", function( ) {

	return function( string, limit ){

		if( string.length > limit ){
			return string.substring( 0, limit ) + "...";
		}	
		return string;
	}

} );
