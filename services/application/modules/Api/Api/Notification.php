<?php
class Api_Api_Notification extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	public function fetch ( $user, $params ) {
		$notificationTable = Engine_Api::_()->getDbTable( 'notifications', 'api' );		
		$notificationCollection = $notificationTable->fetchById( $user, $params );
		$return = $this->_parse( array(
			'user'       => $user,
			'collection' => $notificationCollection,
			'params'     => $params
		) );
	
		if ( empty( $return ) ) {
			return array(
				'yamba'  => array(),
				'error' => array( 'No results found' )
			);
		}
		
		return array(
			'yamba'  => $return,
			'error' => array()
		);
	}
	
	protected function _parse ( $options ) {

		if ( empty( $options[ 'collection' ]->toArray() ) ) {
			return array();
		}
		
		// get user
		$userApi           = Engine_Api::_()->getApi( 'user', 'api' );
		$mediaApi          = Engine_Api::_()->getApi( 'whmedia', 'api' );
		$notificationTable = Engine_Api::_()->getDbTable( 'notifications', 'api' );
		
		foreach( $options[ 'collection' ]->toArray() as $key => $model ) {
			$ret = array();

			// parse into acceptable type
			$type = $this->getNotifcationType( $model[ 'type' ] );
			$ret[ 'type' ] = $type[ 'name' ]; 

			// get project information
			$project = Engine_Api::_()->getApi( 'project', 'api' )->getProject( $model[ 'object_id' ] );
			
			// Fetch Media(s)	
			$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
			$media = $objMedia->fetchMediaDetails( $project->project_id, $project->cover_file_id );
			
			// check weather if related to project or not
			$ommittedTypes = array(
				'friend_request',
				'whmedia_following'
			);
			if ( !in_array( $type[ 'name' ], $ommittedTypes ) ) {
				$ret[ 'data' ] = $this->getNotificationData( $model[ 'type' ], $media, $model, $project );
			}
			
			// get user information
			$user = $userApi->fetchUserDetails( $model[ 'subject_id' ] );
			$ret[ 'data' ][ 'description' ] = $type[ 'description' ];
			$ret[ 'data' ][ 'user' ] = array(
				'user_id'            => $user[ 'user_id' ],
				'display_name'       => $user[ 'username' ],
				'image_storage_path' => $user[ 'storage_path' ]
			);
		
			if ( !is_null( $ret[ 'type' ] )  && !is_null( $ret[ 'data' ][ 'user' ][ 'user_id' ] ) ) {
				$newCollection[] = $ret;
			}
		}
		return $newCollection;
	}
	
	public function set ( $key, $params ) {		
		$notificationTable = Engine_Api::_()->getDbTable( 'notifications', 'api' );
		$notification = $notificationTable->find( $params[ 'notification_id' ] );

		$row = $notification->current();
		$row->$key = $params[ $key ];
		$row->save();
	
		$user = Engine_Api::_ ()->user ()->getUser ( $params[ 'user_id' ] );
		return $this->_parse( array(
			'user'       => $user,
			'collection' => $notification
		) );
	}
	
	public function approveRequest ( $user, $params ) {
		$followApi = Engine_Api::_()->getApi( 'follow', 'api' );
		
		$this->set( 'read', '1' );
		
		/*
		$followApi->set( array(
			'user_id'       => $params[ 'user_id' ],
			'follower_id'   => $user->getIdentity(),
			'creation_date' => now()
		) );
		*/
		
		$response = $followApi->toggleFollowAction( $params[ 'user_id' ], $user->getIdentity() );
		$this->set( 'mitigated', '1' );
		// set Read 1
		// set mitigated 1
		// insert data to engine4_whmedia_follow
		return $response;
	}
	
	public function denyRequest ( $user, $params ) {
		$followApi = Engine_Api::_()->getApi( 'follow', 'api' );
		
		$this->set( 'read', '1' );
		
		/*
		$followApi->set( array(
			'user_id'       => $params[ 'user_id' ],
			'follower_id'   => $user->getIdentity(),
			'creation_date' => now()
		) );
		*/
		
		$this->set( 'mitigated', '1' );

		return $response;	
	}
	
	public function getNotifcationType ( $type ) {
		$types = array(
			'liked' => array(
				'name'        => 'LIKED_POST',
				'description' => 'liked your post.'
			),

			'commented' => array(
				'name'        => 'COMMENTED_POST',
				'description' => 'commented your post.'
			),

			'friend_request'  => array(
				'name'        => 'FRIEND_REQUEST',
				'description' => 'request to follow you.'
			),

			'whmedia_following' => array(
				'name'        => 'FRIEND_ACCEPTED',
				'description' => 'is now following you.'
			),

			'followed_favo' => array(
				'name'        => 'FOLLOWED_FAVO',
				'description' => 'is now following your favo'
			) 
		);

		return $types[ $type ];
	}
	
	public function getNotificationData ( $type, $media, $model, $project ) {
		$favo = '';
		if ( $type == 'followed_favo' ) {
			$favo = json_decode( $model[ 'params' ] );
		}
		
		$data = array(
			'liked' => array(
				'post' => array(
					'project_id'         => $media[ 'project_id' ],
					'title'              => $project->title,
					'image_storage_path' => $media[ 'storage_path' ]
				)
			),

			'commented' => array(
				'post' => array(

					'project_id'         => $media[ 'project_id' ],
					'title'              => $project->title,
					'image_storage_path' => $media[ 'storage_path' ]
				)
			),

			'followed_favo' => array(
				'favo' => array(
					'favo_id'            => $favo->favcircle_id,
					'image_storage_path' => $media[ 'storage_path' ]
				)
			)

		);

		return $data[ $type ];
	}
	
	public function getNotifcationDetails ( $user, $params ) {

		$notificationDbTable = Engine_Api::_()->getDbTable( 'notifications', 'api' );

		// notification not yet read		
		$unseenCount = $notificationDbTable->readNotifcationCount( $user );
		
		// has new general notification?
		$hasNewGeneralNotification = $notificationDbTable->readNewGeneralNotification( $user );
		
		// has new request notification?
		$hasNewRequestNotification = $notificationDbTable->readNewRequestNotification( $user );
		
		$obj = new Api_Model_NotificationDetails();
		$obj->setNotificationCount( $unseenCount );
		$obj->setHasGeneralNotification( $hasNewGeneralNotification );
		$obj->setHasRequestNotification( $hasNewRequestNotification );

		return $obj;
	}
	
	public function updateToReadNotification ( $user ) {
		$notificationDbTable = Engine_Api::_()->getDbTable( 'notifications', 'api' );

		// prototype
		$hasUpdatedToReadNotification = 0;
		$hasUpdatedToReadNotification = $notificationDbTable->updateToReadNotification( $user );
		
		$ret = array();
		$ret[ 'status' ] = $hasUpdatedToReadNotification;
		
		return $ret;
	}
	
	public function countNotification( $user, $params ){
		$notificationDbTable = Engine_Api::_()->getDbTable( 'notifications', 'api' );
		$responseJson = array( "data" => array(), "error" => array() );

		$count = $notificationDbTable->getNotificationCount( $user );

		$responseJson[ 'data' ] = array(
			'count' => $count,
			'success' => true
		);

		return $responseJson; 
	}
}