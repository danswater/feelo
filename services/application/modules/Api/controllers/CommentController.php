<?php
class Api_CommentController extends Zend_Rest_Controller {
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
		
		// uses whmedia_project as type
		
		// $type = $this->_getParam ( 'type' );
		
	}
	
	public function headAction() {
		$this->_forward('index');
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

		$this->requireOnce();
		
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
		
		$this->fetchComments($user);
		
	}
	
	public function editAction() {
		$this->_forward ( 'index' );
	}
	
	public function putAction() {
		
		$this->requireOnce();
		
		$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1) {
			return $this->_forward ( 'forbidden' );
		}
		if ($auth->expire_date < time ()) {
			return $this->_forward ( 'expired' );
		}
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );

		$this->createComment($user);
	}
	
	public function fetchComments($user) {
		$apiApiComment = Engine_Api::_()->getApi( 'comment', 'api' );

		$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1) {
			return $this->_forward ( 'expired' );
		}
		if ($auth->expire_date < time ()) {
			return $this->_forward ( 'expired' );
		}
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		$offset = $this->_getParam('offset',null);
		$project_id = $this->_getParam('project_id',null);
		
		// Fetch comment count
		$statement_fetch_comment_count = "Select count(*) as count from yambai.engine4_whcomments_comments where deleted=0 AND resource_id = ".$project_id;
		$result_fetch_comment_count = mysql_query ( $statement_fetch_comment_count ) or die ( "Error on fetch_comment_count: " . mysql_error () );
		while($comment_count = mysql_fetch_assoc($result_fetch_comment_count)){
			$c_count = $comment_count['count'];
		}
		
		// Fetch Project Comments
		$statement_fetch_project_comments = "SELECT comment_id,
				parent_id,
				resource_id as project_id,
				poster_id as user_id,
						body,
						creation_date,
								deleted
								FROM yambai.engine4_whcomments_comments
								WHERE deleted=0 AND resource_id = " . $project_id ." ORDER BY creation_date DESC LIMIT 10 OFFSET ".$offset."0";
		
		$result_fetch_project_comments = mysql_query ( $statement_fetch_project_comments ) or die ( "Invalid Query 2: " . mysql_error () );
		
		$comments_counter = 0;
		while ( $project_comments = mysql_fetch_assoc ( $result_fetch_project_comments ) ) {
				
			$p_comments = $project_comments;
			$p_comments ['body'] = str_replace ( "&quot;", "\"", $p_comments ['body'] );

			$newComments = $apiApiComment->findTagsInComment( $p_comments[ 'body' ] );
			

			$p_comments [ 'body' ]        = $newComments[ 'body' ];
			$p_comments [ 'tag_userids' ] = $newComments[ 'tag_userids' ];

			$project_commentsRow [] = $p_comments;
				
			$result_fetch_user_commenter_photo = "SELECT u.displayname,
										sf.storage_path
										FROM engine4_users u
										LEFT JOIN engine4_storage_files sf
										ON (u.user_id = sf.user_id
									AND sf.type = 'thumb.profile')
								AND sf.parent_file_id = u.photo_id
										WHERE u.user_id = " . $p_comments ['user_id'];
				
			$result_fetch_user_commenter_photo = mysql_query ( $result_fetch_user_commenter_photo ) or die ( "Invalid Query Fetch Comment: " . mysql_error () );
				
			while ( $comment_photo = mysql_fetch_assoc ( $result_fetch_user_commenter_photo ) ) {
		
				$c_photo = $comment_photo;
			}
				
			$project_commentsRow [$comments_counter] ['storage_path'] = $c_photo ['storage_path'];
			$project_commentsRow [$comments_counter] ['displayname'] = $c_photo ['displayname'];
			$project_commentsRow [$comments_counter] ['parent_id'] = '';
			unset ( $comment_photoRow );
				
			$comments_counter ++;
		}
		
		$this->_helper->json(array ('Comments' => $project_commentsRow, 'comment_count' => $c_count));
		
	}
	public function deleteAction() {
		$this->requireOnce();
		
		$token = $this->_getParam ( 'token', null );
		
		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
		$select = $table->select ();
		$select->where ( 'token = ?', $token );
		
		$auth = $table->fetchRow ( $select );
		
		if (count ( $auth ) != 1) {
			return $this->_forward ( 'forbidden' );
		}
		if ($auth->expire_date < time ()) {
			return $this->_forward ( 'expired' );
		}
		
		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		
		// Comment id
		$comment_id = $this->_getParam ( 'comment_id' );
		if (! $comment_id) {
			$this->_helper->json(array ('message' => 'No comment'));
			return;
		}
		
		// Comment
		$comment = Engine_Api::_ ()->getItem ( 'whcomments_comment', $comment_id );
		if (! $comment) {
			$this->_helper->json(array ('message' => 'No comment or wrong parent'));
			return;
		}
		
		if ( $comment->poster_id != $user->getIdentity() ) {
			$this->_helper->json( array( 'message' => 'You cannot delete this comment' ) );
			return;
		}

		// Process
		$db = Engine_Api::_ ()->getDbTable ( 'comments', 'whcomments' )->getAdapter ();
		$db->beginTransaction ();
		
		try {
			$comment->deleted = true;
			$comment->save ();
			
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}

		$projectId = $this->_getParam( 'project_id', null );
		
		$commentsTable = Engine_Api::_()->getDbTable('comments', 'whcomments');
        $countSelect = $commentsTable->select();
        $countSelect->from( $commentsTable, array( 'count(*) as result_count' ) );
		$countSelect->where( 'deleted = 0' );
		$countSelect->where( 'resource_id = ?', $projectId );
		$countSelect->order( array( 'creation_date DESC' ) );
		$result_count = $commentsTable->fetchAll( $countSelect );

		if ( count( $result_count ) < 1 ) {
			$resultCount = 0;
		} else {
			$resultCount = $result_count[ 0 ]->result_count;
		}
		
		$ret[ 'comment_count' ] = $resultCount; 
		
		$this->_helper->json( array(
			'data' => $ret,
			'error' => array()
		) );
	}
	
	private function createComment($viewer){
		// $viewer = Engine_Api::_()->user()->getViewer();
		$subject;
		if ( !Engine_Api::_()->core()->hasSubject() ) {
			$subject = Engine_Api::_()->core()->setSubject( $viewer );
		}
		$subject = Engine_Api::_()->core()->getSubject();
		
		// Process
		// Filter HTML
		$filter = new Zend_Filter();
		$filter->addFilter(new Engine_Filter_Censor());
		$filter->addFilter(new Engine_Filter_HtmlSpecialChars());
		
		$body = $this->_getParam('body');
		$body = $filter->filter($body);
		
		switch ($body) {
			case 'happy':
				$body = '<img src="application/modules/Whcomments/externals/images/happy.png" alt="Happy" /> HAPPY';
				break;
			case 'nice':
				$body = '<img src="application/modules/Whcomments/externals/images/nice.png" alt="Nice" /> NICE';
				break;
			case 'omg':
				$body = '<img src="application/modules/Whcomments/externals/images/omg.png" alt="Omg" /> OMG';
				break;
			case 'sad':
				$body = '<img src="application/modules/Whcomments/externals/images/sad.png" alt="Sad" /> SAD';
				break;
			default:
				preg_match_all('/@[^\s]+/', $body, $userTags);
		
				if (isset($userTags[0]) && count($userTags[0]) > 0) {
					foreach ($userTags[0] as $userTag) {
						$user = Engine_Api::_()->user()->getUser(mb_substr($userTag, 1));
						if ($user->getIdentity()) {
							$href = $this->view->htmlLink($user->getHref(), $userTag);
							$body = str_replace($userTag, $href, $body);
						}
					}
				}
				break;
		}
		
		$parent_id = (int) $this->_getParam('parent_id', 0);
		if ($parent_id == 0)
			$parent_id = null;
		
		
		$db = $subject->comments()->getCommentTable()->getAdapter();
		$db->beginTransaction();
		
		try {
			Engine_Api::_ ()->getDbTable ( 'comments', 'whcomments' )->addComment ( $subject, $viewer, $body, $parent_id );
			
			$activityApi = Engine_Api::_ ()->getDbtable ( 'actions', 'activity' );
			$notifyApi = Engine_Api::_ ()->getDbtable ( 'notifications', 'activity' );
			$subjectOwner = $subject->getOwner ( 'user' );
			
			// Activity
			$action = $activityApi->addActivity ( $viewer, $subject, 'comment_' . $subject->getType (), '', array (
					'owner' => $subjectOwner->getGuid (),
					'body' => $body 
			) );
			
			// Notifications
			// Add notification for owner (if user and not viewer)
			$this->view->subject = $subject->getGuid ();
			$this->view->owner = $subjectOwner->getGuid ();
			if ($subjectOwner->getType () == 'user' && $subjectOwner->getIdentity () != $viewer->getIdentity ()) {
				$notifyApi->addNotification ( $subjectOwner, $viewer, $subject, 'commented', array (
						'label' => $subject->getShortType () 
				) );
			}
			
			// Increment comment count
			Engine_Api::_ ()->getDbtable ( 'statistics', 'core' )->increment ( 'core.comments' );
			
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			throw $e;
		}
		
		$comment = Engine_Api::_()->getDbTable( 'comments', 'whcomments' );
		$row = $comment->fetchRow( $comment->select()->order( array( 'comment_id DESC' ) )->limit( 1 ) );
		
		$storage = Engine_Api::_()->getDbTable( 'files', 'storage' );
		$select = $storage->select ();
		$select->where ( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $viewer->photo_id, $viewer->getIdentity() );
		$rowStorage = $storage->fetchRow ( $select );

		try {
			$response = array(
				'comment_id'    => $row->comment_id,
				'parent_id'     => $row->parent_id,
				'project_id'    => $this->_getParam( 'project_id', null ),
				'user_id'       => $viewer->getIdentity(),
				'body'          => $row->body,
				'creation_date' => $row->creation_date,
				'deleted'       => $row->deleted,
				'storage_path'  => $rowStorage->storage_path
			);
		} catch( Exception $e ) {
			$this->_helper->json( array(
				'message' => $e->getMessage()
			) );
		}
		$this->_helper->json ( array (
				'message' => 'success',
				'data'    => $response,
				'error'   => array()
		) );
// 		$this->view->status = true;
// 		$this->view->message = 'Comment added';
// 		$this->view->body = $this->view->action ( 'list', 'comment', 'whcomments', array (
// 				'type' => $this->_getParam ( 'type' ),
// 				'id' => $this->_getParam ( 'id' ),
// 				'format' => 'html' 
// 		) );
// 		$this->_helper->contextSwitch->initContext ();
		
	}
	
	private function requireOnce(){
		$db_host = 'localhost';
		$db_user = 'root';
		$db_pass = '15under15';
		$db_schema = 'yambai';
		
		// Create connection
		$con = mysql_connect($db_host,$db_user,$db_pass);
		
		$conn = mysql_select_db($db_schema, $con);
		
		// Check connection
		if (mysqli_connect_errno($con)){
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$type = "whmedia_project";
		$project_id = $this->_getParam ( 'project_id', null );
		
		if ($type && $project_id) {
			$item = Engine_Api::_ ()->getItem ( $type, $project_id );
			if ($item instanceof Core_Model_Item_Abstract && (method_exists ( $item, 'comments' ) || method_exists ( $item, 'likes' ))){
				if (! Engine_Api::_ ()->core ()->hasSubject ()){
					Engine_Api::_ ()->core ()->setSubject ( $item );
				}
			}
		}
		$this->_helper->requireSubject ();
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
