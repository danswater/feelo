<?php
class Api_LogoutController extends Zend_Rest_Controller {
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
		
		$delete = $table->getAdapter()->quoteInto('token = ?', $token);
		
		$table->delete($delete);
		
		$this->_helper->json(array ('message' => 'Success!'));
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
