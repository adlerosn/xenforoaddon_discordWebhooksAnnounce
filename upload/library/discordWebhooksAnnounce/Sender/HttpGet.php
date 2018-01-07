<?php

class discordWebhooksAnnounce_Sender_HttpGet{
	public static function blockingRequest($url,$data,$logError=true,$retriesAvailable=0,$retryCooldown=0){
		$result = false;
		if($retriesAvailable<0){
			return $result;
		}
		$context = stream_context_create($data);
		$success = false;
		try{
			$result = file_get_contents($url, false, $context);
			$success = true;
		}catch(Exception $e){
			if($logError){
				static::logException($e);
			}
		}catch(Error $e){
			if($logError){
				static::logException($e);
			}
		}catch(ErrorException $e){
			if($logError){
				static::logException($e);
			}
		}
		if(!$success){
			sleep($retryCooldown);
			return static::blockingRequest($url,$data,$logError,$retriesAvailable-1,$retryCooldown);
		}
		return $result;
	}
	public static function logException($e){
		XenForo_Error::logException($e);
	}
	public static function backgroundRequest($url,$data){
		$cmd = ('nohup php '.dirname(__FILE__).'/commandlineInit.php'.' '.
			escapeshellarg(json_encode([
				'url'=>$url,
				'data'=>$data,
				'xfRootDir'=>XenForo_Application::getInstance()->getRootDir(),
				'xfConfigDir'=>XenForo_Application::getInstance()->getConfigDir(),
			])).' 2>&1 1>/dev/null &'
		);
		exec($cmd);
		return $cmd;
	}
}
