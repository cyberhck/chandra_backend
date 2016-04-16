<?php
	require_once ('status_header.php');

	/**
	 * Class Route
	 */
	class Route
	{
		/**
		 * @var $routes
		 * @property $db db
		 */
		public static $routes;
		public static $flag=false;
		public $db;
		/**
		 * @param $routes
		 *
		 * @return bool|string false if 404 else action on where to send incoming request
		 */
		private static function framework_check_route(&$routes){
			$REQUEST_URI = $_SERVER['REQUEST_URI'];
			if($REQUEST_URI == '/'){
				$REQUEST = 'Welcome@index';
				$routes['/']  = $REQUEST;
				return '/';
			}
			if(substr($REQUEST_URI, strlen($REQUEST_URI)-1,1) == '/'){
				$REQUEST_URI = substr($REQUEST_URI, 0, strlen($REQUEST_URI)-1);
			}
			$REQUEST_URI = explode('/', $REQUEST_URI);
			if(count($REQUEST_URI)<3){
				return false;
			}
			$REQUEST = [$REQUEST_URI[0],$REQUEST_URI[1],$REQUEST_URI[2]];
			$REQUEST = implode('/', $REQUEST);
			if(isset($routes[$REQUEST])){
				return $REQUEST;
			}else{
				return false;
			}
		}

		/**
		 * @param $routes array of routes
		 * Handles GET requests
		 */
		public static function get($routes){
			$data = self::framework_check_route($routes);
			if($data && $_SERVER['REQUEST_METHOD']=="GET"){
				$destination=$routes[$data];
				$controller = explode('@', $destination)[0];
				$function = explode('@', $destination)[1];
				require_once("application/controllers/{$controller}.php");
				$instance=new $controller;
				$instance->$function();
			}else if ($_SERVER['REQUEST_METHOD']=="GET"){
				set_status_header(404);
				echo "404";
			}
		}
		/**
		 * @param $routes array of routes
		 * Handles POST requests
		 */

		public static function post($routes){
			$data = self::framework_check_route($routes);
			if($data && $_SERVER['REQUEST_METHOD']=="POST"){
				$destination=$routes[$data];
				$controller = explode('@', $destination)[0];
				$function = explode('@', $destination)[1];
				require_once("application/controllers/{$controller}.php");
				$instance=new $controller;
				$instance->$function();
			}else if ($_SERVER['REQUEST_METHOD']=="POST"){
				set_status_header(404);
				echo "404";
			}
		}
		public static function delete($routes){
			$data = self::framework_check_route($routes);
			if($data && $_SERVER['REQUEST_METHOD']=="DELETE"){
				$destination=$routes[$data];
				$controller = explode('@', $destination)[0];
				$function = explode('@', $destination)[1];
				require_once("application/controllers/{$controller}.php");
				$instance=new $controller;
				$instance->$function();
			}else if ($_SERVER['REQUEST_METHOD']=="DELETE"){
				set_status_header(404);
				echo "404";
			}
		}

		/**
		 * @param string $value model to load
		 * Loads model to current framework
		 */
		public function load_model($value='')
		{
			require_once ('application/models/'.$value.'.php');
			$this->$value = new $value;
		}

		/**
		 * loads database to current framework
		 *
		 */
		public function load_db(){
			require_once ('system/db.php');
			$this->db=new db();
		}

		/**
		 * @param $value array
		 * outputs json encoded representation for array provided
		 */
		public function json_out($value)
		{
			header('Content-Type:application/json');
			echo json_encode($value);
		}
	}
