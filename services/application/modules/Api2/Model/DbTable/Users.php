<?php
class Api2_Model_DbTable_Users extends Engine_Db_Table {

	protected $_name     = 'users';

	public function readAboutMeInfo ( $userId ) {

    	$results = $this->getDefaultAdapter()->query( "SELECT *
      		FROM engine4_core_fields_values AS fv
      		WHERE item_id = ". $userId  )->fetchAll();

    	return $results;
   
	}

	public function readByUsername ( $username ) {

		$select = $this->select()
			->where( 'username = ?', $username );

		$row = $this->fetchRow( $select );

		if( !$row ) {
			throw new Exception( 'No result found' );
		}

		return $row;

	}

	public function fetchAllByKeyword ( $userId, $params ) {
		$select = $this->select();
		$select->where( 'username LIKE ? OR displayname LIKE ?', '%'. $params[ 'keyword' ] .'%', '%'. $params[ 'keyword' ] .'%' );
		$select->order( array( 'user_id DESC' ) );

		if ( isset( $params[ 'offset' ] ) ) {
			$rows   = 5;
			$suffix = $params[ 'offset' ] * $rows;

			$select->limit( $rows, $suffix );
		}

		$rows = $this->fetchAll( $select );

		if ( count( $rows ) < 1 ) {
			throw new Exception( 'No results found.' );
		}

		return $rows;
	}

	public function readAndCountUserByKeyword ( $keyword ) {

		$select = $this->select();
        $select->from( $this, array('count(*) as result_count' ) );
        $select->where( 'username LIKE ?', '%'. $keyword .'%' );

        $rows = $this->fetchAll($select);
        
        return( $rows[0]->result_count );

	}

}