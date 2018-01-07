<?php

class discordWebhooksAnnounce_adminOptionChatToWebhook {
	public static function renderView(XenForo_View $view, $fieldPrefix, array $preparedOption, $canEdit){
		$editLink = $view->createTemplateObject('option_list_option_editlink', array(
			'preparedOption' => $preparedOption,
			'canEditOptionDefinition' => $canEdit
		));
		$t = $preparedOption['option_value'];
		
		$siropuChatInstalledAndActive = static::_isAddOnIdInstalledAndActivated('siropu_chat');
		
		if(!$siropuChatInstalledAndActive){
			return $view->createTemplateObject('kiror_option_template_discord_wh_siropuchat', array(
				'fieldPrefix' => $fieldPrefix,
				'listedFieldName' => $fieldPrefix . '_listed[]',
				'preparedOption' => $preparedOption,
				'formatParams' => $preparedOption['formatParams'],
				'editLink' => $editLink,
				
				'siropuChatInstalledAndActive' => $siropuChatInstalledAndActive,
			));
		}
		
		$rooms = XenForo_Model::create('Siropu_Chat_Model')->getAllRooms();
		ksort($rooms);
		
		$disp = [];
		foreach($rooms as $room){
			$disp[$room['room_id']]=[
				'id'=>$room['room_id'],
				'nm'=>strval($room['room_name']),
				'wh'=>(array_key_exists($room['room_id'],$t)?$t[$room['room_id']]:''),
			];
		}
		/*
		homeOrServer_DownloadHelper::sendAsDownload(
		json_encode(
		//$fn
		[$t,$disp,$rooms]
		,JSON_PRETTY_PRINT)
		,'a','',false);
		//*/
		return $view->createTemplateObject('kiror_option_template_discord_wh_siropuchat', array(
			'fieldPrefix' => $fieldPrefix,
			'listedFieldName' => $fieldPrefix . '_listed[]',
			'preparedOption' => $preparedOption,
			'formatParams' => $preparedOption['formatParams'],
			'editLink' => $editLink,
			
			'siropuChatInstalledAndActive' => $siropuChatInstalledAndActive,
			
			'rooms' => $disp,
		));
	}
	
	public static function validate(array &$fields, XenForo_DataWriter $dw, $fieldName){
		$output = array();
		
		foreach($fields as $k=>$v)
			if(substr($v,0,4)=='http')
				$output[$k]=$v;
		
		$fields = $output;
		
		/*
		homeOrServer_DownloadHelper::sendAsDownload(
		json_encode(
		$fields
		,JSON_PRETTY_PRINT)
		,'a','',false);
		//*/
		
		return true;
	}
	
	protected static function _isAddOnIdInstalledAndActivated($addOnId){
		$addon = XenForo_Model::create('XenForo_Model_AddOn')->getAddOnById($addOnId);
		return (is_array($addon) && isset($addon['active']) && $addon['active']);
	}
}
