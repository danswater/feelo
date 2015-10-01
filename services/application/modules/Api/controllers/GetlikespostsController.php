<?php
class Api_GetlikespostsController extends Zend_Rest_Controller {
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
		$params = $this->getRequest()->getPost();
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
		
		$id = $auth->user_id;
		if ( isset( $params[ 'user_id' ] ) ) {
			$id = $params[ 'user_id' ];
		}
		$user = Engine_Api::_ ()->user ()->getUser ( $id );
		
		try{
			$projectFeed = Engine_Api::_()->getApi( 'project', 'api' );
			$arrResultSet        = $projectFeed->fetchFeed( $user, 'likes', $this->_getParam( 'offset', null ) );
		} catch( Exception $e ) {
			$arrResultSet = array(
				'data'  => array(),
				'error' => array( 'No results found' )
			);
		}
		
		$this->getHelper( 'json' )->sendJson( $arrResultSet );
		
		//$this->fetchMyLikes($user);
		
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
	
	private function fetchMyLikes($user) {
	
		$user_id = $user->getIdentity();
	
		$other_user = $this->_getParam('user_id',0);
		
		if($other_user != 0){
			$user_id = $other_user;
		}
		
		$offset = $this->_getParam ( 'offset', null );
		
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
		
		
		
		if (true) {
			
			$resource_type = "whmedia_project";
			
			$statement_fetch_activity_feed = "select p.* from engine4_users u
												left join engine4_core_likes l on l.poster_id = u.user_id
												left join engine4_whmedia_projects p on p.project_id = l.resource_id
												where u.user_id = ".$user_id." and l.resource_type = \"".$resource_type."\"
												order by p.creation_date desc
												LIMIT 10 OFFSET " . $offset . "0";
			
			$result_fetch_activity_feed = mysql_query ( $statement_fetch_activity_feed ) or die ( "Invalid Query: " . mysql_error () );
			
			$counter = 0;
			
			while ( $activity_feed_list = mysql_fetch_assoc ( $result_fetch_activity_feed ) ) {

				$af_list = $activity_feed_list;
				
				$timeStamp = $af_list ['creation_date'];
				
				$af_list ['creation_date'] = $this->time_ago ( $timeStamp );
				
				$activity_feedListRow [] = $af_list;
				
				$statement_fetch_project_media = "SELECT media_id, title, project_id, code
					FROM engine4_whmedia_medias whmm
			WHERE whmm.project_id = " . $af_list ['project_id'];
				
				$result_fetch_media = mysql_query ( $statement_fetch_project_media ) or die ( "Invalid Query 2: " . mysql_error () );
				
				$inner_counter = 0;
				
				while ( $project_media = mysql_fetch_assoc ( $result_fetch_media ) ) {
					
					$p_media = $project_media;
					$project_mediaRow [] = $p_media;
					
					if ($af_list ['cover_file_id'] == "") {
						$af_list ['cover_file_id'] = "null";
					}
					
					$statement_fetch_project_thumb = "SELECT sf.storage_path
			FROM engine4_storage_files sf
			WHERE sf.parent_id = " . $af_list ['cover_file_id'];
					
					$result_fetch_project_thumb = mysql_query ( $statement_fetch_project_thumb ) or die ( "Invalid Query 3: " . mysql_error () );
					
					while ( $project_thumb = mysql_fetch_assoc ( $result_fetch_project_thumb ) ) {
						
						$p_thumb = $project_thumb;
						$project_thumbRow = $p_thumb;
					}
					
					$project_mediaRow [$inner_counter] ['storage_path'] = $project_thumbRow ['storage_path'];
					
					unset ( $project_thumbRow );
					
					$inner_counter ++;
				}
					
				$activity_feedListRow [$counter] ['Media'] = $project_mediaRow;

				unset ( $project_mediaRow );
				
				$counter ++;
			}
			
			$this->_helper->json ( array (
					'my_likes' => $activity_feedListRow
			) );
		}
	}
	
	private function time_ago($date,$granularity=2) {
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
