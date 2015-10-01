<?php
class Api2_Api_Favo extends Core_Api_Abstract {

	public function fetchFeaturedFavoFollowedByAdmin ( $user, $params ) {
		$offset = $params[ 'offset' ];

		$dbTableFavos = Engine_Api::_()->getDbTable( 'favos', 'api2' );

		$favos = $dbTableFavos->readFeaturedFavoFollowedByAdmin( $offset );

		$ret = array();
		foreach( $favos as $key => $favo ) {

			// read project
			$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
			$project = $dbTableProjects->readFeedByProjectId( $favo->project_id );

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectId( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $project->user_id, $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );
			if ( $likeCount  == 'null' ) {
				$likeCount    = '0';
				$likeCountInt = 0;
			} else {
				$likeCountInt = count( $rowLikes );
			}

			// is current user like this feed?
			$isLiked   = $apiLike->isLiked( $rowLikes[ 0 ][ 'resource_id' ], $user->getIdentity() );

			// Get comments
			$apiComment = Engine_Api::_()->getApi( 'comment', 'api' );
			$comment   = $apiComment->fetchComments( $project->project_id );

			if( $comment == 'null' ) {
				$commentCount    = '0';
				$commentCountInt = 0;
			}
			else {
				$commentCount     = Api2_Helpers_Utils::formatNumber( $comment );
				$commentCountInt  = count( $comment );
			}

			$apiFavo = Engine_Api::_()->getApi( 'favo', 'api' );
			$is_followed  = ( int )$apiFavo->isFavoFollowed( $user, array(
				'favcircle_id' => $favo->favcircle_id
			) );

			// Return only the first tag
			$retFavo = '';
			$retFavo[ 'favcircle_id' ] = $favo->favcircle_id;
			$retFavo[ 'title' ] = $favo->title;
			$retFavo[ 'is_followed' ] = $is_followed;

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::execute( $mediaRow[ 'storage_path' ] );

			$feed = new Api2_Model_FeaturedFavo();
			$feed->setProjectId( $project->project_id );
			$feed->setUserId( $project->user_id );
			$feed->setCategoryId( $project->category_id );
			$feed->setTitle( $project->title );
			$feed->setDescription( $project->description );
			$feed->setCreationDate( $creationDate );
			$feed->setProjectViews( $projectViews );
			$feed->setOwnerType( $project->owner_type );
			$feed->setSearch( $project->search );
			$feed->setCoverFileId( $project->cover_file_id );
			$feed->setCommentCount( $commentCount );
			$feed->setIsPublished( $project->is_published );
			$feed->setMedia( $mediaRow );
			$feed->setUser( $resultSetUser );
			$feed->setLikeCount( $likeCount );
			$feed->setLikeCountInt( $likeCountInt );
			$feed->setIsLiked( $isLiked );
			$feed->setCommentCountInt( $commentCountInt );
			$feed->setImageColor( $imageColor );
			$feed->setFavo( $retFavo );

			$ret[][ 'FeaturedFavo' ] = $feed;
		}


		return $ret;
	}

	public function relatedFavo( $user, $params ){
/*
		$ftable = Engine_Api::_()->getDbTable('favcircle', 'whmedia');
        $fname = $ftable->info('name');

	    $fcTable = Engine_Api::_()->getDbTable('favcircleitems', 'whmedia');
        $fcName = $fcTable->info('name');

	    $rtable = Engine_Api::_()->getDbtable('projects', 'whmedia');
	    $rName = $rtable->info('name');

	    $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
	    $tagName = $tagTable->info('name');
	    $select_tags = $tagTable->select();
	    $select_tags->from($tagName, array('tag_id'))
	                ->where($tagName.'.resource_id = ?', $params[ "project_id" ] );

	    $select = $ftable->select()->from($fname, array("*", 'similarity' => 'COUNT(tag_id)'))
    							  ->distinct()
	 							  ->joinInner($fcName, "{$fcName}.favcircle_id={$fname}.favcircle_id", array())
	 							  ->joinLeft($rName, $rName.'.project_id = '.$fcName.'.project_id', array( "project_id" ))
	 							  ->joinLeft($tagName, 'resource_type="whmedia_project" AND ' . $tagName . '.resource_id = ' . $rName.'.project_id', array())
	                              ->where($tagName.".`resource_type` = 'whmedia_project'")
	                              ->where($tagName.".`resource_id` != ?", $params[ "project_id" ] )
	                              ->where($tagName.'.tag_id in (?)', $select_tags)
	                              ->group("{$tagName}.resource_id")
	                              ->order('similarity DESC')
	                              ->order($rName . '.creation_date DESC')
								  ->limit(5)
								  ->setIntegrityCheck( false );
	    $favos = $fcTable->fetchAll($select);


	    $filteredArray = array();
	    foreach( $favos as $key => $favo ) {

	    	// read project
			$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
			$project = $dbTableProjects->readFeedByProjectId( $favo->project_id );

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectId( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $project->user_id, $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );
			if ( $likeCount  == 'null' ) {
				$likeCount    = '0';
				$likeCountInt = 0;
			} else {
				$likeCountInt = count( $rowLikes );
			}

			// is current user like this feed?
			$isLiked   = $apiLike->isLiked( $rowLikes[ 0 ][ 'resource_id' ], $user->getIdentity() );

			// Get comments
			$apiComment = Engine_Api::_()->getApi( 'comment', 'api' );
			$comment   = $apiComment->fetchComments( $project->project_id );

			if( $comment == 'null' ) {
				$commentCount    = '0';
				$commentCountInt = 0;
			}
			else {
				$commentCount     = Api2_Helpers_Utils::formatNumber( $comment );
				$commentCountInt  = count( $comment );
			}


	    	$storagePhoto = Engine_Api::_()->getItem('storage_file', $favo->photo_id );
            $children = $storagePhoto->getChildren();
            $photoArray = array();
            foreach($children as $child){
                $photoArray[$child["type"]] = $child["storage_path"];
            }

            $favoDetails = array(
               	"photos" => $photoArray,
                "title" => $favo->title,
                "category" => $favo->category,
                "favcircle_id" => $favo->favcircle_id
            );

            // get dominant color
			$imageColor = Api2_Helpers_DominantColor::execute( $mediaRow[ 'storage_path' ] );

			$feed = new Api2_Model_FeaturedFavo();
			$feed->setProjectId( $project->project_id );
			$feed->setUserId( $project->user_id );
			$feed->setCategoryId( $project->category_id );
			$feed->setTitle( $project->title );
			$feed->setDescription( $project->description );
			$feed->setCreationDate( $creationDate );
			$feed->setProjectViews( $projectViews );
			$feed->setOwnerType( $project->owner_type );
			$feed->setSearch( $project->search );
			$feed->setCoverFileId( $project->cover_file_id );
			$feed->setCommentCount( $commentCount );
			$feed->setIsPublished( $project->is_published );
			$feed->setMedia( $mediaRow );
			$feed->setUser( $resultSetUser );
			$feed->setLikeCount( $likeCount );
			$feed->setLikeCountInt( $likeCountInt );
			$feed->setIsLiked( $isLiked );
			$feed->setCommentCountInt( $commentCountInt );
			$feed->setImageColor( $imageColor );
			$feed->setFavo( $favoDetails );

			$filteredArray[] = $feed;
	    }

	    return array( "RelatedFavo" => $filteredArray );
*/
	}

	public function fetchFavosByUserId ( $user, $params ) {
		if ( !isset( $params[ 'user_id' ] ) ) {
			$params[ 'user_id' ] = $user->getIdentity();
		}

		$dbTableFavos = Engine_Api::_()->getDbTable( 'favos', 'api2' );

		$params[ 'order' ] = 'fc.favcircle_id DESC';
		$favos = $dbTableFavos->readUserFavos( $user, $params );

		$ret = array();
		foreach( $favos as $favo ) {
			$storageTable = Engine_Api::_()->getDbTable( 'files', 'api' );

			try {
				$file = $storageTable->getFavoImage( $favo->photo_id );

				$storagePath = $file->getStoragePath();

				$dimension   = $file->getImageDimension();
				$imageWidth  = $dimension[ 'image_width' ];
				$imageHeight = $dimension[ 'image_height' ];

			} catch ( Exception $e ) {
				$storagePath = '';
				$imageWidth  = 0;
				$imageHeight = 0;
			}

			$params[ 'favcircle_id' ] = $favo->favcircle_id;

			$isFollowed = (int)$dbTableFavos->isFavoFollowed( $user, $params );


			$ret[ 'favcircle_id' ] = $favo->favcircle_id;
			$ret[ 'user_id' ] = $favo->user_id;
			$ret[ 'title' ] = $favo->title;
			$ret[ 'category' ] = $favo->category;
			$ret[ 'storage_path' ] = $storagePath;
			$ret[ 'image_width' ] = $imageWidth;
			$ret[ 'image_height' ] = $imageHeight;
			$ret[ 'is_followed' ] = $isFollowed;

			$response[] = $ret;
		}
		return array( 'Favos' => $response );

	}

	public function fetchAllByKeyword ( $user, $params ) {

        $dbTableFavo = Engine_Api::_()->getDbTable( 'favos', 'api2' );

        $favos = $dbTableFavo->fetchAllByKeyword( $user, $params );

        $ret = array();
        foreach( $favos as $favo ) {
        	$dbTableFavos = Engine_Api::_()->getDbTable( 'favos', 'api2' );
        	$isFollowed = (int)$dbTableFavos->isFavoFollowed( $user, array(
        		'favcircle_id' => $favo->favcircle_id,
        		'user_id'      => $user->getIdentity()
        	) );


			try {
				$storageTable = Engine_Api::_()->getDbTable( 'files', 'api' );
				$file = $storageTable->getFavoImage( $favo->photo_id );

				$storagePath = $file->getStoragePath();

				$dimension   = $file->getImageDimension();
				$imageWidth  = $dimension[ 'image_width' ];
				$imageHeight = $dimension[ 'image_height' ];

			} catch ( Exception $e ) {
				$storagePath = '';
				$imageWidth  = 0;
				$imageHeight = 0;
			}

			$retFavo = new Api2_Model_Favo();
			$retFavo->setFavCircleId( $favo->favcircle_id );
			$retFavo->setUserId( $favo->user_id );
			$retFavo->setTitle( $favo->title );
			$retFavo->setCategory( $favo->category );
			$retFavo->setPhotoId( $favo->photo_id );
			$retFavo->setStoragePath( $storagePath );
			$retFavo->setImageWidth( $imageWidth );
			$retFavo->setImageHeight( $imageHeight );
			$retFavo->setStatus( 0 );
			$retFavo->setIsFollowed( $isFollowed );

			$ret[] = $retFavo;

        }


        return $ret;

	}

	public function createFavo ( $user, $params ) {
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );

		Engine_Api::_()->user()->setViewer( $user );

		$files = $this->formatToFiles( $params );

		unset( $values[ 'Filedata' ] );
		$arrResultSet = $favo->save( $user, $files, $params );

		$favo = $arrResultSet[ 'data' ];

		list( $width, $height ) = getimagesize( $favo[ 'storage_path' ] );
		$favo[ 'image_width' ]  = $width;
		$favo[ 'image_height' ] = $height;

		return array( 'Favo' => $favo );
	}

	public function createFavo2 ( $user, $params ) {
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );

		Engine_Api::_()->user()->setViewer( $user );

		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/no-image-m.jpg';

		$files[ 'Filedata' ][ 'name' ]     = 'no-image-m.jpg';
		$files[ 'Filedata' ][ 'type' ]     = 'image/jpeg';
		$files[ 'Filedata' ][ 'tmp_name' ] = $path;
		$files[ 'Filedata' ][ 'error' ]    = 0;
		$files[ 'Filedata' ][ 'size' ]     = 0;


		$arrResultSet = $this->_createFavo2( $user, $files, $params );

		$favo = $arrResultSet[ 'data' ];

		list( $width, $height ) = getimagesize( $favo[ 'storage_path' ] );
		$favo[ 'image_width' ]  = $width;
		$favo[ 'image_height' ] = $height;

		return array( 'Favo' => $favo );
	}

	private function _createFavo2( $user, $files, $data ) {
		try {
			$fileName = $files[ 'Filedata' ][ 'tmp_name' ];

			$params = array(
				'parent_type' => "favphoto",
				'parent_id'   => $user->getIdentity(),
				'user_id'     => $user->getIdentity(),
				'name'        => $fileName
			);

            $filesTable = Engine_Api::_()->getDbtable('files', 'storage');

            $extension = ltrim(strrchr(basename($fileName), '.'), '.');
			$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
			$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

			// main
			$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
			$image = Engine_Image::factory();
			$image->open($fileName)
			  ->resize(720, 720)
			  ->write($mainPath)
			  ->destroy();

			// profile thumb
			$profilePath = $path . DIRECTORY_SEPARATOR . $base . '_p.' . $extension;
			$image = Engine_Image::factory();
			$image->open($fileName)
			  ->resize(200, 400)
			  ->write($profilePath)
			  ->destroy();

			// cover_photo
			$coverPhotoPath = $path . DIRECTORY_SEPARATOR . $base . '_cp.' . $extension;
			$image = Engine_Image::factory();
			$image->open($fileName)
			  ->resize(80, 80)
			  ->write($coverPhotoPath)
			  ->destroy();

			// icon image
			$squarePath = $path . DIRECTORY_SEPARATOR . $base . '_is.' . $extension;
			$image = Engine_Image::factory();
			$image->open($fileName);

			$size = min($image->height, $image->width);
			$x = ($image->width - $size) / 2;
			$y = ($image->height - $size) / 2;

			$image->resample($x, $y, $size, $size, 48, 48)
			  ->write($squarePath)
			  ->destroy();

			$iMain = $filesTable->createFile($mainPath, $params);
			$iProfile = $filesTable->createFile($profilePath, $params);
			$iCoverPhoto = $filesTable->createFile($coverPhotoPath, $params);
			$iSquare = $filesTable->createFile($squarePath, $params);

			$iMain->bridge($iProfile, 'thumb');
			$iMain->bridge($iSquare, 'icon');
			$iMain->bridge($iCoverPhoto, 'cover');

			@unlink($mainPath);
			@unlink($profilePath);
			@unlink($squarePath);

            $storagePhoto = Engine_Api::_()->getItem('storage_file', $iMain->file_id);
			$children = $storagePhoto->getChildren();

			$arrayFilter = array("file_id" => $iMain->file_id);
			foreach($children as $child){
				$arrayFilter[$child["type"]] = $child["storage_path"];
			}

		} catch( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}

		try {
			$objFav = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );
			$row = $objFav->createRow();
			$row->user_id  = $user->getIdentity();
			$row->title    = $data[ 'title' ];
			$row->private  = $data[ 'privacy' ];
			$row->photo_id =  $iMain->file_id;
			$row->category = $data[ 'category' ];
			$row->save();
		} catch( Exception $e ) {
			throw new Exception( $e->getMessage() );
		}

		try {
			$dbTableDefaultFavoImage = Engine_Api::_()->getDbTable( 'defaultfavoimage', 'api2' );

			$defaultfavoimage = $dbTableDefaultFavoImage->createRow();
			$defaultfavoimage->favcircle_id = $row->favcircle_id;
			$defaultfavoimage->status = 1;
			$defaultfavoimage->save();
		} catch ( Exception $e ) {

		}

		$result = $row->toArray();
		$result[ 'storage_path' ] = $storagePhoto->storage_path;

		$size = getimagesize ( $result[ 'storage_path' ] );
		$result[ 'image_width' ] = $size[ 0 ];
		$result[ 'image_height' ] = $size[ 1 ];

		unset( $result[ 'photo_id' ] );
		return array(
			'data' => $result,
			'error' => array()
		);
	}

	public function updateFavo ( $user, $params ) {
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );

		Engine_Api::_()->user()->setViewer( $user );

		$files = $this->formatToFiles( $params );

		if ( empty( $params[ 'favcircle_id' ] ) ) {
			throw new Exception( 'Favo not found' );
		}

		unset( $values[ 'Filedata' ] );
		$arrResultSet = $favo->save( $user, $files, $params );

		$favo = $arrResultSet[ 'data' ];

		return array( 'Favo' => $favo );
	}

	public function deleteFavo ( $user, $params ) {
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );
		$arrResultSet = $favo->deleteFavo( $params );

		if ( !empty( $arrResultSet[ 'error' ] ) ) {
			throw new Exception( $arrResultSet[ 'error' ] );
		}

		unset( $arrResultSet[ 'data' ][ 'circle_id' ] );
		return $arrResultSet[ 'data' ];
	}

	public function deleteFavo2 ( $user, $params ) {
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );
		$arrResultSet = $favo->deleteFavo( $params );

		try {
			$dbTableDefaultFavoImage = Engine_Api::_()->getDbTable( 'defaultfavoimage', 'api2' );
			$dbTableDefaultFavoImage->delete( 'favcircle_id = '. $params[ 'favcircle_id' ] );

		} catch ( Exception $e ) {

		}

		if ( !empty( $arrResultSet[ 'error' ] ) ) {
			throw new Exception( $arrResultSet[ 'error' ] );
		}

		unset( $arrResultSet[ 'data' ][ 'circle_id' ] );
		return $arrResultSet[ 'data' ];
	}

	public function fetchFavoPosts ( $user, $params ) {
		$favo = Engine_Api::_()->getApi( 'favo', 'api' );
		$arrResultSet = $favo->fetchFavoPosts( $user, $params );

		$ret = $arrResultSet[ 'data' ][ 'Favo_Feed' ];

		if ( empty( $ret ) ) {
			throw new Exception( 'No results found.' );
		}

		$return = array();

		foreach( $ret as $value ) {

			$feed = new Api2_Model_FeedWithType();
			$feed->setProjectId( $value[ 'project_id' ] );
			$feed->setUserId( $value[ 'user_id' ] );
			$feed->setCategoryId( $value[ 'category_id' ] );
			$feed->setTitle( $value[ 'title' ] );
			$feed->setDescription( $value[ 'description' ] );
			$feed->setCreationDate( $value[ 'creation_date' ] );
			$feed->setProjectViews( $value[ 'project_views' ] );
			$feed->setOwnerType( $value[ 'owner_type' ] );
			$feed->setSearch( $value[ 'search' ] );
			$feed->setCoverFileId( $value[ 'cover_file_id' ] );
			$feed->setCommentCount( $value[ 'comment_count' ] );
			$feed->setIsPublished( $value[ 'is_published' ] );
			$feed->setMedia( $value[ 'Media' ] );
			$feed->setUser( $value[ 'User' ] );
			$feed->setLikeCount( $value[ 'like_count' ] );
			$feed->setLikeCountInt( $value[ 'like_count_int' ] );
			$feed->setIsLiked( $value[ 'is_liked' ] );
			$feed->setCommentCountInt( $value[ 'comment_count_int' ] );
			$feed->setFeedType( $value[ 'feed_type' ] );
			$feed->setImageColor( $value[ 'Image_color' ] );
			$feed->setHashtag( $value[ 'Hashtag' ] );

			$return[] = $feed;

		}

		return array(
			'Feeds' => $return
		);
	}

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

	public function addToFavo ( $user, $params ) {

		if( empty( $params[ 'favcircle_id'] ) || empty( $params[ 'project_id' ] ) ) {
			throw new Exception( 'Missing parameter field' );
		}

		$dbTableFavcircleitems = Engine_Api::_()->getDbtable( 'favcircleitems', 'whmedia' );

		if( Engine_Api::_()->getApi( 'favo', 'api' )->isFollowed( $params ) ) {
			throw new Exception( 'Feed already added' );
		}

		$row = $dbTableFavcircleitems->createRow();
		$row->favcircle_id = $params[ 'favcircle_id' ];
		$row->user_id      = $user->getIdentity();
		$row->project_id   = $params[ 'project_id' ];
		$row->save();

		$dbTableFavCircle = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );

		$select = $dbTableFavCircle->select();
		$select->where( 'favcircle_id = ?', $params[ 'favcircle_id' ] );
		$row  = $dbTableFavCircle->fetchRow( $select );
		$favo = $row->toArray();

		$status = ( int )Engine_Api::_()->getApi( 'favo', 'api' )->isFollowed( array(
			'project_id'   => $params[ 'project_id' ],
			'favcircle_id' => $params[ 'favcircle_id' ]
		) );

		$dbTableStorage = Engine_Api::_()->getDbTable( 'files', 'storage' );


		$select = $dbTableStorage->select();
		$select->where( 'file_id = ?', $favo[ 'photo_id' ] );
		$rowStorage = $dbTableStorage->fetchRow( $select );
		$storagePath = $rowStorage->storage_path;

		$size = getimagesize ( $storagePath );
		$imageWidth  = $size[ 0 ];
		$imageHeight = $size[ 1 ];


		$return[ 'message' ] = 'Added';
		$return[ 'Favo' ] = $favo;
		$return[ 'Favo' ][ 'status' ] = $status;
		$return[ 'Favo' ][ 'storage_path' ] = $storagePath;
		$return[ 'Favo' ][ 'image_width' ] = $imageWidth;
		$return[ 'Favo' ][ 'image_height' ] = $imageHeight;


		return $return;
	}

	public function removeFromFavo ( $user, $params ) {

		if( empty( $params[ 'favcircle_id'] ) || empty( $params[ 'project_id' ] ) ) {
			throw new Exception( 'Missing parameter field' );
		}

		$dbTableFavcircleitems = Engine_Api::_()->getDbtable( 'favcircleitems', 'whmedia' );
		if( Engine_Api::_()->getApi( 'favo', 'api' )->isFollowed( $params ) ) {
			$select = $dbTableFavcircleitems->select();
			$select->where( 'project_id = ?', $params[ 'project_id' ] );
			$select->where( 'favcircle_id = ?', $params[ 'favcircle_id' ] );

			$rowSet = $dbTableFavcircleitems->fetchRow( $select );

			$dbTableFavcircleitems->delete( 'favcircleitem_id ='. $rowSet->favcircleitem_id );

			$return = array();
			$return[ 'message' ] = 'Removed';
			$return[ 'Favo' ] = array();
		} else {
			throw new Exception( 'Feed not found' );
		}

		return $return;
	}

	public function followFavo( $user, $params ) {
        if ( empty( $params[ 'favcircle_id' ] ) || empty( $params[ 'user_id' ] ) ) {

			return array(
				'data' => array(),
				'error' => array( 'Missing parameter' )
			);

        }

		$followFavo = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );
		$message = '';

		$dbTableFavos = Engine_Api::_()->getDbTable( 'favos', 'api2' );
		$isFollowed = (int)$dbTableFavos->isFavoFollowed( $user, $params );

		if( $isFollowed ) {
			throw new Exception( 'Favo already followed' );
		}

		$row = $followFavo->createRow();

		$row->user_id      = $params[ 'user_id' ];
		$row->follower_id  = $user->getIdentity();
		$row->favcircle_id = $params[ 'favcircle_id' ];

		$row->save();

		$arrResultSet = $row->toArray();
		$message = 'Followed';

		return array(
				'message' => $message,
				'Favo'    => $arrResultSet
		);
	}

	public function unFollowFavo( $user, $params ) {
        if ( empty( $params[ 'favcircle_id' ] ) || empty( $params[ 'user_id' ] ) ) {

			return array(
				'data' => array(),
				'error' => array( 'Missing parameter' )
			);

        }

		$followFavo = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );
		$message = '';

		$dbTableFavos = Engine_Api::_()->getDbTable( 'favos', 'api2' );
		$isFollowed = (int)$dbTableFavos->isFavoFollowed( $user, $params );

		if( !$isFollowed ) {
			throw new Exception( 'Favo not yet followed' );
		}

		$row = $followFavo->fetchRow( 'favcircle_id ='. $params[ 'favcircle_id' ] .' AND follower_id = '. $params[ 'user_id']  );
		$followFavo->delete( 'followfav_id = '. $row->followfav_id );

		$arrResultSet = new StdClass();
		$message = 'Unfollowed';

		return array(
				'message' => $message,
				'Favo'    => $arrResultSet
		);
	}

}
