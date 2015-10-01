(function() {
	/**
	 * Handler and listener of filter
	 */
	var item1 = document.getElementById( 'filter-item1' );
	item1.addEventListener( 'click', function( e ) {
		var keyword = document.getElementById( 'query' );
		var attr = 'search?query=' + keyword.value + '&type=';
		item1.setAttribute( 'href', attr );
		return;
	} );
	var item2 = document.getElementById( 'filter-item2' );
	item2.addEventListener( 'click', function( e ) {
		var keyword = document.getElementById( 'query' );
		var attr = 'search?query=' + keyword.value + '&type=whmedia_project';
		item2.setAttribute( 'href', attr );
		return;
	} );
	var item3 = document.getElementById( 'filter-item3' );
	item3.addEventListener( 'click', function( e ) {
		var keyword = document.getElementById( 'query' );
		var attr = 'search?query=' + keyword.value + '&type=tags';
		item3.setAttribute( 'href', attr );
		return;
	} );
	var item4 = document.getElementById( 'filter-item4' );
	item4.addEventListener( 'click', function( e ) {
		var keyword = document.getElementById( 'query' );
		var attr = 'search?query=' + keyword.value + '&type=favo';
		item4.setAttribute( 'href', attr );
		return;
		//e.preventDefault();
	} );

	/**
	 * Handler and listener of hashtag
	 */
	var wh_search_project = new whmedia.search();
	try {
		var viewAllTag = document.getElementById( 'view-all-tag' );
		viewAllTag.addEventListener( 'click', function( e ) {
			tagHandler( e );
		} );
	}
	catch( e ) {}

    var wh_search_project = new whmedia.search();

	$$('.hash-tag').addEvent('click', function(event){
		event.preventDefault();
		
		var cElem = $(this);
		var children = cElem.getChildren();
		var keyword = children[ 0 ].textContent.substring( 1 );


		/*
		wh_search_project.tagAction( keyword )
		*/
	
	});

	try {
		var byTime = document.getElementById( 'bytime' );
		byTime.addEventListener( 'change', function() {
			var values = byTime.options[ byTime.selectedIndex ].value;
			var arrValues = values.split( ',' );
			var url     = 'search?query=' + arrValues[ 0 ] + '&type=' + arrValues[ 1 ] + '&filter=' + arrValues[ 2 ];
			location.href= url;

		} );
	}
	catch( e ) {}

} )();
