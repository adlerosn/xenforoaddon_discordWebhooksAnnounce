<?php

class discordWebhooksAnnounce_Announcer {
	public static function buildMessageFull($hookUserName,$hookText,$hookIcon='',$authorName='',$authorIcon='',$color='',$title='',$value=''){
		return [
			'username' => $hookUserName,
			'text' => $hookText,
			'icon_url' => $hookIcon,
			'attachments' => [[
				'author_name'=>$authorName,
				'author_icon'=>$authorIcon,
				'color'=>$color,
				'fields'=>[[
					'title'=>$title,
					'value'=>$value,
				]],
			]],
		];
	}
	public static function buildMessageLesser($hookUserName,$hookText,$hookIcon){
		return [
			'username' => $hookUserName,
			'text' => $hookText,
			'icon_url' => $hookIcon,
		];
	}
	public static function preparedAnnounce($webhookUrl,$data){
		if(gettype($webhookUrl)=='array'){
			$o = [];
			foreach($webhookUrl as $url){
				$o[]=static::preparedAnnounce($url,$data);
			}
			if(count($o)==0){
				$o=null;
			}
			return $o;
		}
		if(strlen($webhookUrl)<5) return false;
		$url = $webhookUrl.'/slack';
		$url.='?wait=false';
		
		$options = [
			'http' => [
				'header'  => "Content-type: multipart/form-data\r\n",
				'method'  => 'POST',
				'content' => json_encode($data),
				'timeout' => XenForo_Application::getOptions()->discordWebhookTimeout,
			]
		];
		
		$context = stream_context_create($options);
		$result = false;
		
		if(XenForo_Application::getOptions()->discordWebhookSubprocess){
			//throw new XenForo_Exception('background');
			discordWebhooksAnnounce_Sender_HttpGet::backgroundRequest($url,$options);
			$result = null;
		}else{
			//throw new XenForo_Exception('foreround');
			$result = discordWebhooksAnnounce_Sender_HttpGet::blockingRequest($url,$options,XenForo_Application::getOptions()->discordWebhookErrorToLog);
		}
		return ($result);
	}
	
	public static function announce($webhookUrl,$hookUserName,$hookText,$hookIcon='',$authorName='',$authorIcon='',$color='',$title='',$value=''){
		return static::preparedAnnounce($webhookUrl,static::buildMessageFull($hookUserName,$hookText,$hookIcon,$authorName,$authorIcon,$color,$title,$value));
	}
	
	public static function lesserAnnounce($webhookUrl,$hookUserName,$hookText,$hookIcon){
		return static::preparedAnnounce($webhookUrl,static::buildMessageLesser($hookUserName,$hookText,$hookIcon));
	}
}
