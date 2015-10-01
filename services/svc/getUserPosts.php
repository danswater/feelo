<?php

	include('conf/database.php');

	$user_id = $_GET['user_id'];
	$offset = $_GET['offset'];
	
	/*$statement_fetch_user_projects = "SELECT whmp.*, sf.storage_path 
								FROM wazzup.engine4_whmedia_projects whmp 
								LEFT JOIN engine4_storage_files sf 
								ON whmp.user_id = sf.user_id 
								WHERE whmp.user_id = 1 
								AND sf.parent_id = whmp.cover_file_id 
								ORDER BY whmp.creation_date DESC 
								LIMIT 10 OFFSET ".$offset."0";*/
								
	$statement_fetch_user_projects="SELECT whmp.*
								FROM wazzup.engine4_whmedia_projects whmp 
								WHERE whmp.user_id = 1 
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
	
	$json = json_encode(array('Posts' => $user_project_row));
	
	echo $json;

?>