<?php
/**
* 
*/
class user_model extends Route
{
	public function test_model($value='')
	{
		$this->load_db();
		$data = $this->db->get('users');
		$result = [];
		while($row = $data->fetch_assoc()){
			$result[] = $row;
		}
		return $result;
	}
}
