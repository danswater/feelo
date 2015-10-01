<?php

class Whmedia_Widget_BrowseMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if ($user_whmedia_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_id', false)) {    
        $this->view->owner = Engine_Api::_()->getItem('user', $user_whmedia_id);
    } 
    else {
        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('whmedia_main');
    }
  }
}
