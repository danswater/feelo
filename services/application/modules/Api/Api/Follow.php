<?php
class Api_Api_Follow extends Api_Api_Base {

	// to be append on users offset
	const OFFSET_SUFFIX = 0;

	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	public function toggleFollowAction( $user, $params ) {
		$viewer = $user;

		if( null == ( $subject = Engine_Api::_()->getItem('user', $params[ 'subject_id' ] ) ) ) {
		  $this->view->status = false;

		  return array(
			'data' => array(),
			'error' => array( 'No member specified' )
		  );
		}
		
		$is_req = 0;
		if( !$subject->authorization()->isAllowed( $viewer, 'view' ) ){
			$is_req = 1;
		}

		if ( $subject->getIdentity() == $viewer->getIdentity() ) {
			$this->view->status = false;
			
			return array(
				'data' => array(),
				'error' => "You can't follow yourself."
			);
		}
		
        $followApi = Engine_Api::_()->getDbtable('follows', 'api' );
		
        // Process
        $db = $followApi->getAdapter();
        $db->beginTransaction();
		
        try {
          $notification = Engine_Api::_()->getDbtable('notifications', 'activity')
            ->getNotificationBySubjectAndType($subject, $viewer, 'friend_follow_request');

          if ($followApi->isFollow($viewer, $subject) || $followApi->isForPendingApproval( $viewer, $subject ) ) {
              $followApi->unFollow($viewer, $subject);
              $isFollow = false;
			  
			  

              if( $is_req == 1 && $notification){
                $notification->delete();
              }
			  
			  // viewer wants to unfollow the subject so we must set this to 0
			  $is_req = 0;
          }
          else {
            $isFollow = true;
            if( $is_req == 1){
              $followApi->Follow( $viewer, $subject, 1 );
              Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subject, $viewer, $subject, 'friend_follow_request');
            }else{
              	$followApi->Follow($viewer, $subject, 0 );
              	$notifier = Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($subject, $viewer, $subject, 'friend_follow');

				$notifier = Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($viewer, $subject, $viewer, 'friend_follow_accepted');
              //Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subject, $viewer, $subject, 'whmedia_following');
            }
          }
          
          $db->commit();
			
		  $objUser = Engine_Api::_()->getApi( 'user', 'api' );
		  $resultSet[ 'User' ] = $user = $objUser->fetchUserDetails( $subject->getIdentity() );

		  $projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
		  $resultSet[ 'User' ][ 'posts' ]  = $posts = $projectFeed->countAllPosts( $viewer );		  
		  
		  $obj = new Api_Model_UserExt();
		  $obj->setUserId( $user[ 'user_id' ] );
		  $obj->setDisplayname( $user[ 'displayname' ] );
		  $obj->setUsername( $user[ 'username' ] );
		  $obj->setPhotoId( $user[ 'photo_id' ] );
		  $obj->setStatus( $user[ 'status' ] );
		  $obj->setStatusDate( $user[ 'status_date' ] );
		  $obj->setEmail( $user[ 'email' ] );
		  $obj->setLocale( $user[ 'locale' ] );
		  $obj->setLanguage( $user[ 'language' ] );
		  $obj->setStoragePath( $user[ 'storage_path' ] );
		  $obj->setFollowing( $user[ 'following' ] );
		  $obj->setFollowers( $user[ 'followers' ] );
		  $obj->setPosts( $posts );
		  
		  $resultSet[ 'User' ] = $obj;
		  
		  $resultSet[ 'is_followed' ] = ( int )$followApi->isFollow( $viewer, $subject ) ;

		  $resultSet[ 'pending_approval' ] = ( int ) $followApi->isForPendingApproval( $viewer, $subject );

		  return $resultSet;
		  
        }
        catch( Exception $e ) {
			return array(
				'data' => array(),
				'error' => $e->getMessage()
			);
        }

	}

	public function countIFollow( $user ) {
		
		$dbTableFollow = Engine_Api::_()->getDbTable( 'follows', 'api' );
		$select = $dbTableFollow->select()
			->where( 'follower_id = '. $user->getIdentity() )
			->where( 'pending_approval != 1' );

		$result = $dbTableFollow->fetchAll( $select );

		return count( $result );

	}

	public function countMyFollowers( $user ) {
		$objTable = Engine_Api::_()->getDbtable( 'follow', 'whmedia' );
		$objDb = $objTable->getAdapter();


		$where = 'user_id = ?';
		$value = array( 'user_id' => $user->getIdentity() );
		$objSelect = $objTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $value ) ) );

		$result = $objDb->fetchAll( $objSelect );

		return count( $result );
	}

	public function getFollowedUsers( $user, $offset ) {
		$objTable = Engine_Api::_()->getDbTable( 'follow', 'whmedia' );
		$objDb = $objTable->getAdapter();

		$suffix = $offset . self::OFFSET_SUFFIX;
		$objSelect = $objTable->select()
			->where( 'follower_id ='. $user->getIdentity() )
			->order( array( 'creation_date DESC' ) )
			->limit( 10, $suffix );

		$result = $objDb->fetchAll( $objSelect );

		// Fetch user
		$objUser = Engine_Api::_()->getApi( 'user', 'api' );
		foreach( $result as $key => $value) {
			$resultSetUser = $objUser->fetchUserDetails( $value[ 'user_id' ] );
			$feedResultSet[ $key ][ 'User' ] = $resultSetUser;
		}

		if( is_null( $feedResultSet ) ) {
			return array(
				'data' => array(),
				'error' => array( 'No results found' )
			);
		}
		return array(
			'data' => $feedResultSet,
			'error' => array()
		);
	}
	
	public function fecthFollowers ( $user, $subject, $params ) {
		if ( is_null( $params[ 'offset' ] ) ) {
			return array(
				'data' => array(),
				'error' => array( 'Missing parameters' )
			);
		}
		
		$params[ 'offset' ] += 1;
		
		$whmediaFollow = Engine_Api::_()->getDbTable( 'follow', 'whmedia' );
		$activityNotifications = Engine_Api::_()->getDbtable('notifications', 'activity');
		$objUser = Engine_Api::_()->getApi( 'user', 'api' );
		$followers = $whmediaFollow->fetchFollowerPaginator( $subject->getIdentity() ) ;
		
		// NOTE: $offset start with 1
		$followers->setCurrentPageNumber( $params[ "offset" ] );
		
		$dbTableFollow = Engine_Api::_()->getDbtable('follows', 'api');
		$filterArray = array();

		foreach ( $followers as $key => $follower ) {
		
			if( $followers->count() >= $params[ "offset" ] ){ 
			
				$filterArray[][ 'User' ] = $subj = $objUser->fetchUserDetails( $follower->follower_id );
				
				$followerUser = Engine_Api::_()->getItem('user', $subj[ 'user_id' ] );
				$filterArray[ $key ][ 'pending_approval' ] = ( int )$dbTableFollow->isForPendingApproval( $user, $followerUser );
				$is_followed = ( int )$dbTableFollow->isFollow( $user,  $followerUser ) ;

				$filterArray[ $key ][ 'is_followed' ] = $is_followed;

			}
		
		}
		
		return array(
			'data' => $filterArray,
			'error' => array()
		);
	}

	public function fetchFollowing ( $user, $subject, $params ) {
		$ret = array();

		if ( is_null( $params[ 'offset' ] ) || empty( $params[ 'user_id' ] ) ) {
			return array(
				'data' => array(),
				'error' => array( 'Missing parameters' )
			);
		}
		$suffix = $params[ 'offset' ] . '0';

		$dbTableFollow = Engine_Api::_()->getDbTable( 'follows', 'api' );
		$select = $dbTableFollow->select()
			->where( 'follower_id = '. $subject->getIdentity() )
			->where( 'pending_approval != 1' )
			->limit( 10, $suffix );

		$followedUsersDetail = $dbTableFollow->fetchAll( $select );

		$apiUser = Engine_Api::_()->getApi( 'user', 'api' );

		foreach( $followedUsersDetail as $key => $followedUserDetail ) {
			$arrfollowedUser = $apiUser->fetchUserDetails( $followedUserDetail->user_id );
			
			$followedUser = Engine_Api::_()->getItem('user', $arrfollowedUser[ 'user_id' ] );
			$isFollowed = ( int )$dbTableFollow->isFollow( $user, $followedUser ) ;

			$ret[ $key ][ 'User' ]             = $arrfollowedUser;
			$ret[ $key ][ 'is_followed' ]      = $isFollowed;
			$ret[ $key ][ 'pending_approval' ] = ( int )$dbTableFollow->isForPendingApproval( $user, $followedUser );;
		}

		if ( empty( $ret ) ) {
			return array(
				'data' => array(),
				'error' => array( 'No results found' )
			);
		}

		return array(
			'data' => $ret,
			'error' => array()
		);
	}

	// 01/20/2015 - Francis : Updated response from an object with a user object to simply an object with `message` key
	public function confirm( $user, $params ) {

		// Get viewer and other user
		$viewer = $user;
		if ( null == ( $subject = Engine_Api::_()->getItem('user', $params[ 'subject_id' ] ) ) ) {
			$status = false;
		
			$ret = array();
			$ret[ 'data' ][ 0 ][ 'message' ] = 'No member specified';
			$ret[ 'data' ][ 0 ][ 'status' ] = 0;
			$ret[ 'error' ] = array();

			return $ret;
		}

		$followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
		$followPendingApi = Engine_Api::_()->getDbtable('follows', 'api' );;

		$friendship = $followPendingApi->isForPendingApproval($viewer, $subject);
		if ( $friendship ) {
			$status = false;

			$ret = array();
			$ret[ 'data' ][ 0 ][ 'message' ] = 'Already friends';
			$ret[ 'data' ][ 0 ][ 'status' ] = 0;
			$ret[ 'error' ] = array();
			
			try{

				$notification = Engine_Api::_()->getDbtable( 'notifications', 'activity' )
				->getNotificationBySubjectAndType( $viewer, $subject, 'friend_follow_request' );
	      
				if( $notification ) {
					$notification->mitigated = 1;
					$notification->read = 1;
					$notification->save();
				}

			} catch( Exception $e ) {
				$ret = array();
				$ret[ 'data' ] = array();
				$ret[ 'error' ] = array( 'No record found' );

				return $ret;
				
			}

			return $ret;
		}

		// process
		$db = $followApi->getAdapter();
		$db->beginTransaction();

		try {
			if ( !$followPendingApi->isForPendingApproval( $viewer, $subject ) ) {
				$followObj = $followApi->Follow( $viewer, $subject );
				$notifier = Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($subject, $viewer, $subject, 'friend_follow_accepted');

				$notifier = Engine_Api::_()->getDbtable('notifications', 'activity')
				->addNotification($viewer, $subject, $viewer, 'friend_follow');
			}

			$notification = Engine_Api::_()->getDbtable( 'notifications', 'activity' )
			->getNotificationBySubjectAndType( $viewer, $subject, 'friend_follow_request' );
      
			if( $notification ) {
				$notification->mitigated = 1;
				$notification->read = 1;
				$notification->save();
			}
		
			$db->commit();
			
			$ret = array();
			$ret[ 'data' ][ 0 ][ 'message' ] = 'success';
			$ret[ 'data' ][ 0 ][ 'status' ] = 1;
			$ret[ 'error' ] = array();
		  
			return $ret;

		} catch( Exception $e ) {
			$ret = array();
			$ret[ 'data' ] = array();
			$ret[ 'error' ] = array( 'No record found' );

			return $ret;
		}

	}

	// 01/20/2015 - Francis : Updated response from an object with a user object to simply an object with `message` key
	public function ignore ( $user, $params ) {

		// Get viewer and other user
		$viewer = $user;
		if( null == ( $subject = Engine_Api::_()->getItem('user', $params[ 'subject_id' ] ) ) ) {
			$this->view->status = false;

			$ret = array();
			$ret[ 'data' ][ 0 ][ 'message' ] = 'Already friends';
			$ret[ 'data' ][ 0 ][ 'status' ] = 0;
			$ret[ 'error' ] = array();
			
			return $ret;
		}

		// Process
		$followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
		$db = $followApi->getAdapter();
		$db->beginTransaction();

		try {
			if ( $followApi->isFollow( $viewer, $subject ) ) {
				$followApi->unFollow( $viewer, $subject );
			}

			$notification = Engine_Api::_()->getDbtable( 'notifications', 'activity' )
			->getNotificationBySubjectAndType( $viewer, $subject, 'friend_follow_request' );

			if( $notification ) {
				$notification->mitigated = 1;
				$notification->read = 1;
				$notification->save();
			}
			
			$db->commit();

			/* where i send the notifications */
			$mobileSender = new Api_Library_MobileNotifier();
			
			
			/* where i send the notifications */
			
			$ret = array();
			$ret[ 'data' ][ 0 ][ 'message' ]= 'success';
			$ret[ 'data' ][ 0 ][ 'status' ] = 1;
			$ret[ 'error' ] = array();
			
			return $ret;

		} catch( Exception $e ) {
			$ret = array();
			$ret[ 'data' ] = array();
			$ret[ 'error' ] = array( 'No record found' );

			return $ret;
		}

	}
	
	public function checkFollowStatus ( $user, $params ) {
		$viewer = $user;
		if( null == ( $subject = Engine_Api::_()->getItem('user', $params[ 'subject_id' ] ) ) ) {
			$this->view->status = false;

			$ret = array();
			$ret[ 'data' ] = array();
			$ret[ 'error' ] = array( 'No member specified' );
			
			return $ret;
		}

		// check if the subject is already followed by the viewer
		$dbTableFollows = Engine_Api::_()->getDbtable( 'follows', 'api' );		
		$isFollowed = ( int ) $dbTableFollows->isFollow( $viewer, $subject );

		$pendingApproval = ( int ) $dbTableFollows->isForPendingApproval( $viewer, $subject );		

		$apiUserDbTable = Engine_Api::_()->getDbTable( 'users', 'api' );
		$obj = $apiUserDbTable->readUserDetails( $subject->getIdentity() );

		$ret = array();
		$ret[ 'error' ] = array();

		$ret[ 'data' ][ 0 ][ 'Follow' ][ 'User' ]             = $obj;
		$ret[ 'data' ][ 0 ][ 'Follow' ][ 'is_followed' ]      = $isFollowed;
		$ret[ 'data' ][ 0 ][ 'Follow' ][ 'pending_approval' ] = $pendingApproval;


		return $ret;
	}

}