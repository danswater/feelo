<?php

class Mediamasonry_AdminSettingsController extends Whmedia_controllers_AdminController {

    public function  init() {
        $this->view->addBasePath(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'Whmedia' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR);
        parent::init();
    }


    public function mediamasonryAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('whmedia_admin_main', array(), 'whmedia_admin_main_mediamasonry');

        $this->view->form = $form = new  Mediamasonry_Form_Admin_Global();

        if( $this->getRequest()->isPost() and $form->isValid($this->getRequest()->getPost())) {

          $values = $form->getValues();
          $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
          foreach ($values as $key => $value){
            $setting_tmp->setSetting($key, $value);
          }

        }
  }

}