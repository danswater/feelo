<?php
class Api_NotificationController extends Zend_Rest_Controller {

	public function indexAction () {}

	public function getAction () {}
	
	public function putAction () {}
	
	public function deleteAction () {}	
	
	public function init () {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
	}
		
	public function postAction () {
	
		$params = $this->getRequest()->getPost();
		
		try {
			$userApi = Engine_Api::_()->getApi( 'user', 'api' ); 
			$user = $userApi->identify( $params );
		} catch ( Exception $e ) {
			//print_r( $e ); exit;
			$this->getHelper( 'json' )->sendJson( array( 
				"data" => array(),
				"error" => array(
					$e->getMessage()
				)
			) );
			exit;
		}
		
		try {
			$notificationApi = Engine_Api::_()->getApi( 'notification', 'api' );
			$ApnGcmResponse = Engine_Api::_()->getApi( 'ApnGcm', 'api' );
			
			switch( $params[ 'method' ] ) {
				case 'fetchAll' :
					$data = $notificationApi->fetch( $user, $params );
				break;
				
				case 'general':
					$data = $ApnGcmResponse->fetch( $user, $params, '' );
				break;
				
				case 'request' : 
					$data =  $ApnGcmResponse->fetch( $user, $params, 'friend_follow_request' );
				break;
				
				case 'confirm' :
					$data = $notificationApi->confirm( $user, $params );
				break;
				
				case 'updateReadStatus' :
					$params[ 'read' ] = 1;
					$data = $notificationApi->set( 'read', $params );
				break;

				case "register" : 
					$pushNotificationObject = Engine_Api::_()->getDbTable ( 'PushNotifications', 'Api' );
					$data = $pushNotificationObject->register( $params );
				break;

				case "unregister":
					$pushNotificationObject = Engine_Api::_()->getDbTable ( 'PushNotifications', 'Api' );
					$data = $pushNotificationObject->unregister( $params );
				break;
				
				case "getNotifcationDetails" :
					$notificationDetails = $notificationApi->getNotifcationDetails( $user, $params );
					
					$data = array();
					$data[ "data" ] = $notificationDetails;
					$data[ "error" ] = array();
				break;
				
				case "updateToReadNotification" :
					$hasUpdatedToReadNotification = $notificationApi->updateToReadNotification( $user );
					
					$data = array();
					$data[ "data" ] = $hasUpdatedToReadNotification;
					$data[ "error" ] = array();
				break;
				
				case 'fetchCountNotification' : 
					$data = $notificationApi->countNotification( $user, $params );
				break;
				
				default:
					$data = array(
						"message" => "undefined method"
					);
				break;
			}
		} catch ( Exception $e ) {
			print_r( $e ); exit;
		}
		$this->getHelper( 'json' )->sendJson( $data );
		
	}
	
}