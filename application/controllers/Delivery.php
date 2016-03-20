<?php
	class Delivery extends Route
	{
		public function generate()
		{
			var_dump($_POST);
			set_status_header(501);
		}
	}
