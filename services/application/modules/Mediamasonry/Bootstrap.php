<?php

class Mediamasonry_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
    protected function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Mediamasonry_Plugin_Core());
    }

    public function  __construct($application) {
        parent::__construct($application);
        $this->initViewHelperPath();
    }

    protected function _initRouter() {

        $router = Zend_Controller_Front::getInstance()->getRouter();
        if (defined('WHMEDIA_URL_WORLD'))
            $wh_url_word = WHMEDIA_URL_WORLD;
        else
            $wh_url_word = Engine_Api::_()->getApi('settings', 'core')->getSetting('url_main_world', 'whmedia');

        $userConfig = array( 'mediamasonry_show' => array(
                                                         'route' => $wh_url_word . '/show/:widget_id/:id/*',
                                                         'defaults' => array(
                                                            'module' => 'mediamasonry',
                                                            'controller' => 'media',
                                                            'action' => 'show'
                                                            ),
                                                         'reqs' => array(
                                                            'widget_id' => '\d+',
                                                            'id' => '\d+'
                                                          )
                                                         )
        );
        $router->addConfig(new Zend_Config($userConfig));

        return $router;
    }
}