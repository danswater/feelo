<?php

class Whcore_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('whcore_admin_main', array(), 'whcore_admin_main_settings');

        $this->view->form = $form = new Whcore_Form_Admin_Global();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
            foreach ($values as $key => $value) {
                $setting_tmp->setSetting($key, $value);
            }
            $form->addNotice('Your changes have been saved.');
        }
    }

}