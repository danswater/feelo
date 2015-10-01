<?php
class Api_Model_Followfavo extends Whmedia_Model_Followfav {
	public function toObject () {
		$data = new StdClass();

		$data->favcircle_id = $this->favcircle_id;
		$data->user_id      = $this->user_id;
		$data->title        = $this->title;
		$data->category     = $this->category;

		return $data;
	}

	public function isFollowed ( $projectId ) {
		$followTable   = Engine_Api::_()->getDbTable( 'favcircleitems', 'whmedia' );
		$followAdapter = $followTable->getAdapter();
		try {
			$select = $followTable->select()
					->where( 'project_id =' . $projectId.' AND favcircle_id ='. $this->favcircle_id );

			$rowSet = $followAdapter->fetchAll( $select );

		} catch ( Exception $e ) {
			print_r( $e );
		}

		if( $rowSet ) {
			return true;
		}
		return false;
	}
	
	public function isFavoFollowed( $user ) {
		$followFavoTable = Engine_Api::_()->getDbTable( 'followfav', 'whmedia' );
		$rowSet = $followFavoTable->fetchRow( array( 'favcircle_id = ?' => $this->favcircle_id, 'follower_id = ?' => $user->getIdentity() ) );
		if ( $rowSet ) {
			return true;
		}
		return false;
	}
}