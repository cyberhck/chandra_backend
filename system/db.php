<?php
/**
* 
*/
class db
{
	private $conn;
	function __construct()
	{
		
	}
	private function connect(){
		require_once('application/config/database.php');
		$config = $db[ENVIRONMENT];
		$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['name']);
		$this->conn = $conn;
	}
	public function get($table_name)
	{
		$this->connect();
		$statement = $this->conn->prepare("SELECT * FROM $table_name");
		//$statement->bind_param("",$table_name);
		$statement->execute();
		$result = $statement->get_result();
		//$result = $this->conn->query($sql);
		return $result;
	}
}
