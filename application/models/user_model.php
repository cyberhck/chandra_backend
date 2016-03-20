<?php
/**
* 
*/
class user_model extends Route
{
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
	public function generate_auth_token($email)
	{
		$token = bin2hex(openssl_random_pseudo_bytes(16));
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
}
