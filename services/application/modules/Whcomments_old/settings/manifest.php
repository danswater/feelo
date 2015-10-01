<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'whcomments',
        'version' => '4.0.1',
        'path' => 'application/modules/Whcomments',
        'title' => 'Whcomments',
        'description' => '',
        'author' => 'WebHive Team',
        'callback' =>
        array(
            'class' => 'Engine_Package_Installer_Module',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Whcomments',
        ),
        'files' =>
        array(
            'application/languages/en/whcomments.csv',
        ),
    ),
    'items' => array(
        'whcomments_comment'
    ),
);
?>