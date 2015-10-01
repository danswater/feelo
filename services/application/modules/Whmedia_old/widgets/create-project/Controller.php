<?php

class Whmedia_Widget_CreateProjectController extends Engine_Content_Widget_Abstract
{
  public function indexAction() {
    if (Engine_Api::_()->whmedia()->isApple()) {
        return $this->setNoRender();
    }
    if (!Zend_Controller_Action_HelperBroker::getStaticHelper('RequireAuth')->setAuthParams('whmedia_project', null, 'create')->checkRequire())
        return $this->setNoRender();
  }

}

