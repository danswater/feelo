<?php

class Whmedia_Controller_Action_Helper_Message extends Zend_Controller_Action_Helper_Abstract {

  protected $_params;
  protected $_isError = false;
  protected $_isAjax = false;

  public function direct($message, $close = true, $reload = true, Exception $e = NULL) {
    $this->_close = $close;
    $this->_reload = $reload;
    $error = Zend_Registry::get('Zend_Translate')->_($message);
    if ($e) {
        $error_code = Engine_Api::getErrorCode(true);
        $log = Zend_Registry::get('Zend_Log');
        $output = '';
        $output .= PHP_EOL . 'Error Code: ' . $error_code . PHP_EOL;
        $output .= $e->__toString();
        $log->log($output, Zend_Log::CRIT);
        $error .= ' ' . $this->getActionController()->view->translate("Please report this to your site administrator with Error Code %s", $error_code);
     }

     $this->_params = array('smoothboxClose' => $close,
                            'parentRefresh'=> $reload,
                            'messages' => array($error)
                           );
     return $this;
  }

  public function preDispatch() {
     $this->_checkDispatch();
  }

  public function  postDispatch() {
     $this->_checkDispatch();   
  }

  public function setError() {
      $this->_isError = true;
      return $this;
  }

  public function setAjax($isAjax = true) {
      $this->_isAjax = $isAjax;
      return $this;
  }

  protected function _checkDispatch() {
      if (!empty ($this->_params)) {
        if ($this->_isAjax === false) {
            if ($this->_isError) {
                $this->getResponse()->appendBody($this->getActionController()->view->render('application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Whmedia' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'error.tpl'));
            }
            $request = $this->getRequest();
            $request->setControllerName('utility');
            $request->setModuleName('core');
            $request->setParams($this->_params);
            $request->setActionName('success')
                    ->setDispatched(false);
            $this->_params = null;
        }
        else {
            $json_array = array('status' => !$this->_isError,
                                'reload' => $this->_params['parentRefresh']);
            if (!empty ($this->_params['messages'])) {
                $type_messages = ($this->_isError === true) ? 'error' : 'message';
                $json_array[$type_messages] = Zend_Registry::get('Zend_Translate')->_($this->_params['messages']);
            }
            Zend_Controller_Action_HelperBroker::getStaticHelper('json')->sendJson($json_array);
        }
    }
  }
}