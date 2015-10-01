<?php
class Api_Model_Hashtag extends Core_Model_Item_Abstract {
	//protected $tagId;
	//protected $text;	
	//protected $is_followed;
	//protected $result_count;
	
	public function init () {
		$this->isFollowed(  Engine_Api::_()->user()->getViewer() );
		$this->getResultCount();
	}
	public function isFollowed(  $user ) {
	
        $tableTag = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
        $dbTag = $tableTag->getAdapter();

        $select = $tableTag->select()
          ->where( 'hashtag_id='. $this->tag_id .' AND follower_id='. $user->getIdentity() );

        $rowSet = $select->query()->fetch();

        if( is_array( $rowSet ) ) {
          $value = true;
        }
        $value = false;

		$this->_data[ 'is_followed' ] = (int)$value;
		return $this->is_followed;
	}
	
	protected function getResultCount () {
		$objTagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
		$objTagMapDb = $objTagMapTable->getAdapter();

		$objSelect = $objTagMapTable->select()
			->where( 'tag_id ='. $this->tag_id );
		
		$result = $objTagMapDb->fetchAll( $objSelect );

		$this->_data[ 'result_count' ] = count( $result );
		
		return $this->result_count;
	}
	
}