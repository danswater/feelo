<?php
class Api_Api_ApnGcm extends Api_Api_Base {


	public function fetch( $user, $params, $type ) {
	
		$params[ "phone" ] = isset( $params[ "phone" ] ) ? $params[ "phone" ] : "ios";
		
		$notifications = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationsPaginator($user, $type);
		
		// hardcode to offset zero
		// $params[ "offset" ] = 0;
		
		$params[ "offset" ] = isset( $params[ "offset" ] ) ? $params[ "offset" ] : 0 ;
		
		$params[ "offset" ]++;
		
		// NOTE: $offset start with 1
		$notifications->setCurrentPageNumber( $params[ "offset" ] );

		if( isset( $params[ "limit" ] ) ){
			$notifications->setItemCountPerPage( $params[ "limit" ] );
		}

		$mobileSender = new Api_Library_MobileNotifier();
		$responseArray = array( "yamba" => array() );
		
		foreach( $notifications as $notification ) {
		
			if( $notifications->count() >= $params[ "offset" ] ){ 
				$typeResponse = $this->getResponse( $notification->getUser(), $notification->getSubject(), $notification->getObject(), $notification, $params[ "phone" ] );
				if( $typeResponse != null ) {
					$responseArray[ "yamba" ][] = $typeResponse;
				}
			}
		}
		
		return $responseArray;
	
	}
	
	public function getResponse( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notification, $phone ){
		
		$androidResponse = new Api_Library_Response_Gcm();
		$iosResponse = new Api_Library_Response_Apn();
		if( $phone == "android" ){
			$customResponse = $androidResponse->getResponse( $user, $subject, $object, null, $notification );
		}
		else{
			$customResponse = $iosResponse->getResponse( $user, $subject, $object, null, $notification );
		}
		
		return $customResponse[ "yamba" ];
		
	}


} 