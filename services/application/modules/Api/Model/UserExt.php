<?php 
class Api_Model_UserExt {
	public $user_id;
	public $displayname;
	public $username;
	public $photo_id;
	public $status;
	public $status_date;
	public $email;
	public $locale;
	public $language;
	public $storage_path;
	public $following;
	public $followers;
	public $posts;
	
	public function __construct () {
		$this->user_id = 0;
		$this->displayname = '';
		$this->username = '';
		$this->photo_id = 0;
		$this->status = 0;
		$this->status_date = '';
		$this->email = '';
		$this->locale = '';
		$this->language = '';
		$this->storage_path = '';
		$this->following = 0;
		$this->followers = 0;
		$this->posts = 0;
		
		return $this;
	}
	
	public function initWithValues ( $params ) {
		$this->user_id = $params[ 'user_id' ];
		$this->displayname = $params[ 'displayname' ];
		$this->username = $params[ 'username' ];
		$this->photo_id = $params[ 'photo_id' ];
		$this->status = $params[ 'status' ];
		$this->status_date = $params[ 'status_date' ];
		$this->email = $params[ 'email' ];
		$this->locale = $params[ 'locale' ];
		$this->language = $params[ 'language' ];
		$this->storage_path = $params[ 'storage_path' ];
		$this->following = $params[ 'following' ];
		$this->followers = $params[ 'followers' ];
		$this->posts = $params[ 'posts' ];
		
		return $this;
	}
	
	public function getUserId () {
		return $this->user_id;
	}
	public function setUserId ( $param ) {
		$this->user_id = $param;
	}
	
	public function getDisplayname () {
		return $this->displayname;
	}
	public function setDisplayname ( $param ) {
		if ( is_null( $param ) ) {
			$this->displayname = 'null';
		} else {
			$this->displayname = $param;
		}
	}
	
	public function getUsername () {
		return $this->username;
	}
	public function setUsername ( $param ) {
		if ( is_null( $param ) ) {
			$this->username = 'null';
		} else {
			$this->username = $param;
		}
	}
	
	public function getPhotoId () {
		return $this->photo_id;
	}
	public function setPhotoId ( $param ) {
		$this->photo_id = $param;
	}
	
	public function getStatus () {
		return $this->status;
	}
	public function setStatus ( $param ) {
		if ( empty( $param ) || is_null( $param ) ) {
			$this->status = 0;
		} else {
			$this->status = $param;
		}
	}
	
	public function getStatusDate () {
		return $this->status_date;
	}
	public function setStatusDate ( $param ) {
		if ( empty( $param ) || is_null( $param ) ) {
			$this->status_date = 'null';
		} else {
			$this->status_date = $param;
		}
	}
	
	public function getEmail () {
		return $this->email;
	}
	public function setEmail ( $param ) {
		if ( empty( $param ) || is_null( $param ) ) {
			$this->email = 'null';
		} else {
			$this->email = $param;
		}
	}

	public function getLocale () {
		return $this->locale;
	}
	public function setLocale ( $param ) {
		if ( empty( $param ) || is_null( $param ) ) {
			$this->locale = 'null';
		} else {
			$this->locale = $param;
		}
	}
	
	public function getLanguage () {
		return $this->language;
	}
	public function setLanguage ( $param ) {
		if ( empty( $param ) || is_null( $param ) ) {
			$this->language = 'null';
		} else {
			$this->language = $param;
		}
	}
	
	public function getStoragePath () {
		return $this->storage_path;
	}
	public function setStoragePath ( $param ) {
		if ( empty( $param ) || is_null( $param ) ) {
			$this->storage_path = 'null';
		} else {
			$this->storage_path = $param;
		}
	}
	
	public function getFollowing () {
		return $this->following;
	}
	public function setFollowing ( $param ) {
		$this->following = $param;
	}
	
	public function getFollowers () {
		return $this->followers;
	}
	public function setFollowers ( $param ) {
		$this->followers = $param;
	}
	
	public function getPosts () {
		return $this->posts;
	}
	public function setPosts ( $param ) {
		$this->posts = $param;
	}
}