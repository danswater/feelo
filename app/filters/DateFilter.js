yamba.App.filter( "ybDisplayTimeDiff", function( ) {

	return function( time_diff, uc_first ){
		uc_first = uc_first||false;

		if( angular.isArray( time_diff ) ) return "now";

		var date_string = "";

		for( var key in time_diff ){
			label = key;
			if(uc_first){
				label = key.toString().ucfirst();
			}

			date_string += time_diff[ key ] + " " + label + " "; 
		}

		if( date_string == "" ){
			date_string = "now";
		}

		return date_string; 
	}

} );
