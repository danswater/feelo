<?php
class Api2_Api_Category extends Core_Api_Abstract {

	public function fetchHashtagCategories ( $user, $params ) {
		$dbTablecategories = Engine_Api::_()->getDbTable( 'categories', 'api2' );
		$categories = $dbTablecategories->fetchHashtagCategories( $user, $params );

		$cat = $categories->toArray();
		if ( empty( $cat ) ) {
			throw new Exception( 'No results found.' );
		} 

		$ret = array();
		foreach( $categories as $key => $category ) {
			$dbTableUserCategoryMaps = Engine_Api::_()->getDbTable( 'usercategorymaps', 'api2' );
			$isAdded = $dbTableUserCategoryMaps->isAdded( $user->getIdentity(), array(
				'category_id' => $category->category_id
			) );

			$arrCategory = array();

			$arrCategory[ 'category_id' ] = $category->category_id;
			$arrCategory[ 'title' ]       = $category->title;
			$arrCategory[ 'added' ]       = $isAdded;

			$ret[] = $arrCategory;
		}


		return array(
			'Categories' => $ret
		);
	}

	public function fetchCategories ( $user, $params ) {
			$dbTablecategories = Engine_Api::_()->getDbTable( 'categories', 'api2' );

			$categories = $dbTablecategories->fetchCategories( $user, $params );

			$cat = $categories->toArray();
			if ( empty( $cat ) ) {
				throw new Exception( 'No results found.' );
			} 

	        $tableTag = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
	        $dbTag    = $tableTag->getAdapter();

	        $key = 0;
	        $ret = array();
	        foreach( $categories as $category ) {

		        // $select = $tableTag->select()
		        //   ->where( 'hashtag_id='. $category->tag_id .' AND follower_id='. $params[ 'user_id' ] .'');

		        // $rowSet = $select->query()->fetch();

		        // if( is_array( $rowSet ) ) {
		        //   $isFollowed = 1;
		        // } else {
		        // 	$isFollowed = 0;
		        // }

		        $dbTableTagmaps = Engine_Api::_()->getDbtable('TagMaps', 'core');

		        $select = $dbTableTagmaps->select()
		            ->where( 'tag_id ='. $category->tag_id );

		        $result = $dbTableTagmaps->fetchAll( $select );
		            
		        $resultCount = count( $result );

		        $ret[ $key ][ 'tag_id' ]       = $category->tag_id;
		        $ret[ $key ][ 'text']          = $category->text;
		        $ret[ $key ][ 'is_followed' ]  = 0;
		        $ret[ $key ][ 'result_count' ] = $resultCount;
	        	$key++;
	        }

			return array(
				'Hashtags' => $ret
			);
	}

	public function createCategoryPreferences ( $user, $params ) {
		$dbTablecategories = Engine_Api::_()->getDbTable( 'categories', 'api2' );

		$params[ 'category_id' ] = explode( ',', $params[ 'category_id' ] );
		$params[ 'category_id'] = array_unique( $params[ 'category_id' ] );

		$userId = $user->getIdentity();
		$ret = '';
		foreach( $params[ 'category_id' ] as $key => $value ) {
			$isValid = $dbTablecategories->isValid( $value );
			if ( $isValid ) {

				$dbTableUserCategoryMaps = Engine_Api::_()->getDbTable( 'usercategorymaps', 'api2' );
				$isAdded = $dbTableUserCategoryMaps->isAdded( $userId, array(
					'category_id' => $value
				) );

				if ( !$isAdded ) {
					$max = $dbTableUserCategoryMaps->readMaxOrder();

					if ( $max->maxOrder === 0 ) {
						$max->maxOrder = 1;
					} else {
						$max->maxOrder += 1;
					}
					$order = $max->maxOrder;

					$category = array();
					$arrCategory[ 'category_id' ] = $value;
					$arrCategory[ 'user_id' ]     = $userId;
					$arrCategory[ 'order' ]       = $order;

					$dbTablecategories->createCategoryPreferences( $arrCategory );

					$category = $dbTableUserCategoryMaps->readByCategoryId( $userId, $value );

					$retCategory = array();
					$retCategory[ 'category_id' ] = $category->category_id;
					$retCategory[ 'title' ]       = $category->title;
					$retCategory[ 'added' ]       = 1;

					$ret = $retCategory;

				} else {
					throw new Exception( 'Some category is already added.' );
				}

			} else {
				throw new Exception( 'Category not found' );
			}		
		}

		return array(
			'Categories' => $ret
		);
	}

	public function deleteCategoryPreferences ( $user, $params ) {
		$dbTablecategories = Engine_Api::_()->getDbTable( 'categories', 'api2' );

		$userId = $user->getIdentity();
		$category = $dbTablecategories->deleteCategoryPreferences( $userId, $params[ 'category_id' ] );

		return array(
			'Categories' => $category
		);
	}

}