<?php
class Api_Model_ResponseExt {
	public $data;
	public $error;
	
	public function __construct () {
		$this->data = array();
		$this->error = array();
	}
	
	public function initWithData ( $params ) {
		$this->data = $params[ 'data' ];
		$this->error = $params[ 'error' ];
	}
	
	public function getData () {
		return $this->data;
	}
	public function setData( $param ) {
		$this->data = $param;
	}
	
	public function getError () {
		return $this->error;
	}
	public function setError ( $param ) {
		$this->error[] = $param;
	}
}