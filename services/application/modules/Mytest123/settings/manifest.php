<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'mytest123',
    'version' => '4.0.0',
    'path' => 'application/modules/Mytest123',
    'title' => 'testmodule',
    'description' => '',
    'author' => 'me',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Mytest123',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/mytest123.csv',
    ),
  ),
); ?>