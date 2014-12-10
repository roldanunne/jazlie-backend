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
            case "LOAD_PROFILE":	 
				$email  = isset($_POST['email']) ? $_POST['email'] : '';
				$imei   = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	    = getClientIp();
                
				$task   = $email."~".$imei."~".$ip;
				
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                
                $sql        = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);  
                    $response = "1~;".$row['id']."~".$row['name']."~".$row['nick_name']."~".$row['email']."~".
                                         $row['gender']."~".$row['bday']."~".$row['about']."~".$row['profile'];                        			
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "WISHES_LIST":	
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
                            
                    $sql   = "SELECT wish.*, user_account.name, user_account.nick_name, user_account.email, 
                              user_account.gender, user_account.bday, user_account.about, user_account.profile
                              FROM wish
                              INNER JOIN user_account ON (wish.user_id = user_account.id)
                              WHERE wish.user_id='".$row_user['id']."' 
                              ORDER BY wish.ts DESC ";
                    
                    $result = $db->query($sql);
    				if ($db->num_rows($result)>0) {
    					$response = "1~;";
    					while ($row = $db->fetch_array($result)) {
    						$response .= $row['id']."~".$row['prod_name']."~".$row['price']."~".$row['location']."~".
    									 $row['occasion_id']."~".$row['note']."~".$row['image']."~".$row['likes']."~".
                                         $row['days']."~".$row['name']."~".$row['nick_name']."~".$row['email']."~".
                                         $row['gender']."~".$row['bday']."~".$row['about']."~".$row['profile']."~".
                                         $row['user_id']."~:";
    					}
    				} else {
    					$response = "2~;No available wishes!"; 
    				}
                } else {
                    $response = "0~;Not valid user.";
                }
				echo $response;					
			break;	
            case "MAKE_IT_TRUE_LIST":
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
                    //`id`, `user_id`, `wish_id`, `stat`, `ts` FROM `make_list
                    $sql   = "SELECT wish.*, user_account.name, user_account.nick_name, user_account.profile,
                              make_list.id AS make_list_id, make_list.stat AS make_list_stat
                              FROM wish
                              INNER JOIN user_account ON (wish.user_id = user_account.id)
                              INNER JOIN make_list ON ( make_list.wish_id = wish.id ) 
                              WHERE make_list.user_id='".$row_user['id']."' AND make_list.stat!='2' 
                              ORDER BY wish.ts DESC ";
                    
                    $result = $db->query($sql);
    				if ($db->num_rows($result)>0) {
    					$response = "1~;";
    					while ($row = $db->fetch_array($result)) {
    						$response .= $row['id']."~".$row['prod_name']."~".$row['price']."~".$row['location']."~".
    									 $row['occasion_id']."~".$row['note']."~".$row['image']."~".$row['likes']."~".
                                         $row['days']."~".$row['name']."~".$row['nick_name']."~".$row['profile']."~".
                                         $row['user_id']."~".$row['make_list_id']."~".$row['make_list_stat']."~:";
    					}
    				} else {
    					$response = "2~;No available list on Make it True!"; 
    				}
                } else {
                    $response = "0~;Not valid user.";
                }
				echo $response;				
			break;				
            case "MAKE_IT_CHECK":	
				$make_list_id = isset($_POST['make_list_id']) ? $_POST['make_list_id'] : '';  
				$user_id    = isset($_POST['make_it_userid']) ? $_POST['make_it_userid'] : '';  
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();
                
				$task       = $make_list_id."~".$email."~".$imei."~".$ip;				
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                
                $sql        = "SELECT * FROM  user_account WHERE email='$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_make   = "UPDATE make_list SET stat='1' WHERE id='$make_list_id' ";                 
                    $result_make = $db->query($sql_make);
    				if ($result_make) {
    				    
                        $sql_notify     = "SELECT * FROM  user_account WHERE id='$user_id' ";                 
                        $result_notify  = $db->query($sql_notify);
                        $row_notify     = $db->fetch_array($result_notify);  
                                                                                      
    				    $msg_notify = "Your wish has been picked by someone.";
                        $gcm_id     = array($row_notify['gcm_id']);
                        $message    = array("message" => $msg_notify);                         
                        $result_gcm = $gcm->send_notification($gcm_id, $message);
                            
                        $insert_notify  = "INSERT INTO notifications(type_of,user_id,sender_id,message)
                                           VALUES ('11','$user_id','$row[id]','$msg_notify') ";
                        $notify_result  = $db->query($insert_notify);
                        
    				    $response = "1~;This item is done on your Make It True list.";
                    } else {
                        $response = "2~;Try to click done again.";
                    }           	
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;					
            case "MAKE_IT_CANCEL":	
				$make_list_id = isset($_POST['make_list_id']) ? $_POST['make_list_id'] : '';  
				$user_id    = isset($_POST['make_it_userid']) ? $_POST['make_it_userid'] : '';  
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();
                
				$task       = $make_list_id."~".$email."~".$imei."~".$ip;
				
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                
                $sql        = "SELECT * FROM  user_account WHERE email='$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_make   = "UPDATE make_list SET stat='2' WHERE id='$make_list_id' ";                 
                    $result_make = $db->query($sql_make);
    				if ($result_make) {
    				    $response = "1~;This item is cancel on your Make It True list.";
                    } else {
                        $response = "2~;Try to click cancel again.";
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