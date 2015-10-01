<?php

class Api2_Api_Media extends Core_Api_Abstract {

	/**
	*	Note : This class interacts with the Api2/Model/DbTable classes
	*/

	// TODO : insert logic here

	public function fetchMediaByProjectId ( $projectId, $coverFileId ) {

		$dbTableMedias = Engine_Api::_()->getDbTable( 'medias', 'whmedia' );
		$objMediasDb = $dbTableMedias->getAdapter();
		$selectedColumn = array( 'media_id', 'title', 'project_id', 'code', 'is_url AS url' );
	
		$select = $dbTableMedias->select()
				->from( array( 'whmm' => 'engine4_whmedia_medias' ), $selectedColumn )
				->where( 'project_id = ?', $projectId );
		$arrWhmediaRowSet = $objMediasDb->fetchAll( $select );

		// convert null into null string
		if( $coverFileId === '' ) {
			$coverFileId = 'null';
		}
		
		$objStorageTable = Engine_Api::_()->getDbTable( 'files', 'storage' );
		$objStorageDb = $objStorageTable->getAdapter();

		// Fetch Photos/Videos
		$where     = "parent_id = ?";			
		$objStorageSelect = $objStorageTable->select()
			->from( array( 'sf' => 'engine4_storage_files' ) )
			->where( 'parent_id = ?', $coverFileId );
		$arrStorageRowSet = $objStorageDb->fetchAll( $objStorageSelect );
		
		$mediaStorage = array();
		if( count( $arrStorageRowSet ) > 1 ) {
			foreach( $arrStorageRowSet as $key => $value ) {
				if( pathinfo ( $value[ 'storage_path' ], PATHINFO_EXTENSION ) == 'mp4' ) {
					$mediaStorage[ 'code' ] = $value[ 'storage_path' ];
				}
				
				if( ( pathinfo( $value[ 'storage_path' ], PATHINFO_EXTENSION ) == 'jpg' ) || ( pathinfo( $value[ 'storage_path' ], PATHINFO_EXTENSION ) == 'jpeg' )  ) {
					$mediaStorage[ 'storage_path' ] = $value[ 'storage_path' ];
				}
			}
			unset( $arrStorageRowSet );
			$arrStorageRowSet[] = $mediaStorage;
		}
		$arrWhmmResultSet[] = $arrWhmediaRowSet;
		$return = array();		
		foreach( $arrStorageRowSet as $key => $value ) {
			$arrWhmediaRowSet[ 0 ][ 'storage_path' ] = $value[ 'storage_path' ];
			if ( empty( $arrWhmediaRowSet[ 0 ][ 'code' ] ) ) {
				$arrWhmediaRowSet[ 0 ][ 'code' ] = $value[ 'code' ];
			}
			$return = $arrWhmediaRowSet[ 0 ];
		}
		if( $return[ 'code' ] !== null ) {
			$arrCode = explode( '"', $return[ 'code' ] );
			if( count( $arrCode ) == 1 ) {
				$return[ 'type' ] = 'direct';
				$return[ 'media_code' ] = $return[ 'code' ];
			}
			else {
				$return[ 'type' ] = $arrCode[ 3 ];
				$return[ 'media_code' ] = $arrCode[ 7 ];	
			}
		}
		else {
			$return[ 'type' ] = 'null';
			$return[ 'media_code' ] = 'null';		
		}
		if( empty( $return[ 'storage_path' ] ) || is_null( $return[ 'storage_path' ] ) ) {
			$return[ 'storage_path' ] = 'null';
		}
		unset( $return[ 'code' ] );
			
		if( pathinfo ( $return[ 'storage_path' ], PATHINFO_EXTENSION ) == 'mp4' ) {
			$return[ 'media_code' ] = $return[ 'storage_path' ];
			$return[ 'storage_path' ] = 'null';			
		}
		
		$size = getimagesize ( $return[ 'storage_path' ] );
		$return[ 'image_width' ] = $size[ 0 ] ;
		$return[ 'image_height' ] = $size[ 1 ];
		
		if ( empty( $return[ 'url' ] ) || is_null( $return[ 'url' ] ) ) {
			$return[ 'url' ] = 'null';
		}

		return $return;
	}

	public function fetchMediaDetailsByMediaId ( $mediaId, $coverFileId ) {
		$mediasTable = Engine_Api::_()->getDbTable( 'medias', 'api2' );
		$arrWhmediaRowSet = $mediasTable->readMediaDetailsByMediaId( $mediaId );

		// convert null into null string
		if( $coverFileId === '' ) {
			$coverFileId = 'null';
		}

		$storage = Engine_Api::_()->getApi( 'storage', 'api2' );
		$arrStorageRowSet = $storage->fetchStorageByCoverFileId( $coverFileId );

		$arrWhmmResultSet[] = $arrWhmediaRowSet;

		$return = array();
		
		foreach( $arrStorageRowSet as $key => $value ) {
			$arrWhmediaRowSet[ 0 ][ 'storage_path' ] = $value[ 'storage_path' ];
			$return = $arrWhmediaRowSet[ 0 ];
		}

		$return = $this->sample( $return );
		
		$arrStorage = pathinfo ( $return[ 'storage_path' ] );
		if( $arrStorage[ 'extension' ] != 'jpg' ) {
			$return[ 'code' ] = null;
			$return[ 'storage_path' ] = '/public/whshow_thumb.jpg';
			$return[ 'type' ] = 'direct';
			$return[ 'media_code' ] = $value[ 'storage_path' ];
		}
		unset( $return[ 'code' ] );

			
		return $return;
	}

	public function sample( &$return ){
		if( $return[ 'code' ] !== null ) {
			$arrCode = explode( '"', $return[ 'code' ] );
			if( count( $arrCode ) == 1 ) {
				$return[ 'type' ] = 'direct';
				$return[ 'media_code' ] = $return[ 'code' ];
			}
			else {
				$return[ 'type' ] = $arrCode[ 3 ];
				$return[ 'media_code' ] = $arrCode[ 7 ];	
			}
		}
		else {
			$return[ 'type' ] = 'null';
			$return[ 'media_code' ] = 'null';		
		}

		return $return;
	}

	public function fetchPhotoMediaByProjectId ( $projectId ) {
		$dbTableMedias = Engine_Api::_()->getDbTable( 'medias', 'api2' );
		$mediaRows = $dbTableMedias->readPhotoMediaByProjectId( $projectId );

		$mediaRow = $mediaRows->toArray();

		// TODO to be remove
		if ( $mediaRow[ 0 ][ 'type' ] == null ) {
			$mediaRow[ 0 ][ 'type' ] = 'null';
		}

		if ( $mediaRow[ 0 ][ 'code' ] == null ) {
			$mediaRow[ 0 ][ 'code' ] = 'null';
		}

		$media = new Api2_Model_Media();
		$media->setMediaId( $mediaRow[ 0 ][ 'media_id' ] );
		$media->setTitle( $mediaRow[ 0 ][ 'title' ] );
		$media->setProjectId( $mediaRow[ 0 ][ 'project_id' ] );
		$media->setUrl( $mediaRow[ 0 ][ 'url' ] );
		$media->setStoragePath( $mediaRow[ 0 ][ 'storage_path' ] );
		$media->setTypee( $mediaRow[ 0 ][ 'type' ] );
		$media->setMediaCode( $mediaRow[ 0 ][ 'code' ] );

		return $media;
	}
	
	public function fetchLinkMediaWithImageSizesByProjectId ( $projectId, $coverFileId ) {
		// Fetch Media(s)
		$apiWhmedia = Engine_Api::_()->getApi( 'media', 'api2' );

		$coverFileId = $coverFileId;
		if ( is_null( $coverFileId ) ) {
			$coverFileId = 'null';
		}

		$mediaRow   = $apiWhmedia->fetchMediaByProjectId( $projectId, $coverFileId );

		$media = new Api2_Model_Media();
		$media->setMediaId( $mediaRow[ 'media_id' ] );
		$media->setTitle( $mediaRow[ 'title' ] );
		$media->setProjectId( $mediaRow[ 'project_id' ] );
		$media->setUrl( $mediaRow[ 'url' ] );
		$media->setStoragePath( $mediaRow[ 'storage_path' ] );
		$media->setExtraLarge( $mediaRow[ 'extra_large' ] );
		$media->setLarge( $mediaRow[ 'large' ] );
		$media->setMedium( $mediaRow[ 'medium' ] );
		$media->setSmall( $mediaRow[ 'small' ] );
		$media->setTypee( $mediaRow[ 'type' ] );
		$media->setMediaCode( $mediaRow[ 'code' ] );
		$media->setImageWidth( $mediaRow[ 'image_width' ] );
		$media->setImageHeight( $mediaRow[ 'image_height' ] );

		return $media;
	}
	
	public function fetchPhotoMediaWithImageSizesByProjectId ( $projectId, $coverFileId ) {
		// Fetch Media(s)
		$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );

		if ( is_null( $coverFileId ) ) {
			$coverFileId = 'null';
		}

		$mediaRow = $objMedia->fetchMediaDetails( $projectId, $coverFileId );
		
		$media = new Api2_Model_Media();
		$media->setMediaId( $mediaRow[ 'media_id' ] );
		$media->setTitle( $mediaRow[ 'title' ] );
		$media->setProjectId( $mediaRow[ 'project_id' ] );
		$media->setUrl( $mediaRow[ 'url' ] );
		$media->setStoragePath( $mediaRow[ 'storage_path' ] );
		$media->setExtraLarge( $mediaRow[ 'extra_large' ] );
		$media->setLarge( $mediaRow[ 'large' ] );
		$media->setMedium( $mediaRow[ 'medium' ] );
		$media->setSmall( $mediaRow[ 'small' ] );
		$media->setTypee( $mediaRow[ 'type' ] );
		$media->setMediaCode( $mediaRow[ 'code' ] );
		$media->setImageWidth( $mediaRow[ 'image_width' ] );
		$media->setImageHeight( $mediaRow[ 'image_height' ] );

		return $media;
	}
	
	public function fetchVideoMediaWithImageSizesByProjectId ( $projectId, $coverFileId ) {
		// Fetch Media(s)		
		$apiWhmedia = Engine_Api::_()->getApi( 'media', 'api2' );

		$coverFileId = $coverFileId;
		if ( is_null( $coverFileId ) ) {
			$coverFileId = 'null';
		}

		$mediaRow   = $apiWhmedia->fetchMediaByProjectId( $projectId, $coverFileId );

		$media = new Api2_Model_Media();
		$media->setMediaId( $mediaRow[ 'media_id' ] );
		$media->setTitle( $mediaRow[ 'title' ] );
		$media->setProjectId( $mediaRow[ 'project_id' ] );
		$media->setUrl( $mediaRow[ 'url' ] );
		$media->setStoragePath( $mediaRow[ 'storage_path' ] );
		$media->setExtraLarge( $mediaRow[ 'extra_large' ] );
		$media->setLarge( $mediaRow[ 'large' ] );
		$media->setMedium( $mediaRow[ 'medium' ] );
		$media->setSmall( $mediaRow[ 'small' ] );
		$media->setTypee( $mediaRow[ 'type' ] );
		$media->setMediaCode( $mediaRow[ 'media_code' ] );
		$media->setImageWidth( $mediaRow[ 'image_width' ] );
		$media->setImageHeight( $mediaRow[ 'image_height' ] );

		return $media;
	}
}