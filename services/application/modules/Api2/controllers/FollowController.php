<?php
class Api2_FollowController extends Zend_Rest_Controller {
	const FOLLOW_PROJECT = 'follow_project';
	const FOLLOW_HASHTAG = 'follow_hashtag';

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
		$this->_forward('index');
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
			
		Engine_Api::_()->user()->setViewer($user);
		
		$type = $this->getRequest()->getParam( 'type' );
		$params = $this->getRequest()->getPost();

		switch( $params[ 'method' ] ) {

			case self::FOLLOW_HASHTAG :
				$user_id = (int)$this->_getParam('user_id');
				if (empty($user_id)) {
					return $this->_helper->Message('Invalid user id', false, false)->setAjax()->setError();
				}
				$this->_subject = Engine_Api::_()->user()->getUser($user_id);
				if (!$this->_subject->getIdentity()) {
					return $this->_helper->Message('Invalid user id', false, false)->setAjax()->setError();
				}			
				$objFollow = Engine_Api::_()->getApi( 'follow', 'whmedia' );

				$objResponse = $objFollow->toggleHashTag( array(
					'user'  => Engine_Api::_()->user()->getViewer(),
					'tagId' => $this->getRequest()->getParam( 'tag_id' )
				) );

				$this->getHelper( 'json' )->sendJson( $objResponse );

			break;
			
			case 'fetchFollowing' :
			
				$subject = Engine_Api::_()->user()->getUser( $params[ 'user_id' ] );
	
				$objFollow = Engine_Api::_()->getApi( 'follow', 'api' );

				$following = $objFollow->fetchFollowing( $user, $subject, $params );

				$this->getHelper( 'json' )->sendJson( $following );
			break;
			
			case 'fetchFollowers' :
				
				$subject = Engine_Api::_()->user()->getUser( $params[ 'user_id' ] );
				
				$objFollow = Engine_Api::_()->getApi( 'follow', 'api' );
				
				$followers = $objFollow->fecthFollowers( $user, $subject, $params );
				
				$this->getHelper( 'json' )->sendJson( $followers );
			break;
			
			case 'confirm':
				$followApi = Engine_Api::_()->getApi( 'follow', 'api' );
				$data = $followApi->confirm( $user, $params );
				$this->getHelper( 'json' )->sendJson( $data );
			break;
			
			case 'ignore':
				$followApi = Engine_Api::_()->getApi( 'follow', 'api' );
				$data = $followApi->ignore( $user, $params );
				$this->getHelper( 'json' )->sendJson( $data );
			break;
			
			case 'checkFollowStatus' :
				$followApi = Engine_Api::_()->getApi( 'follow', 'api' );
				$data = $followApi->checkFollowStatus( $user, $params );
				$this->getHelper( 'json' )->sendJson( $data );
				return;
			break;
			
			default:
				$defaultResponse = array( "data" => array(), "error" => array() );
				$params[ 'subject_id' ] = $user_id = (int)$this->_getParam('user_id');
				if (empty($user_id)) {
					$defaultResponse[ "error" ] = "Invalid user id";
					//return $this->_helper->Message('Invalid user id', false, false)->setAjax()->setError();
				}
				$this->_subject = Engine_Api::_()->user()->getUser($user_id);
				if (!$this->_subject->getIdentity()) {
					$defaultResponse[ "error" ] = "Invalid user id";
					//return $this->_helper->Message('Invalid user id', false, false)->setAjax()->setError();
				}			
				
				if( count( $defaultResponse[ "error" ] ) == 0 ) {
					$viewer = Engine_Api::_()->user()->getViewer();
					$objFollow = Engine_Api::_()->getApi( 'follow', 'api' );

					$defaultResponse[ "data" ] = $objFollow->toggleFollowAction( $viewer, $params );		
				}
				//$this->toggleFollowAction();
				$this->getHelper( 'json' )->sendJson( $defaultResponse );			

			break;
		}
		
			
		
	}
	
	public function toggleFollowHashtagAction() {

	}

	public function toggleFollowAction() {
	
		$viewer = Engine_Api::_()->user()->getViewer();
	
		if ($this->_subject->getIdentity() == $viewer->getIdentity()) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get('Zend_Translate')->_("You can't follow yourself.");
			return;
		}
	
		$followApi = Engine_Api::_()->getDbtable('follow', 'whmedia');
	
		// Process
		$db = $followApi->getAdapter();
		$db->beginTransaction();
	
		try {
			if ($followApi->isFollow($this->_subject, $viewer)) {
				$followApi->unFollow($this->_subject, $viewer);
				$isFollow = false;
			}
			else {
				$isFollow = true;
				$followApi->Follow($this->_subject, $viewer);
	
				Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($this->_subject, $viewer, $this->_subject, 'whmedia_following');
			}
	
			$db->commit();
		}
		
		catch( Exception $e ) {
			$db->rollBack();
			throw $e;
		}
		
		$boolarray = Array(false => 'false', true => 'true');
		
		$this->_helper->json ( array (
					'message' => "Following: $boolarray[$isFollow]"
			) );
		
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
		$this->_forward ( 'index' );
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
