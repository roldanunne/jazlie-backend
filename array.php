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
    
    $gcm_id[0]="APA91bEYDc3xzKQrQ5nfoaF-RDDWrB1m1TT7eFiiFuS45XF7SZUFBMx4jSviggAaliwMFv2tAIy0gcMLHplP2KvlRH2jZAB_H50eXZZSfLIEX1HfkjcWchYGU912ROuQNw30_qgUFB8emC76BhlW5n54V9D2_cMjUg";   
    $gcm_id[1]="APA91bGw1x0QXZhR5wX1d0QDdTRo1Qz6kDG0bYAqGW7_rt8uWjNdi3peYx_3FrLqOvGLMO6yDO5995kYOYgBkGaISSdw2GuxIZAuhypSDDjF7-nd9tDGmCWFXkznO_NafGY-4xPVB6Rgv90SNs8ZSwYkd48PithwDA";
    
    $msg_notify = "AKO TO - Replies to your comment.";
    //$gcm_id     = array($row_notify_me['gcm_id']);
    $message    = array("message" => $msg_notify);                         
    $result_gcm = $gcm->send_notification($gcm_id, $message); 
?>