<?php

class Api2_Api_Comment extends Core_Api_Abstract {

	public function fetchComment( $user, $params ){
		$page = (int) !isset( $params[ "page" ] ) ? 1 : $params[ "page" ];
        $page++;
        $comment_limit = (int) !isset( $params[ "limit" ] ) ? 10 : $params[ "limit" ];

        $page_render = ($comment_limit * $page);
		// get the project id
		$subject = Engine_Api::_()->getItem('whmedia_project', $params[ "project_id" ]);

		if( $subject == null ){
			return array(); // return nothing 
		}

        $commentsTable = Engine_Api::_()->getDbTable('comments', 'whcomments');
        $commentSelect = $commentsTable->getCommentSelect($subject);
        $commentSelect->order('lt DESC');
        $commentSelect->where("node.deleted = ?", "0");
        $commentSelect->where("node.parent_id IS NULL");
        $tree = $commentsTable->fetchTree($subject, null, $commentSelect);

		$paginator = Zend_Paginator::factory($tree);       
		$total_comments = $paginator->getTotalItemCount();
		$paginator->setItemCountPerPage($page_render);
        $paginator->setCurrentPageNumber(1);

        $num_pager = floor( ( $total_comments / $comment_limit ) );

        $countSelect = $commentsTable->select();
        $countSelect->from( $commentsTable, array( 'count(*) as result_count' ) );
		$countSelect->where( 'deleted = 0' );
		$countSelect->where( 'resource_id = ?', $params[ 'project_id' ] );
		$countSelect->order( array( 'creation_date DESC' ) );
		$result_count = $commentsTable->fetchAll( $countSelect );

		if ( count( $result_count ) < 1 ) {
			$resultCount = 0;
		} else {
			$resultCount = $result_count[ 0 ]->result_count;
		}

        $treeComment = array();
        $pageLimit = $paginator->getTotalItemCount() > $page_render ? $page_render : $paginator->getTotalItemCount() ; 
        foreach($paginator as $paging){
            $treeComment[ ( $pageLimit - 1) ] = $paging->toArray();
            $pageLimit--;
        }

        $array_chunk = array_chunk( $treeComment, $comment_limit );
        $comment_current_page = $array_chunk[ ($page - 1) ];
        $api2StoragePath = Engine_Api::_()->getApi( 'storage', 'api2' );


        $reverSortArray = array();
        for( $i = count( $comment_current_page ); $i--; ){
        	/** get the user storage path and the displayname **/
			$user = Engine_Api::_()->user()->getUser( $comment_current_page[ $i ][ "poster_id" ] );

			$storagePath = $api2StoragePath->fetchStorageByPhotoIdAndUserId( $user->photo_id, $user->user_id );

        	$reverSortArray[] = array(
        		'comment_id'    => $comment_current_page[ $i ][ 'comment_id' ],
				'parent_id'     => $comment_current_page[ $i ][ 'parent_id' ],
				'project_id'    => $params[ 'project_id' ],
				'user_id'       => $user->getIdentity(),
				'body'          => $comment_current_page[ $i ][ 'body' ],
				'creation_date' => $comment_current_page[ $i ][ 'creation_date' ],
				'time_diff' 	=> $this->dateDiff( date("Y-m-d H:i:s"), $comment_current_page[ $i ][ 'creation_date' ] ),
				'deleted'       => $comment_current_page[ $i ][ 'deleted' ],
				'User' 			=> array( 
									'storage_path'  => $storagePath,
									'displayname' => $user->displayname
								)
        	);
        }

       	return array( "comment_count" => $resultCount, "total_pager" => ( $num_pager  ),  "data" => $reverSortArray );

	} 

	public function create( $viewer, $params ){
	
		$subject = Engine_Api::_()->getItem('whmedia_project', $params[ "project_id" ]);

		// Process
		// Filter HTML
		$filter = new Zend_Filter();
		$filter->addFilter(new Engine_Filter_Censor());
		$filter->addFilter(new Engine_Filter_HtmlSpecialChars());
		
		$body = $params[ 'body' ];
		$body = $filter->filter($body);
		
		switch ($body) {
			case 'happy':
				$body = '<img src="http://yamba.rocks/services/application/modules/Whcomments/externals/images/happy.png" alt="Happy" /> HAPPY';
				break;
			case 'nice':
				$body = '<img src="http://yamba.rocks/services/application/modules/Whcomments/externals/images/nice.png" alt="Nice" /> NICE';
				break;
			case 'omg':
				$body = '<img src="http://yamba.rocks/services/application/modules/Whcomments/externals/images/omg.png" alt="Omg" /> OMG';
				break;
			case 'sad':
				$body = '<img src="http://yamba.rocks/services/application/modules/Whcomments/externals/images/sad.png" alt="Sad" /> SAD';
				break;
			default:
				preg_match_all('/@[^\s]+/', $body, $userTags);
		
				if (isset($userTags[0]) && count($userTags[0]) > 0) {
					foreach ($userTags[0] as $userTag) {
						$user = Engine_Api::_()->user()->getUser(mb_substr($userTag, 1));
						if ($user->getIdentity()) {
							//$href = $this->view->htmlLink($user->getHref(), $userTag);
							$href = '<a href="/main/profile/' . $user->username . '">' . $userTag . '</a>';
							$body = str_replace($userTag, $href, $body);
							Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $subject, "tagged", array( 'url1' => $subject->getHref(), ));
						}
					}
				}
				break;
		}
		
		$parent_id = (int) isset( $params[ 'parent_id' ] ) ? $params[ 'parent_id' ] : 0 ;
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
			die( 'hello world' );
		}
		
		$comment = Engine_Api::_()->getDbTable( 'comments', 'whcomments' );
		$row = $comment->fetchRow( $comment->select()->order( array( 'comment_id DESC' ) )->limit( 1 ) );
		
		$storage = Engine_Api::_()->getDbTable( 'files', 'storage' );
		$select = $storage->select ();
		$select->where ( 'user_id = ? and type = "thumb.profile" and parent_file_id = ' . $viewer->photo_id, $viewer->getIdentity() );
		$rowStorage = $storage->fetchRow ( $select );

        $countSelect = $comment->select();
        $countSelect->from( $comment, array( 'count(*) as result_count' ) );
		$countSelect->where( 'deleted = 0' );
		$countSelect->where( 'resource_id = ?', $params[ 'project_id' ] );
		$countSelect->order( array( 'creation_date DESC' ) );
		$result_count = $comment->fetchAll( $countSelect );

		if ( count( $result_count ) < 1 ) {
			$resultCount = 0;
		} else {
			$resultCount = $result_count[ 0 ]->result_count;
		}

		$response = array(
			'comment_id'    => $row->comment_id,
			'parent_id'     => $row->parent_id,
			'project_id'    => $params[ 'project_id' ],
			'user_id'       => $viewer->getIdentity(),
			'subject_type'	=> $subjectOwner->getType(),
			'subject_user_id' =>  $subjectOwner->getIdentity(),
			'body'          => $row->body,
			'creation_date' => $row->creation_date,
			'time_diff' 	=> $this->dateDiff( date("Y-m-d H:i:s"), $row->creation_date ),
			'deleted'       => $row->deleted,
			'User' 			=> array( 
									'storage_path'  => $rowStorage->storage_path,
									'displayname' => $viewer->displayname
								),
			'comment_count' => $resultCount
		);

		return $response;						
		
	}

	public function fetchByFeedId ( $user, $params ) {
		$dbTableComment = Engine_Api::_()->getDbTable( 'comments', 'whcomments' );
		$apiComment     = Engine_Api::_()->getApi( 'comment', 'api' );
		$apiUser        = Engine_Api::_()->getApi( 'user', 'api' );

		$select = $dbTableComment->select();
		$select->from( $dbTableComment );
		$select->where( 'deleted = 0' );
		$select->where( 'resource_id = ?', $params[ 'project_id' ] );
		$select->order( array( 'creation_date DESC' ) );


		if ( isset( $params[ 'offset' ] ) ) {
			$rows = 10;
			$suffix = $rows * ( int )$params[ 'offset' ];
			$select->limit( $rows, $suffix );
		}

		$commentRows = $dbTableComment->fetchAll( $select );

		if ( count( $commentRows ) < 1 ) {
			throw new Exception( 'No results found' );
		}

		$ret = array();
		foreach( $commentRows as $row ) {
			$body = str_replace( '&qout;', '\'', $row->body );
			$bodyWithTags = $apiComment->findTagsInComment( $body );

			$user = $apiUser->fetchUserDetails( $row->poster_id );

			$commentObj                    = array();
			$commentObj[ 'comment_id' ]    = $row->comment_id;
			$commentObj[ 'parent_id' ]     = $row->parent_id ? $row->parent_id : 0;
			$commentObj[ 'project_id' ]    = $row->resource_id;
			$commentObj[ 'user_id' ]       = $row->poster_id;
			$commentObj[ 'body' ]          = $bodyWithTags[ 'body' ];
			$commentObj[ 'tag_userids' ]   = $bodyWithTags[ 'tag_userids' ];
			$commentObj[ 'creation_date' ] = $row->creation_date;
			$commentObj[ 'deleted' ]       = $row->deleted;
			$commentObj[ 'storage_path' ]  = $user[ 'storage_path' ];
			$commentObj[ 'displayname' ]   = $user[ 'displayname' ];

			$ret[] = $commentObj;
		}

		return array(
			'Comments' => $ret
		);

	}


	public function dateDiff($time1, $time2, $precision = 6) {
        if (!is_int($time1)) {
          $time1 = strtotime($time1);
        }
        if (!is_int($time2)) {
          $time2 = strtotime($time2);
        }
     
        if ($time1 > $time2) {
          $ttime = $time1;
          $time1 = $time2;
          $time2 = $ttime;
        }
     
        $intervals = array('year','month','day','hour','minute','second');
        $diffs = array();
     
        foreach ($intervals as $interval) {
          $diffs[$interval] = 0;
          $ttime = strtotime("+1 " . $interval, $time1);
          while ($time2 >= $ttime) {
        $time1 = $ttime;
        $diffs[$interval]++;
        $ttime = strtotime("+1 " . $interval, $time1);
          }
        }
     
        $count = 0;
        $times = array();
        foreach ($diffs as $interval => $value) {
          if ($count >= $precision) {
        break;
          }
          if ($value > 0) {
        if ($value != 1) {
          $interval .= "s";
        }
        $times[ $interval ] = $value;
        $count++;
          }
        }
     
        return $times;
    }

}