<?php

class Mytest123_IndexController extends Core_Controller_Action_Standard {

    public function indexAction() {
        $table = Engine_Api::_()->getDbtable('collections', 'grandopening');
        $rName = $table->info('name');
        echo "<pre>" . print_r($rName, true) . "</pre>";
        $this->view->someVar = 'indexAction';
    }

    public function helloAction() {
        $this->view->someVar = 'helloAction';
    }

    public function contentAction() {
        $this->view->someVar = 'contentAction';
    }

}
