<?php
class Api_RegisterController extends Zend_Rest_Controller {

	public function init() {
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
	}

	public function indexAction() {
	  
	}

	public function getAction() {
		
		
		
	}

	public function postAction() {
	
		$returnJson = array( "data" => array(), "error" => array() );
	
		switch( $this->getRequest()->getParam( 'method', 'add' ) ) {
		
			/* get the timezone throught post */
			case 'timezone':
				$this->getResponse()->setHttpResponseCode( 200 );
				$returnJson[ "data" ] = array( 
					
					'timezone' => array( 
					
						array( 'name' => 'US/Pacific', 'utc'  => '(UTC-8) Pacific Time (US & Canada)' ),
						array( 'name' => 'US/Mountain', 'utc' => '(UTC-7) Mountain Time (US & Canada)' ),
						array( 'name' => 'US/Central', 'utc'  => '(UTC-6) Central Time (US & Canada)' ),
						array( 'name' => 'US/Eastern', 'utc'  => '(UTC-5) Eastern Time (US & Canada)' ),
						array( 'name' => 'America/Halifax', 'utc'   => '(UTC-4)  Atlantic Time (Canada)' ),
						array( 'name' => 'America/Anchorage', 'utc' => '(UTC-9)  Alaska (US & Canada)' ),
						array( 'name' => 'Pacific/Honolulu', 'utc'  => '(UTC-10) Hawaii (US)' ),
						array( 'name' => 'Pacific/Samoa', 'utc' => '(UTC-11) Midway Island, Samoa' ),
						array( 'name' => 'Etc/GMT-12', 'utc' => '(UTC-12) Eniwetok, Kwajalein' ),
						array( 'name' => 'Canada/Newfoundland', 'utc' => '(UTC-3:30) Canada/Newfoundland' ),
						array( 'name' => 'America/Buenos_Aires', 'utc' => '(UTC-3) Brasilia, Buenos Aires, Georgetown' ),
						array( 'name' => 'Atlantic/South_Georgia', 'utc' => '(UTC-2) Mid-Atlantic' ),
						array( 'name' => 'Atlantic/Azores', 'utc' => '(UTC-1) Azores, Cape Verde Is.' ),
						array( 'name' => 'Europe/London', 'utc' => 'Greenwich Mean Time (Lisbon, London)' ),
						array( 'name' => 'Europe/Berlin', 'utc' => '(UTC+1) Amsterdam, Berlin, Paris, Rome, Madrid' ),
						array( 'name' => 'Europe/Athens', 'utc' => '(UTC+2) Athens, Helsinki, Istanbul, Cairo, E. Europe' ),
						array( 'name' => 'Europe/Moscow', 'utc' => '(UTC+3) Baghdad, Kuwait, Nairobi, Moscow' ),
						array( 'name' => 'Iran', 'utc' => '(UTC+3:30) Tehran' ),
						array( 'name' => 'Asia/Dubai', 'utc' => '(UTC+4) Abu Dhabi, Kazan, Muscat' ),
						array( 'name' => 'Asia/Kabul', 'utc' => '(UTC+4:30) Kabul' ),
						array( 'name' => 'Asia/Yekaterinburg', 'utc' => '(UTC+5) Islamabad, Karachi, Tashkent' ),
						array( 'name' => 'Asia/Dili', 'utc' => '(UTC+5:30) Bombay, Calcutta, New Delhi' ),
						array( 'name' => 'Asia/Katmandu', 'utc' => '(UTC+5:45) Nepal' ),
						array( 'name' => 'Asia/Omsk', 'utc' => '(UTC+6) Almaty, Dhaka' ),
						array( 'name' => 'India/Cocos', 'utc' => '(UTC+6:30) Cocos Islands, Yangon' ),
						array( 'name' => 'Asia/Krasnoyarsk', 'utc' => '(UTC+7) Bangkok, Jakarta, Hanoi' ),
						array( 'name' => 'Asia/Hong_Kong', 'utc' => '(UTC+8) Beijing, Hong Kong, Singapore, Taipei' ),
						array( 'name' => 'Asia/Tokyo', 'utc' => '(UTC+9) Tokyo, Osaka, Sapporto, Seoul, Yakutsk' ),
						array( 'name' => 'Australia/Adelaide', 'utc' => '(UTC+9:30) Adelaide, Darwin' ),
						array( 'name' => 'Australia/Sydney', 'utc' => '(UTC+10) Brisbane, Melbourne, Sydney, Guam' ),
						array( 'name' => 'Asia/Magadan', 'utc' => '(UTC+11) Magadan, Soloman Is., New Caledonia' ),
						array( 'name' => 'Pacific/Auckland', 'utc' => '(UTC+12) Fiji, Kamchatka, Marshall Is., Wellington' ),
					)	
						
				);
				break;
			
			case 'verifyAccount':
					$params = $this->getRequest()->getPost();
					
					$settings = Engine_Api::_()->getApi('settings', 'core');

					// No code or email
					if( empty( $params[ 'code' ] ) || empty( $params[ 'email' ] ) ) {
						$returnJson[ "error" ]  = 'The email or verification code was not valid.';
					}
					
					if( count( $returnJson[ "error" ] ) == 0 ){ 
						$userTable = Engine_Api::_()->getDbtable('users', 'user');
						$user = $userTable->fetchRow( $userTable->select()->where( 'email = ?', $params[ 'email' ] ) );

						if( !$user || !$user->getIdentity() ) {
							$returnJson[ "error" ]  = 'The email does not match an existing user.';
						}
						
						if( $user->verified && count( $returnJson[ "error" ] ) == 0 ) {
							$returnJson[ "error" ]  = 'The email is already verified.';
						}
						
						if( count( $returnJson[ "error" ] ) == 0 ){ 
							$verifyTable = Engine_Api::_()->getDbtable('verify', 'user');
							$verifyRow = $verifyTable->fetchRow($verifyTable->select()->where('user_id = ?', $user->getIdentity()));
							if( !$verifyRow || $verifyRow->code != $params[ 'code' ] ) {
								$returnJson[ "error" ]  = 'There is no verification info for that user.';
							}
						
						}
						
						if( count( $returnJson[ "error" ] ) == 0 ){ 
						
							 $db = $verifyTable->getAdapter();
							$db->beginTransaction();

							try {
							  $verifyRow->delete();
							  $user->verified = 1;
							  $user->save();
							  $db->commit();
							  
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
								
								$returnJson[ "data" ][ "User" ] = $ret;
							  
							} catch( Exception $e ) {
							  $db->rollBack();
							  $returnJson[ "error" ]  = $e->getMessage();
							}
						
						}
					}
			
			break;

	
			/* new registration */
			case 'add2':
			
				$params = $this->getRequest()->getPost();
				
				$userApi = Engine_Api::_()->getApi( 'user', 'api' );
				
				$ret = $userApi->createNewUser( $params );
				
				$this->getHelper( 'json' )->sendJson( $ret ) ;			
			break;
			
			/* registration area here */
			case 'add':
			default:
				$postRequired = array( "email" => "" , 
									"password" => "",  
									"passconf" => "",
									"timezone" => "",
									"username" => "" );

				foreach( $postRequired as $key => $value ) 
				{
					if( $this->getRequest()->getParam( $key, false ) !== false ) 
					{
						$postRequired[ $key ] = $this->getRequest()->getParam( $key );
						
					}else
						$returnJson[ "error" ][] = ucfirst( $key ) . " is required ";
				}

				$table = Engine_Api::_()->getDbtable('users', 'user');
				
				$emailSelect = $table->select( )
				  ->where('email = ?', $postRequired[ "email" ] );
				$emailExist = $table->fetchRow( $emailSelect );
				
				if( $emailExist != null && count( $returnJson[ "error" ] ) == 0 ) {
					$returnJson[ "error" ]  = "Email is already exist";
				}
				
				$emailSelect = $table->select( )
				  ->where('username = ?', $postRequired[ "username" ] );
				$usernameExist = $table->fetchRow( $emailSelect );

				if( $usernameExist != null && count( $returnJson[ "error" ] ) == 0 ) {
					$returnJson[ "error" ]  = "Username is already exist";
				}
				
				if( $postRequired[ "password" ] != $postRequired[ "passconf" ] ) {
					$returnJson[ "error" ]  = "Password and Confirm Password not matched.";
				}
				
				if( count( $returnJson[ "error" ] ) == 0 ) 
				{
				
					$this->_normalRegistration( $postRequired );
					
					$userTable = Engine_Api::_()->getDbtable('users', 'user');
					$user = $userTable->fetchRow($userTable->select()->where('username = ?', $postRequired[ 'username' ] ) );
					
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
					
					$returnJson[ "data" ][ "User" ] = $ret;
					
					
					if( $this->getRequest()->getParam( "is_mobile", false ) != false ){	
						$userSelect = $table->select( )
							->where('email = ?', $postRequired[ "email" ] );
							
						$user = $table->fetchRow( $userSelect );
						$user->verified = 1;
						$user->save();
							
						// auto login
						$apiAuth = Engine_Api::_()->getDbTable('auth', 'api');
							
						$response = $apiAuth->authenticate( $postRequired[ "email" ], $postRequired[ "password" ] );
							
						$returnJson[ "data" ][ "token" ] = $response[ "data" ][ "token" ];
							
						$returnJson[ "data" ][ "expire_date" ] = $response[ "data" ][ "expire_date" ];
							
						$returnJson[ "data" ][ "User" ] = $apiAuth->getUserData( $response[ "data" ][ "user" ] );
					}
						
				}
				
				
				
			break;
			
			case 'subscribeIos' :
				$params = $this->getRequest()->getPost();
				$headers = 'From: yambai.ios@gmail.com' . "\r\n" .
				'Reply-To: norepy@gmail.com.com' . "\r\n";
				
				if( mail('philipmacairan@hotmail.com', 'IOS Subscription', $params[ 'email' ], $headers ) ) {
					$returnJson[ "data" ] = array( 
						'success' => true,
						'email' => $params[ 'email' ]
					);
				}else {
					$returnJson[ "data" ] = array( 
						'success' => false,
						'email' => $params[ 'email' ]
					);
				}
			break;
			
			
			case 'validateUsername' :

				$params = $this->getRequest()->getPost();
				
				if ( empty( $params[ "username" ] ) ) {
					$returnJson[ "error" ][] = ucfirst( "username" ) . " is required ";
					$this->getHelper( 'json' )->sendJson( $returnJson ) ;
				}

				$table = Engine_Api::_()->getDbtable('users', 'user');				
				
				$emailSelect = $table->select( )
				  ->where('username = ?', $params[ "username" ] );
				$usernameExist = $table->fetchRow( $emailSelect );
	
				if( $usernameExist != null && count( $returnJson[ "error" ] ) == 0 ) {
					$returnJson[ "data" ][ "message" ]  = "Username is already exist";
					$returnJson[ "data" ][ "status" ] = 1;
				} else {
					$returnJson[ "data" ][ "message" ]  = "Username is available";
					$returnJson[ "data" ][ "status" ] = 0;			
				}
				
			break;
		
		}
		
		$this->getHelper( 'json' )->sendJson( $returnJson ) ;

	}

	public function putAction() {
	 
	}

	public function deleteAction() {
	  
	}

	public function _normalRegistration( $params ) {
			
		/*
		 * convert the string to integer
		 * 2 is male and 3 is female
		 */
		if( $params[ "gender" ] == "female" )
			$params[ "gender" ] = 3;
		else 
			$params[ "gender" ] = 2;
		
		/*
		 * for normal registration, manual
		 * only two plugins for now are supportd, it can be change if the other plugin activated.
		 * table plugin lookup
		 *	engine4_users_singup
		 */
		$_SESSION = array(
		  "facebook_lock" => 0,
		  "Signup_Confirm" => array(
			'approved' => 1,
			'verified' => 0,
			'enabled' => 1,
		  ),
		  'User_Plugin_Signup_Account' => array(
			'active' => false, // indicator of what plugins is currently viewed. false is done and true is currently viewed.
			'data' => array(
			  'email' => $params[ "email" ], // label email
			  'password' => $params[ "password" ], // label password
			  'passconf' => $params[ "passconf" ], // label password confirm
			  'username' => $params[ "username" ], // username
			  'displayname' => $params[ "username" ], // displayname
			  'profile_type' => null,
			  'timezone' => empty( $params[ "timezone" ] ) ? 'US/Central' : $params[ "timezone" ], 
			  'language' => 'English', // default language should be english.
			  'terms' => '1', // term if agree
			)
		  ),
		  'User_Plugin_Signup_Fields' => array(
			'active' => false,
			'data' => array(
			  3 => empty( $params[ "brandname" ] ) ? '' : $params[ "brandname" ], // brand name
			  4 => empty( $params[ "description" ] ) ? 'US/Central' : $params[ "description" ], //  description about you
			  5 => $params[ "gender" ], // 2 male 3 femail gender
			)
		  )
		);
		/*
		 * Loop all the plugin and execute the process.. manually
		 */
		$formSequenceHelper = $this->_helper->formSequence;
		foreach( Engine_Api::_()->getDbtable('signup', 'user')->fetchAll() as $row ) {
		  if( $row->enable == 1 ) {
			$class = $row->class;
			$clazz = new $class;
			$clazz->onProcess();
		  }
		}
	
	}
	
}