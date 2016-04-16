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
		public function check(){
			$data['status']='OK';
			$data['message']='GET';
			$this->load_model('user_model');
			$result = $this->user_model->test_model();
			set_status_header(200);
			$this->json_out($result);
		}
		public function test(){
			require_once ('application/helpers/sms/textlocal.class.php');
			$text_local = new Textlocal(null,null,"txIVgKKOAOE-H0L8z3VKeKRqB8jYYgufOcpoilHQ4f");
			$result = $text_local->sendSms(["7411336384"],"test message","TXTLCL");
			var_dump($result);
		}
	}
