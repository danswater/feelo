<?php

class Whmedia_controllers_AdminController extends Core_Controller_Action_Admin
{
  public function  postDispatch() {
        parent::postDispatch();
        if ($this->_helper->contextSwitch->getCurrentContext() === null) {
            $this->getResponse()->appendBody($this->view->render('etc/admin_head.tpl'));
        }
    }
}