<?php
class Api2_Model_Box {

	public $group_id;
	public $title;
	public $user_count;
	public $storage_path;
	public $image_width;
	public $image_height;


	public function __construct () {

		$this->group_id      = 0;
		$this->title         = '';
		$this->user_count    = 0;
		$this->storage_path  = '';
		$this->image_width   = 0;
		$this->image_height = 0;

		return $this;

	}

	public function initWithValues ( $params ) {

		$this->setGroupId( $params[ 'group_id' ] );
		$this->setTitle( $params[ 'title' ] );
		$this->setUserCount( $params[ 'user_count' ] );
		$this->setStoragePath( $params[ 'storage_path' ] );
		$this->setImageWidth( $params[ 'image_width' ] );
		$this->setImageHeight( $params[ 'image_height' ] );

		return $this;

	}

	public function getGroupId () {
		return $this->group_id;
	}
	public function setGroupId ( $param ) {
		if ( is_int( $param ) ) {
			$this->group_id = $param;
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

	public function getUserCount () {
		return $this->user_count;
	}
	public function setUserCount ( $param ) {
		if ( is_int( $param ) ) {
			$this->user_count = $param;
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

	public function getImageWidth () {
		return $this->image_width;
	}
	public function setImageWidth ( $param ) {
		if ( is_int( $param ) ) {
			$this->image_width = $param;
		}
	}

	public function getImageHeight () {
		return $this->image_height;
	}
	public function setImageHeight ( $param ) {
		if ( is_int( $param ) ) {
			$this->image_height = $param;
		}
	}

}
