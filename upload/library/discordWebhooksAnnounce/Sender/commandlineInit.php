<?php
if(php_sapi_name()=="cli" && count($argv)==2){
	$backgroundedTask = json_decode($argv[1],true);
	$startTime = microtime(true);
	$fileDir = $backgroundedTask['xfRootDir'];
	require_once($fileDir.'/library/XenForo/Autoloader.php');
	XenForo_Autoloader::getInstance()->setupAutoloader($fileDir.'/library');
	XenForo_Application::initialize($fileDir.'/library', $fileDir);
	XenForo_Application::set('page_start_time',$startTime);
	discordWebhooksAnnounce_Sender_HttpGet::blockingRequest(
		$backgroundedTask['url'],
		$backgroundedTask['data'],
		XenForo_Application::getOptions()->discordWebhookErrorToLog,
		XenForo_Application::getOptions()->discordWebhookMaxRetry,
		XenForo_Application::getOptions()->discordWebhookRetryCooldown
	);
}
