<?php
class Api2_Api_Search extends Api_Api_Base {

    protected $_manageNavigation;
    protected $_moduleName = 'Api2';


    public function fetchAllByKeyword ( $user, $params ) {

        $ret = array();
        $dbTableStorage = Engine_Api::_()->getDbTable( 'storages', 'api2' );

        $dbTableHashtag = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );
        $hashtagCount   = $dbTableHashtag->readAndCountHashtagByKeyword( $params[ 'keyword' ] );

        if ( $hashtagCount >= 1) {

            $hashtagFile = $dbTableStorage->readByFeatureType( 'hashtag', $params[ 'keyword' ] );

            $hashtag = new Api2_Model_SearchInfo();
            $hashtag->setKeyword( $params[ 'keyword' ] );
            $hashtag->setResultCount( $hashtagCount );
            $hashtag->setStoragePath( $hashtagFile[ 'storage_path' ] );

            $ret[ 'Hashtag' ] = $hashtag;

        }


        $dbTableUser = Engine_Api::_()->getDbTable( 'users', 'api2' );
        $userCount   = $dbTableUser->readAndCountUserByKeyword( $params[ 'keyword' ] );

        if ( $userCount >= 1 ) {

            $userFile = $dbTableStorage->readByFeatureType( 'user', $params[ 'keyword' ] );

            $user = new Api2_Model_SearchInfo();
            $user->setKeyword( $params[ 'keyword' ] );
            $user->setResultCount( $userCount );
            $user->setStoragePath( $userFile[ 'storage_path' ] );

            $ret[ 'User' ] = $user;

        }

        $dbTableFavo = Engine_Api::_()->getDbTable( 'favos', 'api2' );
        $favoCount   = $dbTableFavo->readAndCountFavoByKeyword( $params[ 'keyword' ] );

        if ( $favoCount >= 1 ) {

            $favoFile = $dbTableStorage->readByFeatureType( 'favo', $params[ 'keyword' ] );

            $favo = new Api2_Model_SearchInfo();
            $favo->setKeyword( $params[ 'keyword' ] );
            $favo->setResultCount( $favoCount );
            $favo->setStoragePath( $favoFile[ 'storage_path' ] );

            $ret[ 'Favo' ] = $favo;

        }

        $dbTablePost = Engine_Api::_()->getDbTable( 'projects', 'api2' );
        $feedCount   = $dbTablePost->readAndCountFeedByKeyword( $params[ 'keyword' ] );

        if ( $feedCount >= 1 ) {

            $feedFile = $dbTableStorage->readByFeatureType( 'feed', $params[ 'keyword' ] );

            $feed = new Api2_Model_SearchInfo();
            $feed->setKeyword( $params[ 'keyword' ] );
            $feed->setResultCount( $feedCount );
            $feed->setStoragePath( $feedFile[ 'storage_path' ] );

            $ret[ 'Feed' ] = $feed;

        }

        return ( object )$ret;

    }

    public function fetchHashtagsByKeyword ( $user, $params ) {
        $dbTableHashtag = Engine_Api::_()->getDbtable( 'hashtags', 'api2' );

        $hashtags = $dbTableHashtag->fetchAllByKeyword( $user, $params );

        $dbTableTagmaps = Engine_Api::_()->getDbtable('TagMaps', 'core');
        $dbTableStorage = Engine_Api::_()->getDbTable( 'storages', 'api2' );

        $ret = array();
        foreach( $hashtags as $hashtag ) {
            $select = $dbTableTagmaps->select();
            $select->from( $dbTableTagmaps, array('count(*) as result_count' ) );
            $select->where( 'tag_id LIKE ?', '%'. $hashtag->tag_id .'%' );

            $rows = $dbTableTagmaps->fetchAll( $select );
            
            $hashtagCount = $rows[0]->result_count;

            $hashtagFile = $dbTableStorage->readByFeatureType( 'hashtag', $hashtag->tag_id );

            $searchInfo = array();
            $searchInfo[ 'tag_id' ]       = $hashtag->tag_id;
            $searchInfo[ 'title' ]        = $hashtag->text;
            $searchInfo[ 'result_count' ] = $hashtagCount;
            $searchInfo[ 'storage_path' ] = $hashtagFile[ 'storage_path' ];

            $ret[] = $searchInfo;

        }

        return array(
            'Hashtags' => $ret
        );
    }

    public function fetchFavosByKeyword ( $user, $params ) {
        $suffix = $params[ 'offset' ] .'0';
        $dbTableFavcircle = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );

        if ( empty( $params[ 'keyword' ] ) ) {
            throw new Exception( 'No keyword found' );
        }
        
        $select = $dbTableFavcircle->select();
        $select->where( 'title LIKE ?', '%'. $params[ 'keyword' ] .'%' );
        $select->order( array( 'favcircle_id DESC' ) );
        $select->limit( $this->limit, $suffix );

        if ( isset( $params[ 'offset' ] ) ) {
            $rows = 5;
            $suffix = $rows * (int)$params[ 'offset' ];
            $select->limit( $rows, $suffix );
        }
        
        $favos = $dbTableFavcircle->fetchAll( $select );

        if ( count( $favos ) < 1 ) {
            throw new Exception( 'No results found.' );
        }
                        
        $dbTableStorage = Engine_Api::_()->getDbTable( 'files', 'storage' );
        $apiFavo        = Engine_Api::_()->getApi( 'favo', 'api' );

        $ret = array();
        foreach( $favos as $favo ) {
            $row = $dbTableStorage->fetchRow( 'file_id = '. $favo->photo_id );

            $storagePath = $row->storage_path;
            
            $dbTableFavcircleitems = Engine_Api::_()->getDbTable( 'favcircleitems', 'whmedia' );
            $select = $dbTableFavcircleitems->select();
            $select->from( $dbTableFavcircleitems, array('count(*) as result_count' ) );
            $select->where( 'favcircle_id = ?', $favo->favcircle_id );

            $rows = $dbTableFavcircleitems->fetchAll($select);
            
            $resultCount = $rows[0]->result_count;
            
            $searchInfo = array();
            $searchInfo[ 'favcircle_id' ] = $favo->favcircle_id;
            $searchInfo[ 'title' ]        = $favo->title;
            $searchInfo[ 'result_count' ] = $resultCount;
            $searchInfo[ 'storage_path' ] = $storagePath;
            
            $ret[] = $searchInfo;
        }


        return array(
            'Favos'  => $ret
        );
    }

    public function fetchFeedsByKeyword ( $user, $params ) {
        $offset = $params[ 'offset' ];
        $dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );

        $userId = $user->getIdentity();
        if ( isset( $params[ 'user_id'] ) && !empty( $params[ 'user_id' ] ) ) {
            $userId = $params[ 'user_id' ];
        }
    
        $projects = $dbTableProjects->readFeedByTitle( $user, $params );

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

    public function fetchUsersByKeyword ( $user, $params ) {
        $dbTableUser = Engine_Api::_()->getDbtable( 'users', 'api2' );
        $apiUsers    = Engine_Api::_()->getApi( 'user', 'api2' );

        $users = $dbTableUser->fetchAllByKeyword( $user, $params );

        $ret = array();
        foreach( $users as $user ) {
            $currentUser = current( $user );
            $retUser = $apiUsers->fetchByUsername( $user, $currentUser );

            $ret[] = $retUser;
        }

        return array(
            'Users' => $ret
        );
    
    }

}

