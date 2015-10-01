<?php
class Api_Api_Like extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	public function fetchLikes( $userId, $projectId ) {
	
		$objLikeTable = Engine_Api::_()->getDbTable( 'likes', 'core' );

		$objLikeDb = $objLikeTable->getAdapter();

		$whereValue            = "cl.resource_id = ?";
		$whereParams           = array( 'id' => $projectId );
		$objLikeSelect = $objLikeTable->select()
			->from( array( 'cl' => 'engine4_core_likes' ) )
			->joinLeft( array( 'u' => 'engine4_users' ), 'u.user_id = cl.poster_id', array( 'u.displayname', 'u.user_id' ) )
			->where( new Zend_Db_Expr( $this->_quoteInto( $objLikeDb, $whereValue, $whereParams ) ) )
			->order( array( $orderParams ) )
			->setIntegrityCheck( false );

		$arrLikeResultSet = $objLikeDb->fetchAll( $objLikeSelect );		

		foreach( $arrLikeResultSet as $key => $value ) {

			$likeId = $arrLikeResultSet[ $key ][ 'like_id' ];
			$likeId =  ( is_null( $likeId ) ) ? 'null' : $likeId;
			
			$resourceId = $arrLikeResultSet[ $key ][ 'resource_id' ];
			$resourceId = ( is_null( $resourceId ) ) ? 'null' : $resourceId;
			
			$displayname = $arrLikeResultSet[ $key ][ 'displayname' ];
			$displayname = ( is_null( $displayname ) ) ? 'null' : $displayname;
			
			$userId = $arrLikeResultSet[ $key ][ 'user_id' ];
			$userId = ( is_null( $userId ) ) ? 'null' : $userId;
			
			$arrLikeReturn[ $key ][ 'like_id' ] = $likeId;
			$arrLikeReturn[ $key ][ 'resource_id' ] = $resourceId;
			$arrLikeReturn[ $key ][ 'displayname' ] = $displayname;
			$arrLikeReturn[ $key ][ 'user_id' ] = $userId;

		}

		if( empty( $arrLikeResultSet ) ) {
			return 'null';
		}
		
		if( is_null( $arrLikeReturn ) ) {

			return 'null';
		}

		return $arrLikeReturn;
	}
	
	public function isLiked( $resourceId, $userId ) {
		$objLikeTable = Engine_Api::_()->getDbTable( 'likes', 'core' );

		$objLikeDb = $objLikeTable->getAdapter();

		$whereValue            = "resource_id = ? AND poster_id = ?";
		$whereParams           = array( 'resource_id' => $resourceId, 'poster_id' => $userId );
		$objLikeSelect = $objLikeTable->select()
			->where( new Zend_Db_Expr( $this->_quoteInto( $objLikeDb, $whereValue, $whereParams ) ) );

		$arrLikeResultSet = $objLikeDb->fetchAll( $objLikeSelect );

		return count( $arrLikeResultSet );		
	}

}