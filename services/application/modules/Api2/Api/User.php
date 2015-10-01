<?php
class Api2_Api_User extends Core_Api_Abstract {

	public function fetchByUsername ( $user, $params ) {

		$dbTableUsers = Engine_Api::_()->getDbTable( 'users', 'api2' );
	
		$user = $dbTableUsers->readByUsername( $params[ 'username' ] );
		$user = Engine_Api::_ ()->user ()->getUser ( $user->user_id );

		// fetch storage path
		$api2StoragePath = Engine_Api::_()->getApi( 'storage', 'api2' );
		$storagePath     = $api2StoragePath->fetchStorageByPhotoIdAndUserId( $user->photo_id, $user->user_id );

		$api2Follow = Engine_Api::_()->getApi( 'follow', 'api' );
		$following  = $api2Follow->countIFollow( $user );
		$followers  = $api2Follow->countMyFollowers( $user );
	
		$apiProject = Engine_Api::_()->getApi( 'project', 'api' );
		$posts      = $apiProject->countAllPosts( $user );

		$dbTableHashtags = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );
		$hashtags        = $dbTableHashtags->readFollowedHashtags( $user, array(
			'user_id'     => $user->getIdentity(),
			'silentError' => true
		) );
		$hashtagPostsCount = count( $hashtags );

		$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
		$likePosts       = $dbTableProjects->readLikesByUserId( $user, array(
			'user_id' => $user->getIdentity()
		) );
		$likePostsCount = count( $likePosts );

		// get favo length
		$dbTableFavo = Engine_Api::_()->getDbTable( 'favos', 'api2' );
		$favos       = $dbTableFavo->readUserFavos( $user, array(
			'user_id'     => $user->getIdentity(),
			'silentError' => true
		) );
		$favoPostsCount = count( $favos );

		$ret = new Api2_Model_User();
		$ret->setUserId( $user->getIdentity() );
		$ret->setDisplayname( $user->displayname );
		$ret->setUsername( $user->username );
		$ret->setPhotoId( $user->photo_id );
		$ret->setStatus( $user->status );
		$ret->setStatusDate( $user->status_date );
		$ret->setEmail( $user->email );
		$ret->setLocale( $user->locale );
		$ret->setLanguage( $user->language );
		$ret->setStoragePath( $storagePath );
		$ret->setFollowing( $following );
		$ret->setFollowers( $followers );
		$ret->setPosts( $posts );
		$ret->setHashtags( $hashtagPostsCount );
		$ret->setLikes( $likePostsCount );
		$ret->setFavos( $favoPostsCount );

		return array(
			'User' => $ret
		);
	}


	public function forgotPassword( $params ) {
		$result = array( "data" => array(), "error" => array() );


		$user = Engine_Api::_()->getDbtable('users', 'user')
	      ->fetchRow( array('email = ?' => $params[ "email"] ) );

	    if( !$user || !$user->getIdentity() ) {
	    	$result[ "error" ] = 'A user account with that email was not found.';
	      	return $result;
	    }

	    if( !$user->enabled ) {
	     	$result[ "error" ] = 'That user account has not yet been verified or disabled by an admin.';
	      	return $result;
	    }


	     $forgotTable = Engine_Api::_()->getDbtable('forgot', 'user');
	    $db = $forgotTable->getAdapter();
	    $db->beginTransaction();

	    try
	    {
	      // Delete any existing reset password codes
	      $forgotTable->delete(array(
	        'user_id = ?' => $user->getIdentity(),
	      ));

	      // Create a new reset password code
	      $code = base_convert(md5($user->salt . $user->email . $user->user_id . uniqid(time(), true)), 16, 36);
	      $forgotTable->insert(array(
	        'user_id' => $user->getIdentity(),
	        'code' => $code,
	        'creation_date' => date('Y-m-d H:i:s'),
	      ));

	      // Send user an email
	      Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'core_lostpassword', array(
	        'host' => $_SERVER['HTTP_HOST'],
	        'email' => $user->email,
	        'date' => time(),
	        'recipient_title' => $user->getTitle(),
	        'recipient_link' => $user->getHref(),
	        'recipient_photo' => $user->getPhotoUrl('thumb.icon'),
	        'object_link' => '/reset-password/' . $code . "/" . $user->getIdentity(), 
	        'queue' => false,
	      ));

	      // show success
	      $result[ "data" ] = array("email" => $user->email );
	      $db->commit();
	    }

	    catch( Exception $e )
	    {
	      	$db->rollBack();
	    	$result[ "error" ] = $e->getMessage();
	    }

	    return $result;

	}


	public function resetPassword( $params ){
		$result = array( "data" => array(), "error" => array(), "redirect" => false );

		if( empty( $params[ "uid" ] ) || empty( $params[ "code" ] ) ) {
			$result[ "error" ] = 'Invalid code and user.';
			$result[ "redirect" ] = true;
	      	return $result;
	    }

	    $user = Engine_Api::_()->getItem('user', $params[ "uid" ]);
	    if( !$user || !$user->getIdentity() ) {
	      	$result[ "error" ] = 'No user found.';
			$result[ "redirect" ] = true;
			return $result;
	    }

	    $forgotTable = Engine_Api::_()->getDbtable('forgot', 'user');
	    $forgotSelect = $forgotTable->select()
	      ->where('user_id = ?', $user->getIdentity())
	      ->where('code = ?', $params[ "code" ]);
	      
	    $forgotRow = $forgotTable->fetchRow($forgotSelect);
	    if( !$forgotRow || (int) $forgotRow->user_id !== (int) $user->getIdentity() ) {
	      	$result[ "error" ] = 'Invalid code.';
			$result[ "redirect" ] = true;
			return $result;
	    }

	    $min_creation_date = time() - (3600 * 24);
	    if( strtotime($forgotRow->creation_date) < $min_creation_date ) { // @todo The strtotime might not work exactly right
	    	$result[ "error" ] = 'Code is already expired.';
			$result[ "redirect" ] = true;
			return $result;
	    }
	    
	    if( $params['password'] !== $params['password_confirm'] ) {
	    	$result[ "error" ] = 'The passwords you entered did not match.';
			return $result;
	    }
	    
	    $db = $user->getTable()->getAdapter();
    	$db->beginTransaction();
	    try
	    {
	      // Delete the lost password code now
	      	$forgotTable->delete(array(
	        	'user_id = ?' => $user->getIdentity(),
	      	));
	      
	      	// This gets handled by the post-update hook
	      	$user->password = $params['password'];
	      	$user->save();
	      
	      	$db->commit();
	      	$result[ "data" ] = array( 
	      		"user_id" => $user->getIdentity(),
	      	);
	    } catch( Exception $e ) {
	      	$db->rollBack();
	      	$result[ "error" ] = $e->getMessage();
	    }


		return $result; 
	}
 
}
