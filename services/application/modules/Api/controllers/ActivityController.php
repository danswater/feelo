<?php

class Api_ActivityController extends Zend_Rest_Controller {

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
        $this->_helper->json(array('action' => 'index'));
    }

    public function getAction() {
        $token = $this->_getParam('token', null);

        $table = Engine_Api::_()->getDbTable('auth', 'api');
        $select = $table->select();
        $select->where('token = ?', $token);

        $auth = $table->fetchRow($select);

        if (count($auth) != 1)
            return $this->_forward('forbidden', 'auth');

        // if ($auth->expire_date < time())
        //    return $this->_forward('expired');

        $viewer = Engine_Api::_()->user()->getUser($auth->user_id);
        Engine_Api::_()->user()->setViewer($viewer);

//         $select = Engine_Api::_()->getDbtable('stream', 'whmedia')->selectStreamProjects($viewer);
//         $select->order('tmp_stream_projects.creation_date DESC');

//         $paginator = Zend_Paginator::factory($select);
//         $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 0));
//         $pageNumber = $this->_getParam('page', 1);
//         $paginator->setCurrentPageNumber($pageNumber);

//         foreach ($paginator as $item) {
//             $comments = $item->comments()->getAllComments();
//             $parsedComments = array();
//             foreach ($comments as $comment) {
//                 $author = Engine_Api::_()->user()->getUser($comment->poster_id);
//                 $parsedComments[] = array(
//                     'comment_id' => $comment->comment_id,
//                     'author' => array(
//                         'username' => $author->getTitle(),
//                         'href' => $author->getHref(),
//                         'image' => $author->getPhotoUrl(),
//                     ),
//                     'body' => $comment->body,
//                     'creation_date' => $comment->creation_date,
//                     'parent_id' => $comment->parent_id,
//                     'deleted' => $comment->deleted
//                 );
//             }
//             $activity[] = array(
//                 'project_id' => $item->getIdentity(),
//                 'href' => $item->getHref(),
//                 'image' => $item->getPhotoUrl($this->_getParam('thumb_width', 160), null),
//                 'author' => array(
//                     'username' => $item->getParent()->getTitle(),
//                     'href' => $item->getParent()->getHref(),
//                     'image' => $item->getParent()->getPhotoUrl(),
//                 ),
//                 'likes_count' => $item->likes()->getLikeCount(),
//                 'views_count' => $item->project_views,
//                 'comments' => $parsedComments,
//             );
//         }



//         $out = array('Activity_Feed' => $activity);
//         //Zend_Debug::dump($out);
//         $this->_helper->json($out);

        self::getActivityFeed($viewer);
    }
    
    public function getActivityFeed($user){
    	
    	$user_id = $user->getIdentity();
    	
    	$circlesTable = Engine_Api::_ ()->getDbTable ( 'circles', 'whmedia' );
    	$circlesTableName = $circlesTable->info ( 'name' );
    	$circleItemsTable = Engine_Api::_ ()->getDbTable ( 'circleitems', 'whmedia' );
		$circleItemsTableName = $circleItemsTable->info ( 'name' );
		
		$select_circles = $circlesTable->select();
		$select_circles->where ('user_id = ?', $user_id);
		
		$select_circleItems = $circleItemsTable->select ();
		$select_circleItems->where ( 'circle_id = ?', $c_id );
    	
    	$out = array('Activity_Feed' => $activity);
    	//Zend_Debug::dump($out);
    	$this->_helper->json($out);
    	
    }
    
    public function time_ago($date,$granularity=2) {
	    $date = strtotime($date);
	    $difference = time() - $date;
	    $periods = array('decade' => 315360000,
	        'year' => 31536000,
	        'month' => 2628000,
	        'week' => 604800, 
	        'day' => 86400,
	        'hour' => 3600,
	        'minute' => 60,
	        'second' => 1);
	    if ($difference < 5) {
	        $retval = "posted just now";
	        return $retval;
	    } else {                            
	        foreach ($periods as $key => $value) {
	            if ($difference >= $value) {
	                $time = floor($difference/$value);
	                $difference %= $value;
	                $retval .= ($retval ? ' ' : '').$time.' ';
	                $retval .= (($time > 1) ? $key.'s' : $key);
	                $granularity--;
	            }
	            if ($granularity == '0') { break; }
	        }
	        return $retval.' ago';   
	    }
	}

    public function newAction() {

        $this->_forward('index');
    }

    public function postAction() {
        $this->_helper->json(array('action' => 'post'));
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
        
    }

    public function expiredAction() {
        $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
        $this->_helper->json(array('error' => 'Token expired', 'message' => 'Provided token has been expired.'));
    }

}
