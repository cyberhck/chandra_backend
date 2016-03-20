<?php
	class Delivery extends Route
	{
		public function generate()
		{
			if($this->check()){
				$this->load_model('user_model');
				$response = $this->user_model->generate($_SERVER['HTTP_AUTH_TOKEN']);
				$this->json_out($response);
			}
		}

		/**
		 * should do a x-form-encoded
		 */
		public function delete_image(){
//			Todo should actually delete
			$delete_data = [];
			$rawPost = file_get_contents('php://input');
			mb_parse_str($rawPost, $delete_data);
			if($this->check()){
				echo "here";
			}
		}
		public function list_image(){
			if($this->check()){
				$this->load_model('user_model');
				$response = $this->user_model->list_image();
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
