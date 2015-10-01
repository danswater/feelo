<?php
class Api_SocialmediaconnectController extends Zend_Rest_Controller {

	public function init() {
		$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
	}

	public function indexAction() {}

	public function getAction() {}

	public function postAction() {
	
		$params = $this->getRequest()->getPost();
		$apiApiUser = Engine_Api::_()->getApi( 'user', 'api' );
	
		switch( $this->getRequest()->getParam( 'method', 'add' ) ) {
			case 'connectToFacebook':
				try {
					$ret = $apiApiUser->authenticateUsingFacebook( $params );
				} catch ( Exception $e ) {
					$ret = array();
					$ret[ 'data' ] = array();
					$ret[ 'error' ] = array( $e->getMessage() );
				}
			break;
			
			default:
				$ret = array();
				$ret[ 'data' ] = array();
				$ret[ 'error' ] = array( 'Invalid method' );
			break;
		}
		
		$this->getHelper( 'json' )->sendJson( $ret ) ;

	}

	public function putAction() {}

	public function deleteAction() {}

}