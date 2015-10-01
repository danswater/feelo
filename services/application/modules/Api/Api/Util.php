<?php
class Api_Api_Util extends Api_Api_Base {

	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	public $data = array();
	
	public function __set( $key, $value ) {
		$this->data[ $key ]= $value;
	}

	public function __get( $key ) {

		if( array_key_exists( $key, $this->data ) ) {
			return $this->data[ $key ];
		}

		return null;

	}	
	
	public function isEmailRegistered() {
		$user = Engine_Api::_ ()->getDbTable ( 'users', 'user' );
		
		$email = ( !empty( $this->get( 'email' ) ) ) ? $this->get( 'email' ) : '';
		
		$row = $user->fetchRow( 'email LIKE "%'. $email .'%"' );
		
		if( count( $row ) >= 1 ) {
			return true;
		}
		
		return false;
	}
	
	public function isProfileRegistered() {
		$user = Engine_Api::_()->getDbTable ( 'users', 'user' );
		
		$profile = ( !empty( $this->get( 'username' ) ) ) ? $this->get( 'username' ) : '';
		
		$row = $user->fetchRow( 'username LIKE "%'. $profile . '"%' );
		
		if( count( $row ) > = 1 ) {
			return true;
		}
		
		return false;
	}
}