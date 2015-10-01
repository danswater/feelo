<?php

class Api_ListoffriendsController extends Zend_Rest_Controller {

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
        
    }

    public function getAction() {
// 		$token = $this->_getParam ( 'id', null );
// 		$test = $this->_getParam('test',null);
// 		echo $token."<br><br>".$test;
		
// 		$table = Engine_Api::_ ()->getDbTable ( 'auth', 'api' );
// 		$select = $table->select ();
// 		$select->where ( 'token = ?', $token );
		
// 		$auth = $table->fetchRow ( $select );
		
// 		if (count ( $auth ) != 1)
// 			return $this->_forward ( 'forbidden', 'auth' );
		
// 		if ($auth->expire_date < time ())
// 			return $this->_forward ( 'expired' );
		
// 		$user = Engine_Api::_ ()->user ()->getUser ( $auth->user_id );
		
// 		$this->getFriendsList($user);

		$this->_forward('index');
		
    }
    
    public function getFriendsList($user) {
		$user_id = $user->getIdentity ();
		
		$usersTable = Engine_Api::_ ()->getDbTable ( 'users', 'user' );
		$usersTableName = $usersTable->info ( 'name' );
		$followTable = Engine_Api::_ ()->getDbTable ( 'follow', 'whmedia' );
		$followTableName = $followTable->info ( 'name' );
		$circlesTable = Engine_Api::_ ()->getDbTable ( 'circles', 'whmedia' );
		$circlesTableName = $circlesTable->info ( 'name' );
		$circleItemsTable = Engine_Api::_ ()->getDbTable ( 'circleitems', 'whmedia' );
		$circleItemsTableName = $circleItemsTable->info ( 'name' );
		$storageTable = Engine_Api::_()->getDbTable('files', 'storage');
		$storageTableName = $storageTable->info('name');
		
		$select_follow = $followTable->select();
		$select_follow->where('follower_id = ?', $user_id);
		$followRows = $followTable->fetchAll($select_follow);
		for($i=0;$i<count($followRows);$i++){
			$follow_uid = $followRows[$i]->user_id;
			
			$select_users = $usersTable->select();
			$select_users//->setIntegrityCheck(false)
						 ->from($usersTableName)
						 //->joinLeft($storageTableName, "(`{$usersTableName}`.`user_id` = `{$storageTableName}`.`user_id` AND `{$storageTableName}`.`type` = \"thumb.profile\") AND `{$storageTableName}`.`parent_file_id` = `{$usersTableName}`.`photo_id`")
						 ->where("`{$usersTableName}`.`user_id` = ?", $follow_uid);
			
			$usersRows = $usersTable->fetchAll($select_users);
			for($c = 0; $c<count($usersRows);$c++){
				$friendsList[$i]['user_id'] = $usersRows[$c]->user_id;
				$friendsList[$i]['displayname'] = $usersRows[$c]->displayname;
				$friendsList[$i]['username'] = $usersRows[$c]->username;
				$friendsList[$i]['photo_id'] = $usersRows[$c]->photo_id;
				$friendsList[$i]['status'] = $usersRows[$c]->status;
				$friendsList[$i]['status_date'] = $usersRows[$c]->status_date;
				$friendsList[$i]['email'] = $usersRows[$c]->email;
				$friendsList[$i]['locale'] = $usersRows[$c]->locale;
				$friendsList[$i]['language'] = $usersRows[$c]->language;
				
				$table = Engine_Api::_()->getDbTable('files', 'storage');
				$select = $table->select();
				$select->where('user_id = ? and type = "thumb.profile" and parent_file_id = '.$usersRows[$c]->photo_id, $usersRows[$c]->user_id);
				$fetchData = $table->fetchRow($select);
				$friendsList[$i]['storage_path'] = $fetchData->storage_path;
				
				if( is_null( $fetchData->storage_path ) ) {
					$friendsList[ $i ][ 'storage_path' ] = 'public/user/nophoto_user_thumb_icon.png';
				}
				
				$user = Engine_Api::_ ()->user ()->getUser ( $usersRows [$c]->user_id );
				
				$objTable = Engine_Api::_()->getApi( 'follow', 'api' );
				$friendsList[ $i ][ 'following' ] = $objTable->countIFollow( $user );
				$friendsList[ $i ][ 'followers' ] = $objTable->countMyFollowers( $user );
			
				$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
				$friendsList[ $i ][ 'posts' ]  = $projectFeed->countAllPosts( $user );
			}
			
		}
		
		$this->_helper->json(array ('FriendsList' => $friendsList));
	}
	
    public function newAction() {

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
		
		$this->getFriendsList($user);
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
