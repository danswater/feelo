<?php
class Api2_Model_Hashtag {
	public $tag_id;
	public $text;
	public $is_followed;
	public $result_count;

	public function __construct () {
		$this->tag_id       = 0;
		$this->text         = '';
		$this->is_followed  = 0;
		$this->result_count = 0;

		return $this;
	}

	public function initWithValues ( $params ) {
		$this->setTagId( $params[ 'tag_id' ] );
		$this->setText( $params[ 'text' ] );
		$this->setIsFollowed( $params[ 'is_followed' ] );
		$this->setResultCount( $params[ 'result_count' ] );

		return $this;
	}

	public function getTagId () {
		return $tag_id;
	}
	public function setTagId ( $param ) {
		if( is_int( $param ) ) {
			$this->tag_id = $param;
		}
	}

	public function getText () {
		return $text;
	}
	public function setText ( $param ) {
		if( is_string( $param ) ) {
			$this->text = $param;
		}
	}

	public function getIsFollowed () {
		return $this->is_followed;
	}
	public function setIsFollowed ( $param ) {
		if( is_int( $param ) ) {
			$this->is_followed = $param;
		}
	}

	public function getResultCount() {
		return $this->result_count;
	}
	public function setResultCount( $param ) {
		if( is_int( $param ) ) {
			$this->result_count = $param;
		}
	}

}