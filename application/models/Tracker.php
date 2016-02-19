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
			$sql = "SELECT * FROM images,users WHERE image_id = ? AND images.user = users.id;";
			$this->load_db();
			$db = $this->db->get_db();
			$statement = $db->prepare($sql);
			$statement->bind_param("s",$image);
			$statement->execute();
			$result = $statement->get_result();
			$data = $result->fetch_assoc();
			if($result->num_rows == 1){

				$user_agent = $_SERVER['HTTP_USER_AGENT'];
				if(strpos($user_agent,"GoogleImageProxy")){
					$sql = "UPDATE images SET delivery_status='1', delivery_time=now() WHERE image_id = ?";
					$statement = $db->prepare($sql);
					$statement->bind_param("s",$image);
					$statement->execute();
					require ("application/helpers/SendSMS.php");
					$sms = new SendSMS();
					$sms->send($data['phone'], $data['placeholders']." is image file: ".$data['image_id']);
				}
				header("Content-Type:image/png");
				echo file_get_contents("img/tracker.png");
			}else{
				set_status_header(404);
			}
		}
	}
