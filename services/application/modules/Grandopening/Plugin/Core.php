<?php

class Grandopening_Plugin_Core extends Zend_Controller_Plugin_Abstract {

    public function routeShutdown($request) {
        $frontController = Zend_Controller_Front::getInstance();
        $request = $frontController->getRequest();
        $pathinfo = $request->getPathInfo();

        $cookie = $request->getCookie('whGOadmin');
        if ($cookie == 1 && $pathinfo != '/pages/grandopening')
            return;
        
        $settings = Engine_Api::_()->getApi('settings', 'core');
        if (!$settings->getSetting('grandopening_enable', 0) && $pathinfo != '/pages/grandopening')
            return;
        
        $time = Engine_Api::_()->grandopening()->getEndTime();
        if (trim($settings->getSetting('grandopening_endtime', 0), '0-') && $time < time() && $pathinfo != '/pages/grandopening') 
            return;
        
        if (Engine_Api::_()->user()->getViewer()->getIdentity() && $pathinfo != '/pages/grandopening')
            return;

        $path_ok = array('admin', 'login', 'grandopening/email', 'getslide', 'utility/tasks', 'signup', 'user/auth/forgot', 'auth/reset/code', 'api');
        foreach ($path_ok as $value_path) {
            if (strpos($pathinfo, $value_path))
                return;
        }

        if ($pathinfo != '/' && $pathinfo != '/pages/grandopening') {
            $response = $frontController->getResponse();
            return $response->setRedirect(Zend_Registry::get('StaticBaseUrl'));
        }

        $bg = $request->getParam('bg', Engine_Api::_()->getApi('settings', 'core')->getSetting('grandopening_cover'));

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        if (null === $viewRenderer->view) {
            $viewRenderer->initView();
        }
        $view = $viewRenderer->view;

        $request->setModuleName('core')
                ->setControllerName('pages')
                ->setActionName('grandopening');

        $styles = 'html#smoothbox_window {background-image: url(public/opening_cover/' . $bg . ')}';

        $view->headStyle()->appendStyle($styles);
    }

}