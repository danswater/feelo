<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'testservice',
    'version' => '4.0.0',
    'path' => 'application/modules/Testservice',
    'title' => 'Service',
    'description' => 'Service',
    'author' => 'Test',
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
      0 => 'application/modules/Testservice',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/testservice.csv',
    ),
  ),
); ?>