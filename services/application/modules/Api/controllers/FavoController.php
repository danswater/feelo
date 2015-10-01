<?php
class Api_FavoController extends Zend_Rest_Controller {

	public function init() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		
		$this->_helper->AjaxContext ()
			->addActionContext ( 'get', 'json' )
			->addActionContext ( 'post', 'json' )
			->addActionContext ( 'new', 'json' )
			->addActionContext ( 'edit', 'json' )
			->addActionContext ( 'put', 'json' )
			->addActionContext ( 'delete', 'json' )
			->initContext ( 'json' );
	}
	
	public function indexAction() {}

	public function getAction() {}
	
	public function postAction() {
	
		$token = $this->_getParam ( 'token', null );

		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1) {
			return $this->_forward ( 'forbidden' );
		}
        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		$values = $this->getRequest()->getPost();
		$params = $values;
		
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );
		
		switch( $this->_getParam( 'method', null ) ) {
			case 'fetch' :
			case 'fetchUserFavo' :
			case 'fetchFollowedFavo' :
	
				// must implement offset	
				try {
					$response = $favo->fetchFavo( $user, $params );
				} catch ( Exception $e ) {
					$response = array(
						'data'  => array(),
						'error' => array( $e->getMessage() )
					);
				}
				
				$this->getHelper( 'json' )->sendJson( $response );
				
				// fall back to this method if error occured in new one
				//$arrResultSet = $favo->fetch( $user, $values );

				// fall back to this method if error occured in new one
				//$arrResultSet = $favo->fetchUserFavo( $user, $values );

				// fall back to this method if error occured in new one
				//$arrResultSet = $favo->fetchFollowedFavo( $user, $values );				
			break;
			
			case 'create' :
			case 'edit' :
				Engine_Api::_()->user()->setViewer( $user );
				$files = $this->formatToFiles( $values );
				unset( $values[ 'Filedata' ] );
				$arrResultSet = $favo->save( $user, $files, $values );
			break;
			
			case 'addToFavo' :
				$arrResultSet = $favo->toggle( $user, $values );
			break;
			
			case 'fetchPosts' :
				$arrResultSet = $favo->fetchFavoPosts( $user, $values );
			break;
			
			case 'delete' :
				$arrResultSet = $favo->deleteFavo( $values );
			break;
			
			case 'followFavo' :
				$arrResultSet = $favo->followFavo( $user, $values );
			break;
			
			case 'fetchAFavo' :
				$arrResultSet = $favo->fetchAFavo( $user, $values );
			break;
			
			default :
				$arrResultSet = array(
					'data' => array(),
					'error' => array( 'Unknown method value' )
				);
			break;
		}
		
		$this->getHelper( 'json' )->sendJson( $arrResultSet );
	}
	
	public function putAction() {}
	
	public function deleteAction() {}
	
	public function formatToFiles( $values ) {

		if ( !empty( $_FILES ) ) {
			$extension = ltrim( strrchr( basename( $_FILES[ 'Filedata' ][ 'name' ] ), '.'), '.');
			$newName = $_FILES[ 'Filedata' ][ 'tmp_name' ] . '.' .$extension;
			rename( $_FILES[ 'Filedata' ][ 'tmp_name' ], $newName );
			$_FILES[ 'Filedata' ][ 'tmp_name' ] = $newName;
			return $_FILES;
		}
		
		if( empty( $values[ 'Filedata' ] ) ) {
			return null;
		}
		
		$files = array();
		$tmpLocation = '/tmp/php'. time();

		$location = $tmpLocation.'.jpg';
		$files[ 'Filedata' ][ 'name' ] = $values[ 'title' ].'.jpg';		
		$files[ 'Filedata' ][ 'type' ] = 'image/jpeg';
		$files[ 'Filedata' ][ 'tmp_name' ] = $this->base64_to_jpeg( $values[ 'Filedata' ], $location );
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

}