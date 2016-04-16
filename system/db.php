<?php

	/**
	 * Class db
	 */
	class db
	{
		/**
		 * @var $conn resource
		 */
		private $conn;

		/**
		 * @return database connection
		 */
		private function connect(){
			require_once('application/config/database.php');
			$config = $db[ENVIRONMENT];
			$conn = new mysqli($config['host'], $config['username'], $config['password'], $config['name']);
			$this->conn = $conn;
		}

		/**
		 * @param $table_name
		 *
		 * @return mixed
		 */
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

		/**
		 * @param $table name of table
		 * @param $id id to search
		 *
		 * @return mixed
		 */
		public function findOne($table,$id)
		{
			$this->connect();
			$statement = $this->conn->prepare("SELECT * FROM $table WHERE id = ?");
			$statement->bind_param("s",$id);
			$statement->execute();
			$result = $statement->get_result();
			return $result;
		}

		/**
		 * @return mysqli_driver
		 */
		public function get_db()
		{
			$this->connect();
			return $this->conn;
		}
	}
