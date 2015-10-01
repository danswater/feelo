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
        
        $message = new Zend_Mobile_Push_Message_Gcm();
        $message->addToken('APA91bHJpW289u6khHfe4K90Nl7LkyU1oReqSwr88vILuW8GOZKr19r2BSZbnba3de2ltBnwPQ-_LUa1stwDOVfzX_5gapIW6_CAv0pJ0mqe1smJxMM56WlGPrs5vvJ7YSxJSpeH9vB6lj4X9FQ_j_m1NiMrsr_tXl3c5hxeOQ0je8HUfn5JMwA');
        $message->setData(array(
            'foo' => 'bar',
            'bar' => 'foo',
        ));

        $gcm = new Zend_Mobile_Push_Gcm();
        $gcm->setApiKey('AIzaSyDPtkxfX9Rl__AVANHqcBPGIWKfZbn28TE');

        try {
            $objResponse = $gcm->send($message);
        } catch (Zend_Mobile_Push_Exception $e) {
            // exceptions require action or implementation of exponential backoff.
            die($e->getMessage());
        }

        // handle all errors and registration_id's
        foreach ($objResponse->getResults() as $k => $v) {
            if (isset($v['registration_id'])) {
                $arrResponse[] = array( 
                    'message' => '%s has a new registration id of: %s\r\n', $v[ 'registration_id' ] 
                );
            }
            if (isset($v['error'])) {
                $arrResponse[] = array( 
                    'message' => '%s had an error of: %s\r\n', $v[ 'error' ] 
                );
            }
            if ( isset($v['message_id'] ) ) {
                $arrResponse[] = array( 
                    'message' => '%s was successfully sent the message, messageid is: %s', $v[ 'message_id' ] 
                );
            }
        }

        $this->getResponse()->setHttpResponseCode( 200 );
        $this->getHelper( 'json' )->sendJson( $arrResponse );

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
