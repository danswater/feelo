<?php
Engine_Loader::autoload('application_modules_Whmedia_widgets_media_Controller');
class Mediamasonry_Widget_MediaController extends Whmedia_Widget_MediaController
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
        $this->view->show_more = ($this->_getParam('show_media', 'random') == 'random' ) ? false : true;
  }

  
}