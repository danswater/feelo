<?php
class Api_Api_User extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	private $data = array();

	public function __set( $key, $value ) {
		$this->data[ $key ]= $value;
	}

	public function __get( $key ) {

		if( array_key_exists( $key, $this->data ) ) {
			return $this->data[ $key ];
		}

		return null;

	}

	public function fetchAllByKeyword( $keyword, $offset, $limit = 10 ) {
        /*
		$objTable = Engine_Api::_()->getItemTable( 'user' );
        $objDb = $objTable->getAdapter();

        $suffix = $offset ."0";
        $where = '(displayname LIKE ?)';
        $values = array( '%'. $keyword .'%' );

        $objSelect = $objTable->select()
		  ->from( array( 'u' => 'engine4_users'), array( 'user_id' ) )
          ->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) )
          ->order( array( 'username DESC') )
          ->limit( 10, $suffix );

        $objResultSet = $objDb->fetchAll( $objSelect );

        foreach( $objResultSet as $key => $value ) {
			$objUser = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser[] = $objUser->fetchUserDetails( $value[ 'user_id' ] );
        }

		if( empty( $objResultSet ) ){
			return array();
		}
        return $resultSetUser;
		*/

		$usersTable = Engine_Api::_ ()->getDbTable ( 'users', 'user' );
		$usersTableName = $usersTable->info ( 'name' );
		$storageTable = Engine_Api::_()->getDbTable('files', 'storage');
		$storageTableName = $storageTable->info('name');
        $suffix = $offset ."0";
		
		$select_users = $usersTable->select ();
		$select_users->from ( $usersTableName )
			->where ( "`displayname` LIKE ?", '%'.$keyword .'%' )
			->limit( $limit, $suffix );

		$usersRows = $usersTable->fetchAll ( $select_users );



		foreach( $usersRows as $key => $value ) {
			$results[][ 'User' ] = $this->fetchUserDetails( $value[ 'user_id' ] );
		}
		
		if( is_null( $results ) ) {
			return array();
		}
		return $results;

	}
	
	public function fetchUserDetails( $userId ) {
		$usersTable = Engine_Api::_ ()->getDbTable ( 'users', 'user' );

		$usersTableName = $usersTable->info ( 'name' );
		$storageTable = Engine_Api::_()->getDbTable('files', 'storage');
		$storageTableName = $storageTable->info('name');

		$select_users = $usersTable->select ();
		$select_users->from ( $usersTableName )->where ( "`user_id` = ?", $userId );

		$usersRows = $usersTable->fetchAll ( $select_users );
		for($c = 0; $c < count ( $usersRows ); $c++) {
			$friendsList ['user_id']     = $usersRows [$c]->user_id;
			$friendsList ['displayname'] = $usersRows [$c]->displayname;
			$friendsList ['username']    = $usersRows [$c]->username;
			$friendsList ['photo_id']    = $usersRows [$c]->photo_id;
			$friendsList ['status']      = $usersRows [$c]->status;
			$friendsList ['status_date'] = $usersRows [$c]->status_date;
			$friendsList ['email']       = $usersRows [$c]->email;
			$friendsList ['locale']      = $usersRows [$c]->locale;
			$friendsList ['language']    = $usersRows [$c]->language;
			$friendsList ['timezone']    = $usersRows [$c]->timezone;
			
			$table = Engine_Api::_ ()->getDbTable ( 'files', 'storage' );
			$select = $table->select ();
			$select->where ( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $usersRows [$c]->photo_id, $usersRows [$c]->user_id );
			$fetchData = $table->fetchRow ( $select );
			$friendsList ['storage_path'] = $fetchData->storage_path;
			
			if( is_null( $fetchData->storage_path ) ) {
				$friendsList[ 'storage_path' ] = 'public/user/nophoto_user_thumb_icon.png';
			}
			
			$user = Engine_Api::_ ()->user ()->getUser ( $usersRows [$c]->user_id );
			
			$objTable = Engine_Api::_()->getApi( 'follow', 'api' );
			$friendsList [ 'following' ] = $objTable->countIFollow( $user );
			$friendsList [ 'followers' ] = $objTable->countMyFollowers( $user );
		
			$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
			$friendsList[ 'posts' ]  = $projectFeed->countAllPosts( $user );

			$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
			$feed = $dbTableProjects->getRandomFeed( $userId );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $feed->project_id, $feed->cover_file_id );

			$friendsList[ 'cover_photo' ] = $mediaRow->storage_path;
		}
		
		$this->isArrValNull( $friendsList );

		return $friendsList;
	}
	
	public function fetchUserDetails2( $viewer, $subject ) {

		$usersTable = Engine_Api::_ ()->getDbTable ( 'users', 'user' );
		$userId = $subject->getIdentity();

		$usersTableName = $usersTable->info ( 'name' );
		$storageTable = Engine_Api::_()->getDbTable('files', 'storage');
		$storageTableName = $storageTable->info('name');

		$select_users = $usersTable->select ();
		$select_users->from ( $usersTableName )->where ( "`user_id` = ?", $userId );

		$usersRows = $usersTable->fetchAll ( $select_users );
		$friendship = array();
		for($c = 0; $c < count ( $usersRows ); $c++) {
			$friendsList ['user_id']     = $usersRows [$c]->user_id;
			$friendsList ['displayname'] = $usersRows [$c]->displayname;
			$friendsList ['username']    = $usersRows [$c]->username;
			$friendsList ['photo_id']    = $usersRows [$c]->photo_id;
			$friendsList ['status']      = $usersRows [$c]->status;
			$friendsList ['status_date'] = $usersRows [$c]->status_date;
			$friendsList ['email']       = $usersRows [$c]->email;
			$friendsList ['locale']      = $usersRows [$c]->locale;
			$friendsList ['language']    = $usersRows [$c]->language;
			$friendsList ['timezone']    = $usersRows [$c]->timezone;
			
			$table = Engine_Api::_ ()->getDbTable ( 'files', 'storage' );
			$select = $table->select ();
			$select->where ( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $usersRows [$c]->photo_id, $usersRows [$c]->user_id );
			$fetchData = $table->fetchRow ( $select );
			$friendsList ['storage_path'] = $fetchData->storage_path;
			
			if( is_null( $fetchData->storage_path ) ) {
				$friendsList[ 'storage_path' ] = 'public/user/nophoto_user_thumb_icon.png';
			}
			
			$user = Engine_Api::_ ()->user ()->getUser ( $usersRows [$c]->user_id );
			
			$objTable = Engine_Api::_()->getApi( 'follow', 'api' );
			$friendsList [ 'following' ] = $objTable->countIFollow( $user );
			$friendsList [ 'followers' ] = $objTable->countMyFollowers( $user );
		
			$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
			$friendsList[ 'posts' ]  = $projectFeed->countAllPosts( $user );

			$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
			$feed = $dbTableProjects->getRandomFeed( $userId );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $feed->project_id, $feed->cover_file_id );

			$friendsList[ 'cover_photo' ] = $mediaRow->storage_path;
			
			// check if the subject is already followed by the viewer
			$dbTableFollows = Engine_Api::_()->getDbtable( 'follows', 'api' );
			$isFollowed = ( int ) $dbTableFollows->isFollow( $viewer, $subject );

			$pendingApproval = ( int ) $dbTableFollows->isForPendingApproval( $viewer, $subject );
			
			$friendship[ 'is_followed' ]      = $isFollowed;
			$friendship[ 'pending_approval' ] = $pendingApproval;
		}
		
		$this->isArrValNull( $friendsList );

		return array(
			'User'             => $friendsList,
			'is_followed'      => $friendship[ 'is_followed' ],
			'pending_approval' => $friendship[ 'pending_approval' ]
		);
	}
	
	public function identify ( $params ) {
		if ( is_null( $params[ 'user_id' ] ) || empty( $params[ 'user_id' ] ) ) {
			$token = $params [ 'token' ];

			$authTable = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
			$user = $authTable->fetchRow( array( 'token = ?' => $token ) );

			if ( !$user ) {
				throw new Exception( 'Api_Api_User: Invalid token' );
			}
		  
        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');
			
			$authUser = Engine_Api::_()->user()->getUser( $user->user_id );			

			Engine_Api::_()->user()->setViewer( $authUser );   

			return $authUser;
		} else {
			$user = $params[ 'user_id' ];
			$authUser = Engine_Api::_()->user()->getUser( $user );

			Engine_Api::_()->user()->setViewer( $authUser );

			return $authUser;
		}
	}

	public function getUserDetails ( $params ) {
		try {
			$ApiUserDbTable = Engine_Api::_()->getDbTable( 'users', 'api' );
			$obj = $ApiUserDbTable->readUserDetails( $params[ 'user_id' ] );		
	
			$response = new Api_Model_ResponseExt();	
			$response->setData( array( 'User' => $obj ) );
			
			return $response;
			
			$ret = array();
			$ret[ 'data'][ 'User' ] = $obj;
			$ret[ 'error' ] = array();
			
			return $ret;
		} catch ( Exception $e ) {
			$ret = array();
			$ret[ 'data' ] = array();
			$ret[ 'error' ][] = $e->getMessage();
			
			return $ret;
		}
	}
	
	public function createNewUser ( $params ) {
		
		// we are going to populate the lacking fields
		$emailFragment = explode( '@', $params[ 'email' ] );
		$params[ 'username' ] = $emailFragment[ 0 ];
	
		// lets check if the username we provide
		// is haven't been used.
		// if used lets append and integer
		$userTable = Engine_Api::_()->getDbtable('users', 'user');
		$user = $userTable->fetchRow($userTable->select()->where('username = ?', $params[ 'username' ] ) );

		// if username is already been taken then lets concat username + the existing user_id to make it unique
		if( $user->username ) {
			$params[ 'username' ] = $params[ 'username' ] .'.'. $emailFragment[ 1 ];
		}
		
		
		$user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
		$user->email = $params[ 'email' ];
		$user->username = $params[ 'username' ];
		$user->displayname = $params[ 'username' ];
		$user->password = $params[ 'password' ];
		$user->locale = 'en' ;
		$user->language = 'en';
		$user->timezone = $params[ 'timezone' ];		

		try {
			$user->save();
		} catch ( Exception $e ) {
			print_r( $e ); exit;
		}
		
		$ret = new Api_Model_UserExt();
		$ret->setUserId( $user->user_id );
		$ret->setDisplayname( $user->displayname );
		$ret->setUsername( $user->username );
		$ret->setPhotoId( $user->photo_id );
		$ret->setStatus( $user->status );
		$ret->setStatusDate( $user->status_date );
		$ret->setEmail( $user->email );
		$ret->setLocale( $user->locale );
		$ret->setLanguage( $user->language );
		
		$ret->setStoragePath( $user );
		$ret->setFollowing( $user );
		$ret->setFollowers( $user );
		$ret->setPosts( $user );
		
		// auto login
		
		$apiAuth = Engine_Api::_()->getDbTable('auth', 'api');
		
		$response = $apiAuth->authenticate( $user->email, $user->password );
					
		$token = $response[ 'data' ][ 'token' ];
		$expire_date = $response[ 'data' ][ "expire_date" ];		
		
		return array(
			'data' => array( 
				'token' => $token,
				'expire_date' => $expire_date,
				'User' => $response 
			),
			'error' => array()
		);
	}
	
	public function sendNotificationForExpiredSession ( $user ) {
	
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$activeUsers = $table->fetchAll();
		
		$arrayOfUsers = array();
		foreach( $activeUsers as $user ) {
			$arrayOfUsers[] = Engine_Api::_ ()->user ()->getUser ( $user->user_id );
		}	
	
		$apiModelDbTableAuth = Engine_Api::_()->getDbTable( 'auth', 'api' );
		$apiModelDbTablePushNotification = Engine_Api::_()->getDbTable( 'pushNotifications', 'api' );
		
		$ret = array();
		$i = 0;
		foreach( $arrayOfUsers as $user ) {
			$sessionExpirationDate = $apiModelDbTableAuth->readExpiryDateById( $user->getIdentity() );
			
			$message = "";
			if ( $sessionExpirationDate ) {
				if ( $existingToken->expire_date < time() ) {
					// print_r( $user->getIdentity() ); exit;
					 $userDevices[] = $apiModelDbTablePushNotification->fetchRow( 'user_id = ?', $user->getIdentity() );
					
					$message = "Your session has expired. Please login to renew your access.";
					$ret[ $i ] = $user->getIdentity();
					$ret[ $i ][ 'message' ] = $message;
					$ret++;
				}
			}
		}
		print_r( $ret ); exit;
		// Activity_Model_DbTable_Notifications
        // $activityModelDbTableNotifications = Engine_Api::_()->getDbtable('notifications', 'activity');		
        // $activityModelDbTableNotifications->addNotification($user, $viewer, $subject, "sessionExpired", array( 'message' => $message ) );
	}
	
	public function authenticateUsingFacebook ( $params ) {
		$ret = array();
		
		$apiModelDbTableUser = Engine_Api::_()->getDbTable( 'users', 'api' );
		$apiModelDbTableAuth = Engine_Api::_()->getDbTable( 'auth', 'api' );
		
		// check if this account is already save in the database
		$hasAlreadySaved = $user = $apiModelDbTableUser->readUserByEmail( $params[ 'email' ] );
		
		// if naa then do the normal login process
		if ( $hasAlreadySaved ) {
		
			// get the auth record of this particular user
			$tokenExist = $auth = $apiModelDbTableAuth->readAuthByUserId( $user->getIdentity() );
			
			// check if token is exist and also check if the auth is not yet expired
			// if not expired return it to user
			// if expired, delete  the auth record
			// TODO @ lets enchance this process, better design a new auth process
			if ( $tokenExist ) {
				if ( $auth->expire_date > time() ) {
					$ret[ 'token' ] = $auth->token;
					$ret[ 'expire_date' ] = $auth->expire_date;
					$ret[ 'User' ] = $apiModelDbTableUser->readUserDetails( $user->getIdentity() );
					
					return $ret;
				} else {
					$auth->delete();
				}
			}
			
			// if not exists or already expired then we will create new token
			$auth = $apiModelDbTableAuth->createAuth( $user );
			
			$ret[ 'token' ] = $auth->token;
			$ret[ 'expire_date' ] = $auth->expire_date;
			$ret[ 'User' ] = $apiModelDbTableUser->readUserDetails( $user->getIdentity() );
			
			return $ret;
		}

		// if user is wala pa na store then register them

		// Add email and code to invite session if available
		$inviteSession = new Zend_Session_Namespace('invite');
		if( isset($params['email']) ) {
		  $inviteSession->signup_email = $params['email'];
		}
		if( isset($params['code']) ) {
		  $inviteSession->signup_code = $params['code'];
		}
		
		if (isset($params['language'])) {
		  $params['locale'] = $params['language'];
		}
		
		if ( isset( $params[ 'timezone' ] ) ) {
			$userLibraryDateTimezone = new User_Library_DateTimezone( User_Library_DateTimezone::tzOffsetToName( $params[ 'timezone' ] ) ); 
			$params[ 'timezone' ] = $userLibraryDateTimezone->getName();
		}

		// some facebook user don't login using their email
		// probably using their mobile number
		// so we set temporarily their email
		if ( !isset( $params[ 'email' ] ) ) {
			$params[ 'email' ] = strtolower( $params[ 'first_name' ] ) . strtolower( $params[ 'last_name' ] ) .'@facebook.com';
		}

		// lets create an initial name for this user base on facebook
		$params[ 'username' ] = $params[ 'id' ];
		$params[ 'displayname' ] = strtolower( $params[ 'first_name' ] ) . strtolower( $params[ 'last_name' ] );	

		// Create user
		// Note: you must assign this to the registry before calling save or it
		// will not be available to the plugin in the hook
		$user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
		$user->setFromArray($params);
		try {
			$user->save();
		} catch ( Exception $e ) {
			$ret[ 'data' ] = array();
			$ret[ 'error' ] = array( $e->getMessage() );
		}
		
		$user->verified = 1;
		$user->save();	

		// Increment signup counter
		Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.creations');

		if( $user->verified && $user->enabled ) {
		  // Set user as logged in if not have to verify email
		  Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
		}

		$auth = $apiModelDbTableAuth->createAuth( $user );
		
		$ret[ 'token' ] = $auth->token;
		$ret[ 'expire_date' ] = $auth->expire_date;
		$ret[ 'User' ] = $apiModelDbTableUser->readUserDetails( $user->getIdentity() );
			
		return $ret;
	}
	

}