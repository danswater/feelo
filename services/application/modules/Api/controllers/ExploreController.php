<?php

class Api_ExploreController extends Zend_Rest_Controller {

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
		switch( $params[ 'method' ] ) {
			
			case 'fetchTrending' :
				$projectFeed  = Engine_Api::_()->getApi( 'project', 'api' );
				$arrResultSet = $projectFeed->fetchFeed( $user, 'trending', $this->_getParam( 'offset', null ), null, $this->_getParam( 'creation_date', 'week' ) );
			break;
			
			case 'fetchFavo' :
				$favo         = Engine_Api::_()->getApi( 'favo', 'api' );
				$arrResultSet = $favo->fetchExploreFavo( $user, $params );
			break;
			
			case 'fetchHashtag' :
				//select * from engine4_users where level_id = 6;
				//select * from engine4_core_tagmaps where tagger_id = 152;
				//select * from engine4_core_tags where tag_id = 1;
				try {
				$favo         = Engine_Api::_()->getApi( 'favo', 'api' );
				$arrResultSet = $favo->fetchExploreHashtag( $user, $params );
				} catch( Exception $e ) {
					print_r( $e ); exit;
				}
			break;
			
			case 'fetchVideos' :
				$whmedia = Engine_Api::_()->getApi( 'whmedia', 'api' );
				$arrResultSet = $whmedia->fetchExploreVideo( $user, $params );
			break;
			
			default :
				$arrResultSet = array(
					'data'  => array(),
					'error' => array( 'Missing method parameters' )
				);
			break;
			
		}
		$this->_helper->json( $arrResultSet );
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
