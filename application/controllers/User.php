<?php
	class User extends Route
	{
		public function login(){
			$data['status']='OK';
			$data['message']='Done';
			$this->json_out($data);
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
