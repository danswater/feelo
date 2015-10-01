<?php
class Api2_Model_Dbtable_Hashtags extends Engine_Db_Table {
	protected $_name     = 'core_tags';

	public function fetchByHashtagName ( $hashtagName, $offset = 10, $limit = 20 ) {

		$rows = 5;
		$suffix = $offset * $limit;
		
		$select = $this->select()
			->from( array( 'h' => 'engine4_core_tags' ), array( '' ) )
			->joinLeft( array( 'tm' => 'engine4_core_tagmaps' ), 'h.tag_id=tm.tag_id', array( 'resource_id' ) )
			->where( 'h.text = "'. $hashtagName .'"' )
			->order( 'tm.tagmap_id DESC' )
			->limit( $rows, $suffix )
			->setIntegrityCheck( false );		

		$row = $this->fetchAll ( $select );

		return $row;
	}

	public function readByHashtagName ( $hashtagName ) {

		$select = $this->select()
			->from( array( 'h' => 'engine4_core_tags' ) )
			->joinLeft( array( 'tm' => 'engine4_core_tagmaps' ), 'h.tag_id=tm.tag_id' )
			->where( 'h.text = "'. $hashtagName .'"' )
			->order( 'tm.tagmap_id DESC' )

			->setIntegrityCheck( false );		

		$row = $this->fetchRow ( $select );

		if ( count( $row ) < 1 ) {
			throw new Exception( 'No result found' );
		}

		return $row;
	}

	public function readFeedByHashtagId ( $tagId, $offset ) {
		$dbTableTagmaps = Engine_Api::_()->getDbtable( 'TagMaps', 'core' );

		$suffix = $offset . '0';

		$select = $dbTableTagmaps->select()
			->from( array( 'tagmap' => 'engine4_core_tagmaps' ), array( '' ) )
			->joinLeft( array( 'project' => 'engine4_whmedia_projects' ), 'tagmap.resource_id = project.project_id', array( 'project.*' ) )
			->where( 'tag_id ='. $tagId )
			->order( array( 'project.project_id DESC') )
			->limit( 5, $suffix )
			->setIntegrityCheck( false );

		$row = $dbTableTagmaps->fetchAll( $select );

		if ( count( $rpw ) > 1 ) {
			throw new Exception( 'No results found' );
		}

		return $row;
	}

	public function readRandomFeedByHashtagId ( $tagId ) {
		$dbTableTagmaps = Engine_Api::_()->getDbtable( 'TagMaps', 'core' );

		$suffix = $offset . '0';

		$select = $dbTableTagmaps->select()
			->from( array( 'tagmap' => 'engine4_core_tagmaps' ), array( '' ) )
			->joinLeft( array( 'project' => 'engine4_whmedia_projects' ), 'tagmap.resource_id = project.project_id', array( 'project.*' ) )
			->where( 'tag_id ='. $tagId )
			->order( array( 'rand()' ) )
			->limit( 1 )
			->setIntegrityCheck( false );

		$row = $dbTableTagmaps->fetchRow( $select );

		if ( count( $row ) > 1 ) {
			throw new Exception( 'No results found' );
		}

		return $row;
	}

	public function readHashtagById ( $user, $tagId ) {

		$select = $this->select()
			->where( 'tag_id = ?', $tagId );

		$row = $this->fetchRow( $select );

		if ( count( $row ) < 1 ) {
			throw new Exception( 'No results found' );
		}

		return $row;
	}

	public function readFollowedHashtags ( $user, $params ) {

		$select = $this->select()
			->from( array( 'fh' => 'engine4_whmedia_followhashtag' ) )
			->joinLeft( array( 't' => 'engine4_core_tags' ), 'fh.hashtag_id = t.tag_id' ) 
			->where( 'fh.follower_id = ?', $params[ 'user_id' ] )
			->where( 't.tag_id is not null' );

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
			throw new Exception( 'No results found' );
		}

		return $rows;
	}

	public function fetchAllByKeyword ( $userId, $params ) {
		$select = $this->select()
			->where( 'text LIKE ?', '%'. $params[ 'keyword' ] .'%' )
			->order( array( 'tag_id DESC' ) );

		if ( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
			$suffix = $params[ 'offset' ] * $rows;

			$select->limit( $rows, $suffix );
		}

		$rows = $this->fetchAll( $select );

		if ( count( $rows ) < 1 ) {
			throw new Exception( 'No results found.' );
		}

		return $rows;
	}

	public function readAndCountHashtagByKeyword ( $keyword ) {

		$select = $this->select();
        $select->from( $this, array('count(*) as result_count' ) );
        $select->where( 'text LIKE ?', '%'. $keyword .'%' );

        $rows = $this->fetchAll($select);
        
        return( $rows[0]->result_count ); 		
	}

}