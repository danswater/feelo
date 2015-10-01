<?php
class Api2_Model_Category {

	public $category_id;
	public $title;
	public $isFollowed;

	public function __construct () {
		$this->category_id = 0;
		$this->title       = '';
		$this->isFollowed  = 0;

		return $this;
	}

	public function initWithValues ( $params ) {
		$this->setCategoryId( $params[ 'category_id' ] );
		$this->setTitle( $params[ 'title' ] );
		$this->isFollowed( $params[ 'is_followed' ] );

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

	public function getIsFollowed () {
		return $this->isFollowed;
	}
	public function setIsFollowed ( $param ) {
		if ( is_int( $param ) ) {
			$this->isFollowed = $param;
		}
	}

}