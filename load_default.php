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
			case "IF_FOLLOWER":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				$follower_id='';
				$task	  = $user_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                $sql        = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_follower   = "SELECT * FROM  follower WHERE 
                                        (user_id='$user_id' AND follower_id = '$row[id]' AND stat='0') ";                 
                    $result_follower = $db->query($sql_follower);
    				if ($db->num_rows($result_follower)>0) {
				        $response = "1~;Already a follower, or same user.";                                            
                    } else {
                        $response = "2~;Not a follower.";
                    }					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "ADD_FOLLOWER":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				$follower_id='';
				$task	  = $user_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                $sql        = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_follower   = "SELECT * FROM  follower WHERE
                                        (user_id='$user_id' AND follower_id='$row[id]' AND stat='0') ";                 
                    $result_follower = $db->query($sql_follower);
    				if ($db->num_rows($result_follower)==0) {
    				    $sql_insert   = "INSERT INTO follower (user_id, follower_id, stat) VALUES ('$user_id', '$row[id]', '0') ";                 
                        $result_insert = $db->query($sql_insert);
        				if ($result_insert) {
        				    
                            $sql_notify     = "SELECT * FROM  user_account WHERE id='$user_id' ";                 
                            $result_notify  = $db->query($sql_notify);
                            $row_notify     = $db->fetch_array($result_notify);  
                                                                  
                            $notify_msg     = "Wants to follow you.";
                            $insert_notify  = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                               VALUES ('7','$user_id','$row[id]','$notify_msg') ";
                            $notify_result  = $db->query($insert_notify);

                            $msg_notify = $row['nick_name']." - Wants to follow you.";
                            $gcm_id     = array($row_notify['gcm_id']);
                            $message    = array("message" => $msg_notify);                         
                            $result_gcm = $gcm->send_notification($gcm_id, $message); 
                                                                                     
                            $response   = "1~;Successfully followed.";   
                        } else {
                            $response = "0~;Try to follow again.";
                        }                    
                    } else {
                        $response = "2~;Already a follower, or same user.";
                    }					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "COUNT_FOLLOWER":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				
				$task	  = $user_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
				$sql    = "SELECT * FROM  follower WHERE user_id = '$user_id' AND stat='1' ";              
                $result = $db->query($sql);
                $result_count  = $db->num_rows($result)-1;
				if ($result_count>0) {
					$response = "1~;$result_count";
				} else {
					$response = "0~;No available follower."; 
				}
				echo $response;				
			break;
			case "COUNT_FOLLOWING":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				
				$task	  = $user_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                
				$sql        = "SELECT * FROM  follower WHERE follower_id = '$user_id' AND stat='1' ";              
                $result     = $db->query($sql);
                $result_count  = $db->num_rows($result)-1;
				if ($result_count>0) {
					$response = "1~;$result_count";
				} else {
					$response = "0~;No available follower."; 
				}
				echo $response;				
			break;
			case "COUNT_WISHES":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				
				$task	  = $user_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
				$sql = "SELECT * FROM  wish WHERE user_id = '$user_id'";              
                $result = $db->query($sql);
                $result_count  = $db->num_rows($result);
				if ($result_count>0) {
					$response = "1~;$result_count";
				} else {
					$response = "0~;No available wishes."; 
				}
				echo $response;				
			break;
			case "IF_LIKES":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$wish_id    = isset($_POST['wish_id']) ? $_POST['wish_id'] : '';  
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();
                
				$task	  = $user_id."~".$wish_id."~".$email."~".$imei."~".$ip;
				
				//Record Task SELECT `id`, `user_id`, `liker_id`, `wish_id`, `ts` FROM `likes` WHERE 1
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                $sql        = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_likes   = "SELECT * FROM likes WHERE (user_id='$user_id' AND wish_id='$wish_id' AND liker_id = '$row[id]') ";                 
                    $result_likes = $db->query($sql_likes);
    				if ($db->num_rows($result_likes)>0) {
				        $response = "1~;Already liked.";                                            
                    } else {
                        $response = "2~;Not like.";
                    }					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "ADD_LIKES":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$wish_id    = isset($_POST['wish_id']) ? $_POST['wish_id'] : '';  
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();
                
				$task	  = $user_id."~".$wish_id."~".$email."~".$imei."~".$ip;
				
				//Record Task SELECT `id`, `user_id`, `liker_id`, `wish_id`, `ts` FROM `likes` WHERE 1
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                $sql        = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_likes   = "SELECT * FROM likes WHERE (user_id='$user_id' AND wish_id='$wish_id' AND liker_id = '$row[id]') ";                 
                    $result_likes = $db->query($sql_likes);
    				if ($db->num_rows($result_likes)==0) {
    				    $sql_insert   = "INSERT INTO likes (user_id, liker_id, wish_id) VALUES ('$user_id', '$row[id]', '$wish_id') ";                 
                        $result_insert = $db->query($sql_insert);
        				if ($result_insert) {
        				    
                            $result_likes = $db->query("SELECT * FROM likes WHERE wish_id = '".$row['id']."' ");
                            $likes_count  = $db->num_rows($result_likes);
            				if ($likes_count>0) {    
            				    $result = $db->query("UPDATE wish SET likes='$likes_count' WHERE id='$wish_id'");
            				}
                            
                            $sql_notify     = "SELECT * FROM  user_account WHERE id='$user_id' ";                 
                            $result_notify  = $db->query($sql_notify);
                            $row_notify     = $db->fetch_array($result_notify);  
                                                                  
                            $notify_msg     = "Like your wish.";
                            $insert_notify  = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                               VALUES ('2','$user_id','$row[id]','$notify_msg') ";
                            $notify_result  = $db->query($insert_notify);
    
                            $msg_notify = $row['nick_name']." - Like your wish.";
                            $gcm_id     = array($row_notify['gcm_id']);
                            $message    = array("message" => $msg_notify);                         
                            $result_gcm = $gcm->send_notification($gcm_id, $message); 

        				    $response = "1~;Successfully liked.";
                        } else {
                            $response = "0~;Try to like again.";
                        }                    
                    } else {
                        $response = "2~;Already a liked.";
                    }					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "COUNT_LIKES":	
				$wish_id    = isset($_POST['wish_id']) ? $_POST['wish_id'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				
				$task	  = $wish_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
				$sql = "SELECT * FROM likes WHERE wish_id = '$wish_id' ";              
                $result = $db->query($sql);
                $result_count  = $db->num_rows($result);
				if ($result_count>0) {
					$response = "1~;$result_count";
				} else {
					$response = "0~;No available likes."; 
				}
				echo $response;				
			break;
			case "ADD_COMMENT":	
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 
				$wish_id    = isset($_POST['wish_id']) ? $_POST['wish_id'] : ''; 
				$message    = isset($_POST['message']) ? $_POST['message'] : '';
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();
                
                $message    = $db->escape_value($message); 
				
				$task       = $message."~".$wish_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                
                $sql        = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result); 
				    $sql_insert   = "INSERT INTO comment (user_id, commenter_id, wish_id, message) 
                                     VALUES ('$user_id]', '$row[id]', '$wish_id', '$message') ";                 
                    $result_insert = $db->query($sql_insert);
    				if ($result_insert) {
        				    
                        $sql_notify     = "SELECT * FROM  user_account WHERE id='$user_id' ";                 
                        $result_notify  = $db->query($sql_notify);
                        $row_notify     = $db->fetch_array($result_notify);  
                                                              
                        $notify_msg     = "Comment on your wish.";
                        $insert_notify  = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                           VALUES ('3','$user_id','$row[id]','$notify_msg') ";
                        $notify_result  = $db->query($insert_notify);

                        $msg_notify = $row['nick_name']." - Comment on your wish.";
                        $gcm_id     = array($row_notify['gcm_id']);
                        $message    = array("message" => $msg_notify);                         
                        $result_gcm = $gcm->send_notification($gcm_id, $message); 
                                       
                        $sql_all = "SELECT comment.*, user_account.gcm_id FROM comment 
                                    INNER JOIN user_account ON (comment.commenter_id = user_account.id)
                                    WHERE comment.wish_id='$wish_id' AND comment.commenter_id!='$row[id]' 
                                    AND comment.commenter_id !='$user_id'
                                    GROUP BY comment.commenter_id ";                 
                        $result_all         = $db->query($sql_all);
                        $result_count_all   = $db->num_rows($result_all);
                        if ($result_count_all) {
                            $sql_value="";
                            $i=0;
                            $gcm_id = array();
                            $str="";
                            $notify_msg     = "Replies to your comment.";
                            while ($row_notify_me = $db->fetch_array($result_all)) {                                
                                if ($i<($result_count_all-1)){
                                    $sql_value  .= "('4','$row_notify_me[commenter_id]','$row[id]','$notify_msg'), ";
                                } else {
                                    $sql_value  .= "('4','$row_notify_me[commenter_id]','$row[id]','$notify_msg') ";
                                }
                                                                
                                $gcm_id[$i]=$row_notify_me['gcm_id'];
                                $i++;
                            }   
                            
                            $insert_value   = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                               VALUES $sql_value ";
                            $value_result  = $db->query($insert_value);
                           
                            $msg_notify = $row['nick_name']." - Replies to your comment.";
                            $message    = array("message" => $msg_notify);                         
                            $result_gcm = $gcm->send_notification($gcm_id, $message);                               
                        } 
                        $response = "1~;Successfull comment.";    				   
                    } else {
                        $response = "0~;Try to send again.";
                    } 					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
            case "COMMENT_LIST":
				$user_id    = isset($_POST['user_id']) ? $_POST['user_id'] : ''; 	
				$wish_id    = isset($_POST['wish_id']) ? $_POST['wish_id'] : ''; 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				
				$task	  = $user_id."~".$wish_id."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
          
          		$sql = "SELECT comment.*, user_account.nick_name, user_account.profile,
                        TIMESTAMPDIFF(MINUTE, comment.ts, NOW()) AS min_ago
                        FROM comment INNER JOIN user_account ON (comment.commenter_id = user_account.id ) 
                        WHERE comment.user_id = '$user_id' AND comment.wish_id = '$wish_id'
                        ORDER BY comment.ts DESC ";              
                $result = $db->query($sql);
                $result_count  = $db->num_rows($result);
				if ($result_count>0) {
					$response = "1~;$result_count~;";                                        
					while ($row = $db->fetch_array($result)) {
					   $days_ago = $row['min_ago']/1440;
					   $response .= $row['user_id']."~".$row['nick_name']."~".$row['profile']."~".$row['message']."~".$days_ago."~:";                   
                    }
				} else {
					$response = "0~;No available comment."; 
				}
				echo $response;				
			break;
			case "MAKE_IT_TRUE":	
				$wish_id    = isset($_POST['wish_id']) ? $_POST['wish_id'] : '';  
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();
                
				$task       = $wish_id."~".$email."~".$imei."~".$ip;
				
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                $sql        = "SELECT * FROM  user_account WHERE email='$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_make   = "SELECT * FROM make_list WHERE (user_id='".$row['id']."' AND wish_id='$wish_id' AND stat='0') ";                 
                    $result_make = $db->query($sql_make);
    				if ($db->num_rows($result_make)==0) {
    				    $sql_insert   = "INSERT INTO make_list (user_id, wish_id) VALUES ('".$row['id']."', '$wish_id') ";                 
                        $result_insert = $db->query($sql_insert);
        				if ($result_insert) {
        				    
                            $sql_notify     = "SELECT * FROM  user_account WHERE id='$user_id' ";                 
                            $result_notify  = $db->query($sql_notify);
                            $row_notify     = $db->fetch_array($result_notify);  
                                                                  
                            $notify_msg     = "Like your wish.";
                            $insert_notify  = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                               VALUES ('2','$user_id','$row[id]','$notify_msg') ";
                            $notify_result  = $db->query($insert_notify);
    
                            $msg_notify = $row['nick_name']." - Like your wish.";
                            $gcm_id     = array($row_notify['gcm_id']);
                            $message    = array("message" => $msg_notify);                         
                            $result_gcm = $gcm->send_notification($gcm_id, $message); 
                            
        				    $response = "1~;This item is added to your Make It True list.";
                        } else {
                            $response = "0~;Try to make it again.";
                        }                    
                    } else {
                        $response = "2~;This item is going to be bought by another friend.";
                    }					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "IF_MAKE_IT_TRUE":	
				$wish_id    = isset($_POST['wish_id']) ? $_POST['wish_id'] : '';  
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();
                
				$task       = $wish_id."~".$email."~".$imei."~".$ip;
				
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                
                $sql        = "SELECT * FROM user_account WHERE email='$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_make   = "SELECT * FROM make_list WHERE wish_id='$wish_id' AND stat='0' ";                 
                    $result_make = $db->query($sql_make);
    				if ($db->num_rows($result_make)>0) {
                        $response = "1~;This item is going to be bought by another friend.";
                    } else {
                        $response = "2~;This is available to Make it True."; 
                    }					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;		
			default:
            	$email    = isset($_GET['email']) ? $_GET['email'] : '';
				$imei	  = isset($_GET['imei']) ? $_GET['imei'] : ''; 
				$ip		  = getClientIp();	
				$task	  = "Invalid Transaction.";
			
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
								VALUES ('$email','$tag','$task','$imei','$ip')"; 	
				$result_log = $db->query($sql_log);
				echo "0~;Invalid Transaction."; 
		} 
			
	} else {
        $email    = isset($_GET['email']) ? $_GET['email'] : '';
		$imei	  = isset($_GET['imei']) ? $_GET['imei'] : ''; 
		$ip		  = getClientIp();	
		$task	  = "Access Denied.";
	
		//Record Task
		$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
						VALUES ('$email','$tag','$task','$imei','$ip')"; 	
		$result_log = $db->query($sql_log);
		echo "0~;Access Denied";
	}
?>	