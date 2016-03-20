<?php
	/**
	 * Date: 3/20/16
	 * Time: 3:36 PM
	 */

	class Auth extends Route{
		/**
		 * @param $access_token access_token to be sent by client
		 * @return boolean true if it's valid, false otherwise
		 */
		public function check_auth($access_token){
			$this->load_db();
			$db = $this->db->get_db();
			$sql = "SELECT * FROM access WHERE auth_token = ?";
			$statement = $db->prepare($sql);
			$statement->bind_param('s',$access_token);
			$statement->execute();
			$result = $statement->get_result();
			if($result->num_rows == 0){
				return false;
			}else{
				return true;
			}
		}
	}