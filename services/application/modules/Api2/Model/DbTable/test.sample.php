<?php
		// fetch your user_id that you followed		
		$currentlyFollowed         = "(SELECT `user_id` FROM `engine4_whmedia_follow` WHERE `follower_id` = $userId)";
		
		// fetch your stream project or the stream projects that you followed
		$currentlyFollowedStream   = "( SELECT * FROM `engine4_whmedia_stream` WHERE user_id = $userId OR user_id IN $currentlyFollowed ORDER BY `project_id` DESC)";
		
		$currentlyFollowedUser     = "SELECT `project_id`, `creation_date` FROM $currentlyFollowedStream AS tmp_stream GROUP BY tmp_stream.`project_id`";
		$currentlyFollowedHashtag  = "SELECT `tm`.`resource_id` as `project_id`, `fh`.`creation_date` as `creation_date` FROM engine4_whmedia_followhashtag AS fh JOIN engine4_core_tags AS t ON fh.hashtag_id=t.tag_id JOIN engine4_core_tagmaps AS tm ON tm.tag_id=t.tag_id WHERE follower_id=$userId ORDER BY project_id DESC";
		$currentlyFollowedProjects = "$currentlyFollowedUser UNION $currentlyFollowedHashtag";
		$projects                  = " (SELECT DISTINCT followers.* FROM( $currentlyFollowedProjects ) AS followers ) AS tmp_stream_projects, `engine4_whmedia_projects`";
	
		$select = $this->select()
			->distinct()
			->from(new Zend_Db_Expr( $projects ) )
			->setIntegrityCheck(FALSE)  
			->where(new Zend_Db_Expr('t.`project_id` = `tmp_stream_projects`.`project_id`'))
			->limit( 5, $suffix );

		$projectRows = $this->fetchAll( $select );

		// echo '<pre>';
		// print_r( $projectRows );
		// echo '</pre>';
		// exit;