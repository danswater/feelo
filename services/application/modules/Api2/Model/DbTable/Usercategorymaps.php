<?php
class Api2_Model_Dbtable_Usercategorymaps extends Engine_Db_Table {
	protected $_name    = 'core_usercategorymaps';
	protected $_primary = 'usercategory_id';

	public function isAdded ( $userId, $params ) {
		$select = $this->select()
			->where( 'user_id = ?', $userId )
			->where( 'category_id = ?', $params[ 'category_id' ] );

		$userCategoryMaps = $this->fetchAll( $select );

		return count( $this->fetchAll( $select ) );
	}

	public function readByCategoryId ( $userId, $categoryId ) {
		$select = $this->select()
			->from( array( 'ucm' => 'engine4_core_usercategorymaps' ) )
			->joinLeft( array( 'c' => 'engine4_core_categories' ), 'ucm.category_id = c.category_id' )
			->where( 'ucm.category_id = ?', $categoryId )
			->where( 'ucm.user_id = ?', $userId )
			->setIntegrityCheck( false );

		$row = $this->fetchRow( $select );

		return $row;
	}

	public function readMaxOrder () {
		return $this->fetchRow(
				$this->select()
					->from( $this, array( new Zend_Db_Expr( 'max(`order`) as maxOrder' ) ) )
			);
	}
}