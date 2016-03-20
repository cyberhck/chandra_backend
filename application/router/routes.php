<?php
	header('Access-Control-Allow-Origin:*');
	header('Access-Control-Allow-Headers:Content-Type');
	Route::post(['/user/login'=>'User@login']);
	// Route::get(['/user/login'=>'User@login']);
