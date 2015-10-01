<?php
Engine_Loader::autoload('application_modules_Mediamasonry_widgets_profile-fproject_Controller');
class Mediamasonry_Widget_ProfileFprojectLazyController extends Mediamasonry_Widget_ProfileFprojectController
{
  public function  indexAction() {
    parent::indexAction();        
    $this->view->sendScript = ($this->_getParam('page', 1) > 1) ? false : true;
    $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
  }
}