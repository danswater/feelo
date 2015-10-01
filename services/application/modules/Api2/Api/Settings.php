<?php

class Api2_Api_Settings extends Core_Api_Abstract {


	/**
	 * update individual because of the new ui that being implemented
	 * email
	 * username
	 * displayname
	 * password
	 * timezone
	 * locale
	 */
	public function changeGeneralInformation( $params, $user ){

		$response = array( "errors" => array(), 
							"message" => "", 
							"data" => array(),
							"success" => false );
		$fields = array( 
				'status' => array(
						'required' => 'Description is required.'
					),
				"email" => array(
						"required" => "Email is required."
					), 
				"username" => array(
						"required" => "Username is required."
					), 
				"displayname" => array(
						"required" => "Full name is required."
					), 
				"password" => array(
						"required" => "Password is required."
					), 
				"timezone" => array(
						"required" => "Timezone is required."
					), 
				"locale" => array(
						"required" => "Local is required."
					) 
				);

		foreach( $fields as $field => $option ){
			$paramField = $params[ $field ];

			if( isset( $paramField ) ){

				if( $paramField == "" ){
					$response[ "errors" ][] = $option[ "required" ];
				}
				else{

					$table = Engine_Api::_()->getDbtable('users', 'user');
					$settings = Engine_Api::_()->getApi('settings', 'core');

					if( $field == "email" ){ // special trapping

						if( $user->email != $paramField ){ 
							$emailSelect = $table->select( )
							  ->where('email = ?', $paramField );
							$emailExist = $table->fetchRow( $emailSelect );
							
							if( $emailExist != null ) {
								$response[ "errors" ] = "Email is already exist";
							}
						}

					}

					if( $field == "username" ){

						if( $user->username != $paramField ){ 
							$emailSelect = $table->select( )
							  ->where('username = ?', $paramField );
							$usernameExist = $table->fetchRow( $emailSelect );

							if( $usernameExist != null ) {
								$response[ "errors" ] = "Username is already exist";
							}
						}

					}

					if( $field == "password" ){

						$hashPassword = md5( $settings->getSetting('core.secret', 'staticSalt')
			                          . $paramField
			                          . $user->salt );
						if( $hashPassword == $user->password ){
							if( $params[ "new_password" ] != $params[ "confirm_password"] && $params[ "new_password" ] != "" || 
								!isset( $params[ "new_password" ] ) ){
								$response[ "errors" ] = "New password and confirm password is not match.";	
							}
							else{
								$paramField = $params[ "new_password" ];
							}
						}
						else{
							$response[ "errors" ] = "Password is incorrect.";
						}

					}

					if( count( $response[ "errors" ] ) == 0 ){

						if( $user->$field != $paramField ){ 
							$user->$field = $paramField;
							$user->save();
						}
						$response[ "success" ] = true;
						$response[ "message" ] = "Successfully updated.";
					}

				}

			}

		}

		return $response;

	}


	public function uploadPrimaryPic( $params, $user ){

		$result = array( "error" => array(), "data" => array() );


		$file = null;
		if( isset( $_FILE[ "Filedata" ] ) ){
			$file = $_FILE[ "Filedata" ];
		}
		else if( isset( $params[ "Filedata" ] ) ){
			$file = $this->formatToFiles( $params[ "Filedata" ] );
			if( $file == false ){
				$result[ "error" ] = "Invalid file type.";
			}
			else{
				$file = $file[ "Filedata" ];
			}

		}
		else{
			$result[ "error" ] = "Upload file not found.";
		}

		if( count( $result[ "error" ] ) == 0 ){

			$db = $user->getTable()->getAdapter();
      		$db->beginTransaction();

      		try {

      			$user->setPhoto( $file );
        
       			$iMain = Engine_Api::_()->getItem('storage_file', $user->photo_id);

       			$action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $user, 'profile_photo_update', '{item:$subject} added a new profile photo.');

       			$db->commit();

       			$result[ "data" ] = array(
       				"storage_path" => $iMain->storage_path
       			);
      		}
      		catch( Engine_Image_Adapter_Exception $e ) {
		    	$db->rollBack();
		        $result[ "error" ] = 'The uploaded file is not supported or is corrupt.';
		    }
		   	catch( Exception $e ) {
		    	$db->rollBack();
		        $result[ "error" ] = $e->getMessage();
		    }

		}

		return $result;
	}


	public function formatToFiles( $filedata ) {
		$files = array();
		//$tmpLocation = 'C:/wamp/tmp/php'. time();
		$tmpLocation = '/tmp/php'. time();
		$base =  $this->getB64Type( $filedata ); 

		if( count( $base ) == 0 ){
			return false;
		}

		$location = $tmpLocation;
		$files[ 'Filedata' ][ 'name' ] = $base[ 'filename' ].$base[ 'ext' ];		
		$files[ 'Filedata' ][ 'type' ] = $base[ 'type' ];
		$files[ 'Filedata' ][ 'tmp_name' ] = $this->base64_to_jpeg( $filedata, $tmpLocation );
		$files[ 'Filedata' ][ 'error' ] = 0;
		$files[ 'Filedata' ][ 'size' ] = 0;
	
		return $files;
	}

	public function generateRandomString($length = 10) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	public function getB64Type($str) {
	    $meta = substr($str, 5, strpos($str, ';')-5);
	    $filename = $this->generateRandomString( 5 );

	    switch( $meta ) {

	    	case 'image/jpg':
	    	case 'image/jpeg':
	    		return array( 
	    			"filename" => $filename,
	    			"ext" => ".jpg",
	    			"type" => "image/jpeg"
	    		);
	    	break;

	    	case 'image/png':
				return array( 
	    			"filename" => $filename,
	    			"ext" => ".png",
	    			"type" => "image/png"
	    		);
	    	break;

	    	default:
	    		return array();
	    	break;
	    }
	}
	
	public function base64_to_jpeg( $base64_string, $output_file ) {
		$ifp = fopen( $output_file, "wb" ); 
		$data = explode(',', $base64_string);
		fwrite( $ifp, base64_decode( $data[ 1 ] ) ); 
		fclose( $ifp ); 
		return( $output_file ); 
	} 


	// update privacy
	public function updatePrivacy( $params, $user ) {
		$ret = $user->setFromArray( array( "privacy" => $params[ "privacy" ] ) )->save();
		$result = array( "error" => array(), "data" => array() );

		if( $ret == 1 ){
			$result[ "data" ][ "User" ] = array(
				"access_right" => $params[ "privacy" ]
			);
		}
		else{
			$result[ "data" ][ "error" ] = "Unable to update the privacy.";
		}

		return $result;
	}


	public function getNotificationByType( $typeDb, $params, $user ){

		$result = array( "error" => array(), "data" => array() );

		$modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
	    $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
	    $notificationSettings = Engine_Api::_()->getDbtable( $typeDb, 'activity')->getEnabledNotifications($user);

	    $notificationTypesAssoc = array();
	    $notificationSettingsAssoc = array();



	    foreach( $notificationTypes as $type ) {

	      if( in_array( $type->type, array( "commented_commented", "message_new", "post_user", "whmedia_following" ) ) ) 
	        continue;
	      if( in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user' /* to make one */,'whmedia' )) ) {
	        $elementName = 'general';
	        $category = 'General';
	      } else if( isset($modules[$type->module]) ) {
	        echo $type->module;
	        $elementName = preg_replace('/[^a-zA-Z0-9]+/', '-', $type->module);
	        $category = $modules[$type->module]->title;
	      } else {
	        $elementName = 'misc';
	        $category = 'Misc';
	      }

	      $notificationTypesAssoc[$elementName]['category'] = $category;
	      $notificationTypesAssoc[$elementName]['types'][$type->type] = 'ACTIVITY_TYPE_' . strtoupper($type->type);

	      if( in_array($type->type, $notificationSettings) ) {
	        $notificationSettingsAssoc[$elementName][] = $type->type;
	      }
	    }
		
		if ( isset( $params[ 'is_mobile' ] ) ) {
			$result[ 'data' ][ 'InApp' ] = $this->toResponseObject( $notificationSettingsAssoc[ 'general' ] );
		} else {
			$result[ "data" ] = $notificationSettingsAssoc;
		}
	    return $result;
	}

	public function updateNotificationByType( $typeDb, $params, $user ){

		$result = array( "error" => array(), "data" => array() );
		
		if ( isset( $params[ 'is_mobile' ] ) ) {
			$activeNotifications = $this->fromRequestObject( json_decode( $params[ 'notifications' ] )  );
		} else {
			$activeNotifications = json_decode( $params[ "notifications" ] );
		}
		
		if( is_array( $activeNotifications ) ){
			Engine_Api::_()->getDbtable( $typeDb, 'activity')
        	->setEnabledNotifications($user, $activeNotifications);
		}
		else{
			$result[ "error" ] = "Invalid parameter";
		}

		if ( isset( $params[ 'is_mobile' ] ) ) {		
			$result[ 'data' ][ 'InApp' ] = $this->toResponseObject( $activeNotifications );
		} else {
			$result[ "data" ] = $activeNotifications;
		}
		return $result;
	}

	public function deleteUser( $params, $user ){

		$result = array( "error" => array(), "data" => array() );

		$db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
	    $db->beginTransaction();
	    try {
	      	$user->delete();
	      	$db->commit();
	      	$result[ 'message' ] = 'Successfully deactivated.';
	    } catch( Exception $e ) {
	      	$db->rollBack();
	      	$result[ "error" ] = $e->getMessage();
	    }

	    return $result;

	}
	
	public function fromRequestObject ( $notifications ) {
		$ret = array();		
		foreach( $notifications as $key => $value ) {
			if ( $value != 0 ) {
				$ret[] = $key;
			}
		}
		return $ret;
	}
	
	public function toResponseObject ( $notification ) {
		$types = array(
			'commented' => 0,
			'followed_favo' => 0,
			'follower_favo' => 0,
			'friend_follow' => 0,
			'friend_follow_accepted' => 0,
			'friend_follow_request' => 0,
			'liked' => 0,
			'liked_commented' => 0,
			'tagged' => 0,
			'whmedia_processed_failed' => 0
		);

		if ( empty( $notification ) ) {
			return $types;
		}
		
		$ret = array();
		foreach( $types as $key => $value ) {
			$ret[ $key ] = ( int )in_array( $key, $notification );
		}
		
		return $ret;
	}

	public function updateGeneralInfo ( $user, $params ) {

		$fields = array( 
				"email" => array(
						"required" => "Email is required."
					), 
				"username" => array(
						"required" => "Username is required."
					), 
				"displayname" => array(
						"required" => "Full name is required."
					),
				"timezone" => array(
						"required" => "Timezone is required."
					)
				);



		foreach( $fields as $field => $option ){
			$paramField = $params[ $field ];

			if( empty( $paramField ) ){
				throw new Exception( $option[ 'required' ] );
			}

		}

		$userDbTable     = Engine_Api::_()->getDbtable( 'users', 'user' );
		$settingsDbTable = Engine_Api::_()->getApi( 'settings', 'core' );

		// email
		if( $user->email != $params[ 'email' ] ) { 
			$select = $userDbTable->select( );
			$select->where( 'email = ?', $params[ 'email' ] );

			$emailExist = $userDbTable->fetchRow( $select );
					
			if( !is_null( $emailExist ) ) {
				throw new Exception( 'Email is already exist' );
			}
		}

		// username
		if( $user->username != $params[ 'username' ] ){ 
			$select = $userDbTable->select( );
			$select->where('username = ?', $params[ 'username' ] );
			
			$usernameExist = $userDbTable->fetchRow( $select );

			if( !is_null( $usernameExist ) ) {
				throw new Exception( 'Username is already exist' );
			}
		}

		$user->email       = $params[ 'email' ];
		$user->username    = $params[ 'username' ];
		$user->displayname = $params[ 'fullname' ];
		$user->timezone    = $params[ 'timezone' ];
		$user->save();


		unset( $params[ 'token' ] );
		unset( $params[ 'method' ] );

		return array(
			'GeneralInfo' => $params 
		);
	}

}