<?php

class Share_IndexController extends Core_Controller_Action_Standard {

    private $FB_ID = "1504022509869649"; 

    public function init() {
        $this->_helper->layout()->disableLayout();

        $this->view->fb_id = $this->FB_ID;
    }

    public function indexAction(){

    }

    public function postAction() {
        $project_id = $this->_getParam( 'project_id', null);
        $this->view->project = $project = Engine_Api::_()->getItem( 'whmedia_project', $project_id );
        if ($project == null) {
             return $this->_helper->Message('Incorrect project ID.', false, false)->setError();
        }
    }

}

