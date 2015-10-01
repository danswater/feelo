<?php
class Api2_Model_DbTable_Medias extends Engine_Db_Table {
	
	/**
	*	Note : This class handles queries from the database
	*/

	protected $_name     = 'whmedia_medias';

	// public function getMedia ( Whmedia_Model_Project $target ) {
	// 	$row = $this->fetchRow( array( 'project_id = ?' => $target->getIdentity() ) );
	// 	if ( empty( $row ) ) {
	// 		throw new Exception( 'Api_Model_DbTable_Medias: No results found' );
	// 	}
	// 	return $row;
	// }

	public function readMediaByProjectId ($projectId) {
		$where     = "project_id = ?";
		$selectedFrom[ 'whmm' ] = 'engine4_whmedia_medias';
		$selectedColumn = array( 'media_id', 'title', 'project_id', 'code', 'is_url AS url' );
		$selectedWhere[ 'id' ] = $projectId;
	
		$objMediasSelect = $this->select()
			->from( $selectedFrom, $selectedColumn )
			->where( new Zend_Db_Expr( $this->_quoteInto( $where, $selectedWhere ) ) );
		return $this->fetchAll( $objMediasSelect );
	}

	public function readMediaDetailsByMediaId ($mediaId) {
		$where     = "media_id = ?";
		$selectedFrom[ 'whmm' ] = 'engine4_whmedia_medias';
		$selectedColumn = array( 'media_id', 'title', 'project_id', 'code' );
		$selectedWhere[ 'id' ] = $mediaId;

		$objMediasSelect = $this->select()
			->from( $selectedFrom, $selectedColumn )
			->where( new Zend_Db_Expr( $this->_quoteInto( $where, $selectedWhere ) ) );
		return $this->fetchAll( $objMediasSelect );
	}

	protected function _quoteInto( $where, $values = array() ) {
		foreach( $values as $value ) {
			$where = $this->getAdapter()->quoteInto( $where, $value, '', 1 ); 
		}
		return $where;
	}

	public function readPhotoMediaByProjectId ( $projectId ) {
// select * from engine4_whmedia_medias as wm
// left join engine4_storage_files as sf on sf.parent_id = wm.media_id
// where wm.project_id = 6 and wm.code is null and wm.is_url = "" and parent_type = "whmedia_media" limit 1
		$select = $this->select()
				->from( array( 'm' => 'engine4_whmedia_medias' ) )
				->joinLeft( array( 'f' => 'engine4_storage_files' ), 'f.parent_id = m.media_id' )
				->where( 'm.code is null' )
				->where( 'm.is_url = ""' )
				->where( 'f.parent_type = "whmedia_media"' )
				->where( 'f.type not in ( "video.hd", "thumb.etalon", "video.html5" )' )
				->where( 'm.project_id = ?', $projectId )
				->setIntegrityCheck( false );

		$mediaRows = $this->fetchAll( $select );

		return $mediaRows;		
	}
}