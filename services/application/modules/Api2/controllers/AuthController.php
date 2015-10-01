<?php

class Api2_AuthController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            $this->_helper->json(array('error' => 'Invalid Method', 'messages' => array('Use Post Method to Authenticate.')));
        }

        $this->_helper->AjaxContext()
                ->addActionContext('get', 'json')
                ->addActionContext('post', 'json')
                ->addActionContext('put', 'json')
                ->addActionContext('delete', 'json')
                ->addActionContext('index', 'json')
                ->initContext('json');
    }

    public function indexAction() {
        echo '<pre>';
        print_r( $this->getRequest() );
        echo '</pre>';
        exit;
        $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        $this->_helper->json(array('error' => 'Invalid Method', 'messages' => array('Use Post Method to Authenticate.')));
    }

    public function getAction() {
        $this->_forward('index');
    }

	/*
        01/06/2015 : Francis - Commented response raw header to response 200 ok
							 - Changed response payload key from `messages` to `message`
							 - Changed response format message is now under in error object
    */
    public function postAction() {
        //$email, $pass
		extract($this->getRequest()->getPost());
        
		$table = Engine_Api::_()->getDbTable('auth', 'api');

		$response = $table->authenticate( $identity, $password );
		
		if( $response[ "success" ] === true ) {
			$result[ 'token' ] = $response[ "data" ][ "token" ];
			$result[ 'expire_date' ] = $response[ "data" ][ "expire_date" ];
			$result[ 'User' ] = $this->getUserData( $response[ "data" ][ "user" ] ); 
			$this->_helper->json( $result );
		}else {
			$errorMsg = $response[ "errors"];
			// $this->getResponse()->setRawHeader( $errorMsg[ "header" ] );
			// $this->_helper->json( array('error' => $errorMsg[ "error" ], 'message' => $errorMsg[ "messages" ] ) );
			$this->_helper->json( 
				array(
					'header'  => $errorMsg[ "error" ],
					'error' => $errorMsg[ "messages" ]
				) 
			);
		}
		
		
		/*
		if (!isset($identity) || !isset($password)) {
            $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            $this->_helper->json(array('error' => 'Authorization failure.', 'messages' => array('Missing email or password.')));
        }

        $user = Engine_Api::_()->user()->getUser($identity);
        if (!$user->getIdentity()) {
            $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
            $this->_helper->json(array('error' => 'Authorization failure.', 'messages' => array('You are not authorized to access this resource.')));
        }

        $auth = Engine_Api::_()->user()->authenticate($user->email, $password);
        if (!$auth->isValid()) {
            $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
            $this->_helper->json(array('error' => 'Authentication failure.', 'messages' => $auth->getMessages()));
        }

        $table = Engine_Api::_()->getDbTable('auth', 'api');

        $select = $table->select();
        $select->where('user_id = ?', $user->getIdentity());

        $existingToken = $table->fetchRow($select);

        if ($existingToken) {
            if ($existingToken->expire_date > time())
                return $this->_helper->json(array('token' => $existingToken->token, 'expire_date' => $existingToken->expire_date, 'User' => $this->getUserData($auth)));
            else
                $existingToken->delete();
        }

        $token = $this->makeHash($user->getIdentity(), $user->email, $user->password);
        //1 month
        $expire_date = time() + 2592000;

        $row = $table->createRow();
        $row->user_id = $user->getIdentity();
        $row->token = $token;
        $row->expire_date = $expire_date;
        $row->save();

		$result[ 'token' ] = $token;
		$result[ 'expire_date' ] = $expire_date;
		$result[ 'User' ] = $this->getUserData( $auth );
        $this->_helper->json( $result );
		*/
    }
    
    public function getUserData($user){
		$objUser = Engine_Api::_()->getApi( 'user', 'api' );
		$resultSetUser = $objUser->fetchUserDetails( $user->getIdentity() );
    	return $resultSetUser;
    }

    public function putAction() {

        $this->_forward('index');
    }

    public function deleteAction() {

        $this->_forward('index');
    }

    static public function makeHash($user_id, $email, $password) {
        return sha1($user_id . $email . $password . time());
    }

}
