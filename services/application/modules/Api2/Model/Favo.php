<?php
class Api2_Model_Favo {

	public $favcircle_id;
	public $user_id;
	public $title;
	public $category;
	public $photo_id;
	public $storage_path;
	public $image_width;
	public $image_height;
	public $status;
	public $is_followed;

	public function __construct () {

		$this->favcircle_id = 0;
		$this->user_id      = 0;
		$this->title        = '';
		$this->category     = '';
		$this->photo_id     = 0;
		$this->storage_path = 0;
		$this->image_width  = 0;
		$this->image_height = 0;
		$this->status       = 0;
		$this->is_followed  = 0;

		return $this;

	}

	public function initWithValues ( $params ) {

		$this->setFavcircleId( $params[ 'favcircle_id' ] );
		$this->setUserId( $params[ 'user_id' ] );
		$this->setTitle( $params[ 'title' ] );
		$this->setCategory( $params[ 'category' ] );
		$this->setPhotoId( $params[ 'photo_id' ] );
		$this->setStoragePath( $params[ 'storage_path' ] );
		$this->setImageWidth( $params[ 'image_width' ] );
		$this->setImageHeight( $params[ 'image_height' ] );
		$this->setStatus( $params[ 'status' ] );
		$this->setIsFollowed( $params[ 'is_followed' ] );

		return $this;

	}

	public function getFavCircleId () {
		return $this->favcircle_id;
	}
	public function setFavcircleId( $param ) {
		if ( is_int( $param ) ) {
			$this->favcircle_id = $param;
		}
	}

	public function getUserId () {
		return $this->user_id;
	}
	public function setUserId( $param ) {
		if ( is_int( $param ) ) {
			$this->user_id = $param;
		}
	}

	public function getTitle () {
		return $this->title;
	}
	public function setTitle( $param ) {
		if ( is_string( $param ) ) {
			$this->title = $param;
		}
	}

	public function getCategory () {
		return $this->category;
	}
	public function setCategory( $param ) {
		if ( is_string( $param ) ) {
			$this->category = $param;
		}
	}

	public function getPhotoId () {
		return $this->photo_id;
	}
	public function setPhotoId( $param ) {
		if ( is_int( $param ) ) {
			$this->photo_id = $param;
		}
	}

	public function getStoragePath () {
		return $this->storage_path;
	}
	public function setStoragePath( $param ) {
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

	public function getStatus () {
		return $this->status;
	}
	public function setStatus ( $param ) {
		if ( is_int( $param ) ) {
			$this->status = $param;
		}
	}

	public function getIsFollowed () {
		return $this->is_followed;
	}
	public function setIsFollowed ( $param ) {
		if ( is_int( $param ) ) {
			$this->is_followed = $param;
		}
	}

}