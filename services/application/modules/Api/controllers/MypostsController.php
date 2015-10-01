<?php
class Api_MyPostsController extends Zend_Rest_Controller {
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
		$arrResultSet        = $projectFeed->fetchFeed( $user, 'user', $this->_getParam( 'offset', null ), $user->getIdentity() );
		
		$this->getHelper( 'json' )->sendJson( $arrResultSet );		
		
		//$this->fetchMyFeed($user);
		
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
	
	private function fetchMyFeed($user){
		
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
		
		$user_id = $user->getIdentity();
		$offset = $this->_getParam('offset');
		
		/*$statement_fetch_user_projects = "SELECT whmp.*, sf.storage_path
		 FROM wazzup.engine4_whmedia_projects whmp
		LEFT JOIN engine4_storage_files sf
		ON whmp.user_id = sf.user_id
		WHERE whmp.user_id = 1
		AND sf.parent_id = whmp.cover_file_id
		ORDER BY whmp.creation_date DESC
		LIMIT 10 OFFSET ".$offset."0";*/
		
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
		
			$user_project_row[$counter]['Media'] = $project_mediaRow;
		
			unset($project_mediaRow);
		
			$counter++;
		
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
