<?php

	class Tracker extends Route
	{
		//todo log requests
		/**
		 * @param $tracker
		 */
		public function serve($tracker){
			/**
			 * @var $db mysqli
			 */
			$tracker = explode("/", $tracker);
			$image = $tracker[3];
			$sql = "SELECT * FROM images WHERE image_id = ?";
			$this->load_db();
			$db = $this->db->get_db();
			$statement = $db->prepare($sql);
			$statement->bind_param("s",$image);
			$statement->execute();
			$result = $statement->get_result();
			if($result->num_rows == 1){
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
				if(strpos($user_agent,"GoogleImageProxy")){
					$sql = "UPDATE images SET delivery_status='1', delivery_time=now() WHERE image_id = ?";
					$statement = $db->prepare($sql);
					$statement->bind_param("s",$image);
					$statement->execute();
				}
				header("Content-Type:image/png");
				echo file_get_contents("img/tracker.png");
			}else{
				set_status_header(404);
			}
		}
	}
