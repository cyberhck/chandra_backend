<?php
	class Delivery extends Route
	{
		public function generate()
		{
			if($this->check()){
				$this->load_model('user_model');
				$response = $this->user_model->generate($_SERVER['HTTP_AUTH_TOKEN'],$_POST['placeholder']);
				$this->json_out($response);
			}
		}

		/**
		 * should do a x-form-encoded
		 */
		public function delete_image(){
			$delete_data = [];
			$rawPost = file_get_contents('php://input');
			mb_parse_str($rawPost, $delete_data);
			if($this->check()){
				$this->load_model('user_model');
				$response = $this->user_model->delete_image($_SERVER['HTTP_AUTH_TOKEN'],$delete_data['image']);
				$this->json_out($response);
			}
		}
		public function list_image(){
			if($this->check()){
				$this->load_model('user_model');
				$response = $this->user_model->list_image($_SERVER['HTTP_AUTH_TOKEN']);
				$this->json_out($response);
			}
		}
		private function parse_body(){
			$entityBody = file_get_contents('php://input');
			return json_decode($entityBody);
		}
		public function incoming(){
			$post = $this->parse_body();
			$from = $post->message_data->addresses->from;
			$to = $post->message_data->addresses->to;
			$subject = $post->message_data->subject;
			$this->load_model('user_model');
			$this->user_model->notify($from,$to,$subject);
		}
		public function incoming_failed(){
			$post = $this->parse_body();
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
		public function serve(){
			$REQUEST_URI=str_replace('/images/tracker/','',$_SERVER['REQUEST_URI']);
			$this->load_model('Tracker');
			$this->Tracker->serve($REQUEST_URI);
			}
	}
