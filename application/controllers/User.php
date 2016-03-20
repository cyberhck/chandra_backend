<?php
	class User extends Route
	{
		public function login(){
			if(isset($_POST['access_token'])){
				$access_token = $_POST['access_token'];
				require_once 'application/helpers/google/src/Google/autoload.php';
				$client = new Google_Client();
				$client->setApplicationName("GlobeMail");
				$client->setDeveloperKey("AIzaSyATML0RjA6cHOpSKEGLLsodbufOy88YCr0");
				$ticket = $client->verifyIdToken($access_token);
				if ($ticket) {
					$data = $ticket->getAttributes()['payload'];
					$email = $data['email'];
					$name = $data['name'];
					$picture = $data['picture'];
				}else{
					
				}
			}else{
				set_status_header(400);
				$result['error'] = 'Bad request';
				$this->json_out($result);
			}
		}
		public function check(){
			$data['status']='OK';
			$data['message']='GET';
			$this->load_model('user_model');
			$result = $this->user_model->test_model();
			//die();
			set_status_header(200);
			$this->json_out($result);
		}
		public function test()
		{
			$data['status']="OK";
			$data['message']="TEST";
			json_out($data);
		}
	}
