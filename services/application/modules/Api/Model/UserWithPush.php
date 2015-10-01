<?php
class Api_Model_UserWithPush {
	public $pushnotification_id
	public $device_id
	public $user_id
	public $type
	public $message;
	
	public function __construct () {
		return $this;
	}
	
	public function getPushNotificationId () {
		return $this->pushnotification_id;
	}
	public function setPushNotificationId ( $param ) {
		$this->pushnotification_id = $param;
	}
	
	public function getDeviceId () {
		return $this->device_id;
	}
	public function setDeviceId ( $param ) {
		$this->device_id = $param;
	}
	
	public function getUserId () {
		return $this->user_id;
	}
	public function setUserId ( $param ) {
		$this->user_id = $param;
	}
	
	public function getType () {
		return $type;
	}
	public function setType ( $param ) {
		$this->type = $param;
	}
	
	public function getMessage () {
		return $this->message;
	}
	public function setMessage ( $param ) {
		$this->message = $param;
	}
}