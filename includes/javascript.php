<?php
	function makeScriptVariable( $var, $array ){

		$script = '<script type="text/javascript"> ';
		$script .= 'var ' . $var . ' = [';
		foreach( $array as $src ){
			$script .= "'" . $src . "',";	
		}
		$script .= '];';
		$script .= ' </script>';
		echo $script;
	}

	$custom_javascript_files = FileScanner::dirFileToArray( "assets/custom/javascript" );
	makeScriptVariable( "custom_javascript", $custom_javascript_files );

?>

<script type="text/javascript" src="components/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="components/angular/angular.min.js"></script>
<script type="text/javascript" src="components/angular-route/angular-route.min.js"></script>
<script type="text/javascript" src="components/angular-cookies/angular-cookies.min.js"></script>
<script type="text/javascript" src="components/angular-ui-router/release/angular-ui-router.min.js"></script>
<script type="text/javascript" src="components/ng-file-upload/ng-file-upload-all.min.js"></script>
<script type="text/javascript" src="components/ng-file-upload/ng-file-upload-shim.min.js"></script>
<script type="text/javascript" src="app/utils/app.js"></script>
<script type="text/javascript" src="app/build/yamba.min.js"></script>
<script type="text/javascript" src="app/router.js"></script>
<script type="text/javascript">
	for( var i = 0; i < custom_javascript.length; i++ ){
		addTag( "script", { src : "assets/custom/javascript/" + custom_javascript[ i ] }, true );
	}
</script>
