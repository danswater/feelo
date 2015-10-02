<?php
$servername = getenv( 'OPENSHIFT_MYSQL_DB_HOST' );
$username   = getenv( 'OPENSHIFT_MYSQL_DB_USERNAME' );
$password   = getenv( 'OPENSHIFT_MYSQL_DB_PASSWORD' );

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";
