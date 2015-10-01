<?php

class Api2_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

    protected function _initRequest() {
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        if (null === $front->getRequest()) {
            $request = new Zend_Controller_Request_Http ();
            $front->setRequest($request);
        }
        return $request;
    }
    protected function _initRestRouter() {
        $front = Zend_Controller_Front::getInstance();
        // api module will be overriden if we instantiate
        // new zend rest route in here.
        $restRoute = new Zend_Rest_Route($front, array(), array(
            'api',
            'api-2',
        ));
        $front->getRouter()->addRoute('rest', $restRoute);
    }

    protected function _initLibrary () {
        $vendorPath = 'vendor/autoload.php';
        require_once $vendorPath;
    }

}