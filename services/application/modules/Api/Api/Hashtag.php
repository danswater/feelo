<?php
class Api_Api_Hashtag extends Api_Api_Base {

	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	protected $isFollowed = false;

	private $data = array();

	public function __set( $key, $value ) {
		$this->data[ $key ]= $value;
	}

	public function __get( $key ) {

		if( array_key_exists( $key, $this->data ) ) {
			return $this->data[ $key ];
		}

		return null;

	}

	public function unFollow() {
		$arrResponse = $this->fetchByUserAndTag();
		$first = current( $arrResponse );

		$objTable = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );		
		$objArr = $objTable->delete( 'follow_id ='. $first[ 'follow_id' ] );
		
		return array(
			'data' => array(
				'message' => 'Unfollowed', 
				'Hashtag'    => new StdClass()
			),
			'error' => array()
		);
	}

	public function follow() {

		try{

			$objTable = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
			$objCreate = $objTable->createRow( array(
				'hashtag_id'  => (int)$this->data[ 'tagId' ],
				'follower_id' => $this->data[ 'userId' ]
			) );

			$objCreate->save();
		
		}
		catch( Zend_Db_Table_Row_Exception $e ) {}

		$objArr = $this->fetchByUserAndTag();
		
		unset( $objArr[ 0 ][ 'follower_id' ] );
		unset( $objArr[ 0 ][ 'follow_id' ] );
		unset( $objArr[ 0 ][ 'hashtag_id' ] );
		unset( $objArr[ 0 ][ 'creation_date' ] );
		
		$objArr[ 0 ][ 'is_followed' ] = ( int )$this->_isFollowed( $this->data[ 'userId' ], $objArr[ 0 ] );
		
		return array( 
			'data' => array(
				'message'  => 'Followed',
				'Hashtag'     => $objArr[ 0 ],
			),
			'error' => array()
		);		
	}

	public function toggleHashTag() {
		foreach( $this->data as $key => $value ) {
			if( empty( $value ) ) {
				return array(
					'data' => array(),
					'error' => array( 'empty or missing field' )
				);
			}
		}

		$this->isFollowed();
		if( $this->isFollowed ) {
			return $this->unFollow();
		}
		else {
			return $this->follow();
		}
	}

	public function isFollowed() {
		$objTable = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
		$objDb    = $objTable->getAdapter();

		$where     = "(hashtag_id = ? AND follower_id = ?)";
		$values    = array( $this->data[ 'tagId' ], $this->data[ 'userId' ] );
		$objSelect = $objTable->select()
			->from( $objTable, array( 'count(*) as count', 'follow_id' ) )
			->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) );

		$intCount = $objDb->fetchOne( $objSelect );
		
		$this->isFollowed = $intCount;

		return array( 
			'data' => array(
				'hashtag' => $intCount
			),
			'error' => ''
		);
	}

	public function fetchAll() {
		$objTable = Engine_Api::_()->getDbtable( 'tags', 'core' );
		$objDb    = $objTable->getAdapter();

		$suffix = $this->offset ."0";
		$objSelect = $objTable->select()
			->order( array( 'tag_id DESC' ) )
			->limit( 10, $suffix );

		$objResultSet = $objDb->fetchAll( $objSelect );
		
		return $objResultSet;
	}

	public function fetchTagById( $externalRequest = false ) {
		$objTable = Engine_Api::_()->getDbtable( 'tags', 'core' );
		$objDb    = $objTable->getAdapter();

		$where     = "(tag_id)";
		$values    = array( $this->data[ 'tagId' ] );
		$objSelect = $objTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) );

		$objRowSet = $objDb->fetchRow( $objSelect );

		if( $externalRequest ) {
			return array(
				'data' => array(
					'hashtag' => $objRowSet
				),
				'error' => ''
			);
		}		

		return $objRowSet;
	}

	public function fetchByUserAndTag( $externalRequest = false ) {
		$objTable = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
		$objDb    = $objTable->getAdapter();

		$where     = "(hashtag_id = ? AND follower_id = ?)";
		$values    = array( $this->data[ 'tagId' ], $this->data[ 'userId' ] );

		$objSelect = $objTable->select()
			->from( array( 'f' => 'engine4_whmedia_followhashtag' ) )		
			->joinLeft( array( 't' => 'engine4_core_tags' ), 't.tag_id = f.hashtag_id' )
			->where( 
				new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) 
				) 
			)
			->setIntegrityCheck( false ) 
			;

		$objResultSet = $objDb->fetchAll( $objSelect );

		if( $externalRequest ) {
			return array(
				'data' => array(
					'hashtag' => $objResultSet
				),
				'error' => ''
			);
		}

		return $objResultSet;
	}

	public function fetchAllByKeyword( $keyword, $offset, $id ) {

		$objTable = Engine_Api::_()->getDbtable( 'tags', 'core' );
		$objDb    = $objTable->getAdapter();

        $suffix = $offset ."0";
		$where     = "(text LIKE ?)";
		$values    = array( '%'. $keyword .'%' );
		$objSelect = $objTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) )
			->order( array( 'tag_id DESC' ) );

		$objSelect->limit( 10, $suffix );	

		$objResultSet = $objDb->fetchAll( $objSelect );

		foreach( $objResultSet as $key => $val ) {

			if( $this->_isFollowed( $id, $val ) ) {
				$objResultSet[ $key ][ 'is_followed' ] = 1;
			}
			else {
				$objResultSet[ $key ][ 'is_followed' ] = 0;
			}
			
			$objTagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
			$objTagMapDb = $objTagMapTable->getAdapter();

			$objSelect = $objTagMapTable->select()
				->where( 'tag_id ='. $val[ 'tag_id' ] );
			
			$result = $objTagMapDb->fetchAll( $objSelect );

			$objResultSet[ $key ][ 'result_count' ] = count( $result );

		}
		
		return $objResultSet;		
	}
	
	public function fetchRowByKeyword( $keyword ) {
		$objTable = Engine_Api::_()->getDbtable( 'tags', 'core' );
		$objDb    = $objTable->getAdapter();

		try{
			$where     = "(text = ?)";
			$values    = array( 'keyword' => $keyword );
			$objSelect = $objTable->select()
				->where( new Zend_Db_Expr( $this->_quoteInto( $objDb, $where, $values ) ) );

			$objResultSet = $objDb->fetchAll( $objSelect );
		} catch( Zend_Db_Statement_Mysqli_Exception $e ) {
			return array( $e->getMessage() );
		}
		foreach( $objResultSet as $key => $val ) {

			if( $this->_isFollowed( $id, $val ) ) {
				$objResultSet[ $key ][ 'isFollowed' ] = true;
			}
			else {
				$objResultSet[ $key ][ 'isFollowed' ] = false;
			}

		}

	}

	protected function _isFollowed( $id, $params ) {

        $tableTag = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
        $dbTag = $tableTag->getAdapter();

        $tagId = (int)$params[ 'tag_id' ];
        $select = $tableTag->select()
          ->where( 'hashtag_id='. $tagId .' AND follower_id='. $id .'');

        $rowSet = $select->query()->fetch();

        if( is_array( $rowSet ) ) {
          return true;
        }

        return false;		
	}

	public function publicIsFollowed( $id, $params ) {

        $tableTag = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
        $dbTag = $tableTag->getAdapter();

        $tagId = (int)$params[ 'tag_id' ];
        $select = $tableTag->select()
          ->where( 'hashtag_id='. $tagId .' AND follower_id='. $id .'');

        $rowSet = $select->query()->fetch();

        if( is_array( $rowSet ) ) {
          return true;
        }

        return false;		
	}
	
	public function isFollowed2( $id, $params ) {

        $tableTag = Engine_Api::_()->getDbtable( 'followhashtag', 'whmedia' );
        $dbTag = $tableTag->getAdapter();

        $tagId = (int)$params[ 'tag_id' ];
        $select = $tableTag->select()
          ->where( 'hashtag_id='. $tagId .' AND follower_id='. $id .'');

        $rowSet = $select->query()->fetch();

        if( is_array( $rowSet ) ) {
          return true;
        }

        return false;		
	}
	
	
	public function getPostHashtag( $user, $projectId ) {
		$objTable = Engine_Api::_()->getDbTable( 'TagMaps', 'core' );	
		$objDb = $objTable->getAdapter();
	
		$objSelect = $objTable->select()
			->from( array( 'tagmaps' => 'engine4_core_tagmaps' ), array( '' ) )
			->joinLeft( array( 'hashtag' => 'engine4_core_tags' ), 'tagmaps.tag_id = hashtag.tag_id', array( 'hashtag.tag_id', 'hashtag.text' ) )
			->where( 'tagmaps.resource_id ='. $projectId )
			->setIntegrityCheck( false );

		$arrTagsResult = $objDb->fetchAll ( $objSelect );
		foreach( $arrTagsResult as $key => $value ) {
	
			if( $this->_isFollowed( $user->getIdentity(), $value ) ) {
				$arrTagsResult[ $key ][ 'is_followed' ] = 1;
			}
			else {
				$arrTagsResult[ $key ][ 'is_followed' ] = 0;
			}
			
			$objTagMapTable = Engine_Api::_()->getDbtable('TagMaps', 'core');
			$objTagMapDb = $objTagMapTable->getAdapter();
			
			$objSelect = $objTagMapTable->select()
										->where( 'tag_id ='. $value[ 'tag_id' ] );
			$result    = $objTagMapDb->fetchAll( $objSelect );
			
			$arrTagsResult[ $key ][ 'result_count' ] = count( $result );			
		}
		return $arrTagsResult;
	}
	
	public function fetchFollowedHashtag( $user, $params ) {
		$suffix = $params[ 'offset' ] .'0';

		$followedHashtagTable = Engine_Api::_()->getDbTable( 'followhashtag', 'whmedia' );
		$resultSet = $followedHashtagTable->select()
									   ->from( array( 'f' => 'engine4_whmedia_followhashtag' ), array( '' ) )
									   ->joinLeft( array( 'h' => 'engine4_core_tags'), 'f.hashtag_id = h.tag_id' )
									   ->where( 'f.follower_id = '. $user->getIdentity() .' AND h.tag_id IS NOT NULL' )
									   ->limit( 10, $suffix )
									   ->setIntegrityCheck( false )
									   ->query()
									   ->fetchAll();

		
		$return = array();			   
		foreach( $resultSet as $key => $value ) {
			if( !empty( $value[ 'tag_id' ] ) ) {

				$resultSet[ $key ][ 'is_followed' ] = 1;
				$tagMapTable = Engine_Api::_()->getDbTable( 'TagMaps', 'core' );
				$result = $tagMapTable->fetchAll( 'tag_id = '. $value[ 'tag_id' ] );
				$resultSet[ $key ][ 'result_count' ] = count( $result );
				
			}
		}

		 if( empty( $resultSet ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		 }		
		
		return array(
			'data'  =>  $resultSet,
			'error' => array()
		);
	}
	
	public function oldfetchFollowedHashtag( $user, $params ) {
		$suffix = $params[ 'offset' ] .'0';

		$followedHashtagTable = Engine_Api::_()->getDbTable( 'followhashtag', 'whmedia' );
		$resultSet = $followedHashtagTable->select()
									   ->from( array( 'f' => 'engine4_whmedia_followhashtag' ) )
									   ->joinLeft( array( 'h' => 'engine4_core_tags'), 'f.hashtag_id = h.tag_id' )
									   ->where( 'f.follower_id = '. $user->getIdentity() )
									   ->limit( 10, $suffix )
									   ->setIntegrityCheck( false )
									   ->query()
									   ->fetchAll();

		$return = array();			   
		foreach( $resultSet as $key => $value ) {
			if( !empty( $value[ 'tag_id' ] ) ) {
				$return[ $key ][ 'tag_id' ] = $value[ 'tag_id' ];
				$return[ $key ][ 'text' ] = $value[ 'text' ];
				$return[ $key ][ 'text' ] = $value[ 'text' ];
				$return[ $key ][ 'is_followed' ] = 1;
			
				$tagMapTable = Engine_Api::_()->getDbTable( 'TagMaps', 'core' );
				$result = $tagMapTable->fetchAll( 'tag_id = '. $value[ 'tag_id' ] );
				$return[ $key ][ 'result_count' ] = count( $result );
			}
		}
		
		 if( empty( $return ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		 }		
		 
		return array(
			'data'  => array( 'test' => $return ),
			'error' => array()
		);
	}

}