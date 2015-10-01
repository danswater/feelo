<?php
class Api_UserprofilepostsController extends Zend_Rest_Controller {
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
		
		$subject = null;
		if( !Engine_Api::_()->core()->hasSubject() )
		{
			$id = $this->_getParam('id');
		
			if( null !== $id )
			{
				$subject = Engine_Api::_()->user()->getUser($id);
				if( $subject->getIdentity() )
				{
					Engine_Api::_()->core()->setSubject($subject);
				}
			}
		}
		
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
		$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );

		$projectFeed->userId = $user->getIdentity();
		$projectFeed->offset = $this->_getParam( 'offset', null );
		$arrResultSet        = $projectFeed->fetchFeed( $user, 'user', $this->_getParam( 'offset', null ), $this->_getParam( 'user_id', null ) );	
		$this->getHelper( 'json' )->sendJson( $arrResultSet );

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
	
	private function fetchOtherUserFeed($user){
		
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
		
		// $user_id = $user->getIdentity();
		$user_id = $this->_getParam('user_id');
		$offset = $this->_getParam('offset');
		
		$statement_fetch_user_projects="SELECT whmp.*
								FROM yambai.engine4_whmedia_projects whmp
								WHERE whmp.user_id = ".$user_id." 
								ORDER BY whmp.creation_date DESC
								LIMIT 10 OFFSET ".$offset."0";
		
		$result = mysql_query($statement_fetch_user_projects) or die("Invalid Query 1: ".mysql_error());
		
		$counter = 0;
		
		while($user_project_list = mysql_fetch_assoc($result))
		{
			$up_list=$user_project_list;
			$user_project_row[] = $up_list;
		
			$statement_fetch_project_media = "SELECT media_id, title, project_id, code
										  FROM engine4_whmedia_medias whmm
										  WHERE whmm.project_id = ".$up_list['project_id'];
		
			$result_fetch_media = mysql_query($statement_fetch_project_media) or die("Invalid Query 2: ".mysql_error());
		
			$inner_counter = 0;
		
			while($project_media = mysql_fetch_assoc($result_fetch_media)){
		
				$p_media = $project_media;
				$project_mediaRow[] = $p_media;
					
				if($up_list['cover_file_id']==""){
					$up_list['cover_file_id'] = "null";
				}
				$statement_fetch_project_thumb = "SELECT sf.storage_path
											  FROM engine4_storage_files sf
											  WHERE sf.parent_id = ".$up_list['cover_file_id'];
					
				$result_fetch_project_thumb = mysql_query($statement_fetch_project_thumb) or die("Invalid Query 3: ".mysql_error());
					
				while($project_thumb = mysql_fetch_assoc($result_fetch_project_thumb)){
						
					$p_thumb = $project_thumb;
					$project_thumbRow = $p_thumb;
						
				}
					
				$project_mediaRow[$inner_counter]['storage_path'] = $project_thumbRow['storage_path'];
					
				unset($project_thumbRow);
					
				$inner_counter++;
					
			}
			
			// Need to optimize
			// Removed the slash first so we can implement unserializiation
			// unserialize the code			
			if( $project_mediaRow[ 0 ][ 'code' ] !== null ) {
				$arrCode = explode( '"', $project_mediaRow[ 0 ][ 'code' ] );
				if( count( $arrCode ) == 1 ) {
					$project_mediaRow[ 0 ][ 'codes' ][ 'type' ] = 'direct';
					$project_mediaRow[ 0 ][ 'codes' ][ 'code' ] = $project_mediaRow[ 0 ][ 'code' ];						
				}
				else {
					$project_mediaRow[ 0 ][ 'codes' ][ $arrCode[ 1 ] ] = $arrCode[ 3 ];
					$project_mediaRow[ 0 ][ 'codes' ][ $arrCode[ 5 ] ] = $arrCode[ 7 ];
				}
			}
			else {
				$project_mediaRow[ 0 ][ 'code' ] = 'null';
				$project_mediaRow[ 0 ][ 'codes' ] = new stdClass();
			}
			
			if( is_null( $project_mediaRow[ 0 ][ 'storage_path' ] ) ) {
				$project_mediaRow[ 0 ][ 'storage_path' ] = 'null';
			}			
		
			$user_project_row[$counter]['Media'] = $project_mediaRow;
		
			unset($project_mediaRow);
		
			$counter++;
		
		}
		
		// User info
		foreach( $user_project_row as $key => $value) {

			$usersTable = Engine_Api::_ ()->getDbTable ( 'users', 'user' );
			$usersTableName = $usersTable->info ( 'name' );
			$storageTable = Engine_Api::_()->getDbTable('files', 'storage');
			$storageTableName = $storageTable->info('name');

			$select_users = $usersTable->select ();
			$select_users->from ( $usersTableName )->where ( "`user_id` = ?", $value[ 'user_id' ] );

			$usersRows = $usersTable->fetchAll ( $select_users );
			for($c = 0; $c < count ( $usersRows ); $c++) {
				$friendsList ['user_id']     = $usersRows [$c]->user_id;
				$friendsList ['displayname'] = $usersRows [$c]->displayname;
				$friendsList ['username']    = $usersRows [$c]->username;
				$friendsList ['photo_id']    = $usersRows [$c]->photo_id;
				$friendsList ['status']      = $usersRows [$c]->status;
				$friendsList ['status_date'] = $usersRows [$c]->status_date;
				$friendsList ['email']       = $usersRows [$c]->email;
				$friendsList ['locale']      = $usersRows [$c]->locale;
				$friendsList ['language']    = $usersRows [$c]->language;
					
				$table = Engine_Api::_ ()->getDbTable ( 'files', 'storage' );
				$select = $table->select ();
				$select->where ( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $usersRows [$c]->photo_id, $usersRows [$c]->user_id );
				$fetchData = $table->fetchRow ( $select );
				$friendsList ['storage_path'] = $fetchData->storage_path;
			}

			$user_project_row[ $key ][ 'User' ] = $friendsList;

			//Get Like count
			$objLikes = Engine_Api::_()->getApi( 'like', 'api' );
			$objLikesResultSet = $objLikes->fetchLikes( $user_project_row[ $key ][ 'user_id' ], $user_project_row[ $key ][ 'project_id' ] );
			$user_project_row[ $key ][ 'like_count'] = count( $objLikesResultSet );

			$likeStmt = "SELECT * FROM engine4_core_likes WHERE like_id = '". $objLikesResultSet[ 0 ][ 'like_id' ] ."' AND poster_id = '". $user_id ."'";				
			$likeResult= mysql_query ( $likeStmt ) or die ( "Invalid Query 5: " . mysql_error () );
			$user_project_row[ $key ][ 'is_liked' ] = mysql_num_rows( $likeResult );
				

		}
		
		$this->_helper->json ( array ('Posts' => $user_project_row) );
		
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
