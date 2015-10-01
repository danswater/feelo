<?php

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'grandopening',
        'version' => '4.3.0p1',
        'path' => 'application/modules/Grandopening',
        'title' => 'Grand Opening',
        'description' => 'Grand Opening',
        'author' => 'WebHive Team',
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'dependencies' => array(
            array(
                'type' => 'module',
                'name' => 'whcore',
                'minVersion' => '4.3.0',
            ),
        ),
        'callback' => array(
            'path' => 'application/modules/Grandopening/settings/install.php',
            'class' => 'Grandopening_Installer',
        ),
        'directories' =>
        array(
            0 => 'application/modules/Grandopening',
        ),
        'files' =>
        array(
            0 => 'application/languages/en/grandopening.csv',
        ),
    ),
    // Hooks ---------------------------------------------------------------------
    'hooks' => array(
        array(
            'event' => 'onUserLoginAfter',
            'resource' => 'Grandopening_Plugin_Hooks',
        ),
        array(
            'event' => 'onUserLogoutBefore',
            'resource' => 'Grandopening_Plugin_Hooks'
        )
    )
);
?>