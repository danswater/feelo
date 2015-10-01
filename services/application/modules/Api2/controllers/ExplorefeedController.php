<?php
class Api2_ExplorefeedController extends Zend_Rest_Controller {
	public function init() {
		$this->_disableView();
		$this->_setAuthorizeMethod();
	}
	public function indexAction() {
	    $this->getResponse()->setHttpResponseCode( 200 );
	    $this->getHelper( 'json' )->sendJson( array(
	    	'message' => 'Hello World from index'
	    ) );
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
		$api2Project = Engine_Api::_()->getApi( 'project', 'api2' );
		$result = array();
		switch( $params[ "method"] ){
			case "fetchFeed":
				try {
					$result[ "data" ]  = $api2Project->fetchExploreFeed( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'fetchTrending' :
				try {
					$result[ 'data' ] = $api2Project->fetchTrending( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ] = array();
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

	public function getAction( ) {
	}
	public function putAction() {
	}
	public function deleteAction() {
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
}