<?php

class Api_UserprofiledetailsController extends Zend_Rest_Controller {

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
// 		$token = $this->_getParam ( 'id', null );
// 		$test = $this->_getParam('test',null);
// 		echo $token."<br><br>".$test;
		
// 		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
// 		$select = $table->select ();
// 		$select->where ( 'token = ?', $token );
		
// 		$auth = $table->fetchRow ( $select );
		
// 		if (count ( $auth ) != 1)
// 			return $this->_forward ( 'forbidden', 'auth' );
		
// 		if ($auth->expire_date < time ())
// 			return $this->_forward ( 'expired' );
		
// 		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		
// 		$this->getFriendsList($user);

		$this->_forward('index');
		
    }
    
    public function getSpecificUser( $user ) {
		// $user_id = $user->getIdentity ();
		$user_id = $this->_getParam('user_id');
		$subject = Engine_Api::_ ()->user ()->getUser ( $user_id );
    	
		$objUser = Engine_Api::_()->getApi( 'user', 'api' );
		$resultSetUser[ 'data' ]  = $objUser->fetchUserDetails2( $user, $subject );
 		$resultSetUser[ 'error' ] = array();

		if( is_null( $resultSetUser[ 'data' ][ 'User' ] ) ) {
			$resultSetUser[ 'data' ]  = array();
			$resultSetUser[ 'error' ] = 'No results found';
		}
		
		$this->_helper->json($resultSetUser);
	}
	
    public function newAction() {

        $this->_forward('index');
    }

    public function postAction() {
    	$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1)
			return $this->_forward ( 'forbidden' );
		
        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		
		$this->getSpecificUser( $user );
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
