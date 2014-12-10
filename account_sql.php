<?php
  
	require_once ("include/db_connect.php");
	require_once ("include/MCrypt.php");
	require_once ("smtp/Send_Mail.php");
	require_once ("include/functions.php"); 
    global $db;  
	
	$mcrypt = new MCrypt();
	
	$response = "";
	
	if (isset($_POST['tag']) && $_POST['tag'] != '') {
    	$tag = isset($_POST['tag']) ? $_POST['tag'] : ''; 
		
		switch ($tag)
		{ 
			case "USER_REGISTER":		
            				
				$name		= isset($_POST['name']) ? $_POST['name'] : ''; 
				$nick_name  = isset($_POST['nick_name']) ? $_POST['nick_name'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : ''; 
				$gender	    = isset($_POST['gender']) ? $_POST['gender'] : ''; 
				$bday       = isset($_POST['bday']) ? $_POST['bday'] : ''; 
				$about      = isset($_POST['about']) ? $_POST['about'] : ''; 
				$language	= isset($_POST['language']) ? $_POST['language'] : ''; 
				$country	= isset($_POST['country']) ? $_POST['country'] : ''; 
				$area_code	= isset($_POST['area_code']) ? $_POST['area_code'] : ''; 
				$mobile	    = isset($_POST['mobile']) ? $_POST['mobile'] : ''; 
                $gcm_id     = isset($_POST['regId']) ? $_POST['regId'] : '';
                $img	  	= isset($_POST['img']) ? $_POST['img'] : '0';  
				$imei	  	= isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip  	    = getClientIp();
                
                $name       = $db->escape_value($name); 
				$nick_name  = $db->escape_value($nick_name);
				$about      = $db->escape_value($about);
				
				$task	    = $name."~".$nick_name."~".$email."~".$gender."~".$bday."~".$about."~".$img."~".
							  $language."~".$country."~".$area_code."~".$mobile."~".$gcm_id."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
								VALUES ('$email','$tag','$task','$imei','$ip')"; 	
				$result_log = $db->query($sql_log);
				
				$result = $db->query("SELECT * FROM user_account WHERE LOWER(email)=LOWER('$email')");	
				if ($db->num_rows($result) >= 1) {
					$response = "0~;This email already registered.";
				} else {				
                    $sql    = "INSERT INTO user_account (name, nick_name, email, gender, bday, about, 
                                        language, country, are_code, mobile, gcm_id) 
					           VALUES ('$name', '$nick_name', '$email', '$gender', '$bday', '$about', 
                                        '$language', '$country', '$area_code', '$mobile', '$gcm_id') ";
					$result = $db->query($sql);			
					 
                    if ($result) {
                        $id = $db->insert_id();
                        
                        $sql_insert   = "INSERT INTO follower (user_id, follower_id, stat) VALUES ('$id', '$id', '1') ";                 
                        $result_insert = $db->query($sql_insert);
                                                
                    	$filename = "";
                        if ($img=='1'){
                            $temp = explode('.',$_FILES['profile']['name']);
                            $filename = 'PROF_'.$id.'.'.$temp[1];
                            $path = "images/" . $filename;
                            $upload = move_uploaded_file($_FILES['profile']['tmp_name'], $path);
                            if($upload) {
                                $result = $db->query("UPDATE user_account SET profile='$filename' WHERE id='$id'");
                                $response = "1~;Account registration successful!"; 
                            } else {
                                $response = "2~;Account registration successful,\n but image failed to upload!"; 
                            }
                        } else {
                            $response = "1~;Account registration successful!"; 
                        }
				    } else {
                        $response = "0~;Registration not successfull, Please try again!"; 
                    }  
                } 	
				echo $response;
			break;
            case "USER_UPDATE":		
            				
				$user_id	= isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$name		= isset($_POST['name']) ? $_POST['name'] : ''; 
				$nick_name  = isset($_POST['nick_name']) ? $_POST['nick_name'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : ''; 
				$gender	    = isset($_POST['gender']) ? $_POST['gender'] : ''; 
				$bday       = isset($_POST['bday']) ? $_POST['bday'] : ''; 
				$about      = isset($_POST['about']) ? $_POST['about'] : ''; 
                $gcm_id     = isset($_POST['regId']) ? $_POST['regId'] : '';
                $img	  	= isset($_POST['img']) ? $_POST['img'] : '0';  
				$imei	  	= isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip  	    = getClientIp();
                
                $name       = $db->escape_value($name); 
				$nick_name  = $db->escape_value($nick_name);
				$about      = $db->escape_value($about);
								
				$task	    = $name."~".$nick_name."~".$email."~".$gender."~".$bday."~".$about."~".$img."~".
							  $imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
								VALUES ('$email','$tag','$task','$imei','$ip')"; 	
				$result_log = $db->query($sql_log);
				
				$result = $db->query("SELECT * FROM user_account WHERE id='$user_id' ");	
				if ($db->num_rows($result) == 1) {
									
                    $sql    = "UPDATE user_account SET name='$name', nick_name='$nick_name', email='$email', gender='$gender', bday='$bday', about='$about', gcm_id='$gcm_id'
                               WHERE id='$user_id' ";
					$result = $db->query($sql);			
					 
                    if ($result) {
                        $id = $user_id;                                                
                    	$filename = "";
                        if ($img=='1'){
                            $temp = explode('.',$_FILES['profile']['name']);
                            $filename = 'PROF_'.$id.'.'.$temp[1];
                            $path = "images/" . $filename;
                            $upload = move_uploaded_file($_FILES['profile']['tmp_name'], $path);
                            if($upload) {
                                $result = $db->query("UPDATE user_account SET profile='$filename' WHERE id='$id'");
                                $response = "1~;Account updated successfully!"; 
                            } else {
                                $response = "2~;Account updated successfully,\n but image failed to upload!"; 
                            }
                        } else {
                            $response = "1~;Account updated successfully!"; 
                        }
				    } else {
                        $response = "0~;Registration not successfull, Please try again!"; 
                    }  
                } else {
                    $response = "0~;This user is not existed.";
				} 
				echo $response;
			break;
            case "SHOP_LOGIN":		
        				
				$email  = isset($_POST['email']) ? $_POST['email'] : ''; 
				$pass   = isset($_POST['pass']) ? $_POST['pass'] : ''; 
				$imei   = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip     = getClientIp();
                
				$task   = $email."~".$pass."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
								VALUES ('$email','$tag','$task','$imei','$ip')"; 	
				$result_log = $db->query($sql_log);
				
				$result = $db->query("SELECT * FROM shop_account WHERE LOWER(email)=LOWER('$email') AND pass = '$pass' ");	

                if ($db->num_rows($result)>=1) {
                    $response = "1~;Login successful!"; 
                } else {
                    $response = "0~;Login not successfull, Please try again!";  
                } 	
				echo $response;
			break;
			default:
            	$email    = isset($_POST['email']) ? $_POST['email'] : '';
				$imei	  = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip		  = getClientIp();	
				$task	  = "Invalid Transaction.";
			
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
								VALUES ('$email','$tag','$task','$imei','$ip')"; 	
				$result_log = $db->query($sql_log);
				echo "0~;Invalid Transaction."; 
		} 
			
	} else {
        $email    = isset($_POST['email']) ? $_POST['email'] : '';
		$imei	  = isset($_POST['imei']) ? $_POST['imei'] : ''; 
		$ip		  = getClientIp();	
		$task	  = "Access Denied.";
	
		//Record Task
		$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
								VALUES ('$email','$tag','$task','$imei','$ip')"; 	
		$result_log = $db->query($sql_log);
		echo "0~;Access Denied";
	}
?>	