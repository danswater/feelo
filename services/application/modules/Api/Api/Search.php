<?php
class Api_Api_Search extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	protected $events;

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

	public function fetchMedia3( $keyword, $type = 'all' ) {

		$objTable = Engine_Api::_()->getApi( 'whmedia', 'api' );

		$arrResultSet = $objTable->fetchAllByKeyword3( $keyword, $type, $this->offset );
		
		return array(
			'data' => $arrResultSet,
			'error' => ( empty( $arrResultSet ) ) ? array( 'No results found' ) : array()
		);
	}
	
	public function fetchMedia( $user, $keyword, $type = 'all' ) {
		
		$objTable = Engine_Api::_()->getApi( 'whmedia', 'api' );

		$arrResultSet = $objTable->fetchAllByKeyword($user, $keyword, $type, $this->offset, $this->limit );
		
		return array(
			'data' => $arrResultSet,
			'error' => ( empty( $arrResultSet ) ) ? array( 'No results found' ) : array()
		);
	}

	public function fetchHashtag( $keyword, $userId ) {
		$objTable = Engine_Api::_()->getApi( 'hashtag', 'api' );

		if( is_null($this->offset) ) {
			return $objTable->fetchRowByKeyword( $keyword );
		}

		$arrResultSet = $objTable->fetchAllByKeyword( $keyword, $this->offset, $userId );
		
		return array(
			'data' => $arrResultSet,
			'error' => ( empty( $arrResultSet ) ) ? array( 'No results found' ) : array()
		);
	}

	public function fetchUser( $keyword ) {
		$objTable = Engine_Api::_()->getApi( 'user', 'api' );

		$arrUserResultSet =  $objTable->fetchAllByKeyword( $keyword, $this->offset, $this->limit );	
		return array(
			'data' => $arrUserResultSet,
			'error' => ( empty( $arrUserResultSet ) ) ? array( 'No results found' ) : array()
		);
	}

	public function fetchAll( $keyword, $userId ) {
		$objWhmediaTable = Engine_Api::_()->getApi( 'whmedia', 'api' );
		$arrWhmediaResultSet = $objWhmediaTable->fetchAllByKeyword( $keyword, $this->offset );
		$objHashtagTable = Engine_Api::_()->getApi( 'hashtag', 'api' );
		$arrHashtagResultSet = $objHashtagTable->fetchAllByKeyword( $keyword, $this->offset, $userId );

		$objUserTable = Engine_Api::_()->getApi( 'user', 'api' );
		$arrUserResultSet = $objUserTable->fetchAllByKeyword( $keyword, $this->offset );
		
		if( empty( $arrWhmediaResultSet ) && empty( $arrHashtagResultSet ) && empty( $arrUserResultSet ) ) {
			$error = array( 'No results found' );
		}
		else {
			$error = array();
		}
		
		return array(
			'data' => array(
				'Media'   => $arrWhmediaResultSet,
				'Hashtag' => $arrHashtagResultSet,
				'User'    => $arrUserResultSet
			),
			'error' => $error
		);
	}
	
	public function fetchFavo( $user, $params ) {
		$suffix = $params[ 'offset' ] .'0';
		$favo = Engine_Api::_()->getDbTable( 'favcircle', 'whmedia' );
		try {
			$resultSet = $favo->select()
						->where( 'title LIKE "%'. $params[ 'keyword' ] .'%"' )
						->order( array( 'favcircle_id DESC' ) )
						->limit( $this->limit, $suffix )
						->query()
						->fetchAll();
						
			$storage    = Engine_Api::_()->getDbTable( 'files', 'storage' );
			$favocircle = Engine_Api::_()->getApi( 'favo', 'api' ); 
			foreach( $resultSet as $key => $value ) {
				$row = $storage->fetchRow( 'file_id = '. $value[ 'photo_id' ] );
				$resultSet[ $key ][ 'storage_path' ] =  $row->storage_path;
				$resultSet[ $key ][ 'is_followed' ]  = ( int )$favocircle->isFavoFollowed( $user, array( 'favcircle_id' => $value[ 'favcircle_id' ] ) );
				unset( $resultSet[ $key ][ 'photo_id' ] );
			}
		} catch( Exception $e ) {
			return array(
				'data'  => array(),
				'error' => $e->getMessage()
			);	
		}

		return array(
			'data'  => $resultSet,
			'error' => array()
		);
	}
	
	public function fetch ( $user, $params ) {
		$hashtagTable = Engine_Api::_()->getDbTable( 'hashtags', 'api' );
		$collection = $hashtagTable->findByKeyword( $user, $params );
		foreach( $collection as $key => $value ) {}
		return $collection->toArray();
	}
	
}