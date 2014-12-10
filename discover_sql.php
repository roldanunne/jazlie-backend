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
			case "DISCOVER_LIST":	
				$search     = isset($_POST['search']) ? $_POST['search'] : '';
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();	
				
				$task	  = $search."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
								VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);

                $sql_user   = "SELECT * FROM  user_account WHERE email = '$email'";              
                $result_user= $db->query($sql_user);
				if ($db->num_rows($result_user)>0) {
				    $row_user = $db->fetch_array($result_user);                  

    				$sql = "";
                    if($search==''){                                
                        $sql    = "SELECT wish.*, user_account.name, user_account.nick_name, user_account.email, 
                                   user_account.gender, user_account.bday, user_account.about, user_account.profile
                                   FROM wish
                                   INNER JOIN user_account ON (wish.user_id = user_account.id)
                                   WHERE NOT EXISTS 
                                   (SELECT * FROM follower WHERE follower.user_id=wish.user_id AND follower.follower_id = '".$row_user['id']."'
                                    AND follower.stat='1')
                                   ORDER BY wish.days, wish.ts ASC ";
                    } else {
                        $sql    = "SELECT wish.*, user_account.name, user_account.nick_name, user_account.email,	
                                   user_account.gender, user_account.bday, user_account.about, user_account.profile
                                   FROM wish 
                                   INNER JOIN user_account ON (wish.user_id = user_account.id)
                                   WHERE 
                                   (
                                       LOWER(user_account.name) LIKE LOWER('%".$search."%') OR
                                       LOWER(user_account.nick_name) LIKE LOWER('%".$search."%') OR
                                       LOWER(user_account.email) LIKE LOWER('%".$search."%') OR
                                       LOWER(wish.prod_name) LIKE LOWER('%".$search."%') OR
                                       LOWER(wish.location) LIKE LOWER('%".$search."%') OR
                                       LOWER(wish.note) LIKE LOWER('%".$search."%')
                                   ) 
                                   AND 
                                   (   NOT EXISTS 
                                       (SELECT * FROM follower WHERE follower.user_id=wish.user_id AND follower.follower_id = '".$row_user['id']."'
                                        AND follower.stat='1')
                                   )
                                   ORDER BY wish.days, wish.ts ASC ";
                    }
                    
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
    					$response = "2~;No available photos of people or shops!"; 
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