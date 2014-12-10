<?php
  
	require_once ("include/db_connect.php");
	require_once ("include/MCrypt.php");
	require_once ("smtp/Send_Mail.php");
	require_once ("include/functions.php");  
	require_once ("include/GCM.php");  
      
    global $db;  	
	$mcrypt = new MCrypt();
    $gcm    = new GCM();
	
	$response = "";
	
	if (isset($_POST['tag']) && $_POST['tag'] != '') {
    	$tag = isset($_POST['tag']) ? $_POST['tag'] : ''; 
        
		switch ($tag)
		{
			case "LOAD_NOTIFICATIONS":	
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				
				$task	  = $email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);

                $sql_user   = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result_user= $db->query($sql_user);
				if ($db->num_rows($result_user)>0) {
				    $row_user = $db->fetch_array($result_user);                  
                            
                    $sql   = "SELECT notifications.*, user_account.nick_name,
                              user_account.email, user_account.profile,
                              TIMESTAMPDIFF(MINUTE, notifications.ts, NOW()) AS min_ago
                              FROM notifications
                              INNER JOIN user_account ON (notifications.sender_id = user_account.id)
                              WHERE notifications.user_id='$row_user[id]' 
                              ORDER BY notifications.ts DESC ";
                              
                    $result = $db->query($sql);
    				if ($db->num_rows($result)>0) {
    					$response = "1~;";
    					while ($row = $db->fetch_array($result)) {
                            $days_ago = $row['min_ago']/1440;
    						$response .= $row['type_of']."~".$row['user_id']."~".$row['sender_id']."~".$row['message']."~".
    									 $row['is_read']."~".$row['is_hide']."~".$days_ago."~".$row['nick_name']."~".
                                         $row['email']."~".$row['profile']."~".$row['id']."~:";
    					}
    				} else {
    					$response = "2~;No available notifications!"; 
    				}
                } else {
                    $response = "0~;Not valid user.";
                }
				echo $response;					
			break;	
            case "CONFIRM_FOLLOWER":
				$notif_id   = isset($_POST['notif_id']) ? $_POST['notif_id'] : '';
				$sender_id  = isset($_POST['sender_id']) ? $_POST['sender_id'] : '';
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();				                
				
				////Record Task				
				$task	  = $notif_id."~".$sender_id."~".$email."~".$imei."~".$ip;
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);                
                
                $sql    = "SELECT * FROM  user_account WHERE email='$email'";              
                $result = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);     
                   				    
                    $sql_update   = "UPDATE follower SET stat='1' 
                                     WHERE user_id='$row[id]' AND follower_id='$sender_id' AND stat='0' ";                 
                    $result_update = $db->query($sql_update);
    				if ($result_update) {
    				    
                        $sql_notify     = "SELECT * FROM  user_account WHERE id='$sender_id' ";                 
                        $result_notify  = $db->query($sql_notify);
                        $row_notify     = $db->fetch_array($result_notify);  
                        
                        $notify_msg     = "Accepted your request to follow.";
                        $insert_notify  = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                           VALUES ('8','$sender_id','$row[id]','$notify_msg') ";
                        $notify_result  = $db->query($insert_notify);
                        
                        $update_notify  = "UPDATE notifications SET is_read='1'
                                           WHERE id='$notif_id' ";
                        $notify_update  = $db->query($update_notify);
                                                    
                        $msg_notify     = $row[nick_name]." - Accepted your request to follow.";
                        $gcm_id         = array($row_notify[gcm_id]);
                        $message        = array("message" => $msg_notify);                         
                        $result_gcm     = $gcm->send_notification($gcm_id, $message);

                        $response   = "1~;Successfully confirm followed.";
                    } else {
                        $response = "0~;Try to confirm again.";
                    }          			        				
                } else {
					$response = "0~;Not valid user."; 
				}
               
				echo $response;								
			break;		
            case "CANCEL_FOLLOWER":
				$notif_id   = isset($_POST['notif_id']) ? $_POST['notif_id'] : '';
				$sender_id  = isset($_POST['sender_id']) ? $_POST['sender_id'] : '';
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();				                
				
				//Record Task				
				$task	  = $notif_id."~".$sender_id."~".$email."~".$imei."~".$ip;
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);                
                
                $sql    = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);     
                   				    
                    $sql_update   = "UPDATE follower SET stat='2' WHERE 
                                    user_id='$row[id]' AND follower_id='$sender_id' AND stat='0' ";                 
                    $result_update = $db->query($sql_update);
    				if ($result_update) {
    				    
                        $sql_notify     = "SELECT * FROM  user_account WHERE id='$sender_id' ";                 
                        $result_notify  = $db->query($sql_notify);
                        $row_notify     = $db->fetch_array($result_notify);  
                                                    
                        $msg_notify = $row['nick_name']." - Cancel your request to follow.";
                        $gcm_id     = array($row_notify['gcm_id']);
                        $message    = array("message" => $msg_notify);                         
                        $result_gcm = $gcm->send_notification($gcm_id, $message);
                        
                        $notify_msg     = "Cancel your request to follow.";
                        $insert_notify  = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                           VALUES ('2','$sender_id','$row[id]','$notify_msg') ";
                        $result_notify  = $db->query($insert_notify);
                        
                        $update_notify  = "UPDATE notifications SET is_read='1'
                                           WHERE id='$notif_id' ";
                        $update_notify  = $db->query($update_notify);
                                                                                
                        $response   = "1~;Successfully cancel followed.";
                    } else {
                        $response = "0~;Try to cancel again.";
                    }          
                        			        				
                } else {
					$response = "0~;Not valid user."; 
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