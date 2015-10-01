<?php
class Api2_Model_FeedWithDetails {
	public $project_id;
	public $user_id;
	public $category_id;
	public $title;
	public $description;
	public $creation_date;
	public $project_views;
	public $owner_type;
	public $search;
	public $cover_file_id;
	public $is_published;
	public $like_count;
	public $like_count_int;
	public $is_liked;
	public $comment_count;
	public $comment_count_int;
	public $feed_type;
	public $Image_color;
	public $Likes;
	public $Media;
	public $User;
	public $Comments;
	public $Hashtag;

	public function __construct () {
		$this->project_id        = 0;
		$this->user_id           = 0;
		$this->category_id       = 0;
		$this->title             = '';
		$this->description       = '';
		$this->creation_date     = '';
		$this->project_views     = '0';
		$this->owner_type        = '';
		$this->search            = 0;
		$this->cover_file_id     = 0;
		$this->is_published      = 0;
		$this->like_count        = '0';
		$this->like_count_int    = 0;
		$this->is_liked          = 0;
		$this->comment_count     = '0';
		$this->comment_count_int = 0;
		$this->feed_type         = '';
		$this->Image_color       = '';
		$this->Likes             = '';
		$this->Media             = '';
		$this->User              = '';
		$this->Comments          = '';
		$this->Hashtag           = '';


		return $this;
	}

	public function initWithValues ( $params ) {
		$this->setProjectId( $params[ 'project_id' ] );
		$this->setUserId( $params[ 'user_id'] );
		$this->setCategoryId( $params[ 'category_id' ] );
		$this->setTitle( $params[ 'title' ] );
		$this->setDescription( $params[ 'description' ] );
		$this->setCreationDate( $params[ 'creation_date' ] );
		$this->setProjectViews( $params[ 'project_views' ] );
		$this->setOwnerType( $params[ 'owner_type' ] );
		$this->setSearch( $params[ 'search' ] );
		$this->setCoverFileId( $params[ 'cover_file_id' ] );
		$this->setIsPublished( $params[ 'is_published' ] );
		$this->setLikeCount( $params[ 'like_count' ] );
		$this->setLikeCOuntInt( $params[ 'like_count_int' ] );
		$this->setIsLiked( $params[ 'is_liked' ] );
		$this->setCommentCount( $params[ 'comment_count' ] );
		$this->setCommentCountInt( $params[ 'comment_count_int' ] );
		$this->setFeedType( $params[ 'feed_type' ] );
		$this->setImageColor( $params[ 'image_color' ] );
		$this->setLikes( $params[ 'likes' ] );
		$this->setMedia( $params[ 'media' ] );
		$this->setUser( $params[ 'user' ] );
		$this->setComments( $params[ 'comments' ] );
		$this->setHashtag( $params[ 'hashtags' ] );

		return $this;
	}

	public function getProjectId () {
		return $this->project_id;
	}
	public function setProjectId ( $param ) {
		if ( is_int( $param ) ) {
			$this->project_id = $param;
		}
	}

	public function getUserId () {
		return $this->user_id;
	}
	public function setUserId ( $param ) {
		if ( is_int( $param ) ) {
			$this->user_id = $param;
		}
	}

	public function getCategoryId () {
		return $category_id;
	}
	public function setCategoryId ( $param ) {
		if ( is_int( $param ) ) {
			$this->category_id = $param;
		}
	}

	public function getTitle () {
		return $title;
	}
	public function setTitle ( $param ) {
		if ( is_string( $param ) ) {
			$this->title = utf8_encode( $param );
		}
	}

	public function getDescription () {
		return $this->description;
	}
	public function setDescription ( $param ) {
		if ( is_string( $param ) ) {
			$this->description = utf8_encode( $param );
		}
	}

	public function getCreationDate () {
		return $this->creation_date;
	}
	public function setCreationDate ( $param ) {
		if ( is_string( $param ) ) {
			return $this->creation_date = $param;
		}
	}

	public function getProjectViews () {
		return $this->project_views;
	}
	public function setProjectViews ( $param ) {
		if ( is_string( $param ) && !empty( $param ) ) {
			$this->project_views = $param;
		}
	}

	public function getOwnerType () {
		return $owner_type;
	}
	public function setOwnerType ( $param ) {
		if ( is_string( $param ) ) {
			$this->owner_type = $param;
		}
	}

	public function getSearch () {
		return $this->search;
	}
	public function setSearch ( $param ) {
		if ( is_int( $param ) ) {
			$this->search = $param;
		}
	}

	public function getCoverFileId () {
		return $this->cover_file_id;
	}
	public function setCoverFileId ( $param ) {
		if ( is_int( $param ) ) {
			$this->cover_file_id = $param;
		}
	}

	public function getIsPublished () {
		return $this->is_published;
	}
	public function setIsPublished ( $param ) {
		if ( is_int ( $param ) ) {
			$this->is_published = $param;
		}
	}

	public function getLikeCount () {
		return $this->like_count;
	}
	public function setLikeCount ( $param ) {
		if ( $param ) {
			$this->like_count = $param;
		}
	}

	public function getLikeCountInt () {
		return $this->like_count_int;
	}
	public function setLikeCountInt ( $param ) {
		if ( is_int( $param ) ) {
			$this->like_count_int = $param;
		}
	}

	public function getIsLiked () {
		return $this->is_liked;
	}
	public function setIsLiked ( $param ) {
		if ( is_int( $param ) ) {
			$this->is_liked = $param;
		}
	}

	public function getCommentCount () {
		return $comment_count;
	}
	public function setCommentCount ( $param ) {
		if ( is_string( $param ) && !empty( $param ) ) {
			$this->comment_count = $param;
		}
	}

	public function getCommentCountInt () {
		return $this->comment_count_int;
	}
	public function setCommentCountInt ( $param ) {
		if ( is_int( $param ) ) {
			$this->comment_count_int = $param;
		}
	}

	public function getFeedType () {
		return $this->feed_type;
	}
	public function setFeedType ( $param ) {
		if ( is_string( $param ) ) {
			$this->feed_type = $param;
		}
	}

	public function getImageColor () {
		return $this->Image_color;
	}
	public function setImageColor ( $param ) {
		if ( is_array( $param ) ) {
			$this->Image_color = $param;
		}
	}

	public function getLikes () {
		return $this->Likes;
	}
	public function setLikes ( $param ) {
		if ( is_array( $param ) ) {
			$this->Likes = $param;
		}
	}

	public function getMedia () {
		return $this->Media;
	}
	public function setMedia ( $param ) {
		$this->Media = $param;
	}

	public function getUser () {
		return $this->User;
	}
	public function setUser ( $param ) {
		$this->User = $param;
	}

	public function getComments () {
		return $this->Comments;
	}
	public function setComments ( $param ) {
		if ( is_array( $param ) ) {
			$this->Comments = $param;
		}
	}

	public function getHashtag () {
		return $this->Hashtag;
	}
	public function setHashtag ( $param ) {
		if ( is_array( $param ) ) {
			$this->Hashtag = $param;
		}
	}

}

