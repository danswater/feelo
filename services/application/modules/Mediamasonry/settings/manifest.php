<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'mediamasonry',
    'version' => '4.2.7p1',
    'path' => 'application/modules/Mediamasonry',
    'title' => 'Media Addon: Masonry',
    'description' => 'This addon allows to use masonry layout to display projects from plugin Media (Portfolio). It includes 8 additional widgets.',
    'author' => 'WebHive Team',
    'dependencies' => array(
                array(
                    'type' => 'module',
                    'name' => 'whmedia',
                    'minVersion' => '4.2.7p1',
                ),
            ),
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
      0 => 'application/modules/Mediamasonry',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/mediamasonry.csv',
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'mediamasonry' => array(
      'route' => 'whmedia/:action/*',
      'defaults' => array(
        'module' => 'mediamasonry',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'action' => '(activity-feed)',
      )
    ),
  )  
); ?>