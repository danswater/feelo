<?php

class Api_Model_DbTable_Auth extends Engine_Db_Table {

    protected $_primary = 'user_id';
	
	
	public function authenticate( $identity, $password) {
	
		$response = array( "success" => false, "errors" => array(), "data" => array() );
		
		
		if (!isset($identity) || !isset($password)) {
			$response[ "errors" ] = array( 'header' => $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', 
											'error' => 'Authorization failure.', 
											'messages' => array('Missing email or password.' )
										);
			
			return $response;
        }
		
		$user = Engine_Api::_()->user()->getUser($identity);
		if (!$user->getIdentity()) {
			$response[ "errors" ] = array( 'header' => $_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized', 
											'error' => 'Authorization failure.', 
											'messages' => array('You are not authorized to access this resource.' )
										);
										
			return $response;
		}
		
		$auth = Engine_Api::_()->user()->authenticate($user->email, $password);
		
		if (!$auth->isValid()) {
			$response[ "errors" ] = array( 'header' => $_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized', 
											'error' => 'Authorization failure.', 
											'messages' => $auth->getMessages() 
										);
			return $response;
		}
		
		$table = Engine_Api::_()->getDbTable('auth', 'api');

        $select = $table->select();
        $select->where('user_id = ?', $user->getIdentity());

        $existingToken = $table->fetchRow($select);

		$response[ "success" ] = true;
		
        if ($existingToken) {
            if ($existingToken->expire_date > time()){
				$response[ "data" ] = array(
					"token" => $existingToken->token,
					"expire_date" => $existingToken->expire_date,
					"user" => $auth
				);
				return $response;
			}else
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
		
		$response[ "data" ] = array(
			"token" => $token,
			"expire_date" => $expire_date,
			"user" => $auth
		);
		
		return $response;
	}
	
	static public function makeHash($user_id, $email, $password) {
        return sha1($user_id . $email . $password . time());
    }
	
	public function getUserData($user){
		$objUser = Engine_Api::_()->getApi( 'user', 'api' );
		$resultSetUser = $objUser->fetchUserDetails( $user->getIdentity() );
    	return $resultSetUser;
    }
	
	public function readExpiryDateById ( $id ) {
        $select = $this->select();
        $select->where('user_id = ?', $id );

        $existingToken = $this->fetchRow( $select );

		return $existingToken->expire_date;		
	}
	
	public function authenticateTest( $user, $params ) {
	
		// Login
		$table = Engine_Api::_()->getDbTable('auth', 'api');

        $select = $table->select();
        $select->where('user_id = ?', $user->getIdentity());
        $existingToken = $table->fetchRow($select);
		
		$existingToken->device_id = $params[ 'device_id' ];
		$existingToken->save();
		
		
		// Log out
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		$select->where ( 'device_id = ?', $params[ 'device_id' ] );
		
		$token = $table->fetchRow ( $select );
		
		$this->delete($token);

	}
	
	public function readAuthByUserId ( $id ) {
		$select = $this->select();
		$select->where('user_id = ?', $id );
		$ret = $this->fetchRow( $select );
		
		return $ret;
	}
	
	public function createAuth( $user, $params ) {
		$token = $this->makeHash($user->getIdentity(), $user->email, $user->password);
		
		//1 month
		$expire_date = time() + 2592000;
		
		$row = $this->createRow();
		$row->user_id = $user->getIdentity();
		$row->token = $token;
		$row->expire_date = $expire_date;
		$row->save();
		
		return $row;
	}
}