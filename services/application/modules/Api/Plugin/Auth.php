<?php

class Api_Plugin_Auth extends Zend_Controller_Plugin_Abstract {

    protected $_authObj = NULL;
    protected $_responseObj = NULL;

    const CACHE_ID_PREFIX = 'ApiAuth_';
    const HEADER_APIKEY = 'Apikey';
    const HEADER_REQUEST_HASH = 'ApiRequestHash';
    const AUTH_NAMESPACE = 'ApiAuth';

    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        // get the api key from the header
        $apiKey = $this->getRequest()->getHeader(self::HEADER_APIKEY);
        // get the hash of the request
        $requestHash = $this->getRequest()->getHeader(self::HEADER_REQUEST_HASH);
        // create a basic auth object
        $authObject = NULL;
        // both the api key and request hash are required
        if (!empty($apiKey) && !empty($requestHash)) {
            $authStorage = new Zend_Session_Namespace(self::AUTH_NAMESPACE);
            $cacheKey = self::CACHE_ID_PREFIX . $apiKey;
            if (isset($authStorage->$cacheKey)) {
                $authObject = $authStorage->$cacheKey;
                if (Common_Auth_Adapter_Rest::validRequestHash(
                                $authObject, $requestHash, $request->getParams()
                        )) {
                    return TRUE;
                }
            } else {
                $auth = Zend_Auth::getInstance();
                $authAdapter = new Common_Auth_Adapter_Rest(
                        $this->_db, $request->getParams()
                );
                $authAdapter->setApiKey($apiKey)
                        ->setRequestHash($requestHash);
                try {
                    $result = $authAdapter->authenticate();
                } catch (Zend_Auth_Exception $e) {
                    $this->_redirectNoAuth($request);
                }
            }
        } else {
            $this->_redirectNoAuth($request);
        }
    }

    protected function _redirectNoAuth(Zend_Controller_Request_Abstract $request) {
        if (($request->getParam('controller') == 'error') &&
                ($request->getParam('module') == 'default') &&
                ($request->getParam('action') == 'noauth')) {
            return;
        }
        $redir = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'Redirector'
        );
        $redir->setGotoRoute(array(), 'noauth', true);
        $redir->redirectAndExit();
    }

}