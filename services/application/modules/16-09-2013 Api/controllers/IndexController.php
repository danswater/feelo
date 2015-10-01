<?php

class Api_IndexController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_helper->AjaxContext()
                ->addActionContext('get', 'json')
                ->addActionContext('post', 'json')
                ->addActionContext('new', 'json')
                ->addActionContext('edit', 'json')
                ->addActionContext('put', 'json')
                ->addActionContext('delete', 'json')
                ->initContext('json');
    }

    public function indexAction() {
        
    }

    public function getAction() {
        $this->getResponse()->setBody('Foo!');
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function newAction() {

        $this->_forward('index');
    }

    public function postAction() {

        $this->_forward('index');
        $this->view->response = 1;
    }

    public function editAction() {

        $this->_forward('index');
    }

    public function putAction() {

        $this->_forward('index');
    }

    public function deleteAction() {

        $this->_forward('index');
    }

    public function headAction() {
        
    }

}
