<?php
class Api2_SearchController extends Zend_Rest_Controller {

	public function init() {
		$this->_disableView();
		$this->_setAuthorizeMethod();
		//$this->_authenticate();
	}

	public function indexAction() {
	    $this->getResponse()->setHttpResponseCode( 200 );
	    $this->getHelper( 'json' )->sendJson( array(
	    	'message' => 'Hello World from index'
	    ) );
	}

	public function getAction() {
		$this->_forward( 'index' );
	}

	public function postAction() {
		$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
			
		if (count ( $auth ) != 1) {
			$this->getHelper( 'json' )->sendJson( array( 
				"error" => 'Unauthorized 101'
			) );
			return;
		}
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		$values = $this->getRequest()->getPost();
		$params = $values;

		$api2Hashtag = Engine_Api::_()->getApi( 'Hashtag', 'api2' );

		$result = array();
		switch( $params[ "method"] ){
			case "fetchHashtag":
				$result = $api2Hashtag->fetchHashtagByKeyword( $params, $user ); 
			break;

			case "fetchByKeyword":
				$api2Search = Engine_Api::_()->getApi( 'search', 'api2' );
				try {
					$result[ 'data' ]  = $api2Search->fetchAllByKeyword( $user, $params );
					$result[ 'error' ] = array();					
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = $e->getMessage();
				}

			break;

			case 'fetchHashtagsByKeyword' :
				$api2Search = Engine_Api::_()->getApi( 'search', 'api2' );
				try {
					$result[ 'data' ]  = $api2Search->fetchHashtagsByKeyword( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'fetchFavosByKeyword' :
				$api2Search = Engine_Api::_()->getApi( 'search', 'api2' );
				try {
					$result[ 'data' ]  = $api2Search->fetchFavosByKeyword( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'fetchFeedsByKeyword' :
				$api2Search = Engine_Api::_()->getApi( 'search', 'api2' );
				try {
					$result[ 'data' ] = $api2Search->fetchFeedsByKeyword( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'fetchUsersByKeyword' :
				$api2Search = Engine_Api::_()->getApi( 'search', 'api2' );
				try {
					$result[ 'data' ]  = $api2Search->fetchUsersByKeyword( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			default:
				$result [ 'data' ]  = array();
				$result [ 'error' ] = array( 'No method found.' );
			break;
		}
		
		$this->getHelper( 'json' )->sendJson( $result );
	}

	public function putAction() {
	    $this->getResponse()->setHttpResponseCode( 200 );
	    $this->getHelper( 'json' )->sendJson( array(
	    	'message' => 'Hello World from put'
	    ) );
	}

	public function deleteAction() {
	    $this->getResponse()->setHttpResponseCode( 200 );
	    $this->getHelper( 'json' )->sendJson( array(
	    	'message' => 'Hello World from delete'
	    ) );	
	}

	protected function _disableView() {
		$this->getHelper( 'layout' )->getLayoutInstance()->disableLayout();
		$this->getHelper( 'viewRenderer' )->setNoRender();
	}

	protected function _setAuthorizeMethod() {
		$this->getHelper( 'AjaxContext' )
			->addActionContext( 'get', 'json' )
			->addActionContext ( 'post', 'json' )
			->addActionContext ( 'edit', 'json' )
			->addActionContext ( 'put', 'json' )
			->addActionContext ( 'delete', 'json' )
			->initContext ( 'json' );
	}

	protected function _authenticate() {
		$userId = (int)$this->getRequest()->getParam( 'user_id' );

		if( empty( $userId ) ) {
			$this->getResponse()->setHttpResponseCode( 403 );
			return $this->getHelper( 'json' )->sendJson( array(
				'message' => 'Invalid user id'
			) );
		}

		$this->subject = Engine_Api::_()->user()->getUser( $userId );
		if( !$this->subject->getIdentity() ) {
			$this->getResponse()->setHttpResponseCode( 403 );
			return $this->getHelper( 'json' )->sendJson( array(
				'message' => 'Invalid user id'
			) );
		}
	}

	protected function _isAuthorized() {

		$token = 0;
		if( $this->getRequest()->getParam( 'token' ) ) {
			$token = $this->getRequest()->getParam( 'token' );
		}

		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
	
		$auth = $table->fetchRow ( $select );

		if (count ( $auth ) != 1) {
			return array(
				'response' => false,
				'message'  => 'forbidden'
			);
		}

		if ($auth->expire_date < time ()) {
			return array(
				'response' => false,
				'message'  => 'expired'
			);
		}

		return array(
			'response' => true,
			'message'  => null
		);	
	}

}