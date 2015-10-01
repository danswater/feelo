<?php
Engine_Loader::autoload('application_modules_Mediamasonry_widgets_profile-project_Controller');
class Mediamasonry_Widget_ProfileProjectLazyController extends Mediamasonry_Widget_ProfileProjectController {
  public function  indexAction() {
    parent::indexAction();        
    $this->view->sendScript = ($this->_getParam('page', 1) > 1) ? false : true;
    $this->view->followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
  }
}