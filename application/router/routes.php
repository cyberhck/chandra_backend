<?php
	header('Access-Control-Allow-Origin:*');
	header('Access-Control-Allow-Headers:Content-Type');
	Route::post(['/user/login'=>'User@login',
	             '/delivery/image' => 'Delivery@generate']);
	Route::get(['/delivery/image' => 'Delivery@list_image','/files/images' => 'Delivery@serve']);
	Route::delete(['/delivery/image' => 'Delivery@delete_image']);
