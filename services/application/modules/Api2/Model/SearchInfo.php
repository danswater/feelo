<?php
class Api2_Model_SearchInfo {

	public $keyword;
	public $result_count;
	public $storage_path;

	public function __construct () {

		$this->keyword        = '';
		$this->result_count = 0;
		$this->storage_path = '';

		return $this;

	}

	public function initWithValues ( $params ) {

		$this->setKeyword( $params[ 'keyword' ] );
		$this->setResultCount( $params[ 'result_count' ] );
		$this->setStoragePath( $params[ 'storage_path' ] );

		return $this;

	}

	public function getKeyword () {
		return $this->keyword;
	}
	public function setKeyword ( $param ) {
		if ( is_string( $param ) ) {
			$this->keyword = $param;
		}
	}

	public function getResultCount () {
		return $this->result_count;
	}
	public function setResultCount ( $param ) {
		if ( is_int( $param ) ) {
			$this->result_count = $param;
		}
	}

	public function getStoragePath () {
		return $this->storage_path;
	}
	public function setStoragePath ( $param ) {
		if ( is_string( $param ) ) {
			$this->storage_path = $param;
		}
	} 

}