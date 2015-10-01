<?php

$db_host = 'localhost';
$db_user = 'wazzup';
$db_pass = 'wpk8xndzawae+';
$db_schema = 'wazzup';

// Create connection
$con = mysql_connect($db_host,$db_user,$db_pass);

$conn = mysql_select_db($db_schema, $con);

// Check connection
if (mysqli_connect_errno($con)){
  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

?>