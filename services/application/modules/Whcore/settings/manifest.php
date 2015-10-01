<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'whcore',
        'version' => '4.3.0',
        'path' => 'application/modules/Whcore',
        'title' => 'WebHive Core',
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
            0 => 'application/modules/Whcore',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/whcore.csv',
        ),
    ),
);
?>