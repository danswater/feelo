<?php
class Api2_Model_User {

    const DEFAULT_IMAGE_XL = 'public/no-image-xl.jpg';
    const DEFAULT_IMAGE_L  = 'public/no-image-l.jpg';
    const DEFAULT_IMAGE_M  = 'public/no-image-m.jpg';
    const DEFAULT_IMAGE_S  = 'public/no-image-s.jpg';

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
	public $favos;
	public $hashtags;
	public $likes;
	public $location;

	public function __construct () {
		$this->user_id      = 0;
		$this->displayname  = '';
		$this->username     = '';
		$this->photo_id     = 0;
		$this->status       = '';
		$this->status_date  = '';
		$this->email        = '';
		$this->locale       = '';
		$this->language     = '';
		$this->storage_path = self::DEFAULT_IMAGE_M;
		$this->following    = 0;
		$this->followers    = 0;
		$this->posts        = 0;
		$this->favos        = 0;
		$this->hashtags     = 0;
		$this->likes        = 0;
		$this->location     = '';

		return $this;
	}

	public function initWithValues ( $params ) {
		$this->setUserId( $params[ 'user_id' ] );
		$this->setDisplayname( $params[ 'displayname' ] );
		$this->setUsername( $params[ 'username' ] );
		$this->setPhotoId( $params[ 'photo_id' ] );
		$this->setStatus( $params[ 'status' ] );
		$this->setStatusDate( $params[ 'status_date' ] );
		$this->setEmail( $params[ 'email' ] );
		$this->setLocale( $params[ 'locale' ] );
		$this->setLanguage( $params[ 'language' ] );
		$this->setStoragePath( $params[ 'storage_path' ] );
		$this->setFollowing( $params[ 'following' ] );
		$this->setFollowers( $params[ 'followers' ] );
		$this->setPosts( $params[ 'posts' ] );
		$this->setFavos( $params[ 'favos' ] );
		$this->setHashtags( $params[ 'hashtags' ] );
		$this->setLikes( $params[ 'likes' ] );
		$this->setLocation( $params[ 'location' ] );

		return $this;
	}

	public function setUserId ( $param ) {
		if( is_int( $param ) ) {
			$this->user_id = $param;
		}
	}

	public function setDisplayname ( $param ) {
		if ( is_string( $param ) ) {
			$this->displayname = $param;
		}
	}

	public function setUsername ( $param ) {
		if ( is_string( $param ) ) {
			$this->username = $param;
		}
	}

	public function setPhotoId ( $param ) {
		if ( is_int( $param ) ) {
			$this->photo_id = $param;
		}
	}

	public function setStatus ( $param ) {
		if ( is_string( $param ) ) {
			$this->status = $param;
		}
	}

	public function setStatusDate ( $param ) {
		if ( is_string( $param ) ) {
			$this->status_date = $param;
		}
	}

	public function setEmail ( $param ) {
		if ( is_string( $param ) ) {
			$this->email = $param;
		}
	}

	public function setLocale ( $param ) {
		if ( is_string( $param ) ) {
			$this->locale = $param;
		}
	}

	public function setLanguage ( $param ) {
		if ( is_string( $param ) ) {
			$this->language = $param;
		}
	}

	public function setStoragePath ( $param ) {
		if ( is_string( $param ) ) {
			$this->storage_path = $param;
		}
	}

	public function setFollowing ( $param ) {
		if( is_int( $param ) ) {
			$this->following = $param;
		}
	}

	public function setFollowers ( $param ) {
		if ( is_int( $param ) ) {
			$this->followers = $param;
		}
	}

	public function setPosts ( $param ) {
		if( is_int( $param ) ) {
			$this->posts = $param;
		}
	}

	public function setHashtags( $param ) {
		if ( is_int( $param ) ) {
			$this->hashtags = $param;
		}
	}

	public function setLikes ( $param ) {
		if ( is_int( $param ) ) {
			$this->likes = $param;
		}
	}

	public function setFavos ( $param ) {
		if( is_int( $param ) ) {
			$this->favos = $param;
		}
	}

	public function setLocation ( $param ) {
		if ( is_string( $param ) ) {
			$this->location = $param;
		}
	}

}