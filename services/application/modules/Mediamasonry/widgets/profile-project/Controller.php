<?php
Engine_Loader::autoload('application_modules_Whmedia_widgets_profile-project_Controller');
class Mediamasonry_Widget_ProfileProjectController extends Whmedia_Widget_ProfileProjectController {
  public function  indexAction() {
   

        parent::indexAction();
        if (Zend_Controller_Action_HelperBroker::getStaticHelper('contextSwitch')->getCurrentContext()  == 'html') {
            $this->view->only_items = true;
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        else {
            $this->view->only_items = false;
        }
    }
}