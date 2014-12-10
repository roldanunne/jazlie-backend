<?php

class MySQLDatabase {

	private $DB_HOST = "sql303.byethost11.com";
	private $DB_USER = "b11_14084728";
	private $DB_PASS = "r9_11111";
	private $DB_NAME = "b11_14084728_jazlie";  
	
	private $connection;
	private $last_query;
	private $magic_quotes_active;
	private $real_escape_string_exists;
	
	function __construct() {
		$this->open_connection();
		//$this->magic_quotes_active = get_magic_quotes_gpc();
        //$this->real_escape_string_exists = function_exists( "mysql_real_escape_string" );
	}
	
    function __destruct() {
        // $this->close();
    }
	
	public function open_connection() {
		$this->connection = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
		if (!$this->connection) {
			die("0~;Database connection failed: " . mysqli_error());
		}//else {
			// echo "Connected";
		// }
	}
	
	public function close_connection() {
		if(isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}

	public function query($sql) {
		$this->last_query = $sql;
		$result = mysqli_query( $this->connection,$sql);
		$this->confirm_query($result);
		return $result;
	}
	
	public function escape_value($value) {
		return mysqli_real_escape_string($this->connection, $value);
	}
	
	// "database-neutral" methods
	public function fetch_array($result_set) {
		return mysqli_fetch_array($result_set);
	}
	
	public function fetch_object($result_set) {
		return mysqli_fetch_object($result_set);
	}
	
	public function num_rows($result_set) {
		return mysqli_num_rows($result_set);
	}

	public function insert_id() {
	// get the last id inserted over the current db connection
		return mysqli_insert_id($this->connection);
	}

	public function affected_rows() {
		return mysqli_affected_rows($this->connection);
	}

	private function confirm_query($result) {
		if (!$result) {
	    $output = "0~;Database query failed: ".mysql_error()."\n";
	    $output .= "Last SQL query: ".$this->last_query;
	    die( $output );
		}	
	}
}
	$db = new MySQLDatabase();

	