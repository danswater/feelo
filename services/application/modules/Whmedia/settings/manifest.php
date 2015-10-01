<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'whmedia',
    'version' => '4.2.7p2',
    'path' => 'application/modules/Whmedia',
    'title' => 'Media Plugin',
    'description' => 'Plugin for designers, photographers, artists and creatives that allows to upload images, videos, music and other files (download for other files).',
    'author' => 'WebHive Team',
    'meta' => 
    array (
      'title' => 'Media Plugin',
      'description' => 'Plugin for designers, photographers, artists and creatives that allows to upload images, videos, music and other files (download for other files).',
      'author' => 'WebHive Team',
    ),
    'callback' => 
    array (
      'path' => 'application/modules/Whmedia/settings/install.php',
      'class' => 'Whmedia_Installer',
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
      0 => 'application/modules/Whmedia',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/whmedia.csv',
      1 => 'whshow_thumb.php'
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Whmedia_Plugin_Hooks',
    )    
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'whmedia_project',
    'whmedia_media'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(  
    'whmedia_user_login' => array(
        //'type' => 'Zend_Controller_Router_Route_Static',
        'route' => '/login-pop-up/*',
        'defaults' => array(
          'module' => 'whmedia',
          'controller' => 'auth',
          'action' => 'login'
        )
      )  
  )
); ?>