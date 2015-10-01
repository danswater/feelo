<?php

class Grandopening_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('grandopening_admin_main', array(), 'grandopening_admin_main_settings');

        $this->view->form = $form = new Grandopening_Form_Admin_Global();

        if ($this->getRequest()->isPost()) {
            $post = $this->getRequest()->getPost();

            $grandopening_endtime = $post['grandopening_endtime'];
            unset($post['grandopening_endtime']);

            if ($form->isValidPartial($post)) {
                if ($post['use_date'] && !$form->grandopening_endtime->isValid($grandopening_endtime))
                    return;

                $values = $form->getValues();
                $setting_tmp = Engine_Api::_()->getApi('settings', 'core');
                foreach ($values as $key => $value) {
                    if ($key == 'inviteonly') {
                        $setting_tmp->setSetting('user_signup.inviteonly', $value);
                        continue;
                    }
                    if ($key == 'checkemail') {
                        $setting_tmp->setSetting('user_signup.checkemail', $value);
                        continue;
                    }
                    $setting_tmp->setSetting($key, $value);
                }
                $form->addNotice('Your changes have been saved.')
                        ->hideCheck();
            }
        }
    }

}