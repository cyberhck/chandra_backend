<?php
	class User
	{
		public function login(){
			$data['status']='OK';
			$data['message']='Done';
			header('Content-Type:applicatioin/json');
			header('status:200');
			echo json_encode($data);
		}
		public function check(){
			$data['status']='OK';
			$data['message']='GET';
			header('Content-Type:applicatioin/json');
			header('status:200');
			echo json_encode($data);
		}
		public function test()
		{
			$data['status']="OK";
			$data['message']="TEST";
			echo json_encode($data);
		}
	}
?>
