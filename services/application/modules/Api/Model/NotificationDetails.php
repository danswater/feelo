<?php
class Api_Model_NotificationDetails {

	public $notification_count; // int
	public $has_general_notification; // boolean
	public $has_request_notification; // boolean
	
	public function __construct () {
		$this->notification_count = 0;
		$this->has_general_notification = 0;
		$this->has_request_notification = 0;
		
		return $this;
	}
	
	public function getNotificationCount () {
		return $this->notification_count;
	}
	public function setNotificationCount ( $param ) {
		$this->notification_count = $param;
	}
	
	public function getHasGeneralNotification () {
		return $this->has_general_notification;
	}
	public function setHasGeneralNotification ( $param ) {
		$this->has_general_notification = $param;
	}
	
	public function getHasRequestNotification () {
		return $this->has_request_notification;
	}
	public function setHasRequestNotification ( $param ) {
		$this->has_request_notification = $param;
	}
	
}