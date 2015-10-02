yamba.App.directive( 'ybTextBoxLimit', [ "$timeout", function( $timeout ) {

	return {
		restrict : "AC",
		scope : {
		},
		link : function( $scope, element, attr ){

			var charLength = 0, 
				container_counter = $( "#" + attr.ybTextboxCounterContainer ),
				charMaxLength = parseInt( attr.ybTextBoxLimit );

			// init the box counter
			container_counter.html( "0 / " + charMaxLength )

			$( element ).unbind( 'keypress' ).bind( 'keypress', function() {
				charLength = parseInt( this.value.length ) + 1;

				if( charLength >= charMaxLength ){
					this.value = this.value.toString().substring( 0,  charMaxLength - 1 );
					charLength = charMaxLength;
				}
				if( container_counter.length > 0 ){
					container_counter.html( charLength + " / " + charMaxLength )
				}

			} )	

		}
	}	

} ] );