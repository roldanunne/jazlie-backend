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
            case "OCCASION_LIST":	 
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
	
    			$task	    = $email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                $sql        = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row_user = $db->fetch_array($result);          
				    $sql_occasion   = "SELECT * FROM occasion WHERE user_id='$row_user[id]' ";                 
                    $result_occasion = $db->query($sql_occasion);
    				if ($db->num_rows($result_occasion)>0) {
    					$response = "1~;";
				    	while ($row = $db->fetch_array($result_occasion)) {
				    	   $response .= $row['id']."~".$row['title']."~".$row['occasion_date']."~:"; 
                        }                                        
                    } else {
                        $response = "2~;No Available Occasion!";
                    }					
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "ADD_OCCASION":
				$email	  	= isset($_POST['email']) ? $_POST['email'] : ''; 
                $occasion  	= isset($_POST['occasion']) ? $_POST['occasion'] : ''; 
				$imei	  	= isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip  	    = getClientIp();
                			
                $occasion	= $db->escape_value($occasion); 
                	
				$task	    = $email."~".$occasion."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address) 
								VALUES ('$email','$tag','$task','$imei','$ip')"; 	
				$result_log = $db->query($sql_log);
				
                
				$result = $db->query("SELECT * FROM user_account WHERE LOWER(email)=LOWER('$email')");	
				if ($db->num_rows($result) >= 1) {
				    $row = $db->fetch_array($result);
                    
                    $sql_value  = "";
                    $user_id    = $row['id'];
                    $data_list  = explode('~:',$occasion);  
                    $arr_size   = sizeof($data_list)-1;
                                 
                    for ($i=0;$i<$arr_size;$i++){ 
                        $data  = explode(':',$data_list[$i]); 
                        if ($i<($arr_size-1)){
                            $sql_value  .= "('$user_id', '$email', '".$data[0]."', '".$data[1]."'), ";
                        } else {
                            $sql_value  .= "('$user_id', '$email', '".$data[0]."', '".$data[1]."') ";
                        }
                    }
                    
                    $sql    = "INSERT INTO occasion (user_id, email, title, occasion_date) VALUES " . $sql_value ;
					$result = $db->query($sql);	
                    if ($result) {
                        $response = "1~;Occasion added successful!"; 
                    } else {
                        $response = "0~;Occasion not added. Please try again!";
                    }
                    
			    } else {				
                    $response = "0~;This email address is not valid!";
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