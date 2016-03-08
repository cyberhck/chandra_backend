<?php
	class Route
	{
		public static $routes;
		public static $flag=false;
		public static function get($routes){
			$REQUEST_URI=$_SERVER['REQUEST_URI'];
			if($routes[$REQUEST_URI] && $_SERVER['REQUEST_METHOD']=="GET"){
				$destination=$routes[$REQUEST_URI];
				$controller=explode('@', $destination)[0];
				$function=explode('@', $destination)[1];
				require_once("application/controllers/{$controller}.php");
				$instance=new $controller;
				$instance->$function();
			}else if ($_SERVER['REQUEST_METHOD']=="GET"){
				require("system/status_header.php");
				set_status_header(404);
				echo "404";
			}
		}

		public static function post($routes){
			$REQUEST_URI=$_SERVER['REQUEST_URI'];
			$REQUEST_URI=explode("?", $REQUEST_URI)[0];
			if($routes[$REQUEST_URI] && $_SERVER['REQUEST_METHOD']=="POST"){
				$destination=$routes[$REQUEST_URI];
				$controller=explode('@', $destination)[0];
				$function=explode('@', $destination)[1];
				require_once("application/controllers/{$controller}.php");
				$instance=new $controller;
				$instance->$function();
			}else if ($_SERVER['REQUEST_METHOD']=="POST"){
				require("system/status_header.php");
				set_status_header(404);
				echo "404";
			}
		}
	}
?>
