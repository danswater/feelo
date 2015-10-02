<?php
	session_start();
	$session_id = session_id();
	require_once 'includes/helper/file_scanner.php';
?>
<!-- <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> -->
<!DOCTYPE HTML>
<html ng-app="yamba" ng-controller="AppController" >
	<head>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Yamba | {{ App.title }}</title>
		<!-- for the maintime static base -->
		<?php require_once "includes/fonts.php"; ?>
		<?php require_once "includes/css.php"; ?>
		<?php
			$productionUrl = getenv( 'OPENSHIFT_APP_NAME' );
			$serviceUrl = 'http://localhost/feelo/proxy.php?url=';
			if ( !empty( $productionUrl ) ) {
				$serviceUrl = 'http://feelo-danswater.rhcloud.com/proxy.php?url=';
			}
		?>
		<script type="text/javascript">
			var services_url = '<?php echo $serviceUrl; ?>';
			var session_id   = '<?php echo $session_id; ?>';
		</script>
	</head>
	<body>
		<div ui-view></div>
		<?php require_once "includes/javascript.php"; ?>
	</body>
</html>