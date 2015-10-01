<?php
class Api2_Model_DbTable_Boxes extends Engine_Db_Table {

	protected $_name    = 'whmedia_circles';
	protected $_primary = 'circle_id';

	public function readBoxByUserId( $user, $params ) {

		$select = $this->select();
		$select->where ( 'user_id = ?', $params[ 'user_id' ] );

		if ( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
			$suffix = $rows * $params[ 'offset' ];
			$select->limit( $rows, $suffix );
		}
		
		$rows = $this->fetchAll ( $select );

		if ( count( $rows ) < 1 ) {
			throw new Exception( 'No results found.' );
		}

		return $rows;

	}

	public function readAndCountUserByBoxId ( $user, $params ) {

		$dbTableCircleitems = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );

		$select = $dbTableCircleitems->select();
        $select->from( $dbTableCircleitems, array('count(*) as result_count' ) );
        $select->where( 'circle_id = ?', $params[ 'circle_id' ] );

        $rows = $this->fetchAll( $select );
        
        return( $rows[0]->result_count ); 

	}

	public function readPhotoCoverByUserId ( $user, $params ) {

		$dbTableCircleitems = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );

		$select = $dbTableCircleitems->select();
		$select->from( array( 'ci' => 'engine4_whmedia_circleitems') );
		$select->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'ci.user_id = p.user_id' );
		$select->where( 'ci.circle_id = ?', $params[ 'circle_id' ] );
		$select->where( 'p.project_id is not null' );
		$select->setIntegrityCheck( false );

		$row = $dbTableCircleitems->fetchRow( $select );

		if ( count( $row ) < 1 ) {
			return array();
		}
		return $row->toArray();

	}

	public function readSingleUserByUserId ( $user, $params ) {

		$dbTableCircleitems = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );

		$select = $dbTableCircleitems->select();
        $select->from( $dbTableCircleitems );
        $select->where( 'circle_id = ?', $params[ 'circle_id' ] );

        $rows = $dbTableCircleitems->fetchRow( $select );
        
        return $rows;	

	}

}