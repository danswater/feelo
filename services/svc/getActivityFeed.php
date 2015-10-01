<?php

	include('conf/database.php');
	
	$circle_id = $_GET['circle_id'];
	$user_id = $_GET['user_id'];
	$offset = $_GET['offset'];
	
	if($circle_id!=""){
		
		$statement_fetch_circle_members_id = "SELECT user_id FROM wazzup.engine4_whmedia_circleitems 
											  WHERE circle_id = $circle_id";
		
	} else {
	
		$statement_fetch_circle_members_id = "SELECT user_id FROM wazzup.engine4_whmedia_follow 
											  WHERE follower_id = $user_id ";
		$membersList = $user_id.",";		  
	}
										  
	$result_fetch_circle_members = mysql_query($statement_fetch_circle_members_id) or die("Invalid Query: ".mysql_error());
	
	while($members_list=mysql_fetch_assoc($result_fetch_circle_members)){
		
		$m_list=$members_list;
		$membersRow[] = $m_list;
		
	}
	
	
	
	for($i = 0; $i < count($membersRow); $i++){
		
		if($i+1 == count($membersRow)){
			$membersList = $membersList.$membersRow[$i]['user_id'];
		}else {
			$membersList = $membersList.$membersRow[$i]['user_id'].",";
		}
	}
	
	if($membersList != ""){
	
		$statement_fetch_activity_feed = "SELECT DISTINCT whmp.* FROM wazzup.engine4_whmedia_stream whms 
										  LEFT JOIN engine4_whmedia_projects whmp 
										  ON whms.project_id = whmp.project_id 
										  WHERE whmp.user_id IN ($membersList) AND whmp.is_published = 1 
										  ORDER BY whms.creation_date DESC 
										  LIMIT 10 OFFSET ".$offset."0";
									  
		$result_fetch_activity_feed = mysql_query($statement_fetch_activity_feed) or die("Invalid Query: ".mysql_error());
	
		$counter = 0;
	
		while($activity_feed_list=mysql_fetch_assoc($result_fetch_activity_feed)){
	
			$af_list=$activity_feed_list;
			$activity_feedListRow[] = $af_list;
		
			$statement_fetch_project_media = "SELECT media_id, title, project_id, code 
										  FROM engine4_whmedia_medias whmm 
										  WHERE whmm.project_id = ".$af_list['project_id'];
									  
			$result_fetch_media = mysql_query($statement_fetch_project_media) or die("Invalid Query 2: ".mysql_error());
	
			$inner_counter = 0;
	
			while($project_media = mysql_fetch_assoc($result_fetch_media)){
	
				$p_media = $project_media;
				$project_mediaRow[] = $p_media;
		
				if($af_list['cover_file_id']==""){
					$af_list['cover_file_id'] = "null";
				}
			
				$statement_fetch_project_thumb = "SELECT sf.storage_path 
												  FROM engine4_storage_files sf 
												  WHERE sf.parent_id = ".$af_list['cover_file_id'];
										  
				$result_fetch_project_thumb = mysql_query($statement_fetch_project_thumb) or die("Invalid Query 3: ".mysql_error());
			
				while($project_thumb = mysql_fetch_assoc($result_fetch_project_thumb)){
			
					$p_thumb = $project_thumb;
					$project_thumbRow = $p_thumb;
			
				}
			
				$project_mediaRow[$inner_counter]['storage_path'] = $project_thumbRow['storage_path'];
			
				unset($project_thumbRow);
			
				$inner_counter++;
			
			}
			
			// Fetch Project Comments
			$statement_fetch_project_comments = "SELECT comment_id, 
														parent_id, 
														resource_id as project_id, 
														poster_id as user_id, 
														body, 
														creation_date, 
														deleted  
												 FROM wazzup.engine4_whcomments_comments  
												 WHERE resource_id = ".$af_list['project_id'];
		
			$result_fetch_project_comments = mysql_query($statement_fetch_project_comments) or die("Invalid Query 2: ".mysql_error());
		
			$comments_counter = 0;
			while($project_comments = mysql_fetch_assoc($result_fetch_project_comments)){
			
				$p_comments = $project_comments;
				$p_comments['body'] = str_replace("&quot;", "\"", $p_comments['body']);
				$project_commentsRow[] = $p_comments;
				
				$result_fetch_user_commenter_photo = "SELECT 
													  sf.storage_path 
													  FROM engine4_users u 
													  LEFT JOIN engine4_storage_files sf 
													  ON (u.user_id = sf.user_id 
													  AND sf.type = 'thumb.profile') 
													  AND sf.parent_file_id = u.photo_id 
													  WHERE u.user_id = ".$p_comments['user_id'];
													
				$result_fetch_user_commenter_photo = mysql_query($result_fetch_user_commenter_photo) or die("Invalid Query COmment: ".mysql_error());
			
				while($comment_photo = mysql_fetch_assoc($result_fetch_user_commenter_photo)){
				
					$c_photo = $comment_photo;
				
				}
				
				$project_commentsRow[$comments_counter]['storage_path'] = $c_photo['storage_path'];
				
				unset($comment_photoRow);
				
				$comments_counter++;
				
			}
			
			$activity_feedListRow[$counter]['Media'] = $project_mediaRow;
			$activity_feedListRow[$counter]['Comments'] = $project_commentsRow;
			
			unset($project_commentsRow);
			unset($project_mediaRow);
		
			$counter++;
		
		}
		
	} 
	
	echo $json = json_encode(array('Activity_Feed' => $activity_feedListRow));;						  
	
?>