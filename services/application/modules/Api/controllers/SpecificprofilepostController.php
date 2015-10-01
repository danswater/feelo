<?php
class Api_SpecificprofilepostController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		
		$this->_helper->AjaxContext ()
			->addActionContext ( 'get', 'json' )
			->addActionContext ( 'post', 'json' )
			->addActionContext ( 'new', 'json' )
			->addActionContext ( 'edit', 'json' )
			->addActionContext ( 'put', 'json' )
			->addActionContext ( 'delete', 'json' )
			->initContext ( 'json' );
		
		$subject = null;
		if( !Engine_Api::_()->core()->hasSubject() )
		{
			$id = $this->_getParam('id');
		
			if( null !== $id )
			{
				$subject = Engine_Api::_()->user()->getUser($id);
				if( $subject->getIdentity() )
				{
					Engine_Api::_()->core()->setSubject($subject);
				}
			}
		}
		
	}
	public function indexAction() {
		$this->_helper->json ( array (
				'action' => 'index' 
		) );
	}
	public function getAction() {
		$this->_forward ( 'index' );
	}
	public function newAction() {
		$this->_forward ( 'index' );
	}
	public function postAction() {
		$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1) {
			return $this->_forward ( 'forbidden' );
		}
        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		
		$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );

		try {
			$projectFeed->offset = $this->_getParam( 'offset' );
			$arrResultSet = $projectFeed->newSpecificFeed( $user, $this->_getParam( 'project_id' ) );
			$response = array(
				'data'  => array(
					'Posts' =>$arrResultSet
				),
				'error' => array()
			);		
		} catch ( Exception $e ) {
			$response = array(
				'data'  => array(),
				'error' => array( $e->getMessage() )
			);	
		}

		$this->getHelper( 'json' )->sendJson( $response );			

	}

	public function editAction() {
		$this->_forward ( 'index' );
	}
	public function putAction() {
		$this->_forward ( 'index' );
	}
	public function deleteAction() {
		$this->_forward ( 'index' );
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
