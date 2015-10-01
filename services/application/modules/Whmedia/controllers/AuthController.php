<?php
Engine_Loader::autoload('application_modules_User_controllers_AuthController');
class Whmedia_AuthController extends User_AuthController {

    public function loginAction() {
        parent::loginAction();
        $this->view->form->setTitle("Sign in to Yamba")
                         ->setDescription("")
                         ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format' => 'smoothbox')));
        $this->_helper->content->setEnabled(false);

        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $this->_forward('success', 'utility', 'core', array(
                                                                'smoothboxClose' => true,
                                                                'parentRefresh'=> true,
                                                                'messages' => array(Zend_Registry::get('Zend_Translate')->_("Welcome."))
            ));
        }
    }
}
