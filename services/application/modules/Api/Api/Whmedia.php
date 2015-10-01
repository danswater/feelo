<?php
class Api_Api_Whmedia extends Api_Api_Base {
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

	public function fetchAllByKeyword3( $keyword, $type = 'all', $offset ) {
	    $objTable = Engine_Api::_()->getDbtable('search', 'core');
	    $objDb = $objTable->getAdapter();

		$suffix = $offset ."0";
		$where = 'MATCH(`title`, `description`, `keywords`, `hidden`) AGAINST (? IN BOOLEAN MODE)';
		$whereLike = 'title LIKE ?';
		$whereVal = array( '%'. $keyword .'%' );

		$orderVal = array( $keyword );

	    $objSelect = $objTable->select()
	      ->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $whereLike, $whereVal ) ) )
	      ->order( array( 'id DESC' ) );

	    $availableTypes = Engine_Api::_()->getItemTypes();
	    if( $type && in_array($type, $availableTypes) ) {
	      $objSelect->where('type = ?', $type );
	    } else {
	      $objSelect->where('type IN(?)', $availableTypes );
	    }

	    $objSelect->limit( 10, $suffix );

		$objResultSet = $objDb->fetchAll( $objSelect );
		
	    return $objResultSet;		
	}
	
	public function fetchAllByKeyword5( $keyword, $type = 'all', $offset ) {
		$objMediasTable = Engine_Api::_()->getDbTable( 'medias', 'whmedia' );
		$objMediasDb = $objMediasTable->getAdapter();
		
		$suffix = $offset ."0";
		$where     = "title LIKE ?";
		$selectedFrom[ 'whmm' ] = 'engine4_whmedia_medias';
		$selectedColumn = array( 'media_id', 'title', 'project_id', 'code' );
		$selectedWhere[ 'id' ] = '%'.$keyword.'%';
		$objMediasSelect = $objMediasTable->select()
			->from( $selectedFrom, $selectedColumn )
			->where( new Zend_Db_Expr( $this->_quoteInto( $objMediasDb, $where, $selectedWhere ) ) )
			->limit( 10, $suffix );

		$arrWhmediaRowSet = $objMediasDb->fetchAll( $objMediasSelect );

		$objProjectTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
		$objProjectDb = $objProjectTable->getAdapter();

		foreach( $arrWhmediaRowSet as $key => $value ) {
			$where = "project_id = ?";
			$params = array( 'project_id' => $value[ 'project_id' ] );
			$objProjectSelect = $objProjectTable->select()
				->where( new Zend_Db_Expr( $this->_quoteInto( $objProjectDb, $where, $params ) ) );
				
			$arrProjectResult = $objProjectDb->fetchAll( $objProjectSelect );

			//$user = Engine_Api::_ ()->user ()->getUser ( $arrProjectResult[ 0 ][ 'user_id' ] );

			$coverFileId = $arrProjectResult[ 0][ 'cover_file_id' ];
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
				->where( new Zend_Db_Expr( $this->_quoteInto( $objStorageDb, $where, 
					array( 'id' => $coverFileId ) ) ) );

			$arrStorageRowSet = $objStorageDb->fetchAll( $objStorageSelect );

			$return = array();
	
			foreach( $arrStorageRowSet as $skey => $svalue ) {
				$arrWhmediaRowSet[ $key ][ 'storage_path' ] = $svalue[ 'storage_path' ];

				$return[ $key ] = $arrWhmediaRowSet;
					
			}			
		
		}

		foreach( $return[ 9 ] as $key => $value ) {

			if( $value[ 'code' ] !== null ) {
				$arrCode = explode( '"', $value[ 'code' ] );

				if( count( $arrCode ) == 1 ) {
					$return[ 9 ][ $key ][ 'type' ] = 'direct';
					$return[ 9 ][ $key ][ 'media_code' ] = $value[ 'code' ];
				}
				else {
					$return[ 9 ][ $key ][ 'type' ] = $arrCode[ 3 ];
					$return[ 9 ][ $key ][ 'media_code' ] = $arrCode[ 7 ];	
				}

			}
			else {
				$return[ 9 ][ $key ][ 'type' ] = 'null';
				$return[ 9 ][ $key ][ 'media_code' ] = 'null';		
			}

			$arrStorage = pathinfo ( $value[ 'storage_path' ] );

			if( ( $arrStorage[ 'extension' ] != 'jpg' ) || ( $arrStorage[ 'extension' ] != 'jpeg' ) ) {
				$return[ 9 ][ $key ][ 'code' ] = null;
				$return[ 9 ][ $key ][ 'storage_path' ] = '/public/whshow_thumb.jpg';
				$return[ 9 ][ $key ][ 'type' ] = 'direct';
				$return[ 9 ][ $key ][ 'media_code' ] = $value[ 'storage_path' ];
			}
			unset( $return[ 9 ][ $key ][ 'code' ] );
		}
	
		if( is_null( $return[ 9 ] ) ) {
			return array();
		}
		return $return[ 9 ];		
	}
	
	public function fetchAllByKeyword( $user, $keyword, $type = 'all', $offset, $limit = 10 ) {
		$objMediasTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
		$objMediasDb = $objMediasTable->getAdapter();
		
		$suffix = $offset ."0";
		$where     = "title LIKE ?";
		$selectedFrom[ 'whmm' ] = 'engine4_whmedia_projects';
		$selectedColumn = array( 'project_id' );
		$selectedWhere[ 'id' ] = '%'.$keyword.'%';
		$objMediasSelect = $objMediasTable->select()
			->from( $selectedFrom )
			->where( new Zend_Db_Expr( $this->_quoteInto( $objMediasDb, $where, $selectedWhere ) ) )
			->order( array( 'project_id DESC' ) )
			->limit( $limit, $suffix );

		$feedResultSet = $objMediasDb->fetchAll( $objMediasSelect );
		
		
		foreach( $feedResultSet as $key => $value ) {
			$feedResultSet[ $key ][ 'creation_date' ] = $this->timeAgo( $value[ 'creation_date' ] );
			if( $value[ 'project_views' ] == 0 ) {
				$feedResultSet[ $key ][ 'project_views' ] = ''; 
			}
			else {
				$feedResultSet[ $key ][ 'project_views' ] = (string)$value[ 'project_views' ]; 
			}
			
			// encode description
			$feedResultSet[ $key ][ 'description' ] = utf8_encode( $feedResultSet[ $key ][ 'description' ] );
		
			if( is_null( $value[ 'cover_file_id' ] ) ) {
				$feedResultSet[ $key ][ 'cover_file_id' ] = 'null';
			}
			
		}
		
		// Fetch Media(s)	
		$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
		foreach( $feedResultSet as $key => $value) {
			$resultSetMedia = $objMedia->fetchMediaDetails( $value[ 'project_id' ], $value[ 'cover_file_id' ] );
			$feedResultSet[ $key ][ 'Media' ] = $resultSetMedia;
		}
		
		// Fetch user
		$objUser = Engine_Api::_()->getApi( 'user', 'api' );
		foreach( $feedResultSet as $key => $value) {
			$resultSetUser = $objUser->fetchUserDetails( $value[ 'user_id' ] );
			$feedResultSet[ $key ][ 'User' ] = $resultSetUser;
		}
		
		//Get Like count and check if user liked the project
		$objLikes = Engine_Api::_()->getApi( 'like', 'api' );
		$objComments = Engine_Api::_()->getApi( 'comment', 'api' );
		foreach( $feedResultSet as $key => $value ) {
			$objLikesResultSet = $objLikes->fetchLikes( $feedResultSet[ $key ][ 'user_id' ], $feedResultSet[ $key ][ 'project_id' ] );
			$feedResultSet[ $key ][ 'like_count'] = $this->niceNumber( $objLikesResultSet );
			$feedResultSet[ $key ][ 'is_liked' ] = $objLikes->isLiked( $objLikesResultSet[ 0 ][ 'resource_id' ], $user->getIdentity() );
					
			//Get comment count and Format comment same as like count
			$comment = $objComments->fetchComments( $feedResultSet[ $key ][ 'project_id' ] );
			if( $comment == 'null' ) {
				$feedResultSet[ $key ][ 'comment_count' ] = '';
			}
			else {
				$feedResultSet[ $key ][ 'comment_count' ] = $this->niceNumber( count( $comment ) );
			}
		}
		
		$objHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );
		foreach( $feedResultSet as $key => $value ) {
			$feedResultSet[ $key ][ 'Hashtag' ] = $objHashtag->getPostHashtag( $user, $value[ 'project_id' ] );
		}
		
		return $feedResultSet;
				
	}
	
	public function fetchMediaDetails( $projectId, &$coverFileId ) {

		$objMediasTable = Engine_Api::_()->getDbTable( 'medias', 'whmedia' );
		$objMediasDb = $objMediasTable->getAdapter();

		$where     = "project_id = ?";
		$selectedFrom[ 'whmm' ] = 'engine4_whmedia_medias';
		$selectedColumn = array( 'media_id', 'title', 'project_id', 'code', 'is_url AS url' );
		$selectedWhere[ 'id' ] = $projectId;
	
		$objMediasSelect = $objMediasTable->select()
			->from( $selectedFrom, $selectedColumn )
			->where( new Zend_Db_Expr( $this->_quoteInto( $objMediasDb, $where, $selectedWhere ) ) );
		$arrWhmediaRowSet = $objMediasDb->fetchAll( $objMediasSelect );

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
			->where( new Zend_Db_Expr( $this->_quoteInto( $objStorageDb, $where, 
				array( 'id' => $coverFileId ) ) ) );

		$arrStorageRowSet = $objStorageDb->fetchAll( $objStorageSelect );

		//var_dump( $arrStorageRowSet );

		$mediaStorage = array();
		if( count( $arrStorageRowSet ) > 1 ) {
			foreach( $arrStorageRowSet as $key => $value ) {
				if( pathinfo ( $value[ 'storage_path' ], PATHINFO_EXTENSION ) == 'mp4' ) {
					$mediaStorage[ 'code' ] = $value[ 'storage_path' ];
				}
				
				if( ( pathinfo( $value[ 'storage_path' ], PATHINFO_EXTENSION ) == 'jpg' ) || ( pathinfo( $value[ 'storage_path' ], PATHINFO_EXTENSION ) == 'jpeg' ) || ( pathinfo( $value[ 'storage_path' ], PATHINFO_EXTENSION ) == 'png' ) ) {
					if( $value[ "type" ] == "") continue;
					if( $value[ "type" ] == "original" ){
						$mediaStorage[ 'storage_path' ] = $value[ 'storage_path' ];
					}
					else{
						$mediaStorage[ $value[ "type" ] ] =  $value[ 'storage_path' ];
					}
				}
			}
			unset( $arrStorageRowSet );
			$arrStorageRowSet[] = $mediaStorage;
		}

		$arrWhmmResultSet[] = $arrWhmediaRowSet;

		$return = array();		
		foreach( $arrStorageRowSet as $key => $value ) {
			$arrWhmediaRowSet[ 0 ][ 'storage_path' ] = $value[ 'storage_path' ];



			// making sure all the child storage is included
			foreach( $value as $storage_key => $val_storage_path ){
				if( $storage_key == "storage_path" ) continue;
				$arrWhmediaRowSet[ 0 ][ $storage_key ] = $val_storage_path;
			}


			if ( empty( $arrWhmediaRowSet[ 0 ][ 'code' ] ) ) {
				$arrWhmediaRowSet[ 0 ][ 'code' ] = $value[ 'code' ];
			}
			$return = $arrWhmediaRowSet[ 0 ];
		}

		// equavalent to storage path
		$eqStoragePath = array( "thumb.etalon" );
		foreach( $eqStoragePath as $key ){

			if( isset( $return [ $key ] ) ){
				$return[ "storage_path" ] = $return[ $key ];
			}

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
		
		if ( $return[ 'storage_path' ] == 'null' ) {
			$return[ 'storage_path' ] = 'public/no-image-m.jpg';
		}
		
		$size = getimagesize ( $return[ 'storage_path' ] );
		$return[ 'image_width' ] = $size[ 0 ];
		$return[ 'image_height' ] = $size[ 1 ];


		$imageKey = array( "extra_large", "large", "medium", "small" );

		foreach( $imageKey as $imgKey ){

			if( !isset( $return[ $imgKey ] ) ){
				$return[ $imgKey ] = 'null';
			}

		}


		if ( empty( $return[ 'url' ] ) || is_null( $return[ 'url' ] ) ) {
			$return[ 'url' ] = 'null';
		}

		// Lets encode media title to utf8 to make sure there is no bad data
		$return[ 'title' ] = utf8_encode( $return[ 'title' ] );
		
		return $return;
	}
	
	public function fetchMediaDetailsById( $mediaId, &$coverFileId ) {
		$objMediasTable = Engine_Api::_()->getDbTable( 'medias', 'whmedia' );
		$objMediasDb = $objMediasTable->getAdapter();
		
		$where     = "media_id = ?";
		$selectedFrom[ 'whmm' ] = 'engine4_whmedia_medias';
		$selectedColumn = array( 'media_id', 'title', 'project_id', 'code', 'is_url as url' );
		$selectedWhere[ 'id' ] = $mediaId;
		$objMediasSelect = $objMediasTable->select()
			->from( $selectedFrom, $selectedColumn )
			->where( new Zend_Db_Expr( $this->_quoteInto( $objMediasDb, $where, $selectedWhere ) ) );
		$arrWhmediaRowSet = $objMediasDb->fetchAll( $objMediasSelect );

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
			->where( new Zend_Db_Expr( $this->_quoteInto( $objStorageDb, $where, 
				array( 'id' => $coverFileId ) ) ) );

		$arrStorageRowSet = $objStorageDb->fetchAll( $objStorageSelect );

		$arrWhmmResultSet[] = $arrWhmediaRowSet;

		$return = array();
		
		foreach( $arrStorageRowSet as $key => $value ) {
			$arrWhmediaRowSet[ 0 ][ 'storage_path' ] = $value[ 'storage_path' ];
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
	
    /**
     * Convert timestamp into human readable time
	 * 
     * @param  string|int  	$date   		raw timestamp data
     * @param string|int 	$granularity	???
     * @return string
     */		
	private function timeAgo($date, $granularity = 2) {
		$date = strtotime ( $date );
		$difference = time () - $date;
		$periods = array (
				'decade' => 315360000,
				'year' => 31536000,
				'month' => 2628000,
				'week' => 604800,
				'day' => 86400,
				'hour' => 3600,
				'minute' => 60,
				'second' => 1 
		);
		if ($difference < 5) {
			$retval = "posted just now";
			return $retval;
		} else {
			foreach ( $periods as $key => $value ) {
				if ($difference >= $value) {
					$time = floor ( $difference / $value );
					$difference %= $value;
					$retval .= ($retval ? ' ' : '') . $time . ' ';

					$retval .= (($time > 1) ? $key . 's' : $key);
					$granularity --;
				}
				if ($granularity == '0') {
					break;
				}
			}
			
			$arrPrefix = explode( ' ', $retval );
			
			for( $i = 0; $i < 2; $i++ ) {
				array_pop( $arrPrefix );
			}

			return implode( ' ', $arrPrefix );
		}
	}
	
    /**
     * Convert number into a nice number format
	 * ex. 
	 *		1300000 => 1.3 m
	 * 
     * @param  string|int  	$n   		number

     * @return string
     */	
    public function niceNumber($n) {

		if( $n == 'null' ) {
			return '';
		}
		
		if( !is_int( $n ) ) {
			$n = count( $n );
		}
		
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));

        // is this a number?
        if(!is_numeric($n)) return false;

        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),2).' t';
        else if($n>1000000000) return round(($n/1000000000),2).' b';
        else if($n>1000000) return round(($n/1000000),2).' m';
        else if($n>1000) return round(($n/1000),2).' k';

        return number_format($n);
    }

	
	protected function _quoteInto( $objDb, $where, $values = array() ) {
		
		$db = new Zend_Db();

		foreach( $values as $value ) {
			$where = $objDb->quoteInto( $where, $value, '', 1 ); 
		}

		return $where;

	}
	
	public function fetchExploreVideo( $currentUser, $params ) {
		$suffix  = $params[ 'offset' ] .'0';
		$user    = Engine_Api::_()->getDbTable( 'users', 'user' );
		$writers = $user->fetchAll( 'level_id = 6' );

		$project = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
		$arrProjectResult = array();
		/*
		foreach( $writers as $key => $writer ) {
				$arrProjectResult[] = $project->fetchAll( 'user_id ='. $writer->getIdentity() )->toArray();
		}

		// Fetch Media(s)		
		$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
		foreach( $arrProjectResult as $key => $arrProjectRow ) {
			foreach( $arrProjectRow as $innerKey => $projectRow ) {
				$resultSetMedia = $objMedia->fetchMediaDetails( $projectRow[ 'project_id' ], $projectRow[ 'cover_file_id' ] );
				$arrProjectResult[ $key ][ $innerKey ][ 'Media' ] = $resultSetMedia;
			}
		}
		*/
		
		foreach( $writers as $key => $writer ) {
			$arrProjectResult[] = $project->select()
										  ->from( array( 'p' => 'engine4_whmedia_projects' ) )
										  ->joinLeft( array( 'm' => 'engine4_whmedia_medias' ), 'p.project_id = m.project_id', array( '' ) )
										  ->joinLeft( array( 'f' => 'engine4_storage_files' ), 'm.media_id = f.parent_id', array( '' ) )
										  ->setIntegrityCheck( false )
										  ->where( 'p.user_id ='. $writer->getIdentity() .' AND f.extension = "mp4"')
										  ->limit( 10, $suffix )
										  ->query()
										  ->fetchAll();
		}
		
		// Fetch Media(s)		
		$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
		foreach( $arrProjectResult as $key => $arrProjectRow ) {
			foreach( $arrProjectRow as $innerKey => $projectRow ) {
				$resultSetMedia = $objMedia->fetchMediaDetails( $projectRow[ 'project_id' ], $projectRow[ 'cover_file_id' ] );
				$arrProjectResult[ $key ][ $innerKey ][ 'Media' ] = $resultSetMedia;
			}
		}

		// Fetch user
		$objUser = Engine_Api::_()->getApi( 'user', 'api' );
		foreach( $arrProjectResult as $key => $arrProjectRow ) {
			foreach( $arrProjectRow as $innerKey => $projectRow ) {
				$resultSetUser = $objUser->fetchUserDetails( $projectRow[ 'user_id' ] );
				$arrProjectResult[ $key ][ $innerKey ][ 'User' ] = $resultSetUser;
			}
		}
		
		$objHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );
		foreach( $arrProjectResult as $key => $arrProjectRow ) {
			foreach( $arrProjectRow as $innerKey => $projectRow ) {
				$resultSetHashtag = $objHashtag->getPostHashtag( $currentUser, $projectRow[ 'project_id' ] );
				$arrProjectResult[ $key ][ $innerKey ][ 'Hashtag' ] = $resultSetHashtag;
			}
		}	
	
		//Get Like count and check if user liked the project
		$objLikes = Engine_Api::_()->getApi( 'like', 'api' );	
		$objComments = Engine_Api::_()->getApi( 'comment', 'api' );
		foreach( $arrProjectResult as $key => $arrProjectRow ) {
			foreach( $arrProjectRow as $innerKey => $projectRow ) {
				$objLikesResultSet = $objLikes->fetchLikes( $projectRow[ 'user_id' ], $projectRow[ 'project_id' ] );
				$arrProjectResult[ $key ][ $innerKey ][ 'like_count'] = $this->niceNumber( $objLikesResultSet );
				$arrProjectResult[ $key ][ $innerKey ][ 'like_count_int'] =  $objLikesResultSet == 'null' ? 0 : count( $objLikesResultSet );
				$arrProjectResult[ $key ][ $innerKey ][ 'is_liked' ] = $objLikes->isLiked( $objLikesResultSet[ 0 ][ 'resource_id' ], $currentUser->getIdentity() );


				//Get comment count and Format comment same as like count
				$comment = $objComments->fetchComments( $projectRow[ 'project_id' ] );
				if( $comment == 'null' ) {
					$arrProjectResult[ $key ][ $innerKey ][ 'comment_count' ] = '';
					$arrProjectResult[ $key ][ $innerKey ][ 'comment_count_int' ] = 0;
				}
				else {
					$arrProjectResult[ $key ][ $innerKey ][ 'comment_count' ] = $this->niceNumber( count( $comment ) );
					$arrProjectResult[ $key ][ $innerKey ][ 'comment_count_int' ] = $comment == 'null' ? 0 : count( $comment );
				}				
			}
		}

		$arrVideos = array();
		foreach( $arrProjectResult as $key => $arrProjectRow ) {
			foreach( $arrProjectRow as $innerKey => $projectRow ) {
				if( $projectRow[ 'Media' ][ 'media_code' ] != 'null' ) {
					$arrVideos[ $key ][ $innerKey ] = $projectRow; 
				}
			}
		}		
		
		$return = array();
		foreach( $arrVideos as $key => $arrProjectRow ) {
			foreach( $arrProjectRow as $innerKey => $projectRow ) {
				$return[] = $projectRow;
			}
		 }		
		
		 return array( 'Activity_Feed' => $return );
	}
	
	public function fetchMediaDetail( $projectId ) {	
		$project    = Engine_Api::_()->getApi( 'project', 'api' )->getProject( $projectId );
		$mediaTable = Engine_Api::_()->getDbTable( 'medias', 'api' );		
		$storageTable = Engine_Api::_()->getDbTable( 'files', 'api' );

		$user = Engine_Api::_()->user()->getUser( $project->user_id );
		
		try {
			$media = $mediaTable->getMedia( $project );

			$mediaType = $media->getVideoStructure();

			$img = $this->_imageFactory( $project );

			if ( $img ) {
				$imgDimension = $img->getImageDimension();
			} else {
				$imgDimension[ 'image_width' ] = 0;
				$imgDimension[ 'image_height' ] = 0;
			}

			$media = $media->toArray();
			
			$media[ 'storage_path' ] = $img->getStoragePath();
			if ( $video = $storageTable->getVideo( $media[ 'media_id' ] ) ) {
				$media[ 'type' ] = $mediaType = 'direct';
				$media[ 'code' ] = $video->getStoragePath();
			} else {
				$media[ 'type' ]         = $mediaType[ 'type' ];
				$media[ 'code' ]         = $mediaType[ 'code' ];			
			}
			$media[ 'image_width' ]  = $imgDimension[ 'image_width' ];
			$media[ 'image_height' ] = $imgDimension[ 'image_height' ];
			
			// unset not needed property
			unset( $media[ 'order' ] );
			unset( $media[ 'encode' ] );
			unset( $media[ 'invisible' ] );
			unset( $media[ 'creation_date' ] );
			unset( $media[ 'duration' ] );
			unset( $media[ 'size' ] );
			unset( $media[ 'is_text' ] );
			unset( $media[ 'is_url' ] );
			
		} catch ( Exception $e ) {
			return array();
		}
	
		Api_Helper_Utils::stringnull( $media );	
		return $media;
	}
	
	private function _imageFactory ( $project ) {
		$storageTable = Engine_Api::_()->getDbTable( 'files', 'api' );
		try {
			$img = $storageTable->getImage( $project->getCoverFileId() );
		} catch ( Exception $e ) {
			$img = $storageTable->createRow();

			$data = array(
				'storage_path' => 'public/whshow_thumb.jpg',
				'extension' => 'jpg',
				'mime_major' => 'image',
				'mime_minor' => 'jpeg'
			);

			$img->setFromArray( $data );
		}

		return $img;
	}

}