<?php
class Api2_Api_Project extends Core_Api_Abstract {

	public function fetchLinkFeedsByAdminLikes ( $user, $params ) {
		$offset = $params[ 'offset' ];
		if ( $offset == 0 ) {
			$offset = 10;
		}

		$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
	
		$feeds = $dbTableProjects->readLinkFeedsByAdminLikes( $user, $offset );

		$projectCollection = array();
		$ret = array();
		foreach( $feeds as $key => $feed ) {

			// Expiremental : If the project id is already build as
			// Api2_Model_FeaturedLink lets fetch another one
			if ( in_array( $feed->project_id, $projectCollection ) ) {
				$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
				$project = $dbTableProjects->readNotRedundantFeedByProjectId( $projectCollection );

				if ( !$project ) {
					$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
					$project = $dbTableProjects->readFeedByProjectId( $feed->project_id );					
				}
				
			} else {
				$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
				$project = $dbTableProjects->readFeedByProjectId( $feed->project_id );				
			}
			$projectCollection[] = $project->project_id;


			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchLinkMediaWithImageSizesByProjectId( $project->project_id, $project->cover_file_id );

			if ( $mediaRow->url != 'null' ) {
				$media  = $mediaRow;				
			}

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

			// Get hashtag
			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::execute( $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeaturedLink();
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
			$feed->setLink( $media );
			$feed->setUser( $resultSetUser );
			$feed->setLikeCount( $likeCount );
			$feed->setLikeCountInt( $likeCountInt );
			$feed->setIsLiked( $isLiked );
			$feed->setCommentCountInt( $commentCountInt );
			$feed->setFeedType( 'LINK' );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[][ 'FeaturedLink' ] = $feed;

		}

		return $ret;
	}

	public function fetchPhotoFeedsByAdminLikes ( $user, $params ) {
		$offset = $params[ 'offset' ];
		if ( $offset == 0 ) {
			$offset = 10;
		}

		$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
	
		$projects = $dbTableProjects->readPhotoFeedsByAdminLikes( $user, $offset );

		$ret = array();	
		foreach( $projects as $key => $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchPhotoMediaWithImageSizesByProjectId( $project->project_id, $project->cover_file_id );

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

			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::execute( $mediaRow->getStoragePath() );
		
			$feed = new Api2_Model_FeaturedMedia();
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
			$feed->setFeedType( 'PHOTO' );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[][ 'FeaturedPhoto' ] = $feed;

		}

		return $ret;
	}

	public function fetchVideoFeedsByAdminLikes ( $user, $params ) {
		$offset = $params[ 'offset' ];
		if ( $offset == 0 ) {
			$offset = 10;
		}

		$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
	
		$projects = $dbTableProjects->readVideoFeedsByAdminLikes( $user, $offset );

		$ret = array();	
		foreach( $projects as $key => $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiWhmedia = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow   = $apiWhmedia->fetchVideoMediaWithImageSizesByProjectId( $project->project_id, $project->cover_file_id );
			
			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			//Get Like count and check if user liked the project
			$apiLike    = Engine_Api::_()->getApi( 'like', 'api' );
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

			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::execute( $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeaturedMedia();
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
			$feed->setFeedType( 'VIDEO' );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[][ 'FeaturedVideo' ] = $feed;

		}

		return $ret;
	}

	public function fetchExploreFeed ( $user, $params ) {
		$offset = $params[ 'offset' ];
		$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
	
		$projects = $dbTableProjects->readAllTypesOfFeed( $user->getIdentity(), $offset );

		$ret = array();	
		foreach( $projects as $key => $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $project->user_id, $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );

			if ( $likeCount  == 'null' || empty( $likeCount ) ) {
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

			// Determin feed type
			$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = "";
			$imageColor = Api2_Helpers_DominantColor::secondExecution( $project->project_id, $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeedWithType();
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
			$feed->setFeedType( $feedType );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[] = $feed;

		}

		return array( 'Feeds' => $ret );

	}

	public function relatedPost( $user, $params ){
/*
		$table = Engine_Api::_()->getDbtable('projects', 'whmedia');
	    $rName = $table->info('name');
	    
	    $tagTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
	    $tagName = $tagTable->info('name');
	    $select_tags = $tagTable->select();
	    $select_tags->from($tagName, array('tag_id'))
	                ->where($tagName.'.resource_id = ?', $params[ "project_id"] );
	    
	    $select = $table->select()->from($rName, array('*', 'similarity' => 'COUNT(tag_id)'))
	                              ->joinLeft($tagName, 'resource_type="whmedia_project" AND ' . $tagName . '.resource_id = ' . $rName.'.project_id', array())
	                              ->where($tagName.".`resource_type` = 'whmedia_project'")
	                              ->where($tagName.".`resource_id` != ?", $params[ "project_id"] )
	                              ->where($tagName.'.tag_id in (?)', $select_tags)
	                              ->group("{$tagName}.resource_id")
	                              ->order('similarity DESC')
	                              ->order($rName . '.creation_date DESC')
								  ->limit(5);   
	    $projects = $table->fetchAll($select);	

	    $filterArray = array();

	    foreach( $projects as $key => $project ){

	    	$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project->project_id, $project->cover_file_id );

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

			// Determin feed type
			$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::execute( $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeedWithType();
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
			$feed->setFeedType( $feedType );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$filterArray[] = $feed;
	    }
	    return array( "RelatedPost" => $filterArray );
*/
	}


	public function getEmbedded( $user, $params = array() ){

		$project = Engine_Api::_()->getItem('whmedia_project', $params[ "project_id"] );

		if( $project == null )
			return array( "Embedded" => array() );

		$addition_media_filtr = array();
	    if (!$isOwner and !Engine_Api::_()->whmedia()->isAdmin($user)) {
	        $addition_media_filtr[] = 'encode <= 1';
	    }
	  	$medias = $project->getMedias($addition_media_filtr);
		
		$embedHtml = array();
		foreach( $medias as $media ){
			$embedHtml[] = $media->Embedded();
		}	

		return array( "Embedded" => $embedHtml );

	}

	public function fetchFeedByUserId ( $user, $params ) {
		$offset = $params[ 'offset' ];
		$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );

		$userId = $user->getIdentity();
		if ( isset( $params[ 'user_id'] ) && !empty( $params[ 'user_id' ] ) ) {
			$userId = $params[ 'user_id' ];
		}
	
		$projects = $dbTableProjects->readFeedByUserId( $userId, $offset );

		$ret = array();	
		foreach( $projects as $key => $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $project->user_id, $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );

			if ( $likeCount  == 'null' || empty( $likeCount ) ) {
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

			// Determin feed type
			$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::secondExecution( $project->project_id, $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeedWithType();
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
			$feed->setFeedType( $feedType );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[] = $feed;

		}

        $dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
        $feed            = $dbTableProjects->readRandomFeedByUserId( $userId );

        // Fetch Media(s)
        $apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
        $mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $feed->project_id, $feed->cover_file_id );

		return array( 
			'cover_photo' => $mediaRow->getStoragePath(),
			'Feeds' => $ret
		);
	}

	public function createFeedInRss ( $user, $files, $params ) {
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
			
            $media->is_url = $params[ 'url' ];
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

    public function create ( $user, $params ) {
    	$apiProject = Engine_Api::_()->getApi( 'project', 'api' );

    	$newFeed = $apiProject->feedDetails( $user, $params );

    	$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
    	$project = $dbTableProjects->readFeedByProjectId( $params[ 'project_id' ] );

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

		$ret[ 'Posts' ] = $feed;

		return $ret;

    }

    public function edit($user, $params){
    	$project_id = $params['project_id'];

    	if( $params['privacy'] == 1 ) {
    		$privacy = 'everyone';
    	} elseif( $params['privacy'] == 0 ) {
    		$privacy = 'owner';
    	} else {
    		$privacy = $params['privacy'];
    	}

    	$projectTable = Engine_Api::_()->getDbtable('projects', 'whmedia');
        $project_db = $projectTable->getAdapter();
        $project_db->beginTransaction();
        $viewer = Engine_Api::_()->user()->getViewer();

        $projectTableRow = $projectTable->find($project_id)->current();

        $projectTableRow->title = $params['title'];
        $projectTableRow->description = $params['description'];
        $projectTableRow->save();
		
		$tags = array_filter(preg_split('/[ ]+/', str_replace("#", "", $params['tags'])), "trim");
        $projectTableRow->tags()->setTagMaps($viewer, $tags);

        $auth = Engine_Api::_()->authorization()->context;
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

        $viewMax = array_search( $privacy , $roles);

        foreach ($roles as $i => $role) {
            $auth->setAllowed($projectTableRow, $role, 'view', ($i <= $viewMax));
        }

        $project_db->commit();

        $userInfo = Engine_Api::_ ()->user ()->getUser ( $viewer->getIdentity() );

        $projectFeed = Engine_Api::_()->getApi( 'project', 'api' );

        try {
            $arrResultSet = $projectFeed->newSpecificFeed( $userInfo, $project_id );

            $response = array(
                'data'  => array(
                    'Posts' =>$arrResultSet
                ),
                'error' => array()
            ); 
        } catch ( Exception $e ) {
            $response = array(
                'data'  => array(),
                'error' => array( $e->getMessage() )
            );  
        }

        return $response;
    }

    public function delete ( $user, $params ) {
    	$id = $params[ 'project_id' ];

    	// delete the feed from the stream
    	// delete the feed from the project
    	$db = Engine_Db_Table::getDefaultAdapter();
    	$db->beginTransaction();

    	try {
    		$row = Engine_Api::_()->getItem( 'whmedia_project', $id );

    		if ( $row ) {
    			$row->delete();	
    		}

    		$db->commit();
    	} catch ( Exception $e ) {
    		$db->rollBack();
    		throw $e;
    	}

    	// delete the feed from the favo
    	try {
	    	$dbTableFavo = Engine_Api::_()->getDbTable( 'favcircleitems', 'api2' );
	    	$where       = $dbTableFavo->getAdapter()->quoteInto( 'project_id = ?', $id );
	    	$dbTableFavo->delete( $where );		
    	} catch( Exception $e ) {
    		throw $e;
    	}

    	return array(
    		'status' => 'success',
    		'Posts'   => new StdClass()
    	);

    }

    public function fetchHashtagByUserId ( $user, $params ) {
        $dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );

        $projects = $dbTableProjects->readHashtagFeedByUserId( $user, $params );

        if ( count( $projects ) < 1 ) {
        	throw new Exception( 'No results found' );
        }

		$ret = array();	
		foreach( $projects as $key => $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $params[ 'user_id' ], $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );

			if ( $likeCount  == 'null' || empty( $likeCount ) ) {
				$likeCount    = '0';
				$likeCountInt = 0;
			} else {
				$likeCountInt = count( $rowLikes );
			}

			// is current user like this feed?
			$isLiked   = $apiLike->isLiked( $rowLikes[ 0 ][ 'resource_id' ], $params[ 'user_id' ] );

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

			// Determin feed type
			$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

			$api2Hashtag = Engine_Api::_()->getApi( 'hashtag', 'api2' );
			$subject = Engine_Api::_ ()->user ()->getUser ( $params[ 'user_id' ] );
			$hashTag  = $api2Hashtag->fetchHashtagById( $subject, array(
				'tag_id' => $project->tag_id
			) );

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::secondExecution( $project->project_id, $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeedWithType();
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
			$feed->setFeedType( $feedType );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[] = $feed;

		}

        return array(
        	'Feeds' => $ret
        );
    }

    public function fetchLikesByUserId ( $user, $params ) {
        $dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );

        $projects = $dbTableProjects->readLikesByUserId( $user, $params );

		$ret = array();	
		foreach( $projects as $key => $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $params[ 'user_id' ], $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );

			if ( $likeCount  == 'null' || empty( $likeCount ) ) {
				$likeCount    = '0';
				$likeCountInt = 0;
			} else {
				$likeCountInt = count( $rowLikes );
			}

			// is current user like this feed?
			$isLiked   = $apiLike->isLiked( $rowLikes[ 0 ][ 'resource_id' ], $params[ 'user_id' ] );

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

			// Determin feed type
			$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

			$subject = Engine_Api::_ ()->user ()->getUser ( $params[ 'user_id' ] );

			// Get hashtag
			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );				
			
			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $subject, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}
			
			$api2Hashtag = Engine_Api::_()->getApi( 'hashtag', 'api2' );
			$hashTag2  = $api2Hashtag->fetchHashtagsById( $subject, array(
				'tag_id' => $hashTag[ 'tag_id' ]
			) );

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::secondExecution( $project->project_id, $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeedWithType();
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
			$feed->setFeedType( $feedType );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[] = $feed;

		}

        return array(
        	'Feeds' => $ret
        );
    }

    public function fetchFavoPostsByFavoId ( $user, $params ) {

		if ( !isset( $params[ 'user_id' ] ) ) {
			$params[ 'user_id' ] = $user->getIdentity();
		}

        $dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );

        $projects = $dbTableProjects->readFeedByFavoId( $user, $params );

		$ret = array();	
		foreach( $projects as $key => $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $params[ 'user_id' ], $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );

			if ( $likeCount  == 'null' || empty( $likeCount ) ) {
				$likeCount    = '0';
				$likeCountInt = 0;
			} else {
				$likeCountInt = count( $rowLikes );
			}

			// is current user like this feed?
			$isLiked   = $apiLike->isLiked( $rowLikes[ 0 ][ 'resource_id' ], $params[ 'user_id' ] );

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

			// Determin feed type
			$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

			$subject    = Engine_Api::_ ()->user ()->getUser ( $params[ 'user_id' ] );
			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );
			
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::secondExecution( $project->project_id, $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeedWithType();
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
			$feed->setFeedType( $feedType );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[] = $feed;

		}

        return array(
        	'Feeds' => $ret
        );
    }

	public function fetchFeedByBox( $user, $boxId, $offset ) {
		try {
			$rows   = 5;
			$suffix = $offset * (int) $offset;

			$dbTableCircle = Engine_Api::_()->getDbtable('circleitems', 'whmedia');
			$select = $dbTableCircle->select()
							 ->from( array( 'c' => 'engine4_whmedia_circleitems' ), array( 'user_id' ) )
							 ->where( 'circle_id ='. $boxId );

			$circle = $dbTableCircle->fetchRow( $select );

			$dbTableProject = Engine_Api::_()->getDbTable( 'projects', 'whmedia' );

			$select = $dbTableProject->select()
								  ->where( 'user_id ='. $circle->user_id .' AND is_published = 1' )
								  ->order( array( 'project_id DESC' ) )
								  ->limit( $rows, $suffix );
								  
			$projects = $dbTableProject->fetchAll( $select );

			
			$ret = array();	
			foreach( $projects as $key => $project ) {

				$projectViews = Api2_Helpers_Utils::formatNumber( $project[ 'project_views' ] );
				$creationDate = Api2_Helpers_ElapsedTime::execute( $project[ 'creation_date' ] );

				// Fetch Media(s)
				$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
				$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project[ 'project_id' ], $project[ 'cover_file_id' ] );

				// Fetch User
				$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
				$resultSetUser = $apiApiUser->fetchUserDetails( $project[ 'user_id' ] );

				// Get Like count and check if user liked the project
				$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
				$rowLikes = $apiLike->fetchLikes( $project[ 'user_id' ], $project[ 'project_id' ] );

				// get like count
				$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );

				if ( $likeCount  == 'null' || empty( $likeCount ) ) {
					$likeCount    = '0';
					$likeCountInt = 0;
				} else {
					$likeCountInt = count( $rowLikes );
				}

				// is current user like this feed?
				$isLiked   = $apiLike->isLiked( $rowLikes[ 0 ][ 'resource_id' ], $user->getIdentity() );

				// Get comments
				$apiComment = Engine_Api::_()->getApi( 'comment', 'api' );
				$comment   = $apiComment->fetchComments( $project[ 'project_id' ] );
			
				if( $comment == 'null' ) {
					$commentCount    = '0';
					$commentCountInt = 0;
				}
				else {
					$commentCount     = Api2_Helpers_Utils::formatNumber( $comment );
					$commentCountInt  = count( $comment );
				}

				// Determin feed type
				$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

				$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

				// Return only the first tag
				$hashTag = '';
				$hashTags  = $apiHashtag->getPostHashtag( $user, $project[ 'project_id' ] );
				if ( $hashTags ) {
					$hashTag = current( $hashTags );
				}

				// get dominant color
				$imageColor = Api2_Helpers_DominantColor::secondExecution( $project[ 'project_id' ], $mediaRow->getStoragePath() );

				$feed = new Api2_Model_FeedWithType();
				$feed->setProjectId( $project[ 'project_id' ] );
				$feed->setUserId( $project[ 'user_id' ] );
				$feed->setCategoryId( $project[ 'category_id' ] );
				$feed->setTitle( $project[ 'title' ] );
				$feed->setDescription( $project[ 'description' ] );
				$feed->setCreationDate( $creationDate );
				$feed->setProjectViews( $projectViews );
				$feed->setOwnerType( $project[ 'owner_type' ] );
				$feed->setSearch( $project[ 'search' ] );
				$feed->setCoverFileId( $project[ 'cover_file_id' ] );
				$feed->setCommentCount( $commentCount );
				$feed->setIsPublished( $project[ 'is_published' ] );
				$feed->setMedia( $mediaRow );
				$feed->setUser( $resultSetUser );
				$feed->setLikeCount( $likeCount );
				$feed->setLikeCountInt( $likeCountInt );
				$feed->setIsLiked( $isLiked );
				$feed->setCommentCountInt( $commentCountInt );
				$feed->setFeedType( $feedType );
				$feed->setImageColor( $imageColor );
				$feed->setHashtag( $hashTag );

				$ret[] = $feed;

			}


		} catch( Exception $e ) {
			print_r( $e ); exit;
		}
		return $ret;		
	}

	public function fetchTrending ( $user, $params ) {
		$api2Project  = Engine_Api::_()->getDbTable( 'projects', 'api2' );
		$projects = $api2Project->fetchTrending( $user, $params );

		$ret = array();
		foreach( $projects as $project ) {

			$projectViews = Api2_Helpers_Utils::formatNumber( $project->project_views );
			$creationDate = Api2_Helpers_ElapsedTime::execute( $project->creation_date );

			// Fetch Media(s)
			$apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
			$mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $project->project_id, $project->cover_file_id );

			// Fetch User
			$apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
			$resultSetUser = $apiApiUser->fetchUserDetails( $project->user_id );

			// Get Like count and check if user liked the project
			$apiLike  = Engine_Api::_()->getApi( 'like', 'api' );
			$rowLikes = $apiLike->fetchLikes( $project->user_id, $project->project_id );

			// get like count
			$likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );

			if ( $likeCount  == 'null' || empty( $likeCount ) ) {
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

			// Determin feed type
			$feedType = Api_Helper_DetermineFeedType::execute( $mediaRow );

			$apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' );

			// Return only the first tag
			$hashTag = '';
			$hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );
			if ( $hashTags ) {
				$hashTag = current( $hashTags );
			}

			// get dominant color
			$imageColor = Api2_Helpers_DominantColor::secondExecution( $project->project_id, $mediaRow->getStoragePath() );

			$feed = new Api2_Model_FeedWithType();
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
			$feed->setFeedType( $feedType );
			$feed->setImageColor( $imageColor );
			$feed->setHashtag( $hashTag );

			$ret[] = $feed;
		}

		return array(
			'Feeds' => $ret
		);

	}

}
