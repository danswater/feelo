<?php
class Api2_Model_HashtagCategory {

	public $category_id;
	public $title;
	public $hashtag;

	public function __construct () {
		$this->setCategoryId ( 0 );
		$this->setTitle( '' );
		$this->setHashtag( array() );

		return $this;
	}

	public function initWithValues ( $params ) {
		$this->setCategoryId( $params[ 'category_id' ] );
		$this->setTitle( $params[ 'title' ] );
		$this->setHashtag( $params[ 'hashtag' ] );

		return $this;
	}

	public function getCategoryId () {
		return $this->category_id;
	}
	public function setCategoryId ( $param ) {
		if ( is_int( $param ) ) {
			$this->category_id = $param;
		}
	}

	public function getTitle () {
		return $this->title;
	}
	public function setTitle ( $param ) {
		if ( is_string( $param ) ) {
			$this->title = $param;
		}
	}

	public function getHashtag () {
		return $this->hashtag;
	}
	public function setHashtag ( $param ) {
		if ( is_array( $param ) ) {
			$this->hashtag = $param;
		}
	}

}