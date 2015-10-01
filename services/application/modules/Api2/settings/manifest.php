<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'api-2',
    'version' => '4.2.0',
    'path' => 'application/modules/Api2',
    'title' => 'Api 2',
    'description' => '',
    'author' => 'danswater',
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
      0 => 'application/modules/Api2',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/Api2.csv',
    ),
  ),
); ?>