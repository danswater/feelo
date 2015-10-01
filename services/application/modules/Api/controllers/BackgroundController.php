<?php

class Api_BackgroundController extends Core_Controller_Action_Standard {

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
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$activeUsers = $table->fetchAll();
		
		$arrayUsers = array();
		foreach( $activeUsers as $user ) {
			$arrayUsers[] = Engine_Api::_ ()->user ()->getUser ( $user->user_id );
		}

		$ret = array();		
		try {
			// We expect that all methods we are invoking is in Api::User
			$apiUser = Engine_Api::_()->getApi( 'user', 'api' );
			
			$ret = $apiUser->sendNotificationForExpiredSession();
		} catch ( Exception $e ) {
			print_r( $e ); exit;
		}

		$this->_helper->json( $ret );
    }

    public function getAction() {
		$this->_forward('index');
    }

    public function checkSessionAction() {
    	$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$activeUsers = $table->fetchAll();
		
		$arrayUsers = array();
		foreach( $activeUsers as $user ) {
			$arrayUsers[] = Engine_Api::_ ()->user ()->getUser ( $user->user_id );
		}

		// We expect that all methods we are invoking is in Api::User
		$apiUser = Engine_Api::_()->getApi( 'user', 'api' );
		
		$ret = array();
		try {
			$ret = $apiUser->$params[ 'method' ]( $user, $params );
		} catch ( Exception $e ) {
			$ret[ 'message' ] = $e->getMessage();
		}

		$this->_helper->json( $ret );
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
    	$this->_forward('index');
    }
    
	public function forbiddenAction(){
		
		$message = array('message' => 'Token provided is invalid');
		
		$this->_helper->json ( array (
				'error' => $message
		) );
	}
	
	public function expiredAction($viewer) {
		
		$message = array('message' => 'Token expired');
		
		$this->_helper->json ( array (
				'error' => $message
		) );
		
	}

}
