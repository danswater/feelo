<?php

    class Mediamasonry_Plugin_Core extends Zend_Controller_Plugin_Abstract {

        public function routeShutdown(Zend_Controller_Request_Abstract $req) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            if ($request->getModuleName() != 'whmedia')
                return;
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('mediamasonry_enable', 1)) {
                if ($request->getControllerName() == 'index') {
                    if ($request->getActionName() == 'index' or $request->getActionName() == 'manage')
                        $request->setModuleName('mediamasonry');
                        Zend_Controller_Action_HelperBroker::getStaticHelper('Content')->setContentName('whmedia_' . $request->getControllerName() . '_' . $request->getActionName());
                }
            }
        }
    }