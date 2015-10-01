<?php
class Api_Model_DbTable_Favos extends Engine_Db_Table {

	protected $_name     = 'whmedia_favcircle';
	protected $_rowClass = 'Api_Model_Favo';

	public function fetchFavoList( $user, $params ) {
		$select = $this->select()
					   ->from( array( 'f' => 'engine4_whmedia_favcircle' ) )
					   ->columns(  array( 'favcircle_id', 'user_id', 'title', 'photo_id', 'category' ) )
					   ->where( 'user_id = ?', $user->getIdentity() )
					   ->order( array( 'f.favcircle_id DESC' ) );

		if( !is_null( $params[ 'offset' ] ) ) {
			$suffix = $params[ 'offset' ] .'0';
			$select->limit( 10, $suffix );
		}
			
		$rowSet = $this->fetchAll( $select );

		if ( empty( $rowSet->toArray() ) ) {
			throw new Exception( 'No results found' );
		}
		return $rowSet;
	}
	
	public function fetchUserFavo( $user, $params ) {
		
		$select = $this->select()
					   ->from( array( 'f' => 'engine4_whmedia_favcircle' ) ) 
					   ->columns( array( 'favcircle_id', 'user_id', 'title', 'photo_id', 'category' ) )
					   ->where( 'user_id= ?', $params[ 'user_id' ] )
					   ->order( array( 'f.favcircle_id DESC' ) );
					   
		if ( !is_null( $params[ 'offset' ] ) ) {
			$suffix = $params[ 'offset' ] . '0';
			$select->limit( 10, $suffix );
		}
		
		$rowSet = $this->fetchAll( $select );		
					   
		if ( empty( $rowSet->toArray() ) ) {
			throw new Exception( 'No results found' );
		}
		
		return $rowSet;
	}
	
	public function fetchFollowedFavo( $user, $params ) {
		
		$followfavoTable = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );
		$followfavoTable->setRowClass( 'Api_Model_Followfavo' );

		$select = $followfavoTable->select()
								  ->from( array( 'ff' => 'engine4_whmedia_followfav' ), array( '' ) )
								  ->joinLeft( array( 'f' => 'engine4_whmedia_favcircle' ), 'ff.favcircle_id = f.favcircle_id' )
								  ->where( 'ff.follower_id = '. $user->getIdentity() );
									   
		if( !is_null( $params[ 'offset' ] ) ) {
			$suffix = $params[ 'offset' ] . '0';				
			$select->limit( 10, $suffix );
		}									   
									   
		$select->setIntegrityCheck( false );
		$select->query();
		
		$rowSet = $followfavoTable->fetchAll( $select );
			
		if ( empty( $rowSet->toArray() ) ) {
			throw new Exception( 'No results found' );
		}

		return $rowSet;
	}
	
	public function fetchAFavoByCircleId ( $user, $params ) {
		
		$query = $this->fetchRow( $this->select()->where( 'favcircle_id = ?', $params[ 'favcircle_id' ] )->where( 'user_id = ?', $params[ 'user_id' ] ) );
	
		if ( !$query ) {
			throw new Exception( 'No results found' );
		}

		$data = $query->toObject();
		
		$data->status = 0;
		if ( isset( $params[ 'project_id' ] ) ) {
			$data->status = ( int )$query->isProjectAdded( $params[ 'project_id' ] );
		}
		
		$data->is_followed = ( int )$query->isFavoFollowed( $user );

		return $data;
	}

	public function readAndcountAllFavosByUserId ( $favcircleId ) {
		$fciTable = Engine_Api::_()->getDbtable('favcircleitems', 'whmedia');

		$select = $fciTable->select()
			->from( array( 'f' => 'engine4_whmedia_favcircleitems' ), array( '' ) )
			->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = f.project_id')
			->where( 'f.favcircle_id ='. $favcircleId )
			->setIntegrityCheck( false );

		$rows = $fciTable->fetchAll( $select );
		
		return count( $rows );
	}
	
	public function readFavosByUserId ( $userId, $offset ) {
		$fciTable = Engine_Api::_()->getDbtable('favcircleitems', 'whmedia');

		$suffix = $offset . '0';

		$objSelect = $fciTable->select()
			->from( array( 'f' => 'engine4_whmedia_favcircleitems' ), array( '' ) )
			->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = f.project_id')
			->where( 'f.user_id ='. $userId )
			->order( array( 'p.project_id DESC') )
			->limit( 10, $suffix )
			->setIntegrityCheck( false );

		$rows = $fciTable->fetchAll( $select );
		
		if ( count( $rows ) < 1 ) {
			throw new Exception( 'No results found' );
		}
		
		return $rows;
	}

	public function readAndCountFeedsByFavoId ( $user, $params ) {
		$fciTable = Engine_Api::_()->getDbtable('favcircleitems', 'whmedia');
		$select = $fciTable->select();
		
        $select->from( array( 'f' => 'engine4_whmedia_favcircleitems' ), array('count(*) as result_count' ) );
		$select->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = f.project_id');
        $select->where( 'favcircle_id = ?', $params[ 'favcircle_id' ] );
		$select->setIntegrityCheck( false );

        $rows = $fciTable->fetchAll($select);

        return( $rows[0]->result_count );
	}

}