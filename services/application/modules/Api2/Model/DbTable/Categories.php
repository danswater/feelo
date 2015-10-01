<?php
class Api2_Model_DbTable_Categories extends Engine_Db_Table {
	protected $_name    = 'core_categories';
	protected $_primary = 'category_id';

	public function fetchHashtagCategories ( $user, $params ) {
		$select = $this->select()
			->distinct()
			->from( array( 'tc' => 'engine4_core_tagcategorymaps' ) )
			->joinLeft( array( 'c' => 'engine4_core_categories' ), 'tc.category_id = c.category_id' )
			->order( 'tc.category_id DESC' )
			->group( array( 'tc.category_id' ) )
			->setIntegrityCheck( false );

		if ( isset( $params[ 'offset' ] ) ) {
			$rows = 5;
			$suffix = $params[ 'offset' ] * $rows;
			$select->limit( $rows, $suffix );
		}

		$rows = $this->fetchAll( $select );

		return $rows;
	}

	public function fetchCategories ( $user, $params ) {

		$rows = 5;
		$suffix = $rows * (int)$params[ 'offset' ];
		$select = $this->select()
			->from( array( 'tcm' => 'engine4_core_tagcategorymaps') )
			->joinLeft( array( 't' => 'engine4_core_tags' ), 'tcm.tag_id=t.tag_id' )
			->where( 'tcm.category_id = ?', $params[ 'category_id' ] )
			->limit( $rows, $suffix )
			->setIntegrityCheck( false );

		$rows = $this->fetchAll( $select );

		return $rows;

	}


	public function createCategoryPreferences ( $params ) {
		$dbTableUsercategorymaps = Engine_Api::_()->getDbTable( 'usercategorymaps', 'api2' );

		try {
			$dbTableUsercategorymaps->insert( $params );			
		} catch ( Exception $e ) {
			throw $e;
		}

		return true;
	}

	public function deleteCategoryPreferences ( $userId, $categoryId ) {
		$dbTableUsercategorymaps = Engine_Api::_()->getDbTable( 'usercategorymaps', 'api2' );

		$select = $dbTableUsercategorymaps->select()
			->from( array( 'ucm' => 'engine4_core_usercategorymaps' ) )
			->joinLeft( array( 'c' => 'engine4_core_categories' ), 'ucm.category_id = c.category_id' )
			->where( 'ucm.category_id = ?', $categoryId )
			->where( 'ucm.user_id = ?', $userId )
			->setIntegrityCheck( false );

		$row = $dbTableUsercategorymaps->fetchRow( $select );

		$title = $row->title;

		if ( count( $row ) < 1 ) {
			throw new Exception( 'No results found' );
		}
		
		$row->delete();

		return array(
			'category_id' => $categoryId,
			'title'       => $title,
			'added'       => 0
		);
	}

	public function isValid ( $categoryId ) {
		$select = $this->select()
			->where( 'category_id = ?', $categoryId );

		$row = $this->fetchRow( $select );

		if ( count( $row ) < 1 ) {
			return false;
		}

		return true;
	}
}