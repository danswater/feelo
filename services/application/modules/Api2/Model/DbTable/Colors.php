<?php
class Api2_Model_DbTable_Colors extends Engine_Db_Table {
	
	/**
	*	Note : This class handles queries from the database
	*/

	protected $_name    = 'whmedia_colors';
	protected $_primary = 'project_id';

	public function create ( $projectId, $colors ) {

		$data = array(
			'project_id' => $projectId,
			'colors'     => serialize( $colors )
		);

		$this->insert( $data );

		return $colors;

	}

	public function readByProjectId ( $projectId ) {
		$select = $this->select()
			->where( 'project_id = ?', $projectId );
		$row = $this->fetchRow( $select );

		if ( $row ) {
			return unserialize( $row->colors );			
		}

		return false;

	}

}