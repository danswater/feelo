<?php

class Whcore_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown() {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $wh_settings = $settings->getFlatSetting('wh');
        if ($wh_settings['facebook_type'] == 1)
            $facebookAppId = $settings->getSetting('core_facebook_appid');
        else
            $facebookAppId = $wh_settings['facebook_appid'];

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;

        if ((int) $facebookAppId > 0) {
            $view->headScript()->appendFile('http://connect.facebook.net/en_US/all.js');
            $script = 'FB.init({ appId  : ' . $facebookAppId . ', status : true, cookie : true, xfbml  : true });';
            $view->headScript()->appendScript($script);
        }
    }

}