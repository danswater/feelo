<?php
class Api_Model_FollowExt {
	public $follow_id;
	public $user_id;
	public $follower_id;
	public $status;
	public $creation_date;
	
	public function __construct () {
		$this->follow_id = 0;
		$this->user_id = 0;
		$this->follower_id = 0;
		$this->status = 0;
		$this->creation_date = '';
		
		return $this;
	}
	
	public function initWithValues ( $params ) {
		$this->follow_id = $params[ 'follow_id' ];
		$this->user_id = $params[ 'user_id' ];
		$this->follower_id = $params[ 'follower_id' ];
		$this->status = $params[ 'status' ];
		$this->creation_date = $params[ 'creation_date' ];
		
		return $this;
	}
	
	public function getFollowId () {
		return $this->follow_id;
	}
	public function setFollowId ( $param ) {
		$this->follow_id = $param;
	}
	
	public function getUserId () {
		return $this->user_id;
	}
	public function setUserId ( $param ) {
		$this->user_id = $param;
	}
	
	public function getFollowerId () {
		return $this->follower_id;
	}
	public function setFollowerId ( $param ) {
		$this->follower_id = $param;
	}
	
	public function getStatus () {
		return $this->status;
	}
	public function setStatus( $param ) {
		$this->status = $param;
	}
	
	public function getCreationDate () {
		return $this->creation_date;
	}
	public function setCreationDate ( $param ) {
		$this->creation_date = $param;
	}
}