<?php

class Api2_Api_Storage extends Core_Api_Abstract {

	/**
	*	Note : This class interacts with the Api2/Model/DbTable classes
	*/

	public function fetchStorageByCoverFileId ($coverFileId) {
		$storageTable = Engine_Api::_()->getDbTable( 'storages', 'api2');
		return $storageTable->readStorageByCoverFileId( $coverFileId );

	}

	public function fetchStorageByPhotoIdAndUserId ( $photoId, $userId ) {
		$table = Engine_Api::_ ()->getDbTable ( 'files', 'storage' );
		
		$select = $table->select ();
		$select->where ( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $photoId, $userId );
		$fetchData = $table->fetchRow ( $select );

		return $fetchData->storage_path;
	}

	
}