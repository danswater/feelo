<?php

	include('conf/database.php');

	$email = $_GET['email'];
	$password = $_GET['password'];

	$statement_core_secret = mysql_query("select value from engine4_core_settings where name =\"core.secret\"");
	$core_secret = mysql_fetch_array($statement_core_secret,0);
	
	$statement_fetch_details = "select password, salt from engine4_users ".
				"where engine4_users.email=\"".$email."\"";
				
	$result = mysql_query($statement_fetch_details);
	if (!$result) {
	    echo 'Could not run query: ' . mysql_error();
	} else {
		$data = mysql_fetch_array($result, MYSQL_NUM);

		$user_password = $data[0];
		$user_salt = $data[1];
	}
	
	$enc_password = md5($core_secret[0].$password.$user_salt);
	
	if($user_password === $enc_password){
	
		// User Data -----------------------
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
				WHERE u.email='$email'";
				
		$result = mysql_query($statement_fetch_user_data) or die("Invalid Query: ".mysql_error());
		
		while($user=mysql_fetch_assoc($result))
		{	
			$s=$user;
			$userRow[] = $s;
		}
		
		/* Fetch on first run but idunno 
		// Circles -------------------
		$statement_fetch_circles = "SELECT 
									circle_id, 
									".//user_id, 
									"title 
									FROM engine4_whmedia_circles c WHERE c.user_id = ".$userRow[0]['user_id'];
							
		$result = mysql_query($statement_fetch_circles) or die("Invalid Query: ".mysql_error());
		
		while($circle_list=mysql_fetch_assoc($result))
		{
			$c_list=$circle_list;
			$circleRow[] = $c_list;
		}*/
		
		// Login stamp
		// $statement_login_stamp = "INSERT INTO engine4_user_logins VALUES ()";
		
		$json = json_encode(array(/*'Box' => $circleRow, */'User' => $userRow));
		
	} else{
	
		$message[] = "The credentials you have supplied are invalid.";
		$json = json_encode(array('Message' => $message));  
		
	}
	
	echo $json."<br>".$enc_password."<br>core_secret:".$core_secret[0]."<br>user_pw:".$password."<br>user_salt:".$user_salt;
?>

