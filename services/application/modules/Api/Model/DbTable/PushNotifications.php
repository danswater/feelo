<?php
class Api_Model_DbTable_PushNotifications extends Engine_Db_Table {

	protected $_rowClass = 'Api_Model_PushNotifications';    

	public function register( $params ) {
		$return = array( "data" => array(), "error" => array() );

		if( !isset( $params[ "device_id" ] ) || $params[ "device_id" ] == "" ) {
			$return[ "error" ][] = "Device id is null";
		} 
		if( !isset( $params[ "user_id" ] ) || $params[ "user_id" ] == "" ) {
			$return[ "error" ][] = "User id is null";
		}
		if( !isset( $params[ "type" ] ) || $params[ "type" ] == "" ) {
			$return[ "error" ][] = "Type is null";
		}
		
		if( $params[ "type" ] == "android" && ( !isset( $params[ "unique_id" ] ) || $params[ "unique_id" ] == "" ) ) {
			$return[ "error" ][] = "Unique id is required";
		}
		
		if( count( $return[ "error" ] ) == 0 ){
			try {
				// just to make sure i wont break anything
				
				if( $params[ "type" ] == "android" ) { // below for android registraion
				
					$row = $this->select()
						->from($this)
						->where( 'unique_id = ?', $params[ "unique_id" ] )
						->where( 'user_id = ?', $params[ "user_id" ] )
						->where( 'type = ?', $params[ "type" ] )
						->query()
						->fetch();
					
					if( $row === false ) { // if not registered then create new 			  
						$data = array( 'device_id' => $params[ "device_id" ], "user_id" => $params[ "user_id" ], "type" => $params[ "type"], "unique_id" => $params[ "unique_id" ] );
						$this->createRow( $data )->save();
					} // end of create new 
					else{ // if the unique_id found then update the unique id
						$this->update( array( "device_id" => $params[ "device_id" ] ), array( "unique_id" => $params[ "unique_id" ] ) );
					}
				
				
				}
				else { // below for ios registration
					
					$row = $this->select()
						->from($this)
						->where( 'device_id = ?', $params[ "device_id" ] )
						->where( 'user_id = ?', $params[ "user_id" ] )
						->where( 'type = ?', $params[ "type" ] )
						->query()
						->fetch();
						 
					if( $row === false ) { 			  
						$data = array( 'device_id' => $params[ "device_id" ], "user_id" => $params[ "user_id" ], "type" => $params[ "type"] );
						$this->createRow( $data )->save();
					}
				
				}
				
			    $return[ "data" ] = $data;
		    }
		    catch( Exception $e ) {
		        if ($e->getCode() != 1062) {
		            throw $e;
		        }
		        $return[ "error" ][] = "Unable to save. please try again.";
	    	}
		}
		return $return;
	}

	public function unregister( $params ) {
		$return = array( "data" => $params, "error" => array() );
		
		if( $params[ "type" ] == "android" ) { // for android unregister
			$this->delete( array( 
					"unique_id = '".$params[ "unique_id" ]."'", 
					"user_id = '".$params[ "user_id" ]."'",
					"type = '".$params[ "type" ]."'" ) );
		} else{ // for ios unregister
			$this->delete( array( 
					"device_id = '".$params[ "device_id" ]."'", 
					"user_id = '".$params[ "user_id" ]."'",
					"type = '".$params[ "type" ]."'" ) );
		}
		
		return $return;
	}

	public function getNotificationType( $user ){
	
		$types = $this->select()
		            ->from($this)
		            ->where( 'user_id = ?', $user->getIdentity() )
		            ->query()
		            ->fetchAll();	

		return $types;
	}

	public function sendMobileNotification( $notificationId, $status ){ 

		if( $status == true )
			$status = 1;
		else
			$status = 0;

		Engine_Api::_()->getDbTable ( 'Notifications', 'Api' )->changeMobileSendStatus( $notificationId, $status );	

  	}
}