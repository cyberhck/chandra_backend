<?php

	class Tracker extends Route
	{
		//todo log requests
		/**
		 * @param $tracker
		 * 
		 */
		public function serve($tracker){
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
					header("custom:logging now");
				}
				header("Content-Type:image/png");
				echo file_get_contents("img/tracker.png");
			}else{
				set_status_header(404);
			}
		}
	}
