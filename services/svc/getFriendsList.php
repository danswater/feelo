<?php

	include('conf/database.php');

	$user_id = $_GET['user_id'];
	
	$statement_fetch_circle_data_with_id = "SELECT * FROM wazzup.engine4_whmedia_follow where follower_id = '$user_id'";
	
	$result = mysql_query($statement_fetch_circle_data_with_id) or die("Invalid Query: ".mysql_error());
	
	while($circle_data=mysql_fetch_assoc($result))
	{
		$c_data=$circle_data;
		$circle_dataRow[] = $c_data;
	}
	
	for($i = 0; $i < sizeof($circle_dataRow); $i++){
	
		$statement_fetch_user_data = "SELECT 
				u.user_id, 
				u.displayname, 
				u.username, 
				u.photo_id, 
				u.status, 
				u.status_date, 
				u.email, 
				u.locale, 
				u.language, 
				sf.storage_path 
				FROM engine4_users u 
				LEFT JOIN engine4_storage_files sf 
				ON (u.user_id = sf.user_id 
				AND sf.type = 'thumb.profile') 
				AND sf.parent_file_id = u.photo_id
				WHERE u.user_id = ".$circle_dataRow[$i]['user_id'];
						
		$result = mysql_query($statement_fetch_user_data) or die("Invalid Query: ".mysql_error());
	
		while($friends_list=mysql_fetch_assoc($result))
		{
			$f_list=$friends_list;
			$friendsRow[] = $f_list;
			
		}
	}
	$json = json_encode(array('FriendsList' => $friendsRow));
	
	echo $json;
	
?>

