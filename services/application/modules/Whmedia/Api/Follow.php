<?php
class Whmedia_Api_Follow extends Core_Api_Abstract {

	protected $_manageNavigation;
	protected $_moduleName = 'Whmedia';
	protected $tagId;
	protected $user;

	public function set( $key, $value ) {
		if( $key == "tagId" ) {
			$tagId = $value;
		}

		if( $key == "user" ) {
			$user = $value;
		}
	}

	public function toggleHashTag( $params = array() ) {

		$objTable = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
		$objDb    = $objTable->getAdapter();

		$where     = "(hashtag_id = ? AND follower_id = ?)";
		$values    = array( $params[ 'tagId' ], $params[ 'user' ]->getIdentity() );
		$objSelect = $objTable->select()
			->from( $objTable, array( 'count(*) as count', 'follow_id' ) )
			->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) );

		$arrResponse = $objDb->fetchRow( $objSelect );
		if( $arrResponse[ 'count' ] === 1 ) {

			$objArr = $objTable->delete( 'follow_id ='. $arrResponse[ 'follow_id' ] );
			return array( 
				'message' => 'Unfollowed', 
				'data'    => $objArr
			);

		}

		try{
			//Bug response throw an error
			$objCreate = $objTable->createRow( array(
				'hashtag_id'  => (int)$params[ 'tagId' ],
				'follower_id' => $params[ 'user' ]->getIdentity()
			) );

			$test = $objCreate->save();
			//Zend_Debug::dump( $test ); exit;
		}
		catch( Zend_Db_Table_Row_Exception $e ) {
			//TODO
		}

		$where     = "(hashtag_id = ? AND follower_id = ?)";
		$values    = array( $params[ 'tagId' ], $params[ 'user' ]->getIdentity() );
		$objSelect = $objTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) );

		$objFollow = $objSelect->query()->fetch();
		return array( 
			'message' => 'Followed',
			'data'    => $objFollow
		);

	}

	protected function _quoteInto( $objDb, $where, $values = array() ) {
		
		$db = new Zend_Db();

		foreach( $values as $value ) {
			$where = $objDb->quoteInto( $where, $value, '', 1 ); 
		}

		return $where;

	}

	public function isFollowed() {
		//Todo check if a particular user is following a particular tag
	}

}