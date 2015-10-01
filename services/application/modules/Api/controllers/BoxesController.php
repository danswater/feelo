<?php
use ColorThief\ColorThief;

class Api_BoxesController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_helper->AjaxContext()
                ->addActionContext('get', 'json')
                ->addActionContext('post', 'json')
                ->addActionContext('new', 'json')
                ->addActionContext('edit', 'json')
                ->addActionContext('put', 'json')
                ->addActionContext('delete', 'json')
                ->initContext('json');
    }

    public function indexAction() {
    	$this->_helper->json( array (
    			'action' => 'index'
    	) );
    }

    public function getAction() {

		$this->_forward('index');
		
    }
    
    public function newAction() {

        $this->_forward('index');
    }

    public function postAction() {
		$token = $this->_getParam ( 'token', null );

		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1)
			return $this->_forward ( 'forbidden' );
		
		if ($auth->expire_date < time ())
			return $this->_forward ( 'expired' );
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		$values = $this->getRequest()->getParams();
		$params = $values;
		
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );	

		switch( $this->_getParam( 'method', null ) ) {
			case 'edit' :
				$arrResultSet = $favo->edit( $user, $_FILES, $values );			
			break;
			
			case 'fetchPost' :
				$arrResultSet = $favo->fetchPost( $values );
			break;
			
			case 'upload' :

				$objProject = Engine_Api::_()->getApi( 'project', 'api' );

				$files = $this->formatToFiles( $values );
				unset( $values[ 'Filedata' ] );
				$arrResultSet  = $objProject->uploadFeed( $user, $files  );

				if( !empty( $arrResultSet[ 'data' ] ) ) {
					$values[ 'project_id' ] = $arrResultSet[ 'data' ][ 'project_id' ];
				
					$arrResultSet  = $objProject->feedDetails( $user, $values );			
				}
		
			break;
			
			case 'delete' :
				Engine_Api::_()->user()->setViewer( $user );
				//$favo = Engine_Api::_()->getApi( 'favo', 'api' );
				$arrResultSet = $this->deleteFavo( $this->getRequest()->getPost() );
			break;
			
			case 'createBox' :
				Engine_Api::_()->user()->setViewer( $user );
				$arrResultSet = $this->create( $this->getRequest()->getPost() );
			break;
			
			case 'fetchPosts' :
				Engine_Api::_()->user()->setViewer( $user );
				$box = Engine_Api::_()->getApi( 'box', 'api' );
				$arrResultSet = $this->fetchPosts( $this->getRequest()->getPost() );				
			break;
			
			case 'settings' :
				$settings = Engine_Api::_()->getApi('settings', 'core');
				print_r( $settings->getSetting( 'both.video.format', 0 ) ); exit;
			break;
			
			case 'getUsers' :
				try {
					$circleitem = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );
					$resultSet = $circleitem->fetchAll( 'circle_id ='. $values[ 'circle_id' ] );
				} catch( Exception $e ) {
					print_r( $e->getMessage() );
				}
				
				$return = '';
				$user = Engine_Api::_()->getApi( 'user', 'api' );
				$circle = Engine_Api::_()->getDbTable( 'circles', 'whmedia' );
				foreach( $resultSet as $key => $value ) {
					$return[ $key ][ 'User' ] = $user->fetchUserDetails( $value[ 'user_id' ] );
					$return[ $key ][ 'UsersInBox' ] =  $circleitem->fetchAll( 'user_id ='. $value[ 'user_id' ] )->toArray();
					
/*
					foreach( $return[ $key ][ 'Box' ] as $innerKey => $innerValue ) {
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'user_id' ] );
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'freq' ] );
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'public' ] );
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'photo_id' ] );
					}
*/				
				}
				
				foreach( $return as $key => $value ) {

					foreach( $value[ 'UsersInBox' ] as $innerKey => $innerValue ) {
						$arrCircle = $circle->fetchAll( 'circle_id ='. $innerValue[ 'circle_id' ] .' AND public =1' )->toArray();
						
						if(  !empty( $arrCircle ) ) {
							$return[ $key ][ 'Box' ][] = $arrCircle[ 0 ];
						}
					}
					

					foreach( $return[ $key ][ 'Box' ] as $innerKey => $innerValue ) {
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'user_id' ] );
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'freq' ] );
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'public' ] );
						unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'photo_id' ] );
					}
	
					
					unset( $return[ $key ][ 'UsersInBox' ] );
				}

				$arrResultSet =  array(
					'data'  => $return,
					'error' => array()
				);
			break;
			
			case 'getLikes':
				$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
				$arrResultSet        = $projectFeed->fetchFeed( $user, 'likes', $this->_getParam( 'offset', null ) );			
			break;
			
			case 'getUsers2':
				$objBox = Engine_Api::_()->getApi( 'box', 'api' );
				$arrResultSet = $objBox->getUsersList2( $values );
				$this->getHelper( 'json' )->sendJson( $arrResultSet );			
			break;
			
			case 'getMediaDetails':
				$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
				$cover = 4186;
				$test = $objMedia->fetchMediaDetails2( 2784, $cover );
				print_r( $test ); exit;
			break;
			
			case 'activity' :
				$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
				$arrResultSet = $projectFeed->fetchFeed( $user, 'activity', $this->_getParam( 'offset', null ) );	
			
				$storagePath = APPLICATION_PATH . DIRECTORY_SEPARATOR; 			
				foreach( $arrResultSet[ 'Activity_Feed' ] as $key => $value ) {
					$arrResultSet[ 'Activity_Feed' ][ $key ][ 'Media' ][ 'rgb' ] = ColorThief::getColor( $storagePath . $value[ 'Media' ][ 'storage_path' ] );
				}

				$this->getHelper( 'json' )->sendJson( $arrResultSet );	
			break;
			
			case 'fetchAll' :
				$notificationApi = Engine_Api::_()->getApi( 'boxes', 'api' );
				$arrResultSet = $notificationApi->fetch( $user, $params );
			break;
			
			case 'getUserDetails' :
				$ApiUserApi = Engine_Api::_()->getApi( 'user', 'api' );
				$arrResultSet = $ApiUserApi->getUserDetails( $params );
			break;

			case 'sample':
				$apiMedia = Engine_Api::_()->getApi( 'media', 'api2' );
				print_r( $apiMedia->fetchMediaByProjectId( 12, 4186)); 
				exit;
			break;
			
			case 'login':
				try {
				$ApiUserApi = Engine_Api::_()->getDbTable( 'auth', 'api' );
				$arrResultSet = $ApiUserApi->authenticateTest( $user, $params );
				} catch( Exception $e ) {
					print_r( $e ); exit;
				}
			break;
		}
		//$test1 = 1942;
		//$test2 = 3408;

		//$arrResultSet = $this->fetchMediaDetails( $test1, $test2 );
		//$objHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );		
		//$arrResultSet = $objHashtag->getPostHashtag( $user, 1933 );

		
		
		// Currently followed
		/*
		$objFollow = Engine_Api::_()->getApi( 'follow', 'api' );
		$arrResultSet = $objFollow->getFollowedUsers( $user, $this->_getParam( 'offset', null ) );
        */
		// Feed info
		/*
		$values = $this->getRequest()->getPost();
		$objProject = Engine_Api::_()->getApi( 'project', 'api' );
		$arrResultSet  = $objProject->feedDetails( $user, $values );
		*/
		
		//Upload
		/*
		$values = $this->getRequest()->getPost();
		$objProject = Engine_Api::_()->getApi( 'project', 'api' );
		$arrResultSet  = $objProject->uploadFeed( $user, $_FILES  );
		$values[ 'project_id' ] = $arrResultSet[ 'data' ][ 'project_id' ];
		$arrResultSet  = $objProject->feedDetails( $user, $values );
		*/
		
		/*
		$file_path = $_FILES[ 'Filedata' ][ 'tmp_name' ].'.mp4';
		$fft = new FFMpegThumbnailer( $file_path );
		try{
			$test = $fft->setOutput( '/tmp/test.jpg' )->run();
			print_r( $test ); exit;
		}
		catch( RuntimeException $e ) {
			print_r( $e ); exit;
		}
		*/
		
		//Boxes
/*
		$subject = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		$user = Engine_Api::_ ()->user ()->getUser ( $this->_getParam ( 'user_id', null ) );
		$boxId = $this->_getParam ( 'box_id', null );

		$objBox = Engine_Api::_()->getApi( 'box', 'api' );

		$arrResultSet  = $objBox->toggleBox( $boxId , $user, $subject );			
*/
		
		$this->getHelper( 'json' )->sendJson( $arrResultSet );
    }
	
	public function create( $params ) {
		$data = array(
			'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
			'title'   => $params[ 'title' ]
		);
		
		try {
			$box = Engine_Api::_()->getDbTable( 'circles', 'whmedia' );
			$test = $box->insert( $data );
			
			$row = $box->fetchRow( $box->select()->order( array( 'circle_id DESC' ) )->limit( 1 ) );
		} catch ( Exception $e ) {
			return array(
				'data'  => array(),
				'error' => array( $e->getMessage() )
			);
		}
		
		return array(
			'data'  => $row->toArray(),
			'error' => array()
		);
	}
	
	public function deleteFavo( $params ) {
		try {
			// Delete child reference of favo
			$favCircleItems    = Engine_Api::_()->getDbTable( 'favcircleitems', 'whmedia' );
			$favCircleItems->delete( 'favcircle_id = '. $params[ 'favcircle_id' ] );
			
			// Delete favo
			$favo    = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );
			$favo->delete( 'favcircle_id = '. $params[ 'favcircle_id' ] );
		} catch ( Exception $e ) {
			return array(
				'data'  => array(),
				'error' => array( $e->getMessage() )
			);
		}
		
		return array(
			'data'  => array(),
			'error' => array()
		);
	}
	
	public function formatToFiles( $values ) {
		$files = array();
		$tmpLocation = '/tmp/php'. time();
	
		$location = $tmpLocation;
		$files[ 'Filedata' ][ 'name' ] = $values[ 'title' ].'.jpg';		
		$files[ 'Filedata' ][ 'type' ] = 'image/jpeg';
		$files[ 'Filedata' ][ 'tmp_name' ] = $this->base64_to_jpeg( $values[ 'Filedata' ], $location.'.jpg' );
		$files[ 'Filedata' ][ 'error' ] = 0;
		$files[ 'Filedata' ][ 'size' ] = 0;
	
		return $files;
	}
	
	public function base64_to_jpeg( $base64_string, $output_file ) {
		$ifp = fopen( $output_file, "wb" );
		fwrite( $ifp, base64_decode( $base64_string) );
		fclose( $ifp );
		return( $output_file ); 
	} 

    public function editAction() {

        $this->_forward('index');
    }

    public function putAction() {

        $this->_forward('index');
    }

    public function deleteAction() {

        $this->_forward('index');
    }

    public function headAction() {
    	$this->_forward('index');
    }
    
	public function forbiddenAction(){
		
		$message = array('message' => 'Token provided is invalid');
		
		$this->_helper->json ( array (
				'error' => $message
		) );
	}
	
	public function expiredAction($viewer) {
		
		$message = array('message' => 'Token expired');
		
		$this->_helper->json ( array (
				'error' => $message
		) );
		
	}
	
	protected function _quoteInto( $objDb, $where, $values = array() ) {

		foreach( $values as $value ) {
			$where = $objDb->quoteInto( $where, $value, '', 1 ); 
		}

		return $where;

	}

}
