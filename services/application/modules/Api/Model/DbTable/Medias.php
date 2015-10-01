<?php
class Api_Model_DbTable_Medias extends Whmedia_Model_DbTable_Medias {
	protected $_name     = 'whmedia_medias';
	protected $_rowClass = 'Api_Model_Media';

	public function getMedia ( Whmedia_Model_Project $target ) {
		$row = $this->fetchRow( array( 'project_id = ?' => $target->getIdentity() ) );
		if ( empty( $row ) ) {
			throw new Exception( 'Api_Model_DbTable_Medias: No results found' );
		}
		return $row;
	}
} 