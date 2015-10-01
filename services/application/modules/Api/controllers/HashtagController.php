<?php
class Api_HashtagController extends Zend_Rest_Controller {

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

		$hashTag = Engine_Api::_()->getApi( 'hashtag', 'api' );

		switch( $this->getRequest()->getParam( 'method' ) ) {

			case 'isFollowed' :

				$hashTag->userId = $this->getRequest()->getParam( 'user_id' );
				$hashTag->tagId  = $this->getRequest()->getParam( 'tag_id' );
				$objResponse     = $hashTag->isFollowed();

			break;

			case 'fetchAll' :

				$hashtag->offset = $this->getRequest()->getParam( 'offset' );
				$objResponse = $hashTag->fetchAll();

			break;

			case 'fetchById' :

				$hashTag->tagId = $this->getRequest()->getParam( 'tag_id' );
				$objResponse = $hashTag->fetchById();

			break;

		}

	    $this->getResponse()->setHttpResponseCode( 200 );
	    $this->getHelper( 'json' )->sendJson( $objResponse );
	}

	public function postAction() {

		$authResponse = $this->_isAuthorized();
		if( $authResponse[ 'response' ] != true ) {
			$this->getResponse()->setHttpResponseCode( 403 );
			return $this->getHelper( 'json' )->sendJson( $authResponse );
		}
		
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

		$hashTag = Engine_Api::_()->getApi( 'hashtag', 'api' );
		$params = $this->getRequest()->getPost();
		
		switch( $this->getRequest()->getParam( 'method' ) ) {
			case 'followHashtag' :

				$hashTag->userId = $user->getIdentity();
				$hashTag->tagId  = $this->getRequest()->getParam( 'tag_id' );
				$objResponse     = $hashTag->toggleHashTag( $user, $hashTag );


			break;

			case 'isFollowed' :

				$hashTag->userId = $this->getRequest()->getParam( 'user_id' );
				$hashTag->tagId  = $this->getRequest()->getParam( 'tag_id' );
				$objResponse     = $hashTag->isFollowed();

			break;

			case 'fetchAll' :

				$objResponse = $hashTag->fetchAll();

			break;
			
			case 'fetchHashtagPosts' :
				$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
				$objResponse         = $projectFeed->fetchFeed( $user, 'hashtag', $this->_getParam( 'offset', null ), $this->_getParam( 'tag_id', null ) );
			break;
			
			case 'fetchFollowedHashtag' :
				$objResponse = $hashTag->fetchFollowedHashtag( $user, $params );
			break;
			
			default:
				$objResponse = array(
					'data'  => array(),
					'error' => array( 'No parameters found' )
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