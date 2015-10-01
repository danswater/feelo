<?php
class Api_SearchController extends Zend_Rest_Controller {

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
		/*
		$authResponse = $this->_isAuthorized();
		if( $authResponse[ 'response' ] != true ) {
			$this->getResponse()->setHttpResponseCode( 403 );
			return $this->getHelper( 'json' )->sendJson( $authResponse );
		}
		*/
		$token = $this->_getParam ( 'token', null );
		
 		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
 		$select = $table->select ();
 		$select->where ( 'token = ?', $token );
		
 		$auth = $table->fetchRow ( $select );
		
 		if (count ( $auth ) != 1) {
 			return $this->_forward ( 'forbidden' );
 		}
        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');
		
 		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );		

		$search = Engine_Api::_()->getApi( 'search', 'api' );

		if( empty( trim( $this->getRequest()->getParam( 'keyword' ) )  ) ) {
			$this->getHelper( 'json' )->sendJson( array(
				'data' => array(),
				'error' => array( 'No results found' )
			) );		
		}
		
		$params = $this->getRequest()->getPost();
		
		switch( $this->getRequest()->getParam( 'method' ) ) {
			case 'fetchHashtag' :

				$keyword        = $this->getRequest()->getParam( 'keyword' );
				$search->offset = $this->getRequest()->getParam( 'offset' );
				$objResponse    = $search->fetchHashtag( $keyword, $user->getIdentity() );


			break;
			
			case 'fetchHashtag2' :
				$objResponse = $search->fetch( $user, $params );
			break;

			case 'fetchMedia' :

				$keyword        = $this->getRequest()->getParam( 'keyword' );
				$search->offset = $this->getRequest()->getParam( 'offset' );
				$search->limit 	= $this->getRequest()->getParam( 'limit', 10 );
				$objResponse    = $search->fetchMedia( $user, $keyword );

			break;

			case 'fetchUser' :

				$keyword        = $this->getRequest()->getParam( 'keyword' );
				$search->offset = $this->getRequest()->getParam( 'offset' );
				$search->limit 	= $this->getRequest()->getParam( 'limit', 10 );
				$objResponse    = $search->fetchUser( $keyword );

			break;
			
			case 'fetchMedia2' :

				$keyword        = $this->getRequest()->getParam( 'keyword' );
				$search->offset = $this->getRequest()->getParam( 'offset' );
				$objResponse    = $search->fetchMedia2( $keyword );
			
			break;
			
			case 'fetchFavo' :
			
				$search->offset = $this->getRequest()->getParam( 'offset' );
				$search->limit 	= $this->getRequest()->getParam( 'limit', 10 );
				$objResponse = $search->fetchFavo( $user, $params );
				
			break;

			default :

				$objResponse = array(
					'data' => array(),
					'error' => array( 'Invalid method' )
				);

			break;
		}

	    $this->getResponse()->setHttpResponseCode( 201 );
	    $this->getHelper( 'json' )->sendJson( $objResponse );

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