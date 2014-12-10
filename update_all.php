<?php
  
	require_once ("include/db_connect.php");
	require_once ("include/MCrypt.php");
	require_once ("smtp/Send_Mail.php");
	require_once ("include/functions.php"); 
    global $db;  
	
    //Update Days Left  
    $ctr=1;   
    $response="";    
    $sql_days_left      = "SELECT wish.* FROM wish ORDER BY wish.ts DESC ";   
    $result_days_left   = $db->query($sql_days_left);
	if ($db->num_rows($result_days_left)>0) {
		while ($row = $db->fetch_array($result_days_left)) {
            $sql    = "UPDATE wish SET days = 
                        (
                        SELECT ROUND(TIMESTAMPDIFF(MINUTE,NOW(),occasion.occasion_date)/1440,0) AS days_left
                        FROM occasion
                        WHERE occasion.id =  '".$row['occasion_id']."'
                        ) 
                       WHERE wish.occasion_id='".$row['occasion_id']."'"; 
            $result = $db->query($sql);   
            
            $sql_like = "UPDATE wish SET likes =
                        (
                    	 SELECT COUNT(likes.wish_id) FROM likes WHERE wish_id = '".$row['id']."'
                         )
                         WHERE id='".$row['id']."'";
            $result_like  = $db->query($sql_like );   
            
            if ($result){
                $ctr++;
            }          
		}        
        $response = $ctr;  
	} else {
		$response = "No wish!"; 
	}
    
    echo $response;
?>	