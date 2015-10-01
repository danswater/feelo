<?php
class Api2_Model_DbTable_Favos extends Engine_Db_Table {
	
	/**
	*	Note : This class handles queries from the database
	*/

	protected $_name     = 'whmedia_favcircle';

	public function readFeaturedFavoFollowedByAdmin ( $offset ) {

		$select = $this->select()
				->from( array( 'ff' => 'engine4_whmedia_followfav' ), array( 'favcircle_id', 'follower_id' ) )
				->joinLeft( array( 'fc' => 'engine4_whmedia_favcircle' ), 'fc.favcircle_id = ff.favcircle_id', array( 'title', 'user_id', 'photo_id' ) )
				->joinLeft( array( 'fci' => 'engine4_whmedia_favcircleitems' ), 'fci.favcircle_id = fc.favcircle_id', array( 'project_id' ) )
				->where( 'fci.project_id is not null' )
				->where( 'ff.follower_id in (1,2)' )
				->order( 'rand()' )
				->limit( $offset )
				->setIntegrityCheck( false );

		$favoRows = $this->fetchAll( $select );

		return $favoRows;

	}

	public function readAndCountFavo ( $userId ) {

    	$results = $this->getDefaultAdapter()->query( "SELECT *
      		FROM engine4_whmedia_favcircleitems AS fci
      		WHERE user_id = ". $userId  )->fetchAll();

		if ( !$results ) {
			return 0;
		}

		return count( $results );

	}
	
	public function deleteFavosWithNoFeedInformation () {
		$select = $this->select()
				->from( array( 'ff' => 'engine4_whmedia_followfav' ), array( 'favcircle_id', 'follower_id' ) )
				->joinLeft( array( 'fc' => 'engine4_whmedia_favcircle' ), 'fc.favcircle_id = ff.favcircle_id', array( 'title', 'user_id', 'photo_id' ) )
				->joinLeft( array( 'fci' => 'engine4_whmedia_favcircleitems' ), 'fci.favcircle_id = fc.favcircle_id', array( 'favcircleitem_id', 'project_id' ) )
				->where( 'fci.project_id is not null' )
				->setIntegrityCheck( false );
				
		$favos = $this->fetchAll( $select );

		$empty = array();
		$favoEmpty = array();
		$response = array();
		foreach( $favos as $key => $favo ) {
			// read project
			$dbTableProjects = Engine_Api::_()->getDbTable( 'projects', 'api2' );
			$project = $dbTableProjects->readFeedByProjectId( $favo->project_id );

			if( !$project ) {
				$favoEmpty[] = $favo->favcircleitem_id;
				$empty[] = $favo->project_id;
			}
		}

		for( $i = 0; $i < count( $empty ); $i++ ) {
		
		$test = $this->getDefaultAdapter()->query( "DELETE FROM engine4_whmedia_favcircleitems WHERE favcircleitem_id = " . $favoEmpty[ $i ] )->execute();

			$response[] = 'favcircleitem_id '. $favoEmpty[ $i ] .' is deleted because project_id '. $empty[ $i ] .' does not exists anymore';
		}

		return $response;
	}

	public function readRandomFeedByFavoId ( $favoId ) {
		$dbTableFavcircleitems = Engine_Api::_()->getDbtable('favcircleitems', 'whmedia');

		$select = $dbTableFavcircleitems->select()
			->from( array( 'f' => 'engine4_whmedia_favcircleitems' ), array( '' ) )
			->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'p.project_id = f.project_id')
			->where( 'f.favcircle_id ='. $favoId )
			->order( array( 'rand()' ) )
			->limit( 1 )
			->setIntegrityCheck( false );

		$row = $dbTableFavcircleitems->fetchRow( $select );

		if ( count( $row ) > 1 ) {
			throw new Exception( 'No results found' );
		}

		return $row;
	}

	public function readFollowedFavos ( $user, $params ) {

    	$select = $this->select()
    		->from( array( 'ff' => 'engine4_whmedia_followfav') )
    		->joinLeft( array( 'fc' => 'engine4_whmedia_favcircle' ), 'ff.favcircle_id = fc.favcircle_id' )
    		->where( 'ff.follower_id = ?', $params[ 'user_id' ] );

    	if ( isset( $params[ 'offset' ] ) ) {
    		$suffix = ( int ) $params[ 'offset' ] .'0';
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

	public function readUserFavos ( $user, $params ) {

    	$select = $this->select()
    		->from( array( 'fc' => 'engine4_whmedia_favcircle') )
    		->where( 'fc.user_id = ?', $params[ 'user_id' ] );

    	if ( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
    		$suffix = $rows * ( int ) $params[ 'offset' ];
    		$select->limit( $rows, $suffix );
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

	public function isFavoFollowed( $user, $params ) {
		$followFavoTable = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );
		$rowSet = $followFavoTable->fetchRow( array(
			'favcircle_id = ?' => $params[ 'favcircle_id' ],
			'follower_id = ?'  => $params[ 'user_id' ] ) );
			
		if ( $rowSet ) {
			return true;
		}
		return false;
	}

	public function fetchAllByKeyword ( $userId, $params ) {
		$select = $this->select()
			->where( 'title LIKE ?', '%'. $params[ 'keyword' ] .'%' )
			->order( array( 'title DESC' ) );

		if ( $params[ 'offset' ] ) {
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

	public function readAndCountFavoByKeyword ( $keyword ) {

		$select = $this->select();
        $select->from( $this, array('count(*) as result_count' ) );
        $select->where( 'title LIKE ?', '%'. $keyword .'%' );

        $rows = $this->fetchAll($select);
        
        return( $rows[0]->result_count );

	}

}