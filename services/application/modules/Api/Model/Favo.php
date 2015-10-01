<?php
class Api_Model_Favo extends Core_Model_Item_Abstract {
	protected $_apiData;
	
	public function init () {
		$this->_apiData = new StdClass();
		
		$this->_apiData->favcircle_id = $this->favcircle_id;
		$this->_apiData->user_id      = $this->user_id;
		$this->_apiData->title        = $this->title;
		$this->_apiData->category     = $this->category;
		$this->_apiData->photo_id     = $this->photo_id;
		$this->_apiData->private      = $this->private;
	}
	
	public function toObject () {
		return $this->_apiData;
	}

	public function isProjectAdded ( $projectId ) {
		$followTable   = Engine_Api::_()->getDbTable( 'favcircleitems', 'whmedia' );
		$followAdapter = $followTable->getAdapter();

		$select = $followTable->select()
							  ->where( 'project_id =' . $projectId.' AND favcircle_id ='. $this->_apiData->favcircle_id );

		$rowSet = $followAdapter->fetchAll( $select );

		if( $rowSet ) {
			return true;
		}
		return false;
	}
	
	public function isFavoFollowed( $user ) {
		$followFavoTable = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );
		$rowSet = $followFavoTable->fetchRow( array( 'favcircle_id = ?' => $this->_apiData->favcircle_id, 'follower_id = ?' => $user->getIdentity() ) );
		if ( $rowSet ) {
			return true;
		}
		return false;
	}
}