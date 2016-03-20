<?php

	/**
	 * Class user_model
	 * @method test_model doesn't do anything
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
		 *
		 * @return array with login data
		 */
		public function check_login($access_token)
		{
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
					$sql = "INSERT INTO users (email, name, picture) VALUES(?,?,?)";
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
		
		public function generate($auth_token)
		{
			$this->load_db();
			$db = $this->db->get_db();
			$token = bin2hex (openssl_random_pseudo_bytes (32));
			$sql = 'INSERT INTO images(user,image_id) SELECT user AS user,? AS image_id FROM access WHERE auth_token=?;';
			$statement = $db->prepare($sql);
			$statement->bind_param('ss', $token,$auth_token);
			if($statement->execute()){
				set_status_header(200);
				$response['status'] = 'Success';
				$response['image'] = $token.".jpg";
			}else{
				set_status_header(500);
				$response['status'] = 'Error';
				$response['message'] = 'Error writing to database';
			}
			return $response;
		}
		public function delete_image($auth_token,$image_id){
			//Todo
		}
		public function list_image($auth_token){
			//todo
		}

	}
