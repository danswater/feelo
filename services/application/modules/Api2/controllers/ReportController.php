<?php
class Api2_ReportController extends Zend_Rest_Controller {

	public function init() {
		$this->getHelper( 'layout' )->getLayoutInstance()->disableLayout();
		$this->getHelper( 'viewRenderer' )->setNoRender();

        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            $this->_helper->json(array('error' => 'Invalid Method', 'messages' => array('Use Post Method to Authenticate.')));
        }

        $this->_helper->AjaxContext()
                ->addActionContext('get', 'json')
                ->addActionContext('post', 'json')
                ->addActionContext('put', 'json')
                ->addActionContext('delete', 'json')
                ->addActionContext('index', 'json')
                ->initContext('json');
    }

	public function indexAction(){
		$this->getHelper( 'json' )->sendJson( [] );
	}

	public function getAction(){
		$this->getHelper( 'json' )->sendJson( [] );
	}

	public function postAction(){

		$token = $this->getRequest()->getPost ( 'token', null );
	
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1) {
			$this->getHelper( 'json' )->sendJson( array( 
				"error" => 'Invalid token!'
			) );
			return;
		}

		$method = $this->getRequest()->getPost ( 'method', null );

		switch( $method ) {
			case 'report-api':
				$this->createAction();
				break;
			default:
				# code...
				break;
		}
		
	}

	public function deleteAction(){
		$this->getHelper( 'json' )->sendJson( [] );
	}

	public function putAction(){
		$this->getHelper( 'json' )->sendJson( [] );
	}

	public function createAction(){
		$table = Engine_Api::_()->getItemTable('core_report');
		$db = $table->getAdapter();
		$db->beginTransaction();

		try {
		    $viewer = Engine_Api::_()->user()->getViewer();
		    $report = $table->createRow();

		    $report_data = array(
		        'category' => $this->getRequest()->getPost('category', ''),
		        'description' => $this->getRequest()->getPost('description', ''),
		        'subject_type' => $this->getRequest()->getPost('subject_type', ''), // e.g (whmedia_project / user)
		        'subject_id' => $this->getRequest()->getPost('subject_id', ''), // e.g (project_id if subject_type is whmedia_project / user_id if subject_type is user)
		        'user_id' => $viewer->getIdentity() // the user who report
		    );

		    $report->setFromArray( $report_data );

		    if( $report->save() ){
		        $response = array(
		          'data'  => array( $report->toArray() ),
		          'error' => array()
		        ); 
		    }
		    $db->commit();
		} catch( Exception $e ) {
		    $response = array(
		        'data'  => array(),
		        'error' => array( $e->getMessage() )
		    );  
		}

		$this->getHelper( 'json' )->sendJson( $response );
	}
}