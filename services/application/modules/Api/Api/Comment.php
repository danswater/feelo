<?php
class Api_Api_Comment extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	public function fetchComments( $projectId, $offset = null ) {		
		
		$objCommentTable = Engine_Api::_()->getDbTable( 'comments', 'whcomments' );
		$objCommentDb = $objCommentTable->getAdapter();

		$where = 'deleted = 0 AND resource_id = ?';
		$params = array( 'id' => $projectId );

		$suffix = $this->offset ."0";
		$objCommentSelect = $objCommentTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objCommentDb, $where, $params ) ) )
			->order( 'creation_date DESC' );

		if( !is_null( $offset ) ) {
			$objCommentSelect->limit( 10, $suffix );
		}
		
		$arrCommentResultSet = $objCommentDb->fetchAll( $objCommentSelect );

        $objUserTable = Engine_Api::_()->getItemTable( 'user' );
        $objUserDb = $objUserTable->getAdapter();

		foreach( $arrCommentResultSet as $key => $value ) {
			$objUserSelect = $objUserTable->select()
				->from( array( 'u' => 'engine4_users' ) )
				->joinLeft( array( 'sf' => 'engine4_storage_files' ), 'sf.user_id = u.user_id' )
				->where( ' u.user_id ='. $value[ 'poster_id' ] .' AND sf.type="thumb.profile" AND sf.parent_file_id = u.photo_id' )
				->setIntegrityCheck( false );

			$arrCommentPhoto[] = $objUserDb->fetchAll( $objUserSelect );
			$arrCommentReturn[] = array(
				'comment_id'    => $value[ 'comment_id' ],
				'parent_id'     => ( is_null( $value[ 'parent_id' ] ) ) ? '' : $value[ 'parent_id' ],
				'project_id'    => $projectId,
				'user_id'       => $value[ 'poster_id' ],
				'body'          => $value[ 'body' ],
				'creation_date' => $value[ 'creation_date' ],
				'deleted'       => $value[ 'deleted' ],
				'storage_path'  => $arrCommentPhoto[ $key ][ 0 ][ 'storage_path' ],
				'displayname'   => $arrCommentPhoto[ $key ][ 0 ][ 'displayname' ]
			);
		}
		
		if( is_null( $arrCommentReturn ) ) {
			return 'null';
		}
		return $arrCommentReturn;

	}

	public function fetchCommentsWithLimit( $projectId, $offset = 0, $limit = 5 ) {		
		
		$objCommentTable = Engine_Api::_()->getDbTable( 'comments', 'whcomments' );
		$objCommentDb = $objCommentTable->getAdapter();

		$where = 'deleted = 0 AND resource_id = ?';
		$params = array( 'id' => $projectId );

		$suffix = $this->offset ."0";
		$objCommentSelect = $objCommentTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objCommentDb, $where, $params ) ) )
			->order( 'creation_date DESC' );

		$objCommentSelect->limit( $limit, $suffix );
		
		$arrCommentResultSet = $objCommentDb->fetchAll( $objCommentSelect );

        $objUserTable = Engine_Api::_()->getItemTable( 'user' );
        $objUserDb = $objUserTable->getAdapter();

		foreach( $arrCommentResultSet as $key => $value ) {
			$objUserSelect = $objUserTable->select()
				->from( array( 'u' => 'engine4_users' ) )
				->joinLeft( array( 'sf' => 'engine4_storage_files' ), 'sf.user_id = u.user_id' )
				->where( ' u.user_id ='. $value[ 'poster_id' ] .' AND sf.type="thumb.profile" AND sf.parent_file_id = u.photo_id' )
				->setIntegrityCheck( false );

			$arrCommentPhoto[] = $objUserDb->fetchAll( $objUserSelect );
			$arrCommentReturn[] = array(
				'comment_id'    => $value[ 'comment_id' ],
				'parent_id'     => ( is_null( $value[ 'parent_id' ] ) ) ? '' : $value[ 'parent_id' ],
				'project_id'    => $projectId,
				'user_id'       => $value[ 'poster_id' ],
				'body'          => $value[ 'body' ],
				'creation_date' => $value[ 'creation_date' ],
				'deleted'       => $value[ 'deleted' ],
				'storage_path'  => $arrCommentPhoto[ $key ][ 0 ][ 'storage_path' ],
				'displayname'   => $arrCommentPhoto[ $key ][ 0 ][ 'displayname' ]
			);
		}
		
		if( is_null( $arrCommentReturn ) ) {
			return 'null';
		}
		return $arrCommentReturn;

	}

	public function findTagsInComment ( $comment ) {
		$tag_userids = array();

		$apiModelDbTableUsers = Engine_Api::_()->getDbTable( 'users', 'api' );
		$dom = new DOMDocument();
		$dom->loadHTML( $comment );

		$links = $dom->getElementsByTagName( 'a' );
		foreach( $links as $link ) {
			$link->nodeValue = "{{". $link->nodeValue ."}}";
			$hrefFragments   = explode( '/', $link->getAttribute( 'href' ) );
			$username = end( $hrefFragments );

			$apiApiUsers =  Engine_Api::_()->getDbTable( 'users', 'api' );
			
			if ( count( $hrefFragments ) > 0 ) {
				$taggedUsers = $apiApiUsers->readUserByUsername( $username );
				$tag_userids[] = $taggedUsers->getIdentity();
			}
		}

		$ret = array();
		$ret[ 'body' ]        = $dom->textContent;
		$ret[ 'tag_userids' ] = $tag_userids;

		return $ret;
	}

}