<?php defined('_ENGINE') or die('Access Denied');

$productionHost     = getenv( 'OPENSHIFT_MYSQL_DB_HOST' );
$productionPort     = getenv( 'OPENSHIFT_MYSQL_DB_PORT' );
$productionUser     = getenv( 'OPENSHIFT_MYSQL_DB_USERNAME' );
$productionPassword = getenv( 'OPENSHIFT_MYSQL_DB_PASSWORD' );

$host = 'localhost';
$db   = 'feelo';

if ( !empty( $productionHost ) ) {
  $host = $productionHost .':'. $productionPort;
  $db   = 'feelo';
}

$username = 'root';
if ( !empty( $productionUser ) ) {
  $username = $productionUser;
}

$password = '15feelo15';
if ( !empty( $productionPassword ) ) {
  $password = $productionPassword;
}

return array (
  'adapter' => 'mysqli',
  'params' =>
  array (
    'host'             => $host,
    'username'         => $username,
    'password'         => $password,
    'dbname'           => $db,
    'charset'          => 'UTF8',
    'adapterNamespace' => 'Zend_Db_Adapter',
  ),
  'isDefaultTableAdapter' => true,
  'tablePrefix'           => 'engine4_',
  'tableAdapterClass'     => 'Engine_Db_Table',
); ?>