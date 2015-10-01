<?php
class Api_ActivityFeedController extends Zend_Rest_Controller {
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
		$this->_helper>json ( array (
				'action' => 'index' 
		) );
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
		
		$boxId = $this->_getParam( 'circle_id', null );
		if( is_null( $boxId ) || empty( $boxId ) ) {
			$arrResultSet = $projectFeed->fetchFeed( $user, 'activity', $this->_getParam( 'offset', null ) );		
		} else {
			$arrResultSet = $projectFeed->fetchFeedByBox( $user, $boxId, $this->_getParam( 'offset', null ) );				
		}
		
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
	}
	
	private function fetchActivityFeed($user) {
		
		$circle_id = $this->_getParam ( 'circle_id', null );
		$user_id = $user->getIdentity();
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
		
		$includeUser = false;
		
		if ($circle_id != "" && $circle_id > 0) {
			
			$statement_fetch_circle_members_id = "SELECT user_id FROM yambai.engine4_whmedia_circleitems
			WHERE circle_id = $circle_id";
			
		} else {
			
			$statement_fetch_circle_members_id = "SELECT user_id FROM yambai.engine4_whmedia_follow
			WHERE follower_id = $user_id ";
			$membersList = $user_id;
			
			$includeUser = true;
			
		}
		
		$result_fetch_circle_members = mysql_query ( $statement_fetch_circle_members_id ) or die ( "Invalid Query: " . mysql_error () );
		
		while ( $members_list = mysql_fetch_assoc ( $result_fetch_circle_members ) ) {
			
			$m_list = $members_list;
			$membersRow [] = $m_list;
		}

		for($i = 0; $i < count ( $membersRow ); $i ++) {
			
			if ($i + 1 == count ( $membersRow )) {
				if($includeUser){
					$membersList = $membersList . $membersRow [$i] ['user_id'].",".$user_id;
				}else{
					$membersList = $membersList . $membersRow [$i] ['user_id'];
				}
			} else {
				$membersList = $membersList . $membersRow [$i] ['user_id'] . ",";
			}
		}
		
		if ($membersList != "") {

			$statement_fetch_activity_feed = "SELECT `t`.* FROM ( 
				SELECT DISTINCT followers.* FROM ( 
					SELECT `project_id`, `creation_date` FROM ( 
						SELECT * FROM `engine4_whmedia_stream` WHERE user_id = ". $user_id ." OR user_id IN ( 
							SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = ". $user_id ."
						) ORDER BY creation_date DESC
					) AS tmp_stream GROUP BY tmp_stream.`project_id`
					UNION
					SELECT `tm`.`resource_id` as `project_id`, `fh`.`creation_date` as `creation_date` FROM engine4_whmedia_followhashtag AS fh
					JOIN engine4_core_tags AS t ON fh.hashtag_id=t.tag_id
					JOIN engine4_core_tagmaps AS tm ON tm.tag_id=t.tag_id 
					WHERE follower_id=". $user_id ." ORDER BY creation_date DESC
					LIMIT 10 OFFSET ". $offset ."0
				) AS followers
			)
			AS tmp_stream_projects, `engine4_whmedia_projects`
			AS `t` WHERE (t.`project_id` = `tmp_stream_projects`.`project_id`)";
			
		/*	
			$objStream = Engine_Api::_()->getDbTable( 'stream', 'whmedia' );
			$test = $objStream->selectStreamProjects( $user );
				return $this->_helper->json ( array (
						'Activity_Feed' => $test->query()->fetchAll()
				) );
		*/	
		
			$result_fetch_activity_feed = mysql_query ( $statement_fetch_activity_feed ) or die ( "Invalid Query: " . mysql_error () );
			
			$counter = 0;

			while ( $activity_feed_list = mysql_fetch_assoc ( $result_fetch_activity_feed ) ) {
				$testing[] = $activity_feed_list;
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
					
					// Fetch IMAGE
					
					$statement_fetch_project_thumb = "SELECT sf.storage_path
			FROM engine4_storage_files sf
			WHERE sf.parent_id = " . $af_list ['cover_file_id'] . " AND mime_major=\"image\"";
					
					$result_fetch_project_thumb = mysql_query ( $statement_fetch_project_thumb ) or die ( "Invalid Query 3: " . mysql_error () );
					
					while ( $project_thumb = mysql_fetch_assoc ( $result_fetch_project_thumb ) ) {
						
						$p_thumb = $project_thumb;
						$project_thumbRow = $p_thumb;
					}
					
					// Fetch VIDEO
					
					$statement_fetch_project_vid = "SELECT sf.storage_path
			FROM engine4_storage_files sf
			WHERE sf.parent_id = " . $af_list ['cover_file_id'] . " AND mime_major=\"video\"";
						
					$result_fetch_project_vid = mysql_query ( $statement_fetch_project_vid ) or die ( "Invalid Query 4: " . mysql_error () );
						
					while ( $project_vid = mysql_fetch_assoc ( $result_fetch_project_vid ) ) {
					
						$p_vid = $project_vid;
						$project_vidRow = $p_vid;
					}
					
					$project_mediaRow [$inner_counter] ['storage_path'] = $project_thumbRow ['storage_path'];
					if($project_mediaRow[$inner_counter]['code'] === null){
						$project_mediaRow [$inner_counter] ['code'] = $project_vidRow ['storage_path'];
					}
					unset ( $project_thumbRow );
					
					$inner_counter ++;
				}

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
				$activity_feedListRow [$counter] ['Media'] = $project_mediaRow;

				unset ( $project_mediaRow );
				
				$counter ++;
			}

			if( $activity_feedListRow == null ) {
				$activity_feedListRow = array( "data" => "null" );
				
				$this->_helper->json ( array (
						'Activity_Feed' => $activity_feedListRow
				) );
			}

			// User info
			foreach( $activity_feedListRow as $key => $value) {

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

				$activity_feedListRow[ $key ][ 'User' ] = $friendsList;

				//Get Like count
				$objLikes = Engine_Api::_()->getApi( 'like', 'api' );
				$objLikesResultSet = $objLikes->fetchLikes( $activity_feedListRow[ $key ][ 'user_id' ], $activity_feedListRow[ $key ][ 'project_id' ] );

				if( $objLikesResultSet == 'null' ) {
					$activity_feedListRow[ $key ][ 'like_count'] = '0';
				}
				else {
					$activity_feedListRow[ $key ][ 'like_count'] = ( string )count( $objLikesResultSet );
				}
				
				$likeStmt = "SELECT * FROM engine4_core_likes WHERE like_id = '". $objLikesResultSet[ 0 ][ 'like_id' ] ."' AND poster_id = '". $user_id ."'";				
				$likeResult= mysql_query ( $likeStmt ) or die ( "Invalid Query 5: " . mysql_error () );
				$activity_feedListRow[ $key ][ 'is_liked' ] = mysql_num_rows( $likeResult );
				

			}

			$this->_helper->json ( array (
					'Activity_Feed' => $activity_feedListRow
			) );	
		
		}
	}
	
    public function niceNumber($n) {
        // first strip any formatting;
        $n = (0+str_replace(",","",$n));

        // is this a number?
        if(!is_numeric($n)) return false;

        // now filter it;
        if($n>1000000000000) return round(($n/1000000000000),2).' t';
        else if($n>1000000000) return round(($n/1000000000),2).' b';
        else if($n>1000000) return round(($n/1000000),2).' m';
        else if($n>1000) return round(($n/1000),2).' k';

        return number_format($n);
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
		$this->_helper->json ( array (
				'error' => 'Invalid token',
				'message' => 'Provided is invalid.'
		) );
	}
	public function expiredAction($viewer) {
		$this->getResponse ()->setRawHeader ( $_SERVER ['SERVER_PROTOCOL'] . ' 401 Unauthorized' );
		$this->_helper->json ( array (
				'error' => 'Token expired',
				'message' => 'Provided token has been expired.'
		) );
	}
}
