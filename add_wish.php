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
			case "ADD_WISH":
                $prod_name	= isset($_POST['product']) ? $_POST['product'] : ''; 
				$price      = isset($_POST['price']) ? $_POST['price'] : ''; 
				$location   = isset($_POST['location']) ? $_POST['location'] : ''; 
				$occasion_id= isset($_POST['occasion_id']) ? $_POST['occasion_id'] : ''; 
				$note       = isset($_POST['note']) ? $_POST['note'] : '';
                $img	  	= isset($_POST['img']) ? $_POST['img'] : '0';   
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();

                $prod_name	= $db->escape_value($prod_name); 
				$location   = $db->escape_value($location);
				$note       = $db->escape_value($note);
                
				$task	  = $product."~".$product."~".$price."~".$find."~".$occasion."~".
                            $note."~".$img."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
                               VALUES ('$email','$tag','$task','$imei','$ip') "; 	

				$result_log = $db->query($sql_log);
                                
                $sql        = "SELECT * FROM  user_account WHERE LOWER(email)=LOWER('$email')";	  
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_insert   = "INSERT INTO wish (user_id, prod_name, price, location, occasion_id, note) 
                                     VALUES ('$row[id]', '$prod_name', '$price', '$location', '$occasion_id', '".$db->escape_value($note)."') ";                 
                    $result_insert = $db->query($sql_insert);
    				if ($result_insert) {
    				    $id = $db->insert_id();
                    	$filename = "";
                        if ($img=='1'){
                            $temp = explode('.',$_FILES['img_wish']['name']);
                            $filename = 'WISH_'.$row[id]."_".$id.'.'.$temp[1];
                            $path = "images/" . $filename;
                            $upload = move_uploaded_file($_FILES['img_wish']['tmp_name'], $path);
                            if($upload) {
                                $result = $db->query("UPDATE wish SET image='$filename' WHERE id='$id'");
                                $response = "1~;Wish successfully added!"; 
                            } else {
                                $response = "1~;Wish successfully added,\n but image failed to upload!"; 
                            }
                        } else {
                            $response = "1~;Wish successfully added!"; 
                        }
                    } else {
                        $response = "2~;Try to submit again.";
                    }                    
                    			
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "RE_ADD_WISH":
                $prod_name	= isset($_POST['product']) ? $_POST['product'] : ''; 
				$price      = isset($_POST['price']) ? $_POST['price'] : ''; 
				$location   = isset($_POST['location']) ? $_POST['location'] : ''; 
				$occasion_id= isset($_POST['occasion_id']) ? $_POST['occasion_id'] : ''; 
				$note       = isset($_POST['note']) ? $_POST['note'] : '';
                $img	  	= isset($_POST['img']) ? $_POST['img'] : '0';   
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();

                $prod_name	= $db->escape_value($prod_name); 
				$location   = $db->escape_value($location);
				$note       = $db->escape_value($note);
                
                //Record Task
				$task	  = $product."~".$product."~".$price."~".$find."~".$occasion."~".
                            $db->escape_value($note)."~".$img."~".$email."~".$imei."~".$ip;
				
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
                               VALUES ('$email','$tag','$task','$imei','$ip') "; 
                               
				$result_log = $db->query($sql_log);
                                
                $sql        = "SELECT * FROM  user_account WHERE LOWER(email)=LOWER('$email')";	  
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_insert   = "INSERT INTO wish (user_id, prod_name, price, location, occasion_id, note) 
                                     VALUES ('$row[id]', '$prod_name', '$price', '$location', '$occasion_id', '".$db->escape_value($note)."') ";                 
                    $result_insert = $db->query($sql_insert);
    				if ($result_insert) {
    				    $id = $db->insert_id();
                    	$filename = "";
                        if ($img=='1'){
                            $temp = explode('.',$_FILES['img_wish']['name']);
                            $filename = 'WISH_'.$row[id]."_".$id.'.'.$temp[1];
                            $path = "images/" . $filename;
                            $upload = move_uploaded_file($_FILES['img_wish']['tmp_name'], $path);
                            if($upload) {
                                $result = $db->query("UPDATE wish SET image='$filename' WHERE id='$id'");
                                $response = "1~;Wish successfully added!"; 
                            } else {
                                $response = "1~;Wish successfully added,\n but image failed to upload!"; 
                            }
                        } else if ($img=='2'){
                            $filename = isset($_POST['filename']) ? $_POST['filename'] : ''; 
                            if($filename!='') {
                                $result = $db->query("UPDATE wish SET image='$filename' WHERE id='$id'");
                                $response = "1~;Wish successfully added!"; 
                            } 
                        } else {
                            $response = "1~;Wish successfully added!"; 
                        }
                    } else {
                        $response = "2~;Try to submit again.";
                    }                    
                    			
				} else {
					$response = "0~;Not valid user."; 
				}
				echo $response;				
			break;
			case "EDIT_WISH":
                $wish_id	= isset($_POST['wish_id']) ? $_POST['wish_id'] : ''; 
                $prod_name	= isset($_POST['product']) ? $_POST['product'] : ''; 
				$price      = isset($_POST['price']) ? $_POST['price'] : ''; 
				$location   = isset($_POST['location']) ? $_POST['location'] : ''; 
				$occasion_id= isset($_POST['occasion_id']) ? $_POST['occasion_id'] : ''; 
				$note       = isset($_POST['note']) ? $_POST['note'] : '';  
                $img	  	= isset($_POST['img']) ? $_POST['img'] : '0';   
				$email      = isset($_POST['email']) ? $_POST['email'] : '';
				$imei       = isset($_POST['imei']) ? $_POST['imei'] : ''; 
				$ip	        = getClientIp();

                $prod_name	= $db->escape_value($prod_name); 
				$location   = $db->escape_value($location);
				$note       = $db->escape_value($note);
                
				$task	  = $product."~".$product."~".$price."~".$find."~".$occasion."~".
                            $note."~".$img."~".$email."~".$imei."~".$ip;
				
				//Record Task
				$sql_log 	= "INSERT INTO log_task (email,tag,task,imei,ip_address)  
                               VALUES ('$email','$tag','$task','$imei','$ip') "; 	
				$result_log = $db->query($sql_log);
                                
                $sql        = "SELECT * FROM  user_account WHERE LOWER(email)=LOWER('$email')";	  
                $result     = $db->query($sql);
				if ($db->num_rows($result)>0) {
				    $row = $db->fetch_array($result);          
				    $sql_insert   = "UPDATE wish SET user_id='$row[id]', prod_name='$prod_name', price='$price',
                                            location='$location', occasion_id='$occasion_id', note='$note'
                                     WHERE id='$wish_id' ";                 
                    $result_insert = $db->query($sql_insert);
    				if ($result_insert) {
    				    $id = $db->insert_id();
                    	$filename = "";
                        if ($img=='1'){
                            $temp = explode('.',$_FILES['img_wish']['name']);
                            $filename = 'WISH_'.$row[id]."_".$wish_id.'.'.$temp[1];
                            $path = "images/" . $filename;
                            $upload = move_uploaded_file($_FILES['img_wish']['tmp_name'], $path);
                            if($upload) {
                                $result = $db->query("UPDATE wish SET image='$filename' WHERE id='$wish_id'");
                                $response = "1~;Wish successfully added!"; 
                            } else {
                                $response = "1~;Wish successfully added,\n but image failed to upload!"; 
                            }
                        } else if ($img=='2'){
                            $filename = isset($_POST['filename']) ? $_POST['filename'] : ''; 
                            if($filename!='') {
                                $result = $db->query("UPDATE wish SET image='$filename' WHERE id='$wish_id'");
                                $response = "1~;Wish successfully added!"; 
                            } 
                        } else {
                            $response = "1~;Wish successfully added!"; 
                        }
                    } else {
                        $response = "2~;Try to submit again.";
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