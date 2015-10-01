<?php
class Api_Model_FavoExt  {
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
	public $result_count;
	
	public function __construct () {
		$this->favcircle_id = 0;
		$this->user_id = 0;
		$this->title = '';
		$this->category = '';
		$this->photo_id = 0;
		$this->storage_path = '';
		$this->image_width = '';
		$this->image_height = '';
		$this->status = '';
		$this->is_followed = '';
		$this->result_count = 0;
		
		return $this;
	}
	
	public function initWithValues ( $params ) {
		$this->favcircle_id = $params[ 'favcircle_id' ];
		$this->user_id = $params[ 'user_id' ];
		$this->title = $params[ 'title' ];
		$this->category = $params[ 'category' ];
		$this->photo_id = $params[ 'photo_id' ];
		$this->storage_path = $params[ 'storage_path' ];
		$this->image_width = $params[ 'image_width ' ];
		$this->image_height = $params[ 'image_height' ];
		$this->status = $params[ 'status' ];
		$this->is_followed = $params[ 'is_followed' ];
		$this->result_count = $params[ 'result_count' ];
		
		return $this;
	}
	
	public function getFavcircleId () {
		return $this->favcircle_id;
	}
	public function setFavcircleId ( $param ) {
		if( empty( $param ) ) {
			$this->favcircle_id = 0;
		}
		$this->favcircle_id = $param;
	}
	
	public function getUserId () {
		return $this->user_id;
	}
	public function setUserId ( $param ) {
		if( empty( $param ) ) {
			$this->user_id = 0;
		}
		$this->user_id = $param;
	}
	
	public function getTitle () {
		return $this->title;
	}
	public function setTitle ( $param ) {
		if( empty( $param ) ) {
			$this->title = '';
		}
		$this->title = $param;
	}
	
	public function getCategory () {
		return $this->category;
	}
	public function setCategory ( $param ) {
		if( empty( $param ) ) {
			$this->category = '';
		}
		$this->category = $param;
	}
	
	public function getPhotoId () {
		return $this->photo_id;
	}
	public function setPhotoId ( $param ) {
		if( empty( $param ) ) {
			$this->photo_id = 0;
		}
		$this->photo_id = $param;
	}
	
	public function getStoragePath () {
		return $this->storage_path;
	}
	public function setStoragePath ( $param ) {
		if( empty( $param ) ) {
			$this->storage_path = '';
		}
		$this->storage_path = $param;
	}
	
	public function getImageWidth () {
		return $this->image_width;
	}
	public function setImageWidth ( $param ) {
		if( empty( $param ) ) {
			$this->image_width = 0;
		}
		$this->image_width = $param;
	}
	
	public function getImageHeight () {
		return $this->image_height;
	}
	public function setImageHeight ( $param ) {
		if( empty( $param ) ) {
			$this->image_height = 0;
		}
		$this->image_height = $param;
	}
	
	public function getStatus () {
		return $this->status;
	}
	public function setStatus ( $param ) {
		if( empty( $param ) ) {
			$this->status = 0;
		}
		$this->status = $param;
	}
	
	public function getIsFollowed () {
		return $this->is_followed;
	}
	public function setIsFollowed ( $param ) {
		if( empty( $param ) ) {
			$this->is_followed = 0;
		}
		$this->is_followed = $param;
	}
	
	public function getResultCount () {
		return $this->result_count;
	}
	public function setResultCount ( $param ) {
		if ( is_int( $param ) ) {
			$this->result_count = $param;
		}
	}
	
}