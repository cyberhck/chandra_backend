<?php

	/**
	 * Class user_model
	 * @method check_login verifies if Google login was valid
	 * @method generate_auth_token generates an auth token to use by the client
	 */
	class user_model extends Route
	{
		/**
		 * This is just test model and will be removed
		 * @param string $value
		 * @deprecated will be removed
		 * @return array
		 */
		public function test_model($value='')
		{
			$this->load_db();
			$data = $this->db->get('users');
			$result = [];
			while($row = $data->fetch_assoc()){
				$result[] = $row;
			}
			return $result;
		}

		/**
		 * @param $access_token token from Google's login API
		 * @return array with login data
		 */
		public function check_login($access_token)
		{
			/**
			 * @var $db mysqli
			 */
			require_once('application/config/config.php');
			require_once ('application/helpers/google/src/Google/autoload.php');
			$client = new Google_Client();
			$client->setApplicationName("GlobeMail");
			$client->setDeveloperKey("AIzaSyATML0RjA6cHOpSKEGLLsodbufOy88YCr0");
			try {
				$ticket = $client->verifyIdToken($access_token);
			} catch (Exception $e) {
				$ticket = false;
			}
			if ($ticket) {
				$data = $ticket->getAttributes()['payload'];
				$email = $data['email'];
				$name = $data['name'];
				$picture = $data['picture'];
				$this->load_db();
				$db = $this->db->get_db();
				$statement = $db->prepare("SELECT * FROM users WHERE email = ?");
				$statement->bind_param("s",$email);
				$statement->execute();
				$result = $statement->get_result();
				if($result->num_rows == 0){
					$sql = "INSERT INTO users (email, name, picture, phone) VALUES(?,?,?,'917411336384')";
					$statement = $db->prepare($sql);
					$statement->bind_param("sss",$email,$name,$picture);
					if($statement->execute()){
						set_status_header(200);
						$response['status'] = 'Success';
						$response['auth_token'] = $this->generate_auth_token($email);
					}else{
						set_status_header(500);
						$response['status'] = 'Error';
						$response['error'] = 'Error writing into database';
					}
				}else{
					set_status_header(200);
					$response['status'] = 'Success';
					$response['auth_token'] = $this->generate_auth_token($email);
				}
			}else{
				//bad request
				set_status_header(400);
				$response['status'] = 'Error';
				$response['error'] = 'access_token invalid';
			}
			return $response;
		}

		/**
		 * @param $email Email of user
		 *
		 * @return string auth token for client
		 */
		public function generate_auth_token($email)
		{
			/**
			 * @var $db mysqli
			 */
			$token = bin2hex(openssl_random_pseudo_bytes(32));
			$sql = <<<SQL
			INSERT INTO access (user, auth_token)
				SELECT id,? AS auth_token FROM users WHERE email = ?;
SQL;
			$db = $this->db->get_db();
			$statement = $db->prepare($sql);
			$statement->bind_param("ss",$token,$email);
			$statement->execute();
			return $token;
		}
		
		public function generate($auth_token,$placeholder='')
		{
			/**
			 * @var $db mysqli
			 */
			$this->load_db();
			$db = $this->db->get_db();
			$token = bin2hex (openssl_random_pseudo_bytes (32)).".png";
			$sql = 'INSERT INTO images(user,image_id,placeholder) SELECT user AS user,? AS image_id,? as placeholder FROM access WHERE auth_token=?;';
			$statement = $db->prepare($sql);
			$statement->bind_param('sss', $token,$placeholder,$auth_token);
			if($statement->execute()){
				set_status_header(200);
				$response['status'] = 'Success';
				$response['image'] = $token;
			}else{
				set_status_header(500);
				$response['status'] = 'Error';
				$response['message'] = 'Error writing to database';
			}
			return $response;
		}
		public function delete_image($auth_token,$image_id){
			/**
			 * @var $db mysqli
			 */
			$sql = 'DELETE FROM images WHERE image_id = ? AND user IN(SELECT user FROM access WHERE auth_token=?)';
			$this->load_db();
			$db = $this->db->get_db();
			$statement = $db->prepare($sql);
			$statement->bind_param('ss',$image_id,$auth_token);

			if($statement->execute()){
				if($statement->affected_rows==0){
					set_status_header(404);
					$response['status'] = 'error';
					$response['message'] = 'Image not found on server or already deleted';
				}else{
					set_status_header(200);
					$response['status'] = 'Success';
					$response['message'] = 'Image deleted';
				}
			}else{
				set_status_header(500);
				$response['status'] = 'Error';
				$response['message'] = 'Failed Deleting from Database';
			}
			return $response;
		}
		private function image_properties($auth_token,$image_id){
			//todo
			set_status_header(501);
			var_dump($auth_token);
			die();
		}
		public function list_image($auth_token){
			$REQUEST_URI = $_SERVER['REQUEST_URI'];
			$REQUEST_URI = str_replace('/delivery/image/','',$REQUEST_URI);
			$REQUEST_URI = str_replace('/delivery/image','',$REQUEST_URI);
			if(strlen($REQUEST_URI)!=0){
				return $this->image_properties($auth_token,$REQUEST_URI);
			}
			/**
			 * @var $db mysqli
			 */
			$this->load_db();
			$db = $this->db->get_db();
			$sql = "SELECT image_id as image,placeholder,delivery_status as `status` FROM images WHERE user IN(SELECT user FROM access WHERE auth_token =?);";
			$statement = $db->prepare($sql);
			$statement->bind_param('s',$auth_token);
			$statement->execute();
			$result = $statement->get_result();
			$response = [];
			while ($row = $result->fetch_assoc()){
				$response[]=$row;
			}
			return $response;
		}
		public function set_phone($auth_token,$phone){
			/**
			 * @var $db mysqli
			 */
			$sql = "UPDATE users SET phone = ? WHERE id IN(SELECT user FROM access WHERE auth_token = ?)";
			$this->load_db();
			$db = $this->db->get_db();
			$statement = $db->prepare($sql);
			$statement->bind_param("ss",$phone,$auth_token);
			if($statement->execute()){
				set_status_header(200);
				$response['status'] = 'Success';
				$response['message'] = 'Phone Number Updated';
			}else{
				set_status_header(500);
				$response['status'] = 'Fail';
				$response['message'] = 'Failed Writing to DB';
			}
			return $response;
		}
		public function notify($from,$to,$subject){
			/**
			 * @var $db mysqli
			 */
			$sql = "SELECT * FROM users WHERE email = ?;";
			$this->load_db();
			$db = $this->db->get_db();
			$statement = $db->prepare($sql);
			$statement->bind_param("s",$to);
			$statement->execute();
			$sql = "INSERT INTO sms ()";
			$result = $statement->get_result();
			$row = $result->fetch_assoc();
			$phone = $row['phone'];
			$message = "Dear {$row['name']}, you have a new message from {$from} with subject {$subject}.";
			$sms = new SendSMS();
			$sms->send($phone, $message);
		}
	}
