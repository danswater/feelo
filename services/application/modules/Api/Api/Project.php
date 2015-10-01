<?php
class Api_Api_Project extends Core_Api_Abstract {

	// to be append on users offset
	const OFFSET_SUFFIX = 0;

	protected $_indexes = array();
	protected $_projects;
	// custom data to used
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

    /**
     * Build statement based on type ( featured, activity, user posts, specific )
     * and return an Zend Select Object
     *
     * @param  User_Model_User  $user    user object
     * @param  string           $type    type of query to build
     * @param  string|int       $offset  offset start
     * @return Zend_Db_Select
     */
	private function buildMainQuery( User_Model_User $user,  $type, $offset, $otherUser = null, $creation_date = 'week' ) {
	
		switch( $type ) {
		
			case 'featured' :

				$objTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
				$objDb = $objTable->getAdapter();
				$suffix = $offset . self::OFFSET_SUFFIX;	
				
				$objSelect = $objTable->select()
					->distinct()
					->from( array( 'projects' => 'engine4_whmedia_projects' ) )
					->joinLeft( array( 'likes' => 'engine4_core_likes' ), 'likes.resource_id = projects.project_id', array( '' ) )
					->joinLeft( array( 'users' => 'engine4_users' ), 'users.user_id = likes.poster_id', array( '' ) )
					->where( 'users.level_id <= 2' )
					->order( array( 'projects.project_id DESC') )
					->limit( 10, $suffix )
					->setIntegrityCheck( false );	
					
				$objFeedType[ 'objDb' ] = $objTable->getAdapter();
				$objFeedType[ 'objSelect' ] = $objSelect;
								
			break;

			case 'activity' :
			
			  $user_id = $user->getIdentity();
			  
			  $objTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
			  $objDb = $objTable->getAdapter();
			  $suffix = $offset . self::OFFSET_SUFFIX;

			  $objSelect = new Zend_Db_Table_Select( $objTable );
			  $objSelect->distinct()
			  ->from(new Zend_Db_Expr("
				(SELECT DISTINCT followers.* FROM(
					SELECT `project_id`, `creation_date` FROM 
						`engine4_whmedia_stream` AS tmp_stream
							WHERE user_id = $user_id or user_id in ( SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = $user_id )
							GROUP BY tmp_stream.`project_id`
				UNION
				SELECT `tm`.`resource_id` as `project_id`, `fh`.`creation_date` as `creation_date` FROM engine4_whmedia_followhashtag AS fh
				  JOIN engine4_core_tagmaps AS tm ON fh.hashtag_id=tm.tag_id 
				  WHERE follower_id=$user_id ORDER BY project_id DESC
				) AS followers)
			   AS tmp_stream_projects, `engine4_whmedia_projects`
			  ") )
			  ->setIntegrityCheck(FALSE)  
			  ->where(new Zend_Db_Expr('t.`project_id` = `tmp_stream_projects`.`project_id`'))
			  ->limit( 10, $suffix );

			  $objFeedType[ 'objDb' ] = $objDb;
			  $objFeedType[ 'objSelect' ] = $objSelect;			  
			
			break;
			
			case 'user' :
				$user_id = $otherUser;
				
				$objTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
				$objDb = $objTable->getAdapter();
				$suffix = $offset . self::OFFSET_SUFFIX;
				
				$objSelect = $objTable->select()
					->where( 'user_id ='. $user_id )
					->order( array( 'project_id DESC' ) )
					->limit( 10, $suffix );

				$objFeedType[ 'objDb' ] = $objDb;
				$objFeedType[ 'objSelect' ] = $objSelect;					
			
			break;
			
			case 'likes' :
			
			$user_id = $user->getIdentity();

			$objTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
			$objDb = $objTable->getAdapter();
			$suffix = $offset . self::OFFSET_SUFFIX;
			
			$where = 'u.user_id = ? and l.resource_type = ?';
			$params = array( 'user_id' => $user_id, 'resource_type' => 'whmedia_project' );
			$objSelect = $objTable->select()
				->from( array( 'u' => 'engine4_users' ), array( '' ) )
				->joinLeft( array( 'l' => 'engine4_core_likes' ), 'l.poster_id = u.user_id', array( '' ) )
				->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = l.resource_id', array( 'p.*' ) )
				->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $params ) ) )
				->order( array( 'p.project_id DESC' ) )
				->limit( 10, $suffix )
				->setIntegrityCheck( false );
				
			$objFeedType[ 'objDb' ] = $objDb;
			$objFeedType[ 'objSelect' ] = $objSelect;

			break;
			
			case 'hashtag' :
				$objTagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
				$objDb = $objTagMapTable->getAdapter();
				$suffix = $offset . self::OFFSET_SUFFIX;

				$objSelect = $objTagMapTable->select()
					->from( array( 'tagmap' => 'engine4_core_tagmaps' ), array( '' ) )
					->joinLeft( array( 'project' => 'engine4_whmedia_projects' ), 'tagmap.resource_id = project.project_id', array( 'project.*' ) )
					->where( 'tag_id ='. $otherUser )
					->setIntegrityCheck( false )
					->order( array( 'project.project_id DESC') )
					->limit( 10, $suffix );

				$objFeedType[ 'objDb' ] = $objDb;
				$objFeedType[ 'objSelect' ] = $objSelect;
			break;
			
			case 'favo' :
				$objFav = Engine_Api::_()->getDbtable('favcircleitems', 'whmedia');
				$objDb = $objFav->getAdapter();
				$suffix = $offset . self::OFFSET_SUFFIX;

				$objSelect = $objFav->select()
					->from( array( 'f' => 'engine4_whmedia_favcircleitems' ), array( '' ) )
					->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = f.project_id')
					->where( 'f.favcircle_id ='. $otherUser)
					->order( array( 'p.project_id DESC') )
					->limit( 10, $suffix )
					->setIntegrityCheck( false );

				$objFeedType[ 'objDb' ] = $objDb;
				$objFeedType[ 'objSelect' ] = $objSelect;
				
			break;
			
			case 'trending' :
			/*
			$user_id = $user->getIdentity();

			$objTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
			$objDb = $objTable->getAdapter();
			$suffix = $offset . self::OFFSET_SUFFIX;
			
		  $objSelect = new Zend_Db_Table_Select( $objTable );
		  $objSelect->distinct()
					  ->from(new Zend_Db_Expr("
						(SELECT DISTINCT followers.* FROM(
						SELECT `project_id`, `creation_date` FROM 
						  ( SELECT * FROM `engine4_whmedia_stream` WHERE user_id =". $user_id ." OR user_id IN 
							(SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` =". $user_id ." ) 
						  ORDER BY `project_id` DESC) 
						AS tmp_stream GROUP BY tmp_stream.`project_id`
						UNION
						SELECT `tm`.`resource_id` as `project_id`, `fh`.`creation_date` as `creation_date` FROM engine4_whmedia_followhashtag AS fh
						  JOIN engine4_core_tags AS t ON fh.hashtag_id=t.tag_id
						  JOIN engine4_core_tagmaps AS tm ON tm.tag_id=t.tag_id 
						  WHERE follower_id=". $user_id ." ORDER BY project_id DESC
						) AS followers)
					   AS tmp_stream_projects, `engine4_whmedia_projects`
					  ") )
					  ->setIntegrityCheck(FALSE)  
					  //->where( 't.creation_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)' )
					  ->order( 't.project_views desc' )
					  ->limit( 10, $suffix );

				$curr_time = time();
				switch( strtolower( $creation_date ) ){
					case 'today' :
						$objSelect->where( 't.creation_date > DATE_SUB(NOW(), INTERVAL 1 DAY)' );
						break;
					case 'month' :
						$objSelect->where( 't.creation_date > DATE_SUB(NOW(), INTERVAL 1 MONTH)' );
						break;
					default :
						$objSelect->where( 't.creation_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)' );
				}


				$objFeedType[ 'objDb' ] = $objDb;
				$objFeedType[ 'objSelect' ] = $objSelect;
				*/


				$objTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
				$objDb = $objTable->getAdapter();
				$suffix = $offset . self::OFFSET_SUFFIX;	
				
				$objSelect = $objTable->select()
					->distinct()
					->from( array( 'projects' => 'engine4_whmedia_projects' ) )
					->joinLeft( array( 'likes' => 'engine4_core_likes' ), 'likes.resource_id = projects.project_id', array( '' ) )
					->joinLeft( array( 'users' => 'engine4_users' ), 'users.user_id = likes.poster_id', array( '' ) )
					->where( 'users.level_id <= 2' )
					->order( array( 'projects.project_id DESC') )
					->limit( 10, $suffix )
					->setIntegrityCheck( false );

				$objFeedType[ 'objDb' ] = $objDb;
				$objFeedType[ 'objSelect' ] = $objSelect;	

			break;
		
		}
		
		return $objFeedType;
	
	}
	
    /**
     * Fetch all feed based on type ( featured, activity, user posts, specific )
     *
     * @param  User_Model_User  $user    user object
     * @param  string           $type    type of query to build
     * @param  string|int       $offset  offset start
     * @return ResultSet Array
     */	
	public function fetchFeed( User_Model_User $user, $feedType, $offset, $otherUser = null, $creation_date = 'week' ) {

		$arrFeedType = $this->buildMainQuery( $user, $feedType, $offset, $otherUser, $creation_date );		


		//echo "<pre>" . print_r( $arrFeedType[ "objSelect" ]->assemble(), true ) . "</pre>";

		$feedResultSet = $arrFeedType[ 'objDb' ]->fetchAll( $arrFeedType[ 'objSelect' ] );
		
		foreach( $feedResultSet as $key => $value ) {
			$feedResultSet[ $key ][ 'creation_date' ] = $this->timeAgo( $value[ 'creation_date' ] );
			if( $value[ 'project_views' ] == 0 ) {
				$feedResultSet[ $key ][ 'project_views' ] = ''; 
			}
			else {
				$feedResultSet[ $key ][ 'project_views' ] = (string)$value[ 'project_views' ]; 
			}

			if( is_null( $value[ 'cover_file_id' ] ) ) {
				$feedResultSet[ $key ][ 'cover_file_id' ] = 'null';
			}

			$feedResultSet[ $key ][ 'title' ] = utf8_encode( $feedResultSet[ $key ][ 'title' ] ); 
			$feedResultSet[ $key ][ 'description' ] = utf8_encode( $feedResultSet[ $key ][ 'description' ] ); 
			
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
		
		//Get Like count and check if user liked the project and get feed type
		$objLikes = Engine_Api::_()->getApi( 'like', 'api' );
		$objComments = Engine_Api::_()->getApi( 'comment', 'api' );
		foreach( $feedResultSet as $key => $value ) {
		
			$objLikesResultSet = $objLikes->fetchLikes( $feedResultSet[ $key ][ 'user_id' ], $feedResultSet[ $key ][ 'project_id' ] );
			
			$feedResultSet[ $key ][ 'like_count'] = $this->niceNumber( $objLikesResultSet );
			$feedResultSet[ $key ][ 'like_count_int'] =  $objLikesResultSet == 'null' ? 0 : count( $objLikesResultSet );
			$feedResultSet[ $key ][ 'is_liked' ] = $objLikes->isLiked( $objLikesResultSet[ 0 ][ 'resource_id' ], $user->getIdentity() );
					
			//Get comment count and Format comment same as like count
			$comment = $objComments->fetchComments( $feedResultSet[ $key ][ 'project_id' ] );
			if( $comment == 'null' ) {
				$feedResultSet[ $key ][ 'comment_count' ] = '';
				$feedResultSet[ $key ][ 'comment_count_int' ] = 0;
			}
			else {
				$feedResultSet[ $key ][ 'comment_count' ] = $this->niceNumber( count( $comment ) );
				$feedResultSet[ $key ][ 'comment_count_int' ] = $comment == 'null' ? 0 : count( $comment );
			}
		
			$feedResultSet[ $key ][ 'feed_type' ] = Api_Helper_DetermineFeedType::execute( $feedResultSet[ $key ][ 'Media' ] );
			$feedResultSet[ $key ][ 'Image_color' ] = "";
		/*	// get dominant color
			$feedResultSet[ $key ][ 'Image_color' ] = Api2_Helpers_DominantColor::secondExecution( $value[ 'project_id'], $feedResultSet[ $key ][ 'Media' ][ 'storage_path' ] );
		*/
		}

		$objHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );
		foreach( $feedResultSet as $key => $value ) {
			$feedResultSet[ $key ][ 'Hashtag' ] = $objHashtag->getPostHashtag( $user, $value[ 'project_id' ] );
		}
		
		// doh i added this to make sure walay guba nah post
		$filterPost = array();
		foreach( $feedResultSet as $resultSet ){
			
			if( $resultSet[ "cover_file_id" ] == "null" ||
				$resultSet[ "cover_file_id" ] == null || 
				$resultSet[ "cover_file_id" ] == "" )
				continue;

			$filterPost[] = $resultSet;

		}


		if( $feedType == 'user' ) { 
			return array( 'Posts' => $filterPost );
		}
		
		if( $feedType == 'likes' ) {
			return array( 'my_likes' => $filterPost );
		}
		
		if( $feedType == 'favo' ) {
			$dbTableFavos = Engine_Api::_()->getDbTable( 'favos', 'api' );
			$favoCount = $dbTableFavos->readAndcountAllFavosByUserId( $otherUser );

	        $dbTableProjects = Engine_Api::_()->getDbTable( 'favos', 'api2' );
	        $feed      = $dbTableProjects->readRandomFeedByFavoId( $otherUser );

	        // Fetch Media(s)
	        $apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
	        $mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $feed->project_id, $feed->cover_file_id );
			
			return array(
				'Favo_Feed' => $filterPost,
				'favo_post_count' => $favoCount,
				'cover_photo' => $mediaRow->getStoragePath()
			);
		}
		
		$ret[ 'Activity_Feed' ] = $filterPost;
		if ( $feedType == 'hashtag' ) {
			$dbTableHashtags = Engine_Api::_()->getDbTable( 'hashtags', 'api' );

			$hashtagCount = $dbTableHashtags->readAndCountHashtagPostByHashtagId( $otherUser );
			$ret[ 'hashtag_post_count' ] = $hashtagCount;

		}
		/*
		if ( $feedType == 'hashtag' ) {
			$dbTableHashtags = Engine_Api::_()->getDbTable( 'hashtags', 'api' );

			$hashtagCount = $dbTableHashtags->readAndCountHashtagPostByHashtagId( $otherUser );
			
			return array(
				'Hashtag_Feed' => $filterPost,
				'hashtag_post_count' => $hashtagCount
			);

		}
		*/
		
		return $ret;

	}
	
	public function fetchFeedByBox( $user, $boxId, $offset ) {
		try {
			$suffix = $offset . self::OFFSET_SUFFIX;
			$circle = Engine_Api::_()->getDbtable('circleitems', 'whmedia');
			$select = $circle->select()
							 ->from( array( 'c' => 'engine4_whmedia_circleitems' ), array( 'user_id' ) )
							 ->where( 'circle_id ='. $boxId )
							 ->limit( 10, $suffix );

			$circleResultSet = $circle->fetchAll( $select );
			
			$project = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
			$projectAdapter = $project->getAdapter();
			
			
			foreach( $circleResultSet as $key => $value ) {
				$projectSelect = $project->select()
									  ->where( 'user_id ='. $value[ 'user_id' ] .' AND is_published = 1' )
									  ->order( array( 'project_id DESC' ) )
									  ->limit( 10, $suffix );
									  
				$projectRowSet = $projectAdapter->fetchAll( $projectSelect );

				if( empty( $projectRowSet ) ) {
					unset( $projectRowSet );
				}
				else {
					$projectResultSet[] = $projectRowSet;						
				}
			}
			
			$data = array();
			foreach( $projectResultSet as $projectRowSet ) {
				foreach( $projectRowSet as $key ) {
					$data[] = $key;
				}
			}
			
			foreach( $data as $key => $value ) {
				$data[ $key ][ 'creation_date' ] = $this->timeAgo( $value[ 'creation_date' ] );
				if( $value[ 'project_views' ] == 0 ) {
					$data[ $key ][ 'project_views' ] = ''; 
				}
				else {
					$data[ $key ][ 'project_views' ] = (string)$value[ 'project_views' ]; 
				}

				if( is_null( $value[ 'cover_file_id' ] ) ) {
					$data[ $key ][ 'cover_file_id' ] = 'null';
				}
				
				// Fetch Media(s)	
				$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
				$resultSetMedia = $objMedia->fetchMediaDetails( $value[ 'project_id' ], $value[ 'cover_file_id' ] );
				$data[ $key ][ 'Media' ] = $resultSetMedia;
				
				// Fetch user
				$objUser = Engine_Api::_()->getApi( 'user', 'api' );
				$resultSetUser = $objUser->fetchUserDetails( $value[ 'user_id' ] );
				$data[ $key ][ 'User' ] = $resultSetUser;
				
				//Get Like count and check if user liked the project
				$objLikes = Engine_Api::_()->getApi( 'like', 'api' );
				$objComments = Engine_Api::_()->getApi( 'comment', 'api' );
				$objLikesResultSet = $objLikes->fetchLikes( $value[ 'user_id' ], $value[ 'project_id' ] );
				$data[ $key ][ 'like_count'] = $this->niceNumber( $objLikesResultSet );
				$data[ $key ][ 'like_count_int'] =  $objLikesResultSet == 'null' ? 0 : count( $objLikesResultSet );
				$data[ $key ][ 'is_liked' ] = $objLikes->isLiked( $objLikesResultSet[ 0 ][ 'resource_id' ], $user->getIdentity() );
						
				//Get comment count and Format comment same as like count
				$comment = $objComments->fetchComments( $value[ 'project_id' ] );
				if( $comment == 'null' ) {
					$data[ $key ][ 'comment_count' ] = '';
					$data[ $key ][ 'comment_count_int' ] = 0;
				}
				else {
					$data[ $key ][ 'comment_count' ] = $this->niceNumber( count( $comment ) );
					$data[ $key ][ 'comment_count_int' ] = $comment == 'null' ? 0 : count( $comment );
				}
				
				$objHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );
				$data[ $key ][ 'Hashtag' ] = $objHashtag->getPostHashtag( $user, $value[ 'project_id' ] );
			}
		} catch( Exception $e ) {
			print_r( $e ); exit;
		}
		return array( 'Activity_Feed' => $data );		
	}
	
	public function aasort (&$array, $key) {
		$sorter=array();
		$ret=array();
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii]=$va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii]=$array[$ii];
		}
		$array=$ret;
	}
		
    /**
     * Fetch specific feed
     *
     * @param  string|int  $userId     user id
     * @param  string|int  $projectId  project id

     * @return ResultSet Array
     */		
	public function fetchSpecific( $user, $projectId ) {
	
		$objProjectTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );

		$objProjectDb = $objProjectTable->getAdapter();

		$where            = "project_id = ?";
		$params           = array( 'projectId' => $projectId );	
		$objProjectSelect = $objProjectTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objProjectDb, $where, $params ) ) )
			->order( array( 'creation_date DESC') );

		$feedResultSet = $objProjectDb->fetchAll( $objProjectSelect );

		// Do some login in individual data
		foreach( $feedResultSet as $key => $value ) {
			$feedResultSet[ $key ][ 'creation_date' ] = $this->timeAgo( $value[ 'creation_date' ] );
			if( $value[ 'project_views' ] == 0 ) {
				$feedResultSet[ $key ][ 'project_views' ] = ''; 
			}
			else {
				$feedResultSet[ $key ][ 'project_views' ] = (string)$value[ 'project_views' ]; 
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
		
		$objComments = Engine_Api::_()->getApi( 'comment', 'api' );
		$feedResultSet[ 0 ][ 'Comments' ] = $comments = $objComments->fetchComments( $feedResultSet[ 0 ][ 'project_id' ], 0 );

		//Get comment count and Format comment same as like count
		if( $comment == 'null' || $comment == null ) {
			$feedResultSet[ $key ][ 'comment_count' ] = 0;
		}
		else {
			$feedResultSet[ $key ][ 'comment_count' ] = $this->niceNumber( count( $comment ) );
		}			
		
		//Get Like count and check if user liked the project
		$objLikes = Engine_Api::_()->getApi( 'like', 'api' );		
		foreach( $feedResultSet as $key => $value ) {
			$feedResultSet[ $key ][ 'Likes' ] = $objLikes->fetchLikes( $feedResultSet[ $key ][ 'user_id' ], $feedResultSet[ $key ][ 'project_id' ] );
			$feedResultSet[ $key ][ 'like_int' ] = is_null( $feedResultSet[ $key ][ 'Likes' ] ) ? 0 : count( $feedResultSet[ $key ][ 'Likes' ] );
			$feedResultSet[ $key ][ 'like_count'] = $this->niceNumber( $feedResultSet[ $key ][ 'Likes' ] );
			$feedResultSet[ $key ][ 'is_liked' ] = $objLikes->isLiked( $feedResultSet[ $key ][ 'project_id' ], $user->getIdentity() );
		
			
		}
		
		$objHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );
		$feedResultSet[ $key ][ 'Hashtag' ] = $objHashtag->getPostHashtag( $user, $projectId );		
		
		return array( 'Posts' => $feedResultSet );		
	
	}

	public function newSpecificFeed ( $user, $projectId ) {

		$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
	
		$project = $dbTableProjects->readFeedByProjectId( $projectId );

		if ( !$project ) {
			throw new Exception( 'No results found' );
		}

		$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
		$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

		// Fetch Media(s)	
		$apiMedias = Engine_Api::_()->getApi( 'whmedia', 'api' );
		$mediaRow = $apiMedias->fetchMediaDetails( $project->project_id, $project->cover_file_id );

		// Fetch User
		$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
		$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

		// Get Like count and check if user liked the project
		$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
		$rowLikes = $apiLike->fetchLikes( $project->user_id, $project->project_id );

		// get like count
		$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );
		if ( empty( $likeCount ) ) {
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

		$testComment = $apiComment->fetchCommentsWithLimit( $project->project_id, 0, 5 );
		foreach( $testComment as $key => $c ) {
			$newComment = $apiComment->findTagsInComment( $c[ 'body' ] );
			$testComment[ $key ][ 'body' ] = $newComment[ 'body' ];
			$testComment[ $key ][ 'tag_userids' ] = $newComment[ 'tag_userids' ];
		}
		$comment = $testComment;

		// Determin feed type
		$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

		// Get hashtag
		$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

		// Return only the first tag
		$hashTag = '';
		$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
		if ( $hashTags ) {
			$hashTag = current( $hashTags );
		}

		// get dominant color
		$imageColor = Api2_Helpers_DominantColor::execute( $mediaRow[ 'storage_path' ] );

		// update view count
		$updatedViewCount = $dbTableProjects->updateProjectViews( $project->project_id );

		// Instantiate to Api2_Model_Media object

        $media = new Api2_Model_Media();
        $media->initWithValues( $mediaRow );
        
		$feed = new Api2_Model_FeedWithDetails();
		$feed->setProjectId( $project->project_id );
		$feed->setUserId( $project->user_id );
		$feed->setCategoryId( $project->category_id );
		$feed->setTitle( $project->title );
		$feed->setDescription( $project->description );
		$feed->setCreationDate( $creationDate );
		$feed->setProjectViews( $updatedViewCount );
		$feed->setOwnerType( $project->owner_type );
		$feed->setSearch( $project->search );
		$feed->setCoverFileId( $project->cover_file_id );
		$feed->setIsPublished( $project->is_published );
		$feed->setLikeCount( $likeCount );
		$feed->setLikeCountInt( $likeCountInt );
		$feed->setIsLiked( $isLiked );
		$feed->setCommentCount( $commentCount );
		$feed->setCommentCountInt( $commentCountInt );
		$feed->setFeedType( $feedType );
		$feed->setImageColor( $imageColor );
		$feed->setLikes( $rowLikes );
		$feed->setMedia( $media );
		$feed->setUser( $resultSetUser );
		$feed->setComments( $comment );
		$feed->setHashtag( $hashTags );

		$ret[] = $feed;


		return $ret;
	}
	
	public function feedDetails( $user, $post ) {
	
		$post[ 'hashtags' ] = rtrim( $post[ 'hashtags' ], ',' );
		$post[ 'hashtags' ] = explode( ',', $post[ 'hashtags' ] );
		$post[ 'whtags' ] = $post[ 'hashtags' ];
		unset( $post[ 'hashtags' ] );

        //$validate_tags = array_filter(preg_split('/[ #]+/', $post['whtags']), "trim");
	
        //$is_valid = $form->isValid($this->getRequest()->getPost());

        if( count($post[ 'whtags' ] ) > 3 ){
			return array(
				'data' => array(),
				'error' => array( 'Hashtag limit to 3' )
			);
        }else{

			$projectTable = Engine_Api::_()->getDbtable('projects', 'whmedia');

			//$values = $form->getValues();

			//$viewer = Engine_Api::_()->user()->getViewer();

			// Begin database transaction
			$db = $projectTable->getAdapter();
			$db->beginTransaction();

			try {

				$projectTableRow = $projectTable->find($post['project_id'])->current();	
				$projectTableRow->setFromArray($post);
				$projectTableRow->user_id = $user->getIdentity();
				$projectTableRow->owner_type = $user->getType();
				$projectTableRow->is_published = 1;
				$projectTableRow->save();

				// Auth
				$auth = Engine_Api::_()->authorization()->context;
				$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

				if (empty($post['auth_view'])) {
					$post['auth_view'] = 'everyone';
				}

				if (empty($post['auth_comment'])) {
					$post['auth_comment'] = 'everyone';
				}

				$viewMax = array_search($post['auth_view'], $roles);
				$commentMax = array_search($post['auth_comment'], $roles);

				foreach ($roles as $i => $role) {
					$auth->setAllowed($projectTableRow, $role, 'view', ($i <= $viewMax));
					$auth->setAllowed($projectTableRow, $role, 'comment', ($i <= $commentMax));
				}

				$auth->setAllowed($projectTableRow, 'everyone', 'allow_d_orig', (isset($post[ 'allow_download_original' ] ) and (bool) $form[ 'allow_download_original' ] ) );

				// Add tags
				//$tags = array_filter(preg_split('/[ #]+/', $values['whtags']), "trim");
				if (count($post[ 'whtags' ]))
					$projectTableRow->tags()->addTagMaps($user, $post[ 'whtags' ] );
								//print_r( $projectTableRow ); exit;
				$db->commit();

				Engine_Api::_()->getDbtable('stream', 'whmedia')->addStream($projectTableRow);
			} catch (Exception $e) {
				$db->rollBack();
				//throw $e;
				return $e->getMessage();
			}
			
			$project = $projectTableRow->toArray();
			
			$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
			$project[ 'Media' ] = $objMedia->fetchMediaDetails( $project[ 'project_id' ], $project[ 'cover_file_id' ] );			
			
			$objUser = Engine_Api::_()->getApi( 'user', 'api' );			
			$project[ 'User' ] = $objUser->fetchUserDetails( $user->getIdentity() );
			
			return array(
				'data' => $project,
				'error' => array()
			);

        }	
	}
	
	public function uploadFeed( $user, $files ) {

        try {

            if (!isset($files['Filedata']) ) {
                throw new Engine_Exception('Invalid Upload or file too large');
				return $e->getMessage();
            }
        } catch (Exception $e) {
			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
        }
			

        try {
            $newProject = $this->createProject();

            Engine_Api::_()->core()->setSubject($newProject);

            $file_id = Engine_Api::_()->whmedia()->uploadmedia($files['Filedata']);

            $media = Engine_Api::_()->getItem('whmedia_media', $file_id);

            $newProject->cover_file_id = $file_id;
            $newProject->save();

            if (Engine_Api::_()->core()->getSubject()->is_published) {
                $wh_session = new Zend_Session_Namespace('whmedia_new_media');
                $session_key = 'activity_' . Engine_Api::_()->core()->getSubject()->getIdentity();
                $api = Engine_Api::_()->getDbtable('actions', 'activity');
                if (!isset($wh_session->$session_key)) {
                    $wh_session->$session_key = $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), Engine_Api::_()->core()->getSubject(), 'whmedia_media_new', null);
                } else {
                    $action = $wh_session->$session_key;
                }
                $api->attachActivity($action, $media, 1 );
            }
            $media->save();
			
			$project = $newProject->toArray();
			
			$objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
			$project[ 'Media' ] = $objMedia->fetchMediaDetailsById( $file_id, $project[ 'cover_file_id' ] );

			$objUser = Engine_Api::_()->getApi( 'user', 'api' );			
			$project[ 'User' ] = $objUser->fetchUserDetails( $user->getIdentity() );
			
			return array(
				'data' => $project,
				'error' => array()
			);

        } catch (Exception $e) {

			return array(
				'data' => array(),
				'error' => array( $e->getMessage() )
			);
        }	
	}
	
    public function createProject() {
        $newProject = Engine_Api::_()->getItemTable('whmedia_project')->createRow();
        $newProject->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $newProject->owner_type = 'user';
        $newProject->search = 1;
        $newProject->is_published = 0;
        $newProject->save();

        return $newProject;
    }
	
	public function countAllPosts( $user ) {
		$user_id = $otherUser;
				
		$objTable = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );
		$objDb = $objTable->getAdapter();
		$suffix = $offset . self::OFFSET_SUFFIX;
				
		$objSelect = $objTable->select()
			->where( 'user_id ='. $user->getIdentity() );

		$result = $objDb->fetchAll( $objSelect );

		if( empty( $result ) ) {
			return 0;
		}
		
		return count( $result );
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
	
	public function addFeed() {
        $projectTable = Engine_Api::_()->getDbtable('projects', 'whmedia');

        $values = $form->getValues();
		$viewer = Engine_Api::_()->user()->getViewer();

		// Begin database transaction
		$db = $projectTable->getAdapter();
		$db->beginTransaction();

		try {

			$projectTableRow = $projectTable->find($values['project_id'])->current();
			$projectTableRow->setFromArray($values);
			$projectTableRow->user_id = $viewer->getIdentity();
			$projectTableRow->owner_type = $viewer->getType();
			$projectTableRow->is_published = 1;
			$projectTableRow->save();

			// Auth
			$auth = Engine_Api::_()->authorization()->context;
			$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

			if (empty($values['auth_view'])) {
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment'])) {
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role) {
				$auth->setAllowed($projectTableRow, $role, 'view', ($i <= $viewMax));
				$auth->setAllowed($projectTableRow, $role, 'comment', ($i <= $commentMax));
			}
			$auth->setAllowed($projectTableRow, 'everyone', 'allow_d_orig', (isset($form->allow_download_original) and (bool) $form->allow_download_original->getValue()));
			// Add tags
			$tags = array_filter(preg_split('/[ #]+/', $values['whtags']), "trim");
			if (count($tags))
				$projectTableRow->tags()->addTagMaps($viewer, $tags);

			$db->commit();

			Engine_Api::_()->getDbtable('stream', 'whmedia')->addStream($projectTableRow);
		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}

		$this->_helper->redirector->gotoRoute(array('project_id' => $projectTableRow->project_id), 'whmedia_project_view', true);	
	}
	
	private function oldFetchMedia() {
		$objMediasTable = Engine_Api::_()->getDbTable( 'medias', 'whmedia' );
		$objMediasDb = $objMediasTable->getAdapter();
		
		// Fetch Media(s)
		
		foreach( $feedResultSet as $key => $value ) {
			$where     = "project_id = ?";			
			$objMediasSelect = $objMediasTable->select()
				->from( array( 'whmm' => 'engine4_whmedia_medias' ), array( 'media_id', 'title', 'project_id', 'code' ) )
				->where( new Zend_Db_Expr( $this->_quoteInto( $objMediasDb, $where, array( 'id' => $value[ 'project_id' ] ) ) ) );

			$arrWhmediaRowSet = $objMediasDb->fetchAll( $objMediasSelect );

			// Need to optimize
			// Removed the slash first so we can implement unserializiation
			// unserialize the code
			if( $arrWhmediaRowSet[ 0 ][ 'code' ] !== null ) {
				$arrCode = explode( '"', $arrWhmediaRowSet[ 0 ][ 'code' ] );
				if( count( $arrCode ) == 1 ) {
					$arrWhmediaRowSet[ 0 ][ 'codes' ][ 'type' ] = 'direct';
					$arrWhmediaRowSet[ 0 ][ 'codes' ][ 'code' ] = $arrWhmediaRowSet[ 0 ][ 'code' ];
				}
				else {
					$arrWhmediaRowSet[ 0 ][ 'codes' ][ $arrCode[ 1 ] ] = $arrCode[ 3 ];
					$arrWhmediaRowSet[ 0 ][ 'codes' ][ $arrCode[ 5 ] ] = $arrCode[ 7 ];	
				}
			}
			else {
				$arrWhmediaRowSet[ 0 ][ 'code' ] = 'null';
				$arrWhmediaRowSet[ 0 ][ 'codes' ] = new stdClass();		
			}
			

			// convert null into null string
			if( $value[ 'cover_file_id'] === '' ) {
				$feedResultSet[ $key ][ 'cover_file_id'] = 'null';
			}

			$arrWhmmResultSet[] = $arrWhmediaRowSet;		

		}
		
		if( empty( $feedResultSet ) ) {
			 return array( 'Activity_Feed' => array(
				'data' => 'null'
			 ) );
		}

		$objStorageTable = Engine_Api::_()->getDbTable( 'files', 'storage' );
		$objStorageDb = $objStorageTable->getAdapter();		

		// Fetch Photos/Videos
		foreach( $feedResultSet as $key => $value ) {
			$where     = "parent_id = ?";			
			$objStorageSelect = $objStorageTable->select()
				->from( array( 'sf' => 'engine4_storage_files' ) )
				->where( new Zend_Db_Expr( $this->_quoteInto( $objStorageDb, $where, 
					array( 'id' => $value[ 'cover_file_id' ] ) ) ) );

			$arrStorageRowSet = $objStorageDb->fetchAll( $objStorageSelect );

			$arrStorageResultSet[] = $arrStorageRowSet;
		}

		foreach( $arrStorageResultSet as $keyResultSet => $valueResultSet ) {
			foreach( $valueResultSet as $key => $value ) {
				$arrWhmmResultSet[ $keyResultSet ][ 0 ][ 'storage_path' ] = $value[ 'storage_path' ];
				$feedResultSet[ $keyResultSet ][ 'Media' ] = $arrWhmmResultSet[ $keyResultSet ][ 0 ];
			}
		}
		
		foreach( $feedResultSet as $resultSetKey => $resultSetValue ) {

			if( $resultSetValue[ 'Media' ][ 'code' ] !== 'null' ) {
				$feedResultSet[ $resultSetKey ][ 'Media' ][ 'media_code' ] = $resultSetValue[ 'Media' ][ 'codes' ][ 'code' ];
				$feedResultSet[ $resultSetKey ][ 'Media' ][ 'type' ] = $resultSetValue[ 'Media' ][ 'codes' ][ 'type' ];
			}
			else {
				$feedResultSet[ $resultSetKey ][ 'Media' ][ 'media_code' ] = 'null';
				$feedResultSet[ $resultSetKey ][ 'Media' ][ 'type' ] = 'null';
			}
			
			unset( $feedResultSet[ $resultSetKey ][ 'Media' ][ 'codes' ] );
			unset( $feedResultSet[ $resultSetKey ][ 'Media' ][ 'code' ] );
		}
			
	}
	
	public function getProject( $identity ) {

		// check if identity is an object
		if ( $identity instanceof Api_Model_Project ) {
			return $identity;
		}

		$project = $this->_lookupProject( $identity );
		if ( $project instanceOf Api_Model_Project ) {
			return $project;
		}
		
		$project = $this->_getProject( $identity );
		if ( null === $project ) {
			$project = new Api_Model_Project( array() );
		} else {
			$this->_indexProject( $project );
		}

		return $project;
	}
	
	// check if project is already cached 
	protected function _lookupProject ( $identity ) {
		$index = null;
		if ( is_scalar( $identity ) && isset( $this->_indexes[ $identity ] ) ) {
			$index = $identity;
		} else if ( $identity instanceof Zend_Db_Table_Row_Abstract && isset( $identity->user_id ) ) {
			$index = $identity->user_id;
		} else if ( is_array( $identity ) && is_string( $identity[ 0 ] ) && is_numeric( $identity[ 1 ] ) ) {
			$index = $identity[ 1 ];
		}

	    if( isset($this->_indexes[$index]) && isset($this->_projects[$this->_indexes[$index]]) ) {
	      return $this->_projects[$this->_indexes[$index]];
	    }

	    return null;
	}
	
	protected function _getProject ( $identity ) {
		if ( !$identity ) {
		  $project = new Api_Model_Project(array(
			'table' => Engine_Api::_()->getItemTable( 'whmedia_project' ),
		  ));
		} else if ( $identity instanceof Api_Model_Project ) {
		  $project = $identity;
		} else if ( is_numeric( $identity ) ) {
		  $project = Engine_Api::_()->getDbTable( 'projects', 'api' )->find( $identity )->current();
		}

		// Empty project?
		if( null === $project ) {
		  return null;
		}

		return $project;
	}

	protected function _indexProject ( Api_Model_Project $project ) {
		// Ignore if not an actual user or user is already set
		if( !empty( $project->project_id ) && !isset( $this->_projects[ $project->project_id ] ) ) {
		  $this->_indexes[ $project->project_id ] = $project->project_id;
		  $this->_projects[$project->project_id] = $project;
		}
	}	

}