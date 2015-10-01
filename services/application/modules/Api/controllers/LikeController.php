<?php
class Api_LikeController extends Zend_Rest_Controller {
	public function init() {
		$this->_helper->layout ()->disableLayout ();
		$this->_helper->viewRenderer->setNoRender ( true );
		
		$this->_helper->AjaxContext ()->addActionContext ( 'get', 'json' )->addActionContext ( 'post', 'json' )->addActionContext ( 'new', 'json' )->addActionContext ( 'edit', 'json' )->addActionContext ( 'put', 'json' )->addActionContext ( 'delete', 'json' )->initContext ( 'json' );
		
		$identity = $this->_getParam ( 'project_id' );
		if ($identity) {
			$item = Engine_Api::_ ()->getItem ( 'whmedia_project', $identity );
			if ($item instanceof Whmedia_Model_Project) {
				if (! Engine_Api::_ ()->core ()->hasSubject ()) {
					Engine_Api::_ ()->core ()->setSubject ( $item );
				}
			}
		}
		
		$this->_helper->requireSubject ();
	}
	public function toggleLikeAction() {
		if (! $this->getRequest ()->isPost ()) {
			$this->view->status = false;
			$this->view->error = Zend_Registry::get ( 'Zend_Translate' )->_ ( 'Invalid request method' );
			return;
		}
		$viewer = Engine_Api::_ ()->user ()->getViewer ();
		$subject = Engine_Api::_ ()->core ()->getSubject ();
		
		// Process
		$db = $subject->likes ()->getAdapter ();
		$db->beginTransaction ();

		$isLike = false;
		try {
			if ($subject->likes ()->isLike ( $viewer )) {
				$subject->likes ()->removeLike ( $viewer );
				$isLike = false;
			} else {
				$isLike = true;
				$subject->likes ()->addLike ( $viewer );
				// Add notification
				$owner = $subject->getOwner ();
				$this->view->owner = $owner->getGuid ();
				if ($owner->getType () == 'user' && $owner->getIdentity () != $viewer->getIdentity ()) {
					$notifyApi = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
					$notifyApi->addNotification ( $owner, $viewer, $subject, 'liked', array (
							'label' => $subject->getShortType () 
					) );
				}
			}

			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		$response = array();
		$response[ 'status' ] = true;
		$response[ 'islike' ] = $isLike;
		
		$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );

		$response[ 'count_likes' ] = $projectFeed->niceNumber( $subject->likes()->getLikeCount() );

		$this->_helper->json( array(
			'data' => $response,
			'error' => array()
		) );
		
	}
	private function fetchLikes($user) {
		$db_host = 'localhost';
		$db_user = 'root';
		$db_pass = '15under15';
		$db_schema = 'yambai';
		
		// Create connection
		$con = mysql_connect ( $db_host, $db_user, $db_pass );
		
		$conn = mysql_select_db ( $db_schema, $con );
		
		// Check connection
		if (mysqli_connect_errno ( $con )) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error ();
		}
		
		$user_id = $user->getIdentity ();
		$project_id = $this->_getParam ( 'project_id', null );
		
		// Fetch Project Likes
		$statement_fetch_project_likes = "SELECT u.displayname, u.user_id
					FROM engine4_core_likes cl
					LEFT JOIN engine4_users u
					ON (cl.poster_id = u.user_id)
					WHERE cl.resource_id = " . $project_id . "
					ORDER BY u.user_id IN (" . $user_id . ") DESC";
		
		$result_fetch_project_likes = mysql_query ( $statement_fetch_project_likes ) or die ( "Invalid Query Fetch Likes: " . mysql_error () );
		
		while ( $project_likes = mysql_fetch_assoc ( $result_fetch_project_likes ) ) {

			$objUser = Engine_Api::_()->getApi( 'user', 'api' );
			$user = $objUser->fetchUserDetails( $project_likes[ 'user_id' ] );
			$project_likes[ 'storage_path' ] = $user[ 'storage_path' ];
			$p_likes = $project_likes;
			$project_likesRow [] = $p_likes;
		}
		
		$this->_helper->json ( array (
				'Likes' => $project_likesRow 
		) );
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
		
		Engine_Api::_ ()->user ()->setViewer ( $user );
		
		$type = $this->_getParam ( 'type', null );
		
		if ($type === "post") {
			$this->toggleLikeAction ( $user );
		} else if ($type === "get") {
			$this->fetchLikes ( $user );
		}
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
	public function forbiddenAction() {
		$message = array (
				'message' => 'Token provided is invalid' 
		);
		
		$this->_helper->json ( array (
				'error' => $message 
		) );
	}
	public function expiredAction($viewer) {
		$message = array (
				'message' => 'Token expired' 
		);
		
		$this->_helper->json ( array (
				'error' => $message 
		) );
	}
}
