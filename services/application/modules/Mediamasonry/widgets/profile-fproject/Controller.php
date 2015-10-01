<?php
Engine_Loader::autoload('application_modules_Whmedia_widgets_profile-fproject_Controller');
class Mediamasonry_Widget_ProfileFprojectController extends Whmedia_Widget_ProfileFprojectController
{
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