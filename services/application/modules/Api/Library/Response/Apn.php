<?php


class Api_Library_Response_Apn {

	/*
	 * Responsible to send to the ios
	 * 
	 * @param User_Model_User $user The user to receive the notification
	 * @param Core_Model_Item_Abstract $subject The item responsible for causing the notification
	 * @param Core_Model_Item_Abstract $object Bleh
	 * @param {Array} $notifer; 
	 * @param {Object} $notification
	 *
	 */
	
	public function getResponse( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {
		

		switch( strtolower( $notification->type ) ) {
		
			case "followed_favo" :
				return $this->customFollowedFavo( $user, $subject, $object, $notifer, $notification );
				break;
			
			case "follower_favo" :
				return $this->customFollowerFavo( $user, $subject, $object, $notifer, $notification );
				break;

			case "liked" :
				return $this->customLikedPost( $user, $subject, $object, $notifer, $notification );
				break;

			case "friend_follow" :
			case "friend_follow_request" :
				return $this->customFriendFollowingRequest( $user, $subject, $object, $notifer, $notification );
				break;

			case "friend_follow_accepted" :
				return $this->customFriendFollowingAccepted( $user, $subject, $object, $notifer, $notification );
				break;
	
			case "whmedia_processed_failed" :  
			case "tagged" :
			case "liked_commented" :
			case "commented_commented" :	
			case "commented" :
				return $this->customCommented( $user, $subject, $object, $notifer, $notification );
				break;
			
			default :
				return array();
				break;
		
		
		
		}
	
	}
	
	public function customDescription( $message, $user, $subject ){
		
		$names = array( "username", "displayname" );
		
		foreach( $names as $name ) {
			
			if( isset( $user->$name ) ){
				$message = str_replace( $user->$name, "", $message );
			}
			
			if( isset( $subject->$name ) ){
				$message = str_replace( $subject->$name, "", $message );
			}
		
		}
		
		return trim( $message );
	}
	
	public function customCommented( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {

		$dbTableMedia = Engine_Api::_()->getDbTable( 'medias', 'api2' );
		$medias = $dbTableMedia->readMediaByProjectId( $notification->getObject()->project_id );
		
		$rowMedia = $medias->current();
		
		$arrMedia[ 'media_code' ] = empty( $rowMedia->code ) ? 'null' : $rowMedia->code;
		$arrMedia[ 'url' ] = empty( $rowMedia->url ) ? 'null' : $rowMedia->url;
		
		$feedType = Api_Helper_DetermineFeedType::execute( $arrMedia );
	
		return array(
			"yamba" => array(
				"notification_id" => $notification->notification_id,	
				"type" => strtoupper( $notification->type ),
				"data" => array(
					"description" => $this->customDescription( strip_tags( $notification->__toString() ), $user, $subject ),	
					"receiver" => array(						
						"user_id" => $user->getIdentity(),						
						"display_name" =>  $user->displayname,		
						'username' => $user->username,				
						"image_storage_path" =>  $user->getPhotoUrl("thumb.icon1"),					
					),
					"sender" => array(						
						"user_id" => $subject->getIdentity(),						
						"display_name" =>  $subject->displayname,	
						'username' => $subject->username,					
						"image_storage_path" =>  $subject->getPhotoUrl("thumb.icon1"),					
					),
					"post" => array(
						"project_id" => $object->getIdentity(),
						"title" => $object->getTitle(),
						"image_storage_path" => $object->getPhotoUrl(50, false, false),
						"feed_type" => $feedType
					)
				)	
			)
		);

	}
	

	public function customFriendFollowingAccepted( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {

		return array(
			"yamba" => array(
				"notification_id" => $notification->notification_id,
				"type" => strtoupper( $notification->type ),
				"data" => array(
					"description" => $this->customDescription( strip_tags( $notification->__toString() ), $user, $subject ),	
					"receiver" => array(						
						"user_id" => $user->getIdentity(),						
						"display_name" =>  $user->displayname,	
						'username' => $user->username,						
						"image_storage_path" =>  $user->getPhotoUrl("thumb.icon1"),					
					),
					"sender" => array(						
						"user_id" => $subject->getIdentity(),						
						"display_name" =>  $subject->displayname,		
						'username' => $subject->username,					
						"image_storage_path" =>  $subject->getPhotoUrl("thumb.icon1"),					
					)
				)
			)
		);

	}


	public function customFriendFollowingRequest( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {

		return array(
			"yamba" => array(
				"notification_id" => $notification->notification_id,
				"type" => strtoupper( $notification->type ),
				"data" => array(
					"description" => $this->customDescription( strip_tags( $notification->__toString() ), $user, $subject ),	
					"receiver" => array(						
						"user_id" => $user->getIdentity(),						
						"display_name" =>  $user->displayname,	
						'username' => $user->username,						
						"image_storage_path" =>  $user->getPhotoUrl("thumb.icon1"),					
					),
					"sender" => array(						
						"user_id" => $subject->getIdentity(),						
						"display_name" =>  $subject->displayname,		
						'username' => $subject->username,					
						"image_storage_path" =>  $subject->getPhotoUrl("thumb.icon1"),					
					)	
				)
			)
		);

	}
	public function customLikedPost( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {

		$dbTableMedia = Engine_Api::_()->getDbTable( 'medias', 'api2' );
		$medias = $dbTableMedia->readMediaByProjectId( $notification->getObject()->project_id );
		$rowMedia = $medias->current();
		
		$arrMedia[ 'media_code' ] = empty( $rowMedia->code ) ? 'null' : $rowMedia->code;
		$arrMedia[ 'url' ] = empty( $rowMedia->url ) ? 'null' : $rowMedia->url;
		
		$feedType = Api_Helper_DetermineFeedType::execute( $arrMedia );
	
		return array(
			"yamba" => array(
				"notification_id" => $notification->notification_id,
				"type" => strtoupper( $notification->type ),
				"data" => array(
					"description" => $this->customDescription( strip_tags( $notification->__toString() ), $user, $subject ),	
					"receiver" => array(						
						"user_id" => $user->getIdentity(),						
						"display_name" =>  $user->displayname,	
						'username' => $user->username,						
						"image_storage_path" =>  $user->getPhotoUrl("thumb.icon1"),					
					),
					"sender" => array(						
						"user_id" => $subject->getIdentity(),						
						"display_name" =>  $subject->displayname,		
						'username' => $subject->username,					
						"image_storage_path" =>  $subject->getPhotoUrl("thumb.icon1"),					
					),
					"post" => array(
						"project_id" => $object->getIdentity(),
						"title" => $object->getTitle(),
						"image_storage_path" => $object->getPhotoUrl(50, false, false),
						"feed_type" => $feedType
					)
				)
			)
		);	

	}
	
	
	public function customFollowedFavo( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {

		$favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
        $favcircleName = $favcircleTable->info('name');
        $select = $favcircleTable->select()
          ->from($favcircleName)
          ->where("{$favcircleName}.favcircle_id = ?",  $notification->params[ "favcircle_id" ]);

        $favResult = $favcircleTable->fetchAll($select)->toArray();
        $record = $favResult[0];

        $storagePhoto = Engine_Api::_()->getItem('storage_file', $record[ "photo_id" ] );
        $children = $storagePhoto->getChildren();
        $photoArray = array();
        foreach($children as $child){
          $photoArray[$child["type"]] = $child["storage_path"];
        }

		return array( 		
			"yamba" => array(		
				"notification_id" => $notification->notification_id,		
				"type" => strtoupper( $notification->type ),				
				"data" => array( 					
					"description" => $this->customDescription( strip_tags( $notification->__toString() ), $user, $subject ),					
					"receiver" => array(						
						"user_id" => $user->getIdentity(),						
						"display_name" =>  $user->displayname,	
						'username' => $user->username,						
						"image_storage_path" =>  $user->getPhotoUrl("thumb.icon1"),					
					),
					"sender" => array(						
						"user_id" => $subject->getIdentity(),						
						"display_name" =>  $subject->displayname,		
						'username' => $subject->username,					
						"image_storage_path" =>  $subject->getPhotoUrl("thumb.icon1"),					
					),					
					"favo" => array(						
						"favo_id" => $notification->params[ "favcircle_id" ],						
						"image_storage_path" => $photoArray["icon"]				
					)				
				)			
			)	
		);
	
	}

	public function customFollowerFavo( User_Model_User $user, Core_Model_Item_Abstract $subject, Core_Model_Item_Abstract $object, $notifer, $notification ) {

		$favcircleTable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
        $favcircleName = $favcircleTable->info('name');
        $select = $favcircleTable->select()
          ->from($favcircleName)
          ->where("{$favcircleName}.favcircle_id = ?",  $notification->params[ "favcircle_id" ]);

        $favResult = $favcircleTable->fetchAll($select)->toArray();
        $record = $favResult[0];

        $storagePhoto = Engine_Api::_()->getItem('storage_file', $record[ "photo_id" ] );
        $children = $storagePhoto->getChildren();
        $photoArray = array();
        foreach($children as $child){
          $photoArray[$child["type"]] = $child["storage_path"];
        }
		
		$dbTableMedia = Engine_Api::_()->getDbTable( 'medias', 'api2' );
		$medias = $dbTableMedia->readMediaByProjectId( $notification->getObject()->project_id );
		$rowMedia = $medias->current();
		
		$arrMedia[ 'media_code' ] = empty( $rowMedia->code ) ? 'null' : $rowMedia->code;
		$arrMedia[ 'url' ] = empty( $rowMedia->url ) ? 'null' : $rowMedia->url;
		
		$feedType = Api_Helper_DetermineFeedType::execute( $arrMedia );


		return array( 
			"yamba" => array(
				"notification_id" => $notification->notification_id,
				"type" => strtoupper( $notification->type ),
				"data" => array(
					"description" => $this->customDescription( strip_tags( $notification->__toString() ), $user, $subject ),	
					"receiver" => array(						
						"user_id" => $user->getIdentity(),						
						"display_name" =>  $user->displayname,	
						'username' => $user->username,						
						"image_storage_path" =>  $user->getPhotoUrl("thumb.icon1"),					
					),
					"sender" => array(						
						"user_id" => $subject->getIdentity(),						
						"display_name" =>  $subject->displayname,		
						'username' => $subject->username,					
						"image_storage_path" =>  $subject->getPhotoUrl("thumb.icon1"),					
					),
					"post" => array(
						"project_id" => $object->getIdentity(), 
						"title" => $object->getTitle(),
						"image_storage_path" => $object->getPhotoUrl(50, false, false),
						"feed_type" => $feedType
					),
					"favo" => array(						
						"favo_id" => $notification->params[ "favcircle_id" ],						
						"image_storage_path" => $photoArray["icon"]				
					)	
				)
			)
		);	
	}
	
}