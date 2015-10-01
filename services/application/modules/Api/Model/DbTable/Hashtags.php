<?php
class Api_Model_DbTable_Hashtags extends Engine_Db_Table {
	protected $_name     = 'core_tags';
	protected $_rowClass = 'Api_Model_Hashtag';
	
	public function find ( $user, $id ) {
		$row = parent::find( $id )->current();
		$row->isFollowed( $user );
		return $row->toArray();
	}
	
	public function findByKeyword ( $user, $params ) {
		Engine_Api::_()->user()->setViewer( $user );        
		
		$suffix = $offset ."0";
		$select = $this->select()
						->where( 'text LIKE ?', '%'. $params[ 'keyword' ] .'%' )
						->limit( 10, $suffix );

		$rowSet = $this->fetchAll( $select );
		
		return $rowSet;		
	}

	public function readAndCountHashtagPostByHashtagId ( $hashtagId ) {
			$dbTableTagMaps = Engine_Api::_()->getDbtable('TagMaps', 'core');

			$select = $dbTableTagMaps->select()
				->from( array( 'tagmap' => 'engine4_core_tagmaps' ), array( '' ) )
				->joinLeft( array( 'project' => 'engine4_whmedia_projects' ), 'tagmap.resource_id = project.project_id', array( 'project.*' ) )
				->where( 'tag_id ='. $hashtagId )
				->setIntegrityCheck( false )
				->order( array( 'project.project_id DESC') );

			$row = $dbTableTagMaps->fetchAll( $select );
			
			return count( $row );
	}
	
}