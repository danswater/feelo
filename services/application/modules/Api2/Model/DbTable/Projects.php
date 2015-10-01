<?php
class Api2_Model_DbTable_Projects extends Whmedia_Model_DbTable_Projects {
	protected $_name     = 'whmedia_projects';

	/**
	* this fetch all link type feeds that have been liked by admins
	*/
	public function readLinkFeedsByAdminLikes ( $user, $offset = 10 ) {

		$select = $this->select()
				->from( array( 'l' => 'engine4_core_likes' ) )
				->joinLeft( array( 'm' => 'engine4_whmedia_medias' ), 'm.project_id = l.resource_id' )
				->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = m.project_id' )
				->joinLeft( array( 'u' => 'engine4_users' ), 'u.user_id = p.user_id' )
				->where( 'l.poster_id in (1,2)' )
				->where( 'm.is_url != ""' )
				->order( 'rand()' )
				->limit( $offset )
				->setIntegrityCheck( false );
	
		$projectRows = $this->fetchAll( $select );

		return $projectRows;
						
	}

	/**
	* this fetch all photo type feeds that have been liked by admins
	*/
	public function readPhotoFeedsByAdminLikes ( $user, $offset = 10 ) {

		$select = $this->select()
				->from( array( 'l' => 'engine4_core_likes' ) )
				->joinLeft( array( 'm' => 'engine4_whmedia_medias' ), 'm.project_id = l.resource_id' )
				->joinLeft( array( 'f' => 'engine4_storage_files' ), 'f.parent_id = m.media_id' )
				->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = m.project_id' )
				->where( 'l.poster_id in (1,2)' )
				->where( 'm.code is null' )
				->where( 'm.is_url = ""' )
				->where( 'f.parent_type = "whmedia_media"' )
				->where( 'f.type not in ( "video.hd", "thumb.etalon", "video.html5" )' )
				->order( 'rand()' )
				->limit( $offset )
				->setIntegrityCheck( false );

		$projectRows = $this->fetchAll( $select );

		return $projectRows;

	}

	/**
	* this fetch all video type feeds that have been liked by admins
	*/
	public function readVideoFeedsByAdminLikes ( $user, $offset = 10 ) {

		$select = $this->select()
				->from( array( 'l' => 'engine4_core_likes' ) )
				->joinLeft( array( 'm' => 'engine4_whmedia_medias' ), 'm.project_id = l.resource_id' )
				->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = m.project_id' )
				->joinLeft( array( 'u' => 'engine4_users' ), 'u.user_id = p.user_id' )
				->where( 'l.poster_id in (1,2)' )
				->where( 'm.code is not null' )
				->order( 'rand()' )
				->limit( $offset )
				->setIntegrityCheck( false );

		$projectRows = $this->fetchAll( $select );

		return $projectRows;

	}

	public function readAllTypesOfFeed ( $userId, $offset ) {
		// for your eyes only
		$suffix = $offset . 0;
		$select = $this->select()
			->distinct()
			->from(new Zend_Db_Expr("
				(SELECT DISTINCT followers.* FROM(
				SELECT `project_id`, `creation_date` FROM 
				  ( SELECT * FROM `engine4_whmedia_stream` WHERE user_id = $userId OR user_id IN 
					(SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = $userId) 
				  ORDER BY `project_id` DESC) 
				AS tmp_stream GROUP BY tmp_stream.`project_id`
				UNION
				SELECT `tm`.`resource_id` as `project_id`, `fh`.`creation_date` as `creation_date` FROM engine4_whmedia_followhashtag AS fh
				  JOIN engine4_core_tags AS t ON fh.hashtag_id=t.tag_id
				  JOIN engine4_core_tagmaps AS tm ON tm.tag_id=t.tag_id 
				  WHERE follower_id=$userId ORDER BY project_id DESC
				) AS followers)
			   AS tmp_stream_projects, `engine4_whmedia_projects`
			") )
			->setIntegrityCheck(FALSE)  
			->where(new Zend_Db_Expr('t.`project_id` = `tmp_stream_projects`.`project_id`'))
			->limit( 5, $suffix );

		$projectRows = $this->fetchAll( $select );

		if ( count( $projectRows ) == 0 ) {
			throw new Exception( 'No results found' );
		}

		return $projectRows;
	}
	
	public function readFeedByProjectId ( $projectId ) {
		$row = $this->fetchRow( array(
			'project_id = ?' => $projectId
		) );

		return $row;
	}

	public function readNotRedundantFeedByProjectId ( $projectCollection ) {
		$collection = implode( ',', $projectCollection );

		$select = $this->select()
			->where( 'project_id not in ( '. $collection .')' );

		$row = $this->fetchRow( $select );

		return $row;
	}

	public function updateProjectViews ( $projectId ) {
		$row = $this->fetchRow( array(
			'project_id = ?' => $projectId
		) );

		$row->project_views += 1;
		$row->save();

		return ( string )$row->project_views;
	}

	public function getRandomFeed ( $userId ) {
		$select = $this->select()
			->where( 'user_id = ?', $userId )
			->limit( 1 );

		$row = $this->fetchRow( $select );

		return $row;
	}

	public function readFeedByUserId ( $userId, $offset ) {
		$suffix = $offset . '0';
		
		$select = $this->select()
			->where( 'user_id ='. $userId )
			->order( array( 'project_id DESC' ) )
			->limit( 5, $suffix );

		$rows = $this->fetchAll( $select );

		if ( count( $rows ) < 1 ) {
			throw new Exception( 'No results found' );
		}

		return $rows;


	}

	public function readRandomFeedByUserId ( $userId ) {
		$suffix = $offset . '0';
		
		$select = $this->select()
			->where( 'user_id ='. $userId )
			->order( array( 'rand()' ) )
			->limit( 1 );

		$row = $this->fetchRow( $select );

		return $row;		
	}

	public function readHashtagFeedByUserId ( $user, $params ) {
		$select = $this->select()
			->from( array( 'fh' => 'engine4_whmedia_followhashtag' ) )
			->joinLeft( array( 'tm' => 'engine4_core_tagmaps'), 'fh.hashtag_id = tm.tag_id' )
			->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'tm.resource_id = p.project_id' )
			->where( 'fh.follower_id = ?', $params[ 'user_id' ] )
			->order( 'fh.creation_date DESC' )
			->setIntegrityCheck( false );

		if ( isset( $params[ 'offset' ] ) ) {
			$suffix = ( int )$params[ 'offset' ] . '0';
			$select->limit( 5, $suffix );
		}

		$rows = $this->fetchAll( $select );

		if ( count( $rows ) < 1 ) {
			return array();
		}

		return $rows;
	}


	public function readLikesByUserId ( $user, $params ) {
		$select = $this->select()
			->from( array( 'l' => 'engine4_core_likes' ) )
			->joinLeft( array( 'p' => 'engine4_whmedia_projects'), 'l.resource_id = p.project_id' )
			->where( 'l.poster_id = ?', $params[ 'user_id' ] )
			->order( 'l.like_id DESC' )
			->setIntegrityCheck( false );

		if ( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
			$suffix = $params[ 'offset' ] * $rows;
			$select->limit( 5, $suffix );
		}

		$rows = $this->fetchAll( $select );

		if ( count( $rows ) < 1 ) {
			return array();
		}

		return $rows;
	}

	public function readFeedByFavoId ( $user, $params ) {
		$select = $this->select()
			->from( array( 'p' => 'engine4_whmedia_projects' ) )
			->joinLeft( array( 'fci' => 'engine4_whmedia_favcircleitems' ), 'p.project_id = fci.project_id' )
			->where( 'fci.favcircle_id = ?', $params[ 'favcircle_id' ] );

		if ( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
			$suffix = $params[ 'offset' ] * $rows;
			$select->limit( 5, $suffix );
		}

		if ( isset( $params[ 'order' ] ) ) {
			$select->order( $params[ 'order' ] );
		}

		$select->setIntegrityCheck( false );

		$rows = $this->fetchAll( $select );

		if ( count( $rows ) < 1 ) {
			if ( isset( $params[ 'silentError' ] ) ) {
				return array();
			}

			throw new Exception( 'No results found.' );
		}

		return $rows;
	}

	public function readAndCountFeedByKeyword ( $keyword ) {

		$select = $this->select();
        $select->from( $this, array('count(*) as result_count' ) );
        $select->where( 'title LIKE ?', '%'. $keyword .'%' );

        $rows = $this->fetchAll($select);
        
        return( $rows[0]->result_count );

	}

	public function readFeedByTitle ( $user, $params ) {
		$select = $this->select();
		$select->where( 'title LIKE ?', '%'. $params[ 'keyword' ] .'%' );

		if ( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
			$suffix = $rows * (int)$params[ 'offset' ];
			$select->limit( $rows, $suffix );
		}

		$rows = $this->fetchAll( $select );

		if ( count( $rows ) < 1 ) {
			throw new Exception( 'No results found.' );
		}

		return $rows;
	}

	public function fetchTrending ( $user, $params ) {

		$user_id = $user->getIdentity();

		$db = Engine_Db_Table::getDefaultAdapter();

		$selectedFollowers = $db->select()
			->from( array( 'f' => 'engine4_whmedia_follow' ), array( 'f.user_id' ) )
			->where( 'f.follower_id = ?', $user_id );

		$selectedViewer = $db->select()
			->from( array( 'stream' => 'engine4_whmedia_stream' ), array( 'stream.project_id' ) )
			->where( 'stream.user_id = ?', $user_id )
			->orWhere( 'stream.user_id in ?', $selectedFollowers );

		$selectedHashtag = $db->select()
			->from( array( 'fh' => 'engine4_whmedia_followhashtag' ), array( 'tm.resource_id AS project_id' ) )
			->joinLeft( array( 'tm' => 'engine4_core_tagmaps' ), 'fh.hashtag_id = tm.tag_id', array( '' ) )
			->where( 'fh.follower_id = ?', $user_id )
			->order( array( 'project_id DESC' ) );

		$unionQuery = $db->select()
			->union( array( $selectedViewer, $selectedHashtag ) );
			
		$select = $db->select();
		$select->from( array( 'feeds' => new Zend_Db_Expr( '(' . ( string ) $unionQuery . ')' ) ), array( '' ) );
		$select->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = feeds.project_id' );
		$select->order( 'p.project_views desc' );
		$select->where( 'p.creation_date > DATE_SUB(NOW(), INTERVAL 1 WEEK)' );

		if( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
			$suffix = $rows * (int)$params[ 'offset' ];
			$select->limit( $rows, $suffix );
		}				

		$rows = $objTable->fetchAll( $objSelect );

		if ( count( $rows ) < 1 ) {
			throw new Exception( 'No results found' );
		}

		return $rows;

	}


}