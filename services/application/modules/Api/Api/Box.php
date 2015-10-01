<?php 
class Api_Api_Box extends Api_Api_Base {
	protected $_manageNavigation;
	protected $_moduleName = 'Api';

	public function toggleBox( $boxId, $user, $subject ) {
        
		if( $user->getIdentity() === $subject->getIdentity() ) {
			return array(
				'data'  => array(),
				'error' => array( 'You cannot add your self' )
			);
		}

		if ( ( empty( $boxId ) ) || ( $user->getIdentity() == 0 )  || ( empty( $subject  ) ) ) {
			
			return array(
				'data' => array(),
				'error' => array( 'Missing parameter' )
			);

        }
		
        $objTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
		$objDb = $objTable->getAdapter();
	
		$objSelect = $objTable->select()
			->where( 'user_id = '. $subject->getIdentity() .' AND circle_id = '. $boxId );
			
		$box = $objDb->fetchAll( $objSelect );
		
        if ( empty( $box ) ) {
			
			return array(
				'data' => array(),
				'error' => array( 'Invalid box id' )
			);

        }
		
        if ( $this->has( $boxId, $user ) ) {
			$results = $this->remove( $boxId, $user );
        }
		else {
			$results = $this->add( $boxId, $user );
		}

		return array(
			'data' => $results,
			'error' => array()
		);
	}
	
	public function add( $boxId, $user ) {
		$circleItems = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );
		
		$row = $circleItems->createRow();
		$row->circle_id = $boxId;
		$row->user_id   = $user->getIdentity();
        $row->save();
		
		$box = Engine_Api::_()->getDbTable( 'circles', 'whmedia' );
		
		$rowBox = $box->fetchRow( 'circle_id ='. $boxId );
        
		return array(
			'message' => 'Added',
			'Box'     => array(
				'circle_id' => $rowBox->circle_id,
				'title'     => $rowBox->title,
				'members'   => array(
					'circleitem_id' => $row->circleitem_id,
					'user_id'       => $row->user_id
				)
						
			)
		);
	}
	
	public function remove( $boxId, $user ) {
		$objTable = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );
		$objDb = $objTable->getAdapter();
				
        $row = $objTable->fetchRow( array(
			'circle_id = ?' => $boxId,
            'user_id = ?' => $user->getIdentity() )
		);

        if ($row instanceof Core_Model_Item_Abstract) {
            $row->delete();
			return array(
				'message' => 'Removed'
			);
        }
		return false;
	}
	
    public function has( $boxId, User_Model_User $user ) {
		$objTable = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );
		$objDb = $objTable->getAdapter();
		
        return (bool)$objTable->fetchRow( array( 
			'circle_id = ?' => $boxId,
            'user_id = ?' => $user->getIdentity() ) 
		);

    }

	public function createBox( $title, $user ){
		
		$result = array( "data" => array(), "error" => array() );
		if($title == null)
			$result["error"][] = "Title is empty";
		
		
		if($title != null){
			
			$db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
			
			try {
				$objTable = Engine_Api::_()->getDbtable('circles', 'whmedia');
			
				$data = array( "title" => $title, "user_id" => $user->getIdentity() );
				$row = $objTable->createRow($data);
				$circle_id = $row->save();
				$db->commit();
				
				$data["circle_id"] = $circle_id;
				$result[ "data" ] = $data;
			
			} catch( Exception $e ) {
				$db->rollBack();     
				$result["error"][] = $e->getmessage();
			}
			
		}
		return $result;
	}
	
	
	public function editBox( $title, $boxId, $user ) {
	
		$circlesTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
        $list = $circlesTable->find($boxId)->current();
		$result = array( "data" => array(), "error" => array() );
		
		if( !$list || $list->user_id != $user->getIdentity() ) {
			$result["error"][] = "Group not found";
		}
	
		if( $list || $list->user_id == $user->getIdentity() ) {
			
			$db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
			
			try {
				$data = array( "title" => $title);
				$list->setFromArray($data);
                $list->save();
                $db->commit();
				$data["circle_id"] = $boxId;
				$data["user_id"] = $user->getIdentity();
				
				$result[ "data" ] = $data;
			
			} catch( Exception $e ) {
				$db->rollBack();     
				$result["error"][] = "Saving error";
			}
		
		}
		
		return $result;
	}
	
	public function deleteBox( $boxId, $user ){
		$circlesTable = Engine_Api::_()->getDbTable('circles', 'whmedia');
        $list = $circlesTable->find($boxId)->current();
		$result = array( "data" => array(), "error" => array() );
		
		if( !$list || $list->user_id != $user->getIdentity() ) {
			$result["error"][] = "Group not found";
		}
	
		if( $list || $list->user_id == $user->getIdentity() ) {
			$circleItemsTable = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );
			$delResult     = $circleItemsTable->delete( array( 'circle_id = ?' => $boxId ) );
			$delResult2    = $circlesTable->delete( array( 'circle_id = ?' => $boxId ) );

			$result["data"]["message"] = "Successfully deleted";
			$result["data"]["circle_id"] = $boxId;
		}	
		return $result;
	}
	
	public function getUsersList( $currentUser, $params ) {
		try {
			$suffix = $params[ 'offset' ] . '0';
			$circleitem = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );
			$adapter = $circleitem->getAdapter();

			$select = $circleitem->select()
									->where( 'circle_id ='. $params[ 'circle_id' ] );
			if( isset( $params[ 'offset' ] ) ) {
				$select->limit( 10, $suffix );
			}
			
			$resultSet = $adapter->fetchAll( $select );
		} catch( Exception $e ) {
			print_r( $e->getMessage() );
		}
		
		$return = '';
		$user = Engine_Api::_()->getApi( 'user', 'api' );
		$circle = Engine_Api::_()->getDbTable( 'circles', 'whmedia' );
		foreach( $resultSet as $key => $value ) {
			$return[ $key ][ 'User' ] = $user->fetchUserDetails( $value[ 'user_id' ] );
			$select = $circleitem->select()
								 ->from( array( 'ci' => 'engine4_whmedia_circleitems' ) )
								 ->joinLeft( array( 'c' => 'engine4_whmedia_circles'), 'ci.circle_id = c.circle_id', array( '' ) )
								 ->where( 'ci.user_id ='. $value[ 'user_id' ] )
								 ->where( 'c.user_id ='. $currentUser->getIdentity() )
								 ->setIntegrityCheck( false );
			$return[ $key ][ 'UsersInBox' ] = $adapter->fetchAll( $select );			
		}
		
		foreach( $return as $key => $value ) {
			foreach( $value[ 'UsersInBox' ] as $innerKey => $innerValue ) {
				$arrCircle = $circle->fetchAll( 'circle_id ='. $innerValue[ 'circle_id' ] )->toArray();
				
				if(  !empty( $arrCircle ) ) {
					$return[ $key ][ 'Box' ][] = $arrCircle[ 0 ];
				}
			}
			

			foreach( $return[ $key ][ 'Box' ] as $innerKey => $innerValue ) {
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'user_id' ] );
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'freq' ] );
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'public' ] );
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'photo_id' ] );
			}

			
			unset( $return[ $key ][ 'UsersInBox' ] );
		}
		
		foreach( $return as $key => $value ) {
			if( !isset( $value[ 'Box' ] ) ) {
				$return[ $key ][ 'Box' ] = 'null';
			}
		}
		
		if( empty( $return ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		}		
		
		return array(
			'data'  => $return,
			'error' => array()
		);	
	}
	
	public function getUsersList2( $currentUser, $params ) {
		$suffix = $params[ 'offset' ] .'0';
		try {
			$circleitem = Engine_Api::_()->getDbTable( 'circleitems', 'whmedia' );
			$adapter = $circleitem->getAdapter();

			$select = $circleitem->select()
									->where( 'circle_id ='. $params[ 'circle_id' ] );
			if( isset( $params[ 'offset' ] ) ) {
				$select->limit( 10, $suffix );
			}
			
			$resultSet = $adapter->fetchAll( $select );
		} catch( Exception $e ) {
			print_r( $e->getMessage() );
		}
		
		$return = '';
		$user = Engine_Api::_()->getApi( 'user', 'api' );
		
		foreach( $resultSet as $key => $value ) {
			$return[ $key ][ 'User' ] = $user->fetchUserDetails( $value[ 'user_id' ] );
			//$return[ $key ][ 'UsersInBox' ] =  $circleitem->fetchAll( 'user_id ='. $value[ 'user_id' ] )->toArray();
			$select = $circleitem->select()
								 ->from( array( 'ci' => 'engine4_whmedia_circleitems' ) )
								 ->joinLeft( array( 'c' => 'engine4_whmedia_circles'), 'ci.circle_id = c.circle_id', array( '' ) )
								 ->where( 'ci.user_id ='. $value[ 'user_id' ] )
								 ->where( 'c.user_id ='. $currentUser->getIdentity() )
								 ->setIntegrityCheck( false );
			$return[ $key ][ 'UsersInBox' ] = $adapter->fetchAll( $select );
		}
print_r( $return ); exit;
		$circle = Engine_Api::_()->getDbTable( 'circles', 'whmedia' );		
		foreach( $return as $key => $value ) {
			foreach( $value[ 'UsersInBox' ] as $innerKey => $innerValue ) {
				$arrCircle = $circle->fetchAll( 'circle_id ='. $innerValue[ 'circle_id' ] )->toArray();
				
				if(  !empty( $arrCircle ) ) {
					$return[ $key ][ 'Box' ][] = $arrCircle[ 0 ];
				}
			}
			
			foreach( $return[ $key ][ 'Box' ] as $innerKey => $innerValue ) {
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'user_id' ] );
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'freq' ] );
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'public' ] );
				unset( $return[ $key ][ 'Box' ][ $innerKey ][ 'photo_id' ] );
			}

			
			unset( $return[ $key ][ 'UsersInBox' ] );
		}
		
		foreach( $return as $key => $value ) {
			if( !isset( $value[ 'Box' ] ) ) {
				$return[ $key ][ 'Box' ] = 'null';
			}
		}
		
		if( empty( $return ) ) {
			return array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		}		
		
		return array(
			'data'  => $return,
			'error' => array()
		);	
	}
	
}