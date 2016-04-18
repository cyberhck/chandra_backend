<?php
	/**
	 * Created by PhpStorm.
	 * User: cyberkiller
	 * Date: 4/16/16
	 * Time: 10:30 PM
	 */


	class SendSMS
	{
		private $base_url="https://control.msg91.com/api/sendhttp.php?";
		private $auth_token;
		private $sender = "GLOBML";
		private $route = "4";
		private $country = "91";
		function __construct (){
			require "application/config/config.php";
			$this->auth_token = $SMS['auth_key'];
		}
		function send($mobile,$message){
			require_once ('application/helpers/google/src/Google/autoload.php');
			require ("application/helpers/text_local/textlocal.class.php");
			$sms = new Textlocal(null,null,$this->auth_token);
			$sender = "TXTLCL";
			try{
				$response = $sms->sendSms(array($mobile), $message, $sender); //let's not send now,
			}catch (Exception $e){
				return false;//since it might be a DND number
			}
		}

	}