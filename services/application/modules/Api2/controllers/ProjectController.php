<?php
class Api2_ProjectController extends Zend_Rest_Controller {

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
		$values = $this->getRequest()->getPost();
		$params = $values;
		
		$result = array();

		$api2Project = Engine_Api::_()->getApi( 'project', 'api2' );
		switch( $params[ "method"] ){

			case "fetchEmbedded":
				$result[ "data" ] = $api2Project->getEmbedded( $user, $params );
				$result[ "error" ] = array();
			break;

			case "createFeedInRss":

				$user   = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
				Engine_Api::_()->user()->setViewer( $user );

				date_default_timezone_set( $user->timezone );

				$params = $this->getRequest()->getPost();

				$apiProject  = Engine_Api::_()->getApi( 'project', 'api' );

				$files = $_FILES;
				$file = $api2Project->createFeedInRss( $user, $files, $params );

				if ( !empty( $file[ 'data' ] ) ) {
					$params[ 'project_id' ] = $file[ 'data' ][ 'project_id' ];
					$result[ "data" ]  = $apiProject->feedDetails( $user, $params );
					$result[ 'error' ] = array();
				} else {
					$result[ 'data' ] = array();
					$result[ 'error'] = array( 'Unable to save data' );
				}

			break;

			case "create" :
				Engine_Api::_()->user()->setViewer( $user );
				
				date_default_timezone_set( $user->timezone );
				
				$params     = $this->getRequest()->getPost();
				$apiProject = Engine_Api::_()->getApi( 'project', 'api' );
				
				$files = $_FILES;

				$fileInfo  = $apiProject->uploadFeed( $user, $files  );

				if( !empty( $fileInfo[ 'data' ] ) ) {
					$params[ 'project_id' ] = $fileInfo[ 'data' ][ 'project_id' ];
				
					$api2Project      = Engine_Api::_()->getApi( 'project', 'api2' );
					$result[ 'data' ] = $api2Project->create( $user, $params );			
				}


			break;

			case "delete" :
				try {
					$result[ 'data' ] = $api2Project->delete( $user, $params );
					$result[ 'error' ] = array();
				} catch( Exception $e ) {
					$result[ 'data' ] = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case "edit" :
				$result = $api2Project->edit( $user, $params );
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