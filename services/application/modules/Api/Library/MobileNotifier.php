<?php

class Api_Library_MobileNotifier {

	public function manualSend( User_Model_User $user, Core_Model_Item_Abstract $subject, $message, $params ){

		$notifierTypes = Engine_Api::_()->getDbTable ( 'PushNotifications', 'Api' )->getNotificationType( $user );

		foreach( $notifierTypes as $notiferType ) {	

			if( $notiferType[ "type" ] == "android" ){

				$gcm = new Api_Library_Gcm();
				$gcm->setRegistrationId( $notiferType[ "device_id" ] );
	
				$gcm->setMessageData( strip_tags( $message ), $params );
				$result = $gcm->send(); 

			}
			else if( $notiferType[ "type" ] == "ios" ){

				$pushNotification = new Api_Library_PushNotifications();
				$pushNotification->setDeviceToken( $notiferType[ "device_id" ] );	

				$pushNotification->setMessageData( strip_tags( $message ), $params );
				$result = $pushNotification->send(); 

			}

		} 

	}


	/*
	 * Mobile Notifier
	 * 
	 * @param User_Model_User $user The user to receive the notification
	 * @param Core_Model_Item_Abstract $subject The item responsible for causing the notification
	 * @param Core_Model_Item_Abstract $object Bleh
	 * @param string $type
	 *
	 */

	public function send( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $type ){ 

		$message = array( "ios" => array( "success" => false ), "android" => array( "success" => false ) );

		$notifierTypes = Engine_Api::_()->getDbTable ( 'PushNotifications', 'Api' )->getNotificationType( $user );	
		$notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsUnreadAndMobileSendPaginator( $user, $type );
		
		
		foreach( $notifierTypes as $notiferType ) {
			
			foreach( $notifications as $notification ){
				
				
				if( $notiferType[ "type" ] == "android" ){
					if( $this->sendAndroid( $user, $subject, $object, $notiferType, $notification ) ) {
						$message[ "android" ] = array(
												"success" => true,
												"type" => $notification->type,
												"notification_id" => $notification->getIdentity()
						);
					}
				}
				else if( $notiferType[ "type" ] == "ios" ){
					if( $this->sendIos( $user, $subject, $object, $notiferType, $notification ) ){
						$message[ "ios" ] = array(
												"success" => true,
												"type" => $notification->type,
												"notification_id" => $notification->getIdentity()
						);
					}

				}
					
				
			}
		}
		
		return $message;

	}


	/*
	 * Responsible to send to the ios
	 * 
	 * @param {Object} $subject; the user who is responsible/getting the notification
	 * @param {Array} $notifer; 
	 * @param {Object} $notification
	 *
	 */

	protected function sendIos( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ){
		$pushNotification = new Api_Library_PushNotifications();
		
		$responseApi = new Api_Library_Response_Apn();
		
		$customResponse = $responseApi->getResponse( $user, $subject, $object, $notifer, $notification );

		$pushNotification->setDeviceToken( $notifer[ "device_id" ] );

		$pushNotification->setMessageData( strip_tags( $notification->__toString() ), $customResponse );

		$result = $pushNotification->send(); 

		if( $result == true ){
			// change the mobile_send to 1 if success
			Engine_Api::_()->getDbTable ( 'PushNotifications', 'Api' )->sendMobileNotification( $notification->getIdentity(), true );	

		}

		return $result;

	}

	/*
	 * Responsible to send to the Android
	 * 
	 * @param {Object} $subject; the user who is responsible/getting the notification
	 * @param {Array} $notifer; 
	 * @param {Object} $notification
	 *
	 */

	protected function sendAndroid( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {
		$gcm = new Api_Library_Gcm();

		$responseApi = new Api_Library_Response_Gcm();
		
		$customResponse = $responseApi->getResponse( $user, $subject, $object, $notifer, $notification );
		
		$gcm->setRegistrationId( $notifer[ "device_id" ] );

		$gcm->setMessageData( strip_tags( $notification->__toString() ), $customResponse );

		$result = $gcm->send(); 

		if( $result == true ){
			// change the mobile_send to 1 if success
			Engine_Api::_()->getDbTable ( 'PushNotifications', 'Api' )->sendMobileNotification( $notification->getIdentity(), true );	
		}


		return $result;

	}


}