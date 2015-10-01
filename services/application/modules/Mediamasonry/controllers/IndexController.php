<?php
Engine_Loader::autoload('application_modules_Whmedia_controllers_IndexController');
class Mediamasonry_IndexController extends Whmedia_IndexController {

    public function  postDispatch() {
        parent::postDispatch();
        $this->view->form->removeElement('page');
    }
}
