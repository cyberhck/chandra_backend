<?php
	/**
	 * Class User
	 * Responsible for user related operations
	 */
	class User extends Route
	{
		/**
		 * @return void displays result of request
		 */
		public function login(){
			if(isset($_POST['access_token'])){
				$access_token = $_POST['access_token'];
				$this->load_model('user_model');
				$response = $this->user_model->check_login($access_token);
				$this->json_out($response);
			}else{
				set_status_header(400);
				$result['status'] = 'Error';
				$result['error'] = 'access_token not sent. Please refer to documentation';
				$this->json_out($result);
			}
		}
		public function set_phone(){
			if($this->check()){
				$this->load_model('user_model');
				$response = $this->user_model->set_phone($_SERVER['HTTP_AUTH_TOKEN'], $_POST['phone']);
				$this->json_out($response);
			}
		}
		private function check(){
			$this->load_model('Auth');
			if(isset($_SERVER['HTTP_AUTH_TOKEN'])){
				if($this->Auth->check_auth($_SERVER['HTTP_AUTH_TOKEN'])){
					return true;
				}else{
					set_status_header(400);
					$result['status'] = 'Error';
					$result['error'] = "auth_token not valid";
					$this->json_out($result);
				}
			}else{
				set_status_header(400);
				$result['status'] = 'Error';
				$result['error'] = 'auth_token not sent. Please refer to documentation';
				$this->json_out($result);
			}
		}
	}
