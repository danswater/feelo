<?php
class Api2_CategoryController extends Zend_Rest_Controller {

	public function init() {
		$this->getHelper( 'layout' )->getLayoutInstance()->disableLayout();
		$this->getHelper( 'viewRenderer' )->setNoRender();
		
		$this->getHelper( 'AjaxContext' )
			->addActionContext( 'get', 'json' )
			->addActionContext ( 'post', 'json' )
			->addActionContext ( 'edit', 'json' )
			->addActionContext ( 'put', 'json' )
			->addActionContext ( 'delete', 'json' )
			->initContext ( 'json' );
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
		$params = $this->getRequest()->getPost();
		
		$api2Hashtagcategory = Engine_Api::_()->getApi( 'category', 'api2' );
		$result = array();
		
		switch( $params[ "method"] ){

			case "fetchHashtagCategories":
				try {
					$result[ "data" ]  = $api2Hashtagcategory->fetchHashtagCategories( $user, $params );
					$result[ "error" ] = array();
				} catch ( Exception $e ) {
					$result[ "data" ]  = array();
					$result[ "error" ] = array( $e->getMessage() );
				}
			break;

			case "fetchHashtag":
				try {
					$result[ "data" ]  = $api2Hashtagcategory->fetchCategories( $user, $params );
					$result[ "error" ] = array();
				} catch ( Exception $e ) {
					$result[ "data" ]  = array();
					$result[ "error" ] = array( $e->getMessage() );
				}
			break;

			case "createCategoryPreferences":
				try {
					$result[ 'data' ]  = $api2Hashtagcategory->createCategoryPreferences( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			case "deleteCategoryPreferences":
				try {
					$result[ 'data' ]  = $api2Hashtagcategory->deleteCategoryPreferences( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = $e->getMessage();
				}
			break;

			default:
				$result = array( 
					"error" => "No method found."
				);
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

}