<?php

class Api_StatisticController extends Zend_Rest_Controller {
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
	
	public function indexAction() {}
	
	public function getAction() {}
	
	public function postAction() {
		$params = $this->getRequest()->getPost();

		$statistic = Engine_Api::_()->getApi( 'statistic', 'api' );		
		switch( $this->getRequest()->getParam( 'method', null ) ) {
			case 'incrementViewCount' :
				$resultSet = $statistic->updateViewCount( $params );
			break;
			
			default :
				$resultSet = array(
					'data'  => array(),
					'error' => array( 'No method specified' )
				);
			break;
		}
		
		$this->_helper->json( $resultSet );
	}
	
	public function putAction() {}
	
	public function deleteAction() {}
}