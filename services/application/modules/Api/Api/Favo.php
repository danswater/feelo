<?php
class Api_Api_Favo extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	public function isFollowed( $params ) {
		$objFollow = Engine_Api::_()->getDbTable( 'favcircleitems', 'whmedia' );
		$objFollowAdapter = $objFollow->getAdapter();
		try {
			$select = $objFollow->select()
				->where( 'project_id =' . $params[ 'project_id' ] .' AND favcircle_id ='. $params[ 'favcircle_id' ] );

			$rowSet = $objFollowAdapter->fetchAll( $select );

		} catch ( Exception $e ) {
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
		}

		if( count( $rowSet ) > 0 ) {
			return true;
		}
		else {
			return false;
		}
	}

	public function toggle( $user, $params ) {

		if( empty( $params ) ) {
			return array(
				'data' => array(),
				'error' => array( 'Missing parameter field' )
			);
		}

		$objFollow = Engine_Api::_()->getDbtable( 'favcircleitems', 'whmedia' );
		$objFollowAdapter = $objFollow->getAdapter();
		if( $this->isFollowed( $params ) ) {
			try {
				$select = $objFollow->select()
					->where( 'project_id =' . $params[ 'project_id' ] .' AND favcircle_id ='. $params[ 'favcircle_id' ] );

				$rowSet = $objFollowAdapter->fetchAll( $select );
				$first = current( $rowSet );
				$objFollow->delete( 'favcircleitem_id ='. $first[ 'favcircleitem_id' ] );
			} catch ( Exception $e ) {
				return array(
					'data' => array(),
					'error' => array( $e->getMessage() )
				);
			}
			$return = array();
			$return[ 'message' ] = 'Removed';
			$return[ 'Fav' ] = array();
		}
		else {
			try{
				$row = $objFollow->createRow();
				$row->favcircle_id = $params[ 'favcircle_id' ];
				$row->user_id = $user->getIdentity();
				$row->project_id = $params[ 'project_id' ];
				$row->save();
				$return[ 'message' ] = 'Added';

				$objFav = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );
				$row = $objFav->fetchRow( $objFav->select()->where( 'favcircle_id = '. $params[ 'favcircle_id' ] ) );
				$return[ 'Fav' ] = $row->toArray();

				$return[ 'Fav' ][ 'status' ] = ( int )$this->isFollowed( array(
					'project_id' => $params[ 'project_id' ],
					'favcircle_id' => $params[ 'favcircle_id' ]
				) );

				$objStorage = Engine_Api::_()->getDbTable( 'files', 'storage' );

				$rowStorage = $objStorage->fetchRow( $objStorage->select()->where( 'file_id ='. $return[ 'Fav' ][ 'photo_id' ] ) );
				$return[ 'Fav' ][ 'storage_path' ] = $rowStorage->storage_path;

				$size = getimagesize ( $return[ 'Fav' ][ 'storage_path' ] );
				$return[ 'Fav' ][ 'image_width' ] = $size[ 0 ];
				$return[ 'Fav' ][ 'image_height' ] = $size[ 1 ];

			} catch ( Exception $e ) {
				return array(
					'data' => array(),
					'error' => array( $e->getMessage() )
				);
			}
		}

		return array(
			'data' => $return,
			'error' => array()
		);
	}

	public function save ( $user, $files, $data ) {
		if ( isset( $data[ 'favcircle_id' ] ) ) {
			return $this->edit( $user, $files, $data );
		}

		return $this->create( $user, $files, $data );
	}

	public function create( $user, $files, $data ) {
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
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
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
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
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

	public function edit( $user, $files, $data ) {
		if( $files ) {
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
				return array(
					'data' => array(),
					'error' => array( $e->getMessage() )
				);
			}
		}

		try {
			$objFav = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );
			$row = $objFav->fetchRow( $objFav->select()->where( 'favcircle_id = '. $data[ 'favcircle_id' ] ) );
			$row->user_id  = $user->getIdentity();
			$row->title    = $data[ 'title' ];
			$row->private  = $data[ 'privacy' ];
			$row->photo_id = $storagePhoto ? $storagePhoto->file_id : $row->photo_id;
			$row->category = $data[ 'category' ];
			$row->save();

			$objFileStorage = Engine_Api::_()->getDbtable('files', 'storage');
			$rowStorage = $objFileStorage->fetchRow( $objFileStorage->select()->where( 'file_id = '. $row->photo_id  ) );
		} catch( Exception $e ) {
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
		}

		$result = $row->toArray();
		$result[ 'storage_path' ] = $storagePhoto->storage_path ? $storagePhoto->storage_path : $rowStorage->storage_path;

		$size = getimagesize ( $result[ 'storage_path' ] );
		$result[ 'image_width' ] = $size[ 0 ];
		$result[ 'image_height' ] = $size[ 1 ];

		unset( $result[ 'photo_id' ] );
		return array(
			'data' => $result,
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

		$result["message"] = "Successfully deleted";
		$result["circle_id"] = $params[ 'favcircle_id' ];

		return array(
			'data'  => $result,
			'error' => array()
		);
	}

	public function fetchFavoPosts( $user, $params ) {
		try {
			$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
			$arrResultSet = $projectFeed->fetchFeed( $user, 'favo', $params[ 'offset' ], $params[ 'favcircle_id' ] );
		} catch ( Exception $e ) {
			return array(
				'data' => array(
					'Favo_Feed' => array()
				),
				'error' => array( 'No results found' )
			);
		}

		 if( empty( $arrResultSet[ 'Favo_Feed' ] ) ) {
			return array(
				'data'  => array( 'Favo_Feed' => array() ),
				'error' => array( 'No results found' )
			);
		 }

		return array(
			'data' => $arrResultSet,
			'error' => array()
		);
	}

	public function fetchFavoPosts2( $user, $params ) {
		try {
			$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
			$arrResultSet = $projectFeed->fetchFeed( $user, 'favo', $params[ 'offset' ], $params[ 'favcircle_id' ] );
		} catch ( Exception $e ) {
			return array(
				'data' => array(
					'Favo_Feed' => array()
				),
				'error' => array( 'No results found' )
			);
		}

		 if( empty( $arrResultSet[ 'Favo_Feed' ] ) ) {
			return array(
				'data'  => array( 'Favo_Feed' => array() ),
				'error' => array( 'No results found' )
			);
		 }

		return array(
			'data' => $arrResultSet,
			'error' => array()
		);
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
		if( $this->isFavoFollowed( $user, $params) ) {
			$row = $followFavo->fetchRow( 'favcircle_id ='. $params[ 'favcircle_id' ] .' AND follower_id = '. $params[ 'user_id']  );
			$followFavo->delete( 'followfav_id = '. $row->followfav_id );

			$arrResultSet = new StdClass();
			$message = 'Unfollowed';
		}
		else {
			$row = $followFavo->createRow();

			$row->user_id      = $params[ 'user_id' ];
			$row->follower_id  = $user->getIdentity();
			$row->favcircle_id = $params[ 'favcircle_id' ];

			$row->save();

			$arrResultSet = $row->toArray();
			$message = 'Followed';
		}

		return array(
			'data' => array(
				'message' => $message,
				'Favo'    => $arrResultSet
			),
			'error' => array()
		);
	}

	public function isFavoFollowed( $user, $params ) {
		$followFavo = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );

		$user_id = $user->user_id;
		if( isset( $params[ 'user_id'] ) ){
			$user_id = $params[ 'user_id' ];
		}
		//return ( bool )$followFavo->fetchRow( 'favcircle_id ='. $params[ 'favcircle_id' ] .' AND follower_id = '. $params[ 'user_id'] );

		return ( bool ) $followFavo->fetchRow( $followFavo->select()->where( 'favcircle_id != ?', $params[ 'favcircle_id' ] )->where( 'follower_id = ?', $user_id ) );

	}

	public function fetchExploreFavo( $currentUser, $params ) {
		 //select * from engine4_users where level_id = 6;
		 //select * from engine4_whmedia_favcircle where user_id = 152;
		 $suffix = $params[ 'offset' ] .'0';
		 $user    = Engine_Api::_()->getDbTable( 'users', 'user' );
		 $writers = $user->fetchAll( 'level_id = 6' );

		 $arrResultSet = array();
		 foreach( $writers as $key => $writer ) {
			$favo    = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );
			$favoResultSet = $favo->select()
								  ->where( 'user_id ='. $writer->getIdentity() )
								  ->limit( 10, $suffix )
								  ->query()
								  ->fetchAll();

			foreach( $favoResultSet as $innerKey => $favoRow ) {
				$file    = Engine_Api::_()->getDbTable( 'files', 'storage' );
				$fileRow = $file->fetchRow( 'file_id ='. $favoRow[ 'photo_id' ] );
				$arrResultSet[ $key ][ $innerKey] = $favoRow;
				$arrResultSet[ $key ][ $innerKey ][ 'storage_path' ] = $fileRow->storage_path;

				$size = getimagesize ( $arrResultSet[ $key ][ $innerKey ][ 'storage_path' ] );
				$arrResultSet[ $key ][ $innerKey ][ 'image_width' ] = $size[ 0 ];
				$arrResultSet[ $key ][ $innerKey ][ 'image_height' ] = $size[ 1 ];

				$favocircle = Engine_Api::_()->getApi( 'favo', 'api' );

				$arrResultSet[ $key ][ $innerKey ][ 'Favo' ][ 'is_followed' ]  = ( int )$favocircle->isFavoFollowed( $currentUser, array( 'favcircle_id' => $favoRow[ 'favcircle_id' ] ) );

				unset( $arrResultSet[ $key ][ $innerKey ][ 'photo_id' ] );
			}
		 }

		$return = array();
		 foreach( $arrResultSet as $key => $arrRowSet ) {
			foreach( $arrRowSet as $innerKey => $innerValue ) {
				$return[] = $innerValue;
			}
		 }

		 if( empty( $return ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		 }

		 return array(
			'data'  => $return,
			'error' => array()
		 );
	}

	public function fetchExploreHashtag( $user, $params ) {
		$suffix = $params[ 'offset' ] .'0';
		//select * from engine4_users where level_id = 6;
		//select * from engine4_core_tagmaps where tagger_id = 152;
		//select * from engine4_core_tags where tag_id = 1;
		$objUser    = Engine_Api::_()->getDbTable( 'users', 'user' );
		$writers = $objUser->fetchAll( 'level_id = 6' );

		foreach( $writers as $key => $writer ) {
			$tagMaps    = Engine_Api::_()->getDbTable( 'tagMaps', 'core' );
			$hashTag[]    = $tagMaps->select()
								  ->distinct()
							      ->from( array( 'tm' => 'engine4_core_tagmaps' ), array( '' ) )
								  ->joinLeft( array( 't' => 'engine4_core_tags' ), 'tm.tag_id = t.tag_id' )
								  ->where( 'tm.tagger_id ='. $writer->getIdentity() )
								  ->setIntegrityCheck( false )
								  ->limit( 10, $suffix )
								  ->query()
								  ->fetchAll();
		}

		$hashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );
		foreach( $hashTag as $key => $value ) {
			foreach( $value as $innerKey => $innerValue ) {
				if( $hashtag->isFollowed2( $user->getIdentity(), $value ) ) {
					$hashTag[ $key ][ $innerKey ][ 'is_followed' ] = 1;
				}
				else {
					$hashTag[ $key ][ $innerKey ][ 'is_followed' ] = 0;
				}

				$tagMaps2    = Engine_Api::_()->getDbTable( 'tagMaps', 'core' );

				$hashTag[ $key ][ $innerKey ][ 'result_count' ] = count( $tagMaps2->fetchAll( 'tag_id ='. $innerValue[ 'tag_id' ] )->toArray() );
			}
		}

		 foreach( $hashTag as $key => $arrRowSet ) {
			foreach( $arrRowSet as $innerKey => $innerValue ) {
				$return[] = $innerValue;
			}
		 }

		 if( empty( $return ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		 }


		 return array(
			'data'  => $return,
			'error' => array()
		 );

	}

	public function fetchFavo ( User_Model_User $user, $params ) {
		$collection = $this->_feedFactory( $user, $params );
		return $this->_parseFeed( $user, $collection, $params );
	}

	protected function _feedFactory ( $user, $params ) {
		$favoTable = Engine_Api::_()->getDbTable( 'favos', 'api' );

		switch ( $params[ 'method' ] ) {
			case 'fetchFavo' :
				return $favoTable->fetchExploreFavo( $user, $params );
			break;

			case 'fetch':
				return $favoTable->fetchFavoList( $user, $params );
			break;

			case 'fetchUserFavo':
				return $favoTable->fetchUserFavo( $user, $params );
			break;

			case 'fetchFollowedFavo':
				return $favoTable->fetchFollowedFavo( $user, $params );
			break;
		}
	}

	protected function _parseFeed ( $user, $collection, $params ) {
		$storageTable = Engine_Api::_()->getDbTable( 'files', 'api' );

		$response = array();
		foreach( $collection as $key => $favo ) {
			$fav = $favo->toObject();
			try {
				$file = $storageTable->getFavoImage( $favo->photo_id );

				$fav->storage_path = $file->getStoragePath();

				$dimension         = $file->getImageDimension();
				$fav->image_width  = $dimension[ 'image_width' ];
				$fav->image_height = $dimension[ 'image_height' ];
			} catch ( Exception $e ) {
				$fav->storage_path = '';
				$fav->image_width  = 0;
				$fav->image_height = 0;
			}

			$fav->status = 0;
			if ( isset( $params[ 'project_id' ] ) ) {
				$fav->status      = (int)$favo->isProjectAdded( $params[ 'project_id' ] );
			}
			$fav->is_followed = (int)$favo->isFavoFollowed( $user );



			$response[] = $fav;
		}
		return array( 'data' => $response, 'error' => array() );
	}

	/*
		TO BE REMOVED METHODS
	*/
	public function fetch( $user, $values ) {

		$favCircleDbTable = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );

		$favCircleAdapter = $favCircleDbTable->getAdapter();
		try {
			$select = $favCircleDbTable->select()
				->from( array( 'f' => 'engine4_whmedia_favcircle' ), array( 'favcircle_id', 'user_id', 'title', 'photo_id', 'category' ) )
				->where( 'user_id='. $user->getIdentity() )
				->order( array( 'f.favcircle_id DESC' ) );

			if( !is_null( $values[ 'offset' ] ) ) {
				$suffix = $values[ 'offset' ] .'0';
				$select->limit( 10, $suffix );
			}

			$favResultSet = $favCircleAdapter->fetchAll( $select );

			$objStorage = Engine_Api::_()->getDbTable( 'files', 'storage' );
			$objStorageAdapter = $objStorage->getAdapter();

			foreach( $favResultSet as $key => $value ) {
				$storageSelect = $objStorage->select()
					->where( 'file_id = '. $value[ 'photo_id' ] );

				$storageResultSet = $objStorageAdapter->fetchAll( $storageSelect );
				$favResultSet[ $key ][ 'storage_path' ] = $storageResultSet[ 0 ][ 'storage_path' ];

				if( $values[ 'project_id' ] ) {
					$favResultSet[ $key ][ 'status' ] = ( int )$this->isFollowed( array(
						'project_id' => $values[ 'project_id' ],
						'favcircle_id' => $favResultSet[ $key ][ 'favcircle_id' ]
					) );
				}

				$size = getimagesize ( $favResultSet[ $key ][ 'storage_path' ] );
				$favResultSet[ $key ][ 'image_width' ] = $size[ 0 ];
				$favResultSet[ $key ][ 'image_height' ] = $size[ 1 ];

				unset( $favResultSet[ $key ][ 'photo_id' ] );
			}
		} catch( Exception $e ) {
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
		}

		if( empty( $favResultSet ) ) {
			return array(
				'data' => array(),
				'error' => array( 'No results found' )
			);
		}

		return array(
			'data' => $favResultSet,
			'error' => array( '' )
		);
	}

	public function fetchUserFavo( $user, $params ) {
		$favCircle = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );
		$suffix = $params[ 'offset' ] .'0';

		try {
			$select = $favCircle->select()
					->from( array( 'f' => 'engine4_whmedia_favcircle' ), array( 'favcircle_id', 'user_id', 'title', 'photo_id', 'category' ) )
					->where( 'user_id='. $params[ 'user_id' ] )
					->limit( 10, $suffix )
					->order( array( 'f.favcircle_id DESC' ) );

			$resultSet = $favCircle->fetchAll( $select );

			$storage = Engine_Api::_()->getDbTable( 'files', 'storage' );

			$arrResultSet = '';
			foreach( $resultSet as $key => $value ) {
				$storageResultSet = $storage->fetchRow( 'file_id ='. $value->photo_id );

				$arrResultSet[ $key ][ 'favcircle_id' ] = $value->favcircle_id;
				$arrResultSet[ $key ][ 'user_id' ]      = $value->user_id;
				$arrResultSet[ $key ][ 'title' ]        = $value->title;
				$arrResultSet[ $key ][ 'category' ]     = $value->category;
				$arrResultSet[ $key ][ 'storage_path' ] = $storageResultSet->storage_path;
				$arrResultSet[ $key ][ 'is_followed' ]  = ( int )$this->isFavoFollowed( $user, array( 'favcircle_id' => $value->favcircle_id ) );
				$arrResultSet[ $key ][ 'status' ]  = ( int )$this->isFavoFollowed( $user, array( 'favcircle_id' => $value->favcircle_id ) );

				$size = getimagesize ( $arrResultSet[ $key ][ 'storage_path' ] );
				$arrResultSet[ $key ][ 'image_width' ] = $size[ 0 ];
				$arrResultSet[ $key ][ 'image_height' ] = $size[ 1 ];
			}
		} catch( Exception $e ) {
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
		}

		if( empty( $arrResultSet ) ) {
			return array(
				'data' => array(),
				'error' => array( 'No results found' )
			);
		}

		return array(
			'data' => $arrResultSet,
			'error' => array()
		);
	}

	public function fetchFollowedFavo( $user, $params ) {
		$suffix = $params[ 'offset' ] . '0';
		try {
			$followFavo = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );
			$adapter = $followFavo->getAdapter();
			$select = $followFavo->select()
									   ->from( array( 'ff' => 'engine4_whmedia_followfav' ), array( '' ) )
									   ->joinLeft( array( 'f' => 'engine4_whmedia_favcircle' ), 'ff.favcircle_id = f.favcircle_id' )
									   ->where( 'ff.follower_id = '. $user->getIdentity() );

			if( isset( $params[ 'offset' ] ) ) {
				$select->limit( 10, $suffix );
			}

			$select->setIntegrityCheck( false );
			$select->query();
			$arrResultSet = $adapter->fetchAll( $select );

		} catch( Exception $e ) {
			 return array(
				'data'  => array(),
				'error' => array( $e->getMessage() )
			 );
		}

		foreach( $arrResultSet as $key => $value ) {
			$file    = Engine_Api::_()->getDbTable( 'files', 'storage' );
			$fileRow = $file->fetchRow( 'file_id ='. $value[ 'photo_id' ] );
			$arrResultSet[ $key ][ 'storage_path' ] = $fileRow->storage_path;

			$size = getimagesize ( $arrResultSet[ $key ][ 'storage_path' ] );
			$arrResultSet[ $key ][ 'image_width' ] = $size[ 0 ];
			$arrResultSet[ $key ][ 'image_height' ] = $size[ 1 ];

			$favocircle = Engine_Api::_()->getApi( 'favo', 'api' );
			$arrResultSet[ $key ][ 'is_followed' ]  = ( int )$favocircle->isFavoFollowed( $user, array( 'favcircle_id' => $value[ 'favcircle_id' ] ) );

			unset( $arrResultSet[ $key ][ 'photo_id' ] );
			unset( $arrResultSet[ $key ][ $innerKey ][ 'user_id' ] );
		}

		 if( empty( $arrResultSet ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		 }

		return array(
			'data'  => $arrResultSet,
			'error' => array()
		);
	}

	public function fetchCategory( $user ) {
		$favCircleDbTable = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );

		$favCircleAdapter = $favCircleDbTable->getAdapter();
		try {
			$select = $favCircleDbTable->select()
				->distinct()
				->from( array( 'f' => 'engine4_whmedia_favcircle' ), array( 'category' ) )
				->where( 'user_id='. $user->getIdentity() )
				->order( array( 'f.favcircle_id DESC' ) );
			$favResultSet = $favCircleAdapter->fetchAll( $select );

			$objStorage = Engine_Api::_()->getDbTable( 'files', 'storage' );
			$objStorageAdapter = $objStorage->getAdapter();

		} catch( Exception $e ) {
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
		}

		$return = array();
		foreach( $favResultSet as $key => $value ) {
			$return[ $key ] = $value[ 'category' ];
		}

		return array(
			'data' => $return,
			'error' => array()
		);
	}

	public function fetchAFavo ( $user, $params ) {
		$favoTable = Engine_Api::_()->getDbTable( 'favos', 'api' );

		if ( empty( $params[ 'user_id' ] ) ) {
			$params[ 'user_id' ] = $user->getIdentity();
		}

		try {
			$ret = $favoTable->fetchAFavoByCircleId( $user, $params );

			$favoImageDetails = $this->getFavoImageDetails( $ret );

			$resultCount = $favoTable->readAndCountFeedsByFavoId( $user, $params );

			$obj = new Api_Model_FavoExt();
			$obj->setFavcircleId( $ret->favcircle_id );
			$obj->setUserId( $ret->user_id );
			$obj->setTitle( $ret->title );
			$obj->setCategory( $ret->category );
			$obj->setStatus( $ret->status );
			$obj->setIsFollowed( $ret->is_followed );
			$obj->setPhotoId( $ret->photo_id );
			$obj->setStoragePath( $favoImageDetails[ 'storage_path' ] );
			$obj->setImageWidth( $favoImageDetails[ 'image_width' ] );
			$obj->setImageHeight( $favoImageDetails[ 'image_height' ] );
			$obj->setResultCount( $resultCount );
		} catch ( Exception $e ) {
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
		}

		return array(
			'data' => array(
				'Favo' => $obj
			),
			'error' => array()
		);
	}

	protected function getFavoImageDetails ( $obj ) {
		$storageDbTable = Engine_Api::_()->getDbTable( 'files', 'api' );
		$ret = array();
		try {
			$file = $storageDbTable->getFavoImage( $obj->photo_id );

			$ret[ 'storage_path' ] = $file->getStoragePath();

			$dimension         = $file->getImageDimension();
			$ret[ 'image_width' ]  = $dimension[ 'image_width' ];
			$ret[ 'image_height' ] = $dimension[ 'image_height' ];
		} catch ( Exception $e ) {
			$ret[ 'storage_path' ] = '';
			$ret[ 'image_width' ]  = 0;
			$ret[ 'image_height' ] = 0;
		}

		return $ret;
	}


}
