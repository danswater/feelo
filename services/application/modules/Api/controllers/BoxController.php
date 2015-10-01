<?php

class Api_BoxController extends Zend_Rest_Controller {

    public function init() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_helper->AjaxContext()
                ->addActionContext('get', 'json')
                ->addActionContext('post', 'json')
                ->addActionContext('new', 'json')
                ->addActionContext('edit', 'json')
                ->addActionContext('put', 'json')
                ->addActionContext('delete', 'json')
                ->initContext('json');
    }

    public function indexAction() {
    	$this->_helper>json ( array (
    			'action' => 'index'
    	) );
    }

    public function getAction() {
		$this->_forward('index');
    }
    
    public function getBoxList($user) {
		$user_id = $user->getIdentity ();
		
		$usersTable = Engine_Api::_ ()->getDbTable ( 'users', 'user' );
		$usersTableName = $usersTable->info ( 'name' );
		$followTable = Engine_Api::_ ()->getDbTable ( 'follow', 'whmedia' );
		$followTableName = $followTable->info ( 'name' );
		$circlesTable = Engine_Api::_ ()->getDbTable ( 'circles', 'whmedia' );
		$circlesTableName = $circlesTable->info ( 'name' );
		$circleItemsTable = Engine_Api::_ ()->getDbTable ( 'circleitems', 'whmedia' );
		$circleItemsTableName = $circleItemsTable->info ( 'name' );
		$storageTable = Engine_Api::_()->getDbTable('files', 'storage');
		$storageTableName = $storageTable->info('name');
		
		$select_circles = $circlesTable->select ();
		$select_circles->where ( 'user_id = ?', $user_id );
		
		$circleRows = $circlesTable->fetchAll ( $select_circles );
		
		for($i = 0; $i < count ( $circleRows ); $i ++) {
			
			$c_id = $circleRows [$i]->circle_id;
			$c_title = $circleRows [$i]->title;
			
			$circleRow [$i] ['circle_id'] = $c_id;
			$circleRow [$i] ['title'] = $c_title;
			
			$select_circleItems = $circleItemsTable->select ();
			$select_circleItems->where ( 'circle_id = ?', $c_id );
			
			$circleItemsRows = $circleItemsTable->fetchAll ( $select_circleItems );
			
			if (count ( $circleItemsRows ) < 1) {
				$circleRow [$i] ['members'] = "null";
			} else {
				for($c = 0; $c < count ( $circleItemsRows ); $c ++) {
					$ci_id = $circleItemsRows [$c]->circleitem_id;
					$ci_uid = $circleItemsRows [$c]->user_id;
					
					$circleItemRow [$c] ['circleitem_id'] = $ci_id;
					$circleItemRow [$c] ['user_id'] = $ci_uid;
					
					$circleRow [$i] ['members'] = $circleItemRow;
				}
				unset ( $circleItemRow );
			}
		}

		if( $circleRow == null ){
			$circleRow = array();
		}

		$this->_helper->json(array ('Box' => $circleRow));
	}

    public function newAction() {

        $this->_forward('index');
    }

    public function postAction() {
    	$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1)
			return $this->_forward ( 'forbidden' );
		
        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		$params = $this->getRequest()->getPost();

		switch( $this->_getParam( 'method', null ) ) {
						
			case 'addRemoveBox' :
				$subject = Engine_Api::_ ()->user ()->getUser ( $this->_getParam ( 'user_id', null ) );
				$boxId = $this->_getParam ( 'circle_id', null );
				$objBox = Engine_Api::_()->getApi( 'box', 'api' );
	
				$arrResultSet  = $objBox->toggleBox( $boxId , $subject, $user );
				$this->getHelper( 'json' )->sendJson( $arrResultSet );
				return $arrResultSet;
			break;
			
			case 'editBox' :
				// get all the params that need to update the box
				$boxId = $this->_getParam ( 'circle_id', null );
				$title = $this->_getParam ( 'title', null );
				
				$objBox = Engine_Api::_()->getApi( 'box', 'api' );
				
				$arrResultSet = $objBox->editBox( $title, $boxId, $user );
				
				$this->getHelper( 'json' )->sendJson( $arrResultSet );
				return $arrResultSet;
			break;
			
			case 'createBox' :
			
				$title = $this->_getParam ( 'title', null );
				$objBox = Engine_Api::_()->getApi( 'box', 'api' );
				
				$arrResultSet = $objBox->createBox( $title, $user );
				
				$this->getHelper( 'json' )->sendJson( $arrResultSet );
				return $arrResultSet;
			
			break;
			
			case 'deleteBox': 
				// get the box id
				$boxId = $this->_getParam ( 'circle_id', null );
				$objBox = Engine_Api::_()->getApi( 'box', 'api' );
				
				$arrResultSet = $objBox->deleteBox( $boxId, $user );
				
				$this->getHelper( 'json' )->sendJson( $arrResultSet );
				return $arrResultSet;
			break;	
				
			case 'get' :
				$this->getBoxList($user);
			break;
			
			case 'getUsers' :
				$objBox = Engine_Api::_()->getApi( 'box', 'api' );
				$arrResultSet = $objBox->getUsersList( $user, $params );
				$this->getHelper( 'json' )->sendJson( $arrResultSet );
			break;
			
			case 'getUsers2' :
				$objBox = Engine_Api::_()->getApi( 'box', 'api' );
				$arrResultSet = $objBox->getUsersList2( $user, $params );
				$this->getHelper( 'json' )->sendJson( $arrResultSet );
			break;
			
			default :
				$arrResultSet = array(
					'data'  => array(),
					'error' => array( 'Missing method parameter' )
				);
				
				$this->getHelper( 'json' )->sendJson( $arrResultSet );
			break;
		}

    }

    public function editAction() {

        $this->_forward('index');
    }

    public function putAction() {

        $this->_forward('index');
    }

    public function deleteAction() {

	
	
        $this->_forward('index');
    }

    public function headAction() {
    	$this->_forward('index');
    }
    
	public function forbiddenAction(){
		
		$message = array('message' => 'Token provided is invalid');
		
		$this->_helper->json ( array (
				'error' => $message
		) );
	}
	
	public function expiredAction($viewer) {
		
		$message = array('message' => 'Token expired');
		
		$this->_helper->json ( array (
				'error' => $message
		) );
		
	}

}
