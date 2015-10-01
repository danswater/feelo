<?php

class Api_Bootstrap extends Engine_Application_Bootstrap_Abstract
{

 protected function _initRestRouter() {
        $front = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($front, array(), array(
            'api',
        ));

        $front->getRouter()->addRoute('rest', $restRoute);
    }

}