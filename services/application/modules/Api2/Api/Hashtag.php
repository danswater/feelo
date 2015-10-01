<?php
class Api2_Api_Hashtag extends Api_Api_Base {

    protected $_manageNavigation;
    protected $_moduleName = 'Api2';

    protected $isFollowed = false;

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


    public function featuredHashtag( $viewer, $filter = array() ){
        $container = array();
        $tags = Engine_Api::_()->getDbTable( 'tags', 'core' );
        $tagMaps = Engine_Api::_()->getDbTable( 'tagMaps', 'core' );
        $whmedia = Engine_Api::_()->getApi( 'core', 'whmedia' );
        $objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );

        $featuredTags = $tags->adminLikeHashtag( $filter );

        $results = array();  

        foreach( $featuredTags as $featuredTag ) {
            $tmpResult = array( "Hashtag" => array(), "User" => array(), "post" => array() );

            // hastag 
            $tmpResult[ "Hashtag" ][ "tag_id" ]     = $featuredTag[ "tag_id" ];
            $tmpResult[ "Hashtag" ][ "hashtag_id" ] = $featuredTag[ "tag_id" ];
            $tmpResult[ "Hashtag" ][ "text" ]       = $featuredTag[ "text" ];
            $tmpResult[ "Hashtag" ][ 'isFollowed' ] = false;

            if( $whmedia->isFollowed( $viewer, array( 'tag_id' => $featuredTag[ "tag_id" ] ) ) ) {
                $tmpResult[ "Hashtag" ][ 'isFollowed' ] = true;
            }

            $hashProject = $tagMaps->select()
                ->from( array( 'tagmap' => 'engine4_core_tagmaps' ), array( '' ) )
                ->joinLeft( array( 'project' => 'engine4_whmedia_projects' ), 'tagmap.resource_id = project.project_id', array( 'project.*' ) )
                ->where( 'tagmap.tag_id ='. $featuredTag[ 'tag_id' ] )
                //->where( 'project.project_id = 3012'  )
                //->where( 'tagmap.tag_id = 489')
                ->setIntegrityCheck( false )
                ->limit( 1 )
                ->order( ' RAND() ' );


            $tmpPostResult = $hashProject->query()->fetch();   

            // Fetch User
            $apiApiUser    = Engine_Api::_()->getApi( 'user', 'api' );
            $resultSetUser = $apiApiUser->fetchUserDetails( $featuredTag[ "user_id" ] );

            //Get Like count and check if user liked the project
            $apiLike    = Engine_Api::_()->getApi( 'like', 'api' );
            $rowLikes = $apiLike->fetchLikes( $tmpPostResult[ 'user_id' ], $tmpPostResult[ 'project_id' ] );

            // get like count
            $likeCount = Api2_Helpers_Utils::formatNumber( $rowLikes );
            if ( $likeCount  == 'null' ) {
                $likeCount    = '0';
                $likeCountInt = 0;
            } else {
                $likeCountInt = count( $rowLikes );
            }

            // is current user like this feed?
            $isLiked   = $apiLike->isLiked( $rowLikes[ 0 ][ 'resource_id' ], $viewer->getIdentity() );

            // Get comments
            $apiComment = Engine_Api::_()->getApi( 'comment', 'api' );
            $comment   = $apiComment->fetchComments( $tmpPostResult[ 'project_id' ] );

            if( $comment == 'null' ) {
                $commentCount    = '0';
                $commentCountInt = 0;
            }
            else {
                $commentCount     = Api2_Helpers_Utils::formatNumber( $comment );
                $commentCountInt  = count( $comment );
            }

            // post
            $tmpResult[ "post" ][ "project_id" ] = $tmpPostResult[ "project_id" ];
            $tmpResult[ "post" ][ "title" ] = $tmpPostResult[ "title" ];
            $tmpResult[ "post" ][ "description" ] = $tmpPostResult[ "description" ];
            $projectViews = Api2_Helpers_Utils::formatNumber( $tmpPostResult[ 'project_views' ] );
            $mediaPhoto = $objMedia->fetchMediaDetails( $tmpPostResult[ "project_id" ],$tmpPostResult[ "cover_file_id" ] );

            $media = new Api2_Model_Media();
            $media->initWithValues( $mediaPhoto );
            
            $tmpResult[ "post" ][ "Media" ] = $mediaPhoto;

            // get dominant color
            $imageColor = Api2_Helpers_DominantColor::execute( $media->getStoragePath() );

            $feed = new Api2_Model_FeaturedHashtag();
            $feed->setProjectId( $tmpPostResult[ "project_id" ] );
            $feed->setUserId( $featuredTag[ "user_id" ] );
            $feed->setCategoryId( $tmpPostResult[ 'category_id' ] );
            $feed->setTitle( $tmpPostResult[ 'title' ] );
            $feed->setDescription( $tmpPostResult[ 'description' ] );
            $feed->setCreationDate( $tmpPostResult[ 'creation_date' ] );
            $feed->setProjectViews( $projectViews );
            $feed->setOwnerType( $tmpPostResult[ 'owner_type' ] );
            $feed->setSearch( $tmpPostResult[ 'search' ] );
            $feed->setCoverFileId( $tmpPostResult[ 'cover_file_id' ] );
            $feed->setCommentCount( $commentCount );
            $feed->setIsPublished( $tmpPostResult[ 'is_published' ] );
            $feed->setMedia( $media );
            $feed->setUser( $resultSetUser );
            $feed->setLikeCount( $likeCount );
            $feed->setLikeCountInt( $likeCountInt );
            $feed->setIsLiked( $isLiked );
            $feed->setCommentCountInt( $commentCountInt );
            $feed->setImageColor( $imageColor );
            $feed->setHashtag( $tmpResult[ "Hashtag" ] );

            $results[][ 'FeaturedHashtag' ] = $feed;

        }   

        return $results;


    }

    public function fetchByHashtagName ( $user, $params ) {
        $offset = 0;
        if ( isset( $params[ 'offset' ] ) ) {
            $offset = $params[ 'offset' ];
        }

        $dbTableHashtags = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );
    
        $hashtags = $dbTableHashtags->fetchByHashtagName( $params[ 'text' ], $offset );

        foreach( $hashtags as $hashtag ) {

            $dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
            $project = $dbTableProjects->readFeedByProjectId( $hashtag->resource_id );


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
            $hashTags  = $apiHashtag->getPostHashtag( $user, $project->project_id );


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
            $feed->setHashtag( $hashTags );

            $ret[] = $feed;
        }

        return $ret;
    }

    public function fetchByHashtagName2( $user, $params ) {
        $dbTableHashtags = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );

        $hashtagRow = $dbTableHashtags->readByHashtagName( $params[ 'text' ] );

        $apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' ); 
        if( $apiHashtag->publicIsFollowed( $user->getIdentity(), array( 'tag_id' => $hashtagRow->tag_id ) ) ) {
            $isFollowed = 1;
        }
        else {
            $isFollowed = 0;
        }

        $dbTableTagmaps = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $adapter = $dbTableTagmaps->getAdapter();
        
        $select = $dbTableTagmaps
            ->select()
            ->where( 'tag_id ='. $hashtagRow->tag_id );

        $result    = $adapter->fetchAll( $select );
        
        $resultCount = count( $result );

        $hashtag = new Api2_Model_Hashtag();
        $hashtag->setTagId( $hashtagRow->tag_id );
        $hashtag->setText( $hashtagRow->text );
        $hashtag->setIsFollowed( $isFollowed );
        $hashtag->setResultCount( $resultCount );

        return array(
            'Hashtag' => $hashtag
        );
      
    }

    public function fetchFeedByHashtagId ( $user, $params ) {
        if ( !isset( $params[ 'user_id' ] ) ) {
            $params[ 'user_id' ] = $user->getIdentity();
        }

        $offset = $params[ 'offset' ];
        $dbTableProjects = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );
  
        $projects = $dbTableProjects->readFeedByHashtagId( $params[ 'tag_id' ], $offset );
 

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

            if ( $likeCount  == 'null' || $likeCount == '' ) {
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

            $api2Hashtag = Engine_Api::_()->getApi( 'hashtag', 'api2' );
            $subject = Engine_Api::_ ()->user ()->getUser ( $params[ 'user_id' ] );
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

        $dbTableProjects = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );
        $feed            = $dbTableProjects->readRandomFeedByHashtagId( $params[ 'tag_id' ] );

        $hashtag = $dbTableProjects->readHashtagById( $user, $params[ 'tag_id' ] );

        // Fetch Media(s)
        $apiMedias = Engine_Api::_()->getApi( 'media', 'api2' );
        $mediaRow = $apiMedias->fetchMediaByProjectIdWithStructuredResponse( $feed->project_id, $feed->cover_file_id );


        return array( 
            'cover_photo'   => $mediaRow->getStoragePath(),
            'text'          => $hashtag->text,
            'Hashtag_Feeds' => $ret
        );
    }


    public function fetchHashtagByKeyword( $params, $user ){
        $objMedia = Engine_Api::_()->getApi( 'whmedia', 'api' );

        $keyword = $params[ "keyword" ];
        $offset = $params[ "offset" ]; 
		$limit = isset( $params[ "limit" ] ) ? $params[ "limit" ] : 10;
        $user_id = $user->user_id;

        $objTable = Engine_Api::_()->getDbtable( 'tags', 'core' );
        $objDb    = $objTable->getAdapter();

        $where     = "(text LIKE ?)";
        $values    = array( '%'. $keyword .'%' );
        $objSelect = $objTable->select()
            ->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) )
            ->order( array( 'tag_id DESC' ) );

        $objSelect->limit( $limit, $offset );   
       
        $objResultSet = $objDb->fetchAll( $objSelect );

        foreach( $objResultSet as $key => $val ) {

            if( $this->_isFollowed( $user_id, $val ) ) {
                $objResultSet[ $key ][ 'is_followed' ] = 1;
            }
            else {
                $objResultSet[ $key ][ 'is_followed' ] = 0;
            }
            $objTagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $objTagMapDb = $objTagMapTable->getAdapter();

            $objSelect = $objTagMapTable->select()
                ->where( 'tag_id ='. $val[ 'tag_id' ] );
            
            $result = $objTagMapDb->fetchAll( $objSelect );

            $objResultSet[ $key ][ 'result_count' ] = count( $result );

            $project = $this->getLastProjectByTagId( $val[ 'tag_id' ] );
            $mediaPhoto = $objMedia->fetchMediaDetails( $project->project_id, $project->cover_file_id );

            $objResultSet[ $key ][ "Post" ] = array(
                "project_id" => $project->project_id,
                "image" => $mediaPhoto
            );
        }

        return array( "Hashtag" => $objResultSet );
       

    }

    protected function getLastProjectByTagId( $tag_id ){
        $dbTableTagmaps = Engine_Api::_()->getDbtable( 'TagMaps', 'core' );
        $select = $dbTableTagmaps->select()
            ->from( array( 'tagmap' => 'engine4_core_tagmaps' ), array( '' ) )
            ->joinLeft( array( 'project' => 'engine4_whmedia_projects' ), 'tagmap.resource_id = project.project_id', array( 'project.*' ) )
            ->where( 'tag_id ='. $tag_id )
            ->order( 'project.project_id DESC' )
            ->limit( 1 )
            ->setIntegrityCheck( false );
        return $dbTableTagmaps->fetchRow( $select );
    }

    protected function _isFollowed( $id, $params ) {

        $tableTag = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
        $dbTag = $tableTag->getAdapter();

        $tagId = (int)$params[ 'tag_id' ];
        $select = $tableTag->select()
          ->where( 'hashtag_id='. $tagId .' AND follower_id='. $id .'');

        $rowSet = $select->query()->fetch();

        if( is_array( $rowSet ) ) {
          return true;
        }

        return false;       
    }

    public function fetchHashtagsById ( $user, $params ) {
        $dbTableTags = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );

        $hashtag = $dbTableTags->readHashtagById( $user, $params[ 'tag_id' ] );
        $isFollowed = $this->_isFollowed( $user->getIdentity(), $params );

        $dbTableTagmaps = Engine_Api::_()->getDbtable('TagMaps', 'core');

        $select = $dbTableTagmaps->select()
            ->where( 'tag_id ='. $params[ 'tag_id' ] );

        $result = $dbTableTagmaps->fetchAll( $select );
            
        $resultCount = count( $result );

        $ret = $hashtag->toArray();
        $ret[ 'is_followed' ]  = $isFollowed;
        $ret[ 'result_count' ] = $resultCount;

        return $ret;
    }

    public function fetchHashtagsByUserId ( $user, $params ) {
        if ( !isset( $params[ 'user_id' ] ) ) {
            $params[ 'user_id' ] = $user->getIdentity();
        }
        
        $dbTableHashtags = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );
    
        $hashtags = $dbTableHashtags->readFollowedHashtags( $user, $params );
    
        $ret = array();
        foreach( $hashtags as $tag ) {
            $dbTableTagmaps = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $select = $dbTableTagmaps->select()
                ->where( 'tag_id ='. $tag->tag_id );

            $result = $dbTableTagmaps->fetchAll( $select );
                
            $resultCount = count( $result );
            
            $ret[ 'tag_id' ] = $tag->tag_id;
            $ret[ 'text' ] = $tag->text;
            $ret[ 'is_followed' ] = 1;
            $ret[ 'result_count' ] = $resultCount;
            
            $response[] = $ret;
        }
        
        return array( 'Hashtags' => $response );
    }

    public function fetchAllByKeyword ( $user, $params ) {

        $dbTableHashtag = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );

        $hashtags = $dbTableHashtag->fetchAllByKeyword( $user, $params );

        $ret = array();
        foreach( $hashtags as $tag ) {

            $apiHashtag = Engine_Api::_()->getApi( 'hashtag', 'api' ); 
            if( $apiHashtag->publicIsFollowed( $user->getIdentity(), array( 'tag_id' => $tag->tag_id ) ) ) {
                $isFollowed = 1;
            }
            else {
                $isFollowed = 0;
            }

            $dbTableTagmaps = Engine_Api::_()->getDbtable('TagMaps', 'core');
            $adapter = $dbTableTagmaps->getAdapter();

            $select = $dbTableTagmaps
                ->select()
                ->where( 'tag_id ='. $tag->tag_id );

            $result    = $adapter->fetchAll( $select );
            
            $resultCount = count( $result );

            $hashtag = new Api2_Model_Hashtag();
            $hashtag->setTagId( $tag->tag_id );
            $hashtag->setText( $tag->text );
            $hashtag->setIsFollowed( $isFollowed );
            $hashtag->setResultCount( $resultCount );

            $ret[] = $hashtag;

        }


        return $ret;

    }

    public function fetchHashtagInfo ( $user, $params ) {

        $dbTableHashtag = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );

        $hashtags = $dbTableHashtag->fetchAllByKeyword( $user, array(
            'keyword' => $params[ 'keyword' ]
        ) );

        return $ret;

    }

}

