<?php
class Api2_Model_Comment {

	public $comment_id;
	public $parent_id;
	public $project_id;
	public $user_id;
	public $body;
	public $creation_date;
	public $deleted;
	public $deleted;
	public $tag_userids;
	public $storage_path;
	public $displayname;

	public function __construct () {
		$this->comment_id    = 0;
		$this->parent_id     = 0;
		$this->project_id    = 0;
		$this->user_id       = 1;
		$this->body          = '';
		$this->creation_date = '';
		$this->deleted       = 0;
		$this->storage_path  = '';
		$this->displayname   = '';
		$this->tag_userids   = [];

		return $this;
	}

	public function initWithValues ( $params ) {
		$this->setCommentId( $params[ 'comment_id' ] );
		$this->setParentId( $params[ 'parent_id' ] );
		$this->setProjectId( $params[ 'project_id' ] );
		$this->setUserId( $params[ 'user_id' ] );
		$this->setBody( $params[ 'body' ] );
		$this->setCreationDate( $params[ 'creation_date' ] );
		$this->setDeleted( $params[ 'deleted' ] );
		$this->setStoragePath( $params[ 'storage_path' ] );
		$this->setDisplayname( $params[ 'displayname' ] );
		$this->setTagUserIds( $params[ 'tag_userids' ] );

		return $this;
	}

	public function getCommentId () {
		return $this->comment_id;
	}
	public function setCommentId ( $param ) {
		if ( is_int( $param ) ) {
			$this->comment_id = $param;
		}
	}

	public function getParentId () {
		return $this->parent_id;
	}
	public function setParentId ( $param ) {
		if ( is_int( $param ) ) {
			$this->parent_id = $param;
		}
	}

	public function setProjectId () {
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

	public function getBody () {
		return $this->body;
	}
	public function setBody ( $param ) {
		if ( is_string( $param ) ) {
			$this->body = $param;
		}
	}

	public function getCreationDate () {
		return $this->creation_date;
	}
	public function setCreationDate ( $param ) {
		if ( is_string( $param ) ) {
			$this->creation_date = $param;
		}
	}

	public function getDeleted () {
		return $this->deleted;
	}
	public function setDeleted ( $param ) {
		if ( is_string( $param ) ) {
			$this->deleted = $param;
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

	public function getDisplayname () {
		return $this->displayname;
	}
	public function setDisplayname ( $param ) {
		if ( is_string( $param ) ) {
			$this->displayname = $param;
		}
	}

	public function getTagUserIds () {
		return $this->tag_userids;
	}
	public function setTagUserIds ( $param ) {
		if ( is_array( $param ) ) {
			$this->tag_userids = $param;
		}
	}

	

}