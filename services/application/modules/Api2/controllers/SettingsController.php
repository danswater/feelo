<?php
class Api2_SettingsController extends Zend_Rest_Controller {

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
		
		$api2Settings = Engine_Api::_()->getApi( 'Settings', 'api2' );
		$result = array();
		
		
		switch( $params[ "method"] ){

			case 'changeGeneralInfo' :
				$result = $api2Settings->changeGeneralInformation( $params, $user );
			break;

			case 'updateGeneralInfo' :
				try {
					$result[ 'data' ]  = $api2Settings->updateGeneralInfo( $user, $params );
					$result[ 'error' ] = array();
				} catch ( Exception $e ) {
					$result[ 'data' ]  = array();
					$result[ 'error' ] = array( $e->getMessage() );
				}
			break;

			case 'uploadPrimaryPic':
				$result = $api2Settings->uploadPrimaryPic( $params, $user );
			break;

			case "updatePrivacy":
				$result = $api2Settings->updatePrivacy( $params, $user );
			break;

			case "getNotificationApp":
				$result = $api2Settings->getNotificationByType( "notificationSettings", $params, $user );
			break;

			case "getNotificationEmail":
				$result = $api2Settings->getNotificationByType( "emailNotifications", $params, $user );
			break;

			case "updateNotificationApp": 
				$result = $api2Settings->updateNotificationByType( "notificationSettings", $params, $user );
			break;

			case "updateNotificationEmail": 
				$result = $api2Settings->updateNotificationByType( "emailNotifications", $params, $user );
			break;

			case 'deleteAccount':
				$result = $api2Settings->deleteUser( $params, $user );
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