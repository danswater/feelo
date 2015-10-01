<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'api',
    'version' => '4.0.0',
    'path' => 'application/modules/Api',
    'title' => 'api',
    'description' => 'api',
    'author' => 'api',
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
      0 => 'application/modules/Api',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/api.csv',
    ),
  ),
); ?>