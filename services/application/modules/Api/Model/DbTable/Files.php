<?php
class Api_Model_DbTable_Files extends Storage_Model_DbTable_Files {

  protected $_name     = 'storage_files';
  protected $_rowClass = 'Api_Model_File';
  // Methods  
  public function getImage ( $parentId ) {
  	$row =  $this->fetchRow( array( 'parent_id = ?' => $parentId, 'mime_minor = ?' => 'jpeg' ) );
  	if ( !$row ) {
  		throw new Exception( 'No image found in the storage model' );
  	}
  	return $row;
  }

  public function getVideo ( $parentId ) {
  	return $this->fetchRow( array( 'parent_id = ?' => $parentId, 'extension = ?' => 'mp4' ) );
  }

  public function getFavoImage ( $fileId ) {
    $row = $this->fetchRow( array( 'file_id = ?' => $fileId ) );
    if ( !$row ) {
      throw new Exception( 'No image found in the storage model' );
    }
	return $row;
  }

}