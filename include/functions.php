<?php

	function strip_zeros_from_date( $marked_string="" ) {
	  // first remove the marked zeros
	  $no_zeros = str_replace('*0', '', $marked_string);
	  // then remove any remaining marks
	  $cleaned_string = str_replace('*', '', $no_zeros);
	  return $cleaned_string;
	}

	function redirect_to( $location = NULL ) {
	  if ($location != NULL) {
		header("Location: {$location}");
		exit;
	  }
	}

	function redirect_to_home () {
		echo '<script> window.location.href = "'. SITE_URL . '" </script>';
	}

	function output_message($message="") {
	  if (!empty($message)) { 
		return "<p class=\"message\">{$message}</p>";
	  } else {
		return "";
	  }
	}

	function __autoload($class_name) {
		$class_name = strtolower($class_name);
		$path = LIB_PATH.DS."{$class_name}.php";
		if(file_exists($path)) {
			require_once($path);
		} else {
			return false;
			die("The file {$class_name}.php could not be found.");
		}
	}


	function datetime_to_text($datetime="") {
	  $unixdatetime = strtotime($datetime);
	  return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
	}
	
	function post_check(){
		if ('POST' == $_SERVER['REQUEST_METHOD']) 
		{
			// It's ok for these to be null - or empty
			$exclude = array('submit', 'other_param');
			$allIsOk = true;
			foreach ($_POST as $index => $value) {
				if (!in_array($index, $exclude) && strlen($value)<1) {
				  $allIsOk = false;      
				}
			}
		}
		return $allIsOk;
	}
	// Function to get the client ip address
	function getClientIp() {
		$result = null;
		$ipSourceList = array('HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR',
						'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR',
						'HTTP_FORWARDED', 'REMOTE_ADDR');
		foreach($ipSourceList as $ipSource){
			if ( isset($_SERVER[$ipSource]) ){
				$result = $_SERVER[$ipSource];
				break;
			}
		}
		return $result;
	}
?>