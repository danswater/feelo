<?php
class Api2_Api_Box extends Core_Api_Abstract {

	public function fetchBoxByUserId ( $user, $params ) {
		$user_id = $user->getIdentity();
		
		$dbTableBox = Engine_Api::_ ()->getDbTable ( 'boxes', 'api2' );
		$boxes = $dbTableBox->readBoxByUserId( $user, array(
			'user_id' => $user_id,
			'offset'  => $params[ 'offset' ]
		) );
		
		$ret = array();
		foreach( $boxes as $key => $row ) {
			$circleId = $row->circle_id;
			$title    = $row->title;

			$userCount = $dbTableBox->readAndCountUserByBoxId( $user, array(
				'circle_id' => $circleId
			) );

			$project = $dbTableBox->readPhotoCoverByUserId( $user, array(
				'circle_id' => $circleId
			) );

			if ( empty( $project ) ) {
				$storagePath = 'public/no-image-m.jpg';
			} else {

				$dbTableMedia = Engine_Api::_()->getApi( 'media', 'api2' );
				$mediaRow = $dbTableMedia->fetchMediaByProjectIdWithStructuredResponse( $project[ 'project_id' ], $project[ 'cover_file_id' ] );
				$storagePath = $mediaRow->getStoragePath();

			}

			list( $width, $height ) = getimagesize( $storagePath );


			$box = new Api2_Model_Box();
			$box->setGroupId( $circleId );
			$box->setTitle( $title );
			$box->setUserCount( $userCount );
			$box->setStoragePath( $storagePath );
			$box->setImageWidth( $width );
			$box->setImageHeight( $height );

			$ret[] = $box;

		}

		return array(
			'Groups' => $ret
		);
	}

	public function fetchFeedByGroupId ( $user, $params ) {
		$apiFeed = Engine_Api::_()->getApi( 'project', 'api2' );
		$projects = $apiFeed->fetchFeedByBox( $user, $params[ 'group_id' ], $params[ 'offset' ] );

		if ( empty( $projects ) ) {
			throw new Exception( 'No results found.' );
		}

		return array( 'Feeds' => $projects );


	}

	public function createGroup ( $user, $params ) {
		$title = $params[ 'title' ];

		$apiBox = Engine_Api::_()->getApi( 'box', 'api' );
		
		$arrResultSet = $apiBox->createBox( $title, $user );
		
		if ( !empty( $arrResultSet[ 'error' ] ) ) {
			throw new Exception( $arrResultSet[ 'error' ][ 0 ] );
		}

		$box = $arrResultSet[ 'data' ];
		$box[ 'group_id' ] = (int)$box[ 'circle_id' ];
		unset( $box[ 'circle_id' ] );

		return array(
			'Group' => $box
		);
	}

	public function updateGroup ( $user, $params ) {

		$groupId = $params[ 'group_id' ];
		$title   = $params[ 'title' ];
				
		$apiBox = Engine_Api::_()->getApi( 'box', 'api' );
				
		$arrResultSet = $apiBox->editBox( $title, $groupId, $user );

		if ( !empty( $arrResultSet[ 'error' ] ) ) {
			throw new Exception( $arrResultSet[ 'error' ][ 0 ] );
		}
				
		$box = $arrResultSet[ 'data' ];
		$box[ 'group_id' ] = (int)$box[ 'circle_id' ];
		unset( $box[ 'circle_id' ] );

		return array(
			'Group' => $box
		);
	}

	public function deleteGroup ( $user, $params ) {

		$groupId = $params[ 'group_id' ];
		$apiBox = Engine_Api::_()->getApi( 'box', 'api' );
		
		$arrResultSet = $apiBox->deleteBox( $groupId, $user );

		if ( !empty( $arrResultSet[ 'error' ] ) ) {
			throw new Exception( $arrResultSet[ 'error' ][ 0 ] );
		}

		$box = $arrResultSet[ 'data' ];
		unset( $box[ 'circle_id' ] );

		return $box;

	}

	public function addToGroup ( $user, $params ) {
		$subject = Engine_Api::_ ()->user ()->getUser ( $params[ 'user_id' ] );
		$groupId = $params[ 'group_id' ];

		if( $user->getIdentity() === $subject->getIdentity() ) {
			throw new Exception( 'You cannot add yourself' );
		}

		if ( ( empty( $groupId ) ) || ( $user->getIdentity() == 0 )  || ( empty( $subject  ) ) ) {
			throw new Exception( 'Missing parameter' );
        }
		
        $dbTableCircles = Engine_Api::_()->getDbTable('circles', 'whmedia');
	
		$select = $dbTableCircles->select();
		$select->where( 'user_id = ?', $subject->getIdentity() );
		$select->where( 'circle_id = ?', $groupId );
			
		$group = $dbTableCircles->fetchAll( $select );
		
        if ( empty( $group ) ) {
			throw new Exception( 'Invalid group id' );
        }
		
		$apiBox = Engine_Api::_()->getApi( 'box', 'api' );

        if ( !$apiBox->has( $groupId, $subject ) ) {
			$results = $apiBox->add( $groupId, $subject );
        }
		else {
			throw new Exception( 'User is already in the group' );
		}

		$group = $results;
		$group[ 'Group' ] = $group[ 'Box' ];
		$group[ 'Group' ][ 'group_id' ] = $group[ 'Group' ][ 'circle_id' ];
		$group[ 'Group' ][ 'members' ][ 'groupitem_id' ] = $group[ 'Box' ][ 'members' ][ 'circleitem_id' ];
		unset( $group[ 'Group' ][ 'circle_id' ] );
		unset( $group[ 'Group' ][ 'members' ][ 'circleitem_id' ] );
		unset( $group[ 'Box' ] );

		return $group;		
	}

	public function removeToGroup ( $user, $params ) {
		$subject = Engine_Api::_ ()->user ()->getUser ( $params[ 'user_id' ] );
		$groupId = $params[ 'group_id' ];

		if( $user->getIdentity() === $subject->getIdentity() ) {
			throw new Exception( 'You cannot add yourself' );
		}

		if ( ( empty( $groupId ) ) || ( $user->getIdentity() == 0 )  || ( empty( $subject  ) ) ) {
			throw new Exception( 'Missing parameter' );
        }
		
        $dbTableCircles = Engine_Api::_()->getDbTable('circles', 'whmedia');
	
		$select = $dbTableCircles->select();
		$select->where( 'user_id = ?', $subject->getIdentity() );
		$select->where( 'circle_id = ?', $groupId );
			
		$group = $dbTableCircles->fetchAll( $select );
		
        if ( empty( $group ) ) {
			throw new Exception( 'Invalid group id' );
        }
		
		$apiBox = Engine_Api::_()->getApi( 'box', 'api' );

        if ( $apiBox->has( $groupId, $subject ) ) {
			$results = $apiBox->remove( $groupId, $subject );
        }
		else {
			throw new Exception( 'User is not in the group' );
		}

		$group = $results;
		unset( $group[ 'Box' ][ 'members' ][ 'circleitem_id' ] );
		unset( $group[ 'Box' ] );

		return $group;	
	}

}