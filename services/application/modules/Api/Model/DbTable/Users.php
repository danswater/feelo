<?php
class Api_Model_DbTable_Users extends Engine_Db_Table {

   protected $_name = 'users';

   protected $_rowClass = 'User_Model_User';

	public function readUserDetails ( $user_id ) {
		$storagePath = 'public/user/nophoto_user_thumb_icon.png';

		$queryInfo = $this->fetchRow( array( 'user_id = ?' => $user_id ) );

		if ( !$queryInfo ) {
			throw new Exception( 'No results found' );
		}

		$storageDbTable = Engine_Api::_()->getDbTable( 'files', 'storage' );
		$selectStoragePath = $storageDbTable->select();
		$selectStoragePath->where( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $queryInfo->photo_id, $queryInfo->user_id );
		$queryStoragePath  = $storageDbTable->fetchRow( $selectStoragePath );	
		
		if ( $queryStoragePath->storage_path  ) {
			$storagePath = $queryStoragePath->storage_path;
		}
		
		$userModelUser = Engine_Api::_()->user()->getUser( $queryInfo->user_id );
		$followApi = Engine_Api::_()->getApi( 'follow', 'api' );
		$following = $followApi->countIFollow( $userModelUser );
		$followers = $followApi->countMyFollowers( $userModelUser );
		
		$projectApi = Engine_Api::_()->getApi( 'project', 'api' );
		$posts = $projectApi->countAllPosts( $userModelUser );
		
		$obj = new Api_Model_UserExt();
		$obj->setUserId( $queryInfo->user_id );
		$obj->setDisplayname( $queryInfo->displayname );
		$obj->setUsername( $queryInfo->username );
		$obj->setPhotoId( $queryInfo->photo_id );
		$obj->setStatus( $queryInfo->status );
		$obj->setStatusDate( $queryInfo->status_date );
		$obj->setEmail( $queryInfo->email );
		$obj->setLocale( $queryInfo->locale );
		$obj->setLanguage( $queryInfo->language );
		
		$obj->setStoragePath( $storagePath );
		$obj->setFollowing( $following );
		$obj->setFollowers( $followers );
		$obj->setPosts( $posts );
			
		return $obj;
	}
	
	public function create ( $obj ) {
		$user = Engine_Api::_()->getDbtable('users', 'user')->createRow();
		$user->email = $obj->getEmail();
		$user->username = $obj->getUsername();
		$user->password = $obj->getPassword();
		$user->locale = $obj->getLocale();
		$user->language = $obj->getLanguage();
		$user->timezone = $obj->getTimezone();
		
		print_r( $user ); exit;
	}
	
	public function authenticateUsingFacebook ( $params ) {
		// check if this account is already save in the database
		$select = $this->select();
		$select->where( 'email = ?',  $params[ 'email' ] );
		$user = $this->fetchRow( $select );

		// if naa then do the normal login process
		if ( $user ) {
			$table = Engine_Api::_()->getDbTable('auth', 'api');

			$select = $table->select();
			$select->where('user_id = ?', $user->getIdentity());

			$existingToken = $table->fetchRow($select);

			$response[ "success" ] = true;
		
			if ($existingToken) {
				if ($existingToken->expire_date > time()){
					$response[ "data" ] = array(
						"token" => $existingToken->token,
						"expire_date" => $existingToken->expire_date,
						"user" => $auth
					);
					return $response;
				}else
					$existingToken->delete();
			}
	
			$token = $this->makeHash($user->getIdentity(), $user->email, $user->password);
			//1 month
			$expire_date = time() + 2592000;

			$row = $table->createRow();
			$row->user_id = $user->getIdentity();
			$row->token = $token;
			$row->expire_date = $expire_date;
			$row->save();
			
			$response[ "data" ] = array(
				"token" => $token,
				"expire_date" => $expire_date,
				"user" => $auth
			);

			return $response;
		}
	}
	
	static public function makeHash($user_id, $email, $password) {
        return sha1($user_id . $email . $password . time());
    }
	
	public function readUserByEmail ( $email ) {
		$select = $this->select();
		$select->where( 'email = ?', $email );
		
		$ret = $this->fetchRow( $select );
		
		return $ret;
	}

	public function readUserByUsername ( $username ) {
		$select = $this->select();
		$select->where( 'username LIKE ?', $username );

		$ret = $this->fetchRow( $select );

		return $ret;
	}
	
}