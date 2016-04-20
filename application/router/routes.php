<?php
	header('Access-Control-Allow-Origin:*');
	header('Access-Control-Allow-Headers:Content-Type,auth-token');
	Route::post(['/user/login'=>'User@login',
			'/delivery/image' => 'Delivery@generate',
			'/user/set_phone' => 'User@set_phone',
			'/mail/incoming' => 'Delivery@incoming',
			'/mail/incoming_failed' => 'Delivery@incoming_failed']);
	Route::get(['/delivery/image' => 'Delivery@list_image',
	            '/files/images' => 'Delivery@serve']);
	Route::delete(['/delivery/image' => 'Delivery@delete_image']);
