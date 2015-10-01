<?php
class Api2_Model_DbTable_Storages extends Engine_Db_Table{
	/**
	*	Note : This class handles queries from the database
	*/
	
	//protected $_name     = 'whmedia_storages';
	// protected $_rowClass = 'Api2_Model_Storages';
	protected $_name = 'storage_files';

	public function readStorageByCoverFileId($coverFileId){
		$objStorageTable = Engine_Api::_()->getDbTable( 'files', 'storage' );
		$objStorageDb = $objStorageTable->getAdapter();

		$where     = "parent_id = ?";			
		$objStorageSelect = $objStorageTable->select()
			->from( array( 'sf' => 'engine4_storage_files' ) )
			->where( new Zend_Db_Expr( $this->_quoteInto( $where, array( 'id' => $coverFileId ) ) ) );

		return $objStorageDb->fetchAll( $objStorageSelect );

	}

	public function readStorageByCoverFileId2( $coverFileId ){
		$dbTableStorage = Engine_Api::_()->getDbTable( 'files', 'storage' );
	
		$select = $dbTableStorage->select();
		$select->from( array( 'sf' => 'engine4_storage_files' ) );
		$select->where( 'parent_id = ?', $coverFileId );
		$select->where( 'mime_minor = "jpeg"' );

		return $dbTableStorage->fetchAll( $select );

	}
	
	public function readImagePathsByPhotoId ( $photoId ) {
		$file    = Engine_Api::_()->getDbTable( 'files', 'storage' );
		$fileRow = $file->fetchRow( 'file_id ='. $photoId );
		return $fileRow;
	}

	protected function _quoteInto( $where, $values = array() ) {
		foreach( $values as $value ) {
			$where = $this->getAdapter()->quoteInto( $where, $value, '', 1 ); 
		}
		return $where;
	}


	public function readByFeatureType( $featureType, $keyword ) {

		switch( $featureType ) {
			case 'hashtag' :
				$tag_id = $keyword;
				$dbTableHashtag = Engine_Api::_()->getDbTable( 'hashtags', 'api2' );
				$select = $dbTableHashtag->select()
					->from( array( 'tm' => 'engine4_core_tagmaps' ), array( '' ) )
					->joinLeft( array( 'p' => 'engine4_whmedia_projects' ), 'tm.resource_id = p.project_id', array( 'cover_file_id' ) )
					->where( 'tm.tag_id = ?', $tag_id )
					->where( 'p.cover_file_id is not null' )
					->limit( 1 )
					->setIntegrityCheck( false );

				$row = $dbTableHashtag->fetchRow( $select );

				if ( count( $row ) < 1 ) {
					return array(
						'storage_path' => 'public/no-image-m.jg'
					);
				}				

				$file = $this->readStorageByCoverFileId2( $row->cover_file_id );

				if ( empty( $file[ 0 ] ) ) {
					return array(
						'storage_path' => 'public/no-image-m.jpg'
					);
				}

				return $file[ 0 ];

			break;

			case 'user' :

				$dbTableUser = Engine_Api::_()->getDbTable( 'users', 'api2' );
				$select = $dbTableUser->select()
					->where( 'username LIKE ?', '%'. $keyword .'%' )
					->where( 'photo_id !=0' )
					->order( 'rand()' )
					->limit( 1 );

				$row = $dbTableUser->fetchRow( $select );

				if ( !is_null( $row ) ) {
					$table = Engine_Api::_ ()->getDbTable ( 'files', 'storage' );
					$select = $table->select ();
					$select->where ( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $row->photo_id, $row->user_id );
					$fetchData = $table->fetchRow ( $select );
					$storagePath = $fetchData->storage_path;

					if ( empty( $storagePath ) ) {
						return array(
							'storage_path' => 'public/no-image-m.jpg'
						);
					}

					return array(
							'storage_path' =>$storagePath
					);
				}
				
				return array(
					'storage_path' => 'public/no-image-m.jpg'
				);

			break;

			case 'favo' :

				$dbTableFavo = Engine_Api::_()->getDbTable( 'favos', 'api2' );
				$select = $dbTableFavo->select()
					->where( 'title LIKE ?', '%'. $keyword .'%' );

				$row = $dbTableFavo->fetchRow( $select );

				$storageTable = Engine_Api::_()->getDbTable( 'files', 'api' );
				
				try {
					$file = $storageTable->getFavoImage( $favo->photo_id );

					$storagePath = $file->getStoragePath();
					
				} catch ( Exception $e ) {
					$storagePath = 'public/no-image-m.jpg';
				}

				return array(
					'storage_path' => $storagePath
				);

			break;


			case 'feed' :

				$dbTableFeed = Engine_Api::_()->getDbTable( 'projects', 'api2' );
				$select = $dbTableFeed->select()
					->where( 'title LIKE ?', '%'. $keyword .'%' );

				$row = $dbTableFeed->fetchRow( $select );

				$file = $this->readStorageByCoverFileId( $row->cover_file_id );

				if ( empty( $file[ 0 ] ) ) {
					return array(
						'storage_path' => 'public/no-image-m.jpg'
					);
				}


				return $file[ 0 ];

			break;

		}

	}

}