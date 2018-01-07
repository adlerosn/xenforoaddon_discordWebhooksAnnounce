<?php

class discordWebhooksAnnounce_Extend_SiropuChatDwMessages extends XFCP_discordWebhooksAnnounce_Extend_SiropuChatDwMessages {
	protected function _postSaveAfterTransaction(){
		$xenOpt = XenForo_Application::getOptions();
		$mapping = $xenOpt->discordWebhookSiropuChatAnnouncer;
		if($this->isInsert() && array_key_exists($this->get('message_room_id'),$mapping)
		&& !in_array($this->get('message_type'),['whisper','activity'])){
			$uid = $this->get('message_user_id');
			if($this->get('message_type')=='me'){
				$uid=0;
			}
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$destWh = $mapping[$this->get('message_room_id')];
			$text = $this->get('message_text');
			$text = XenForo_Helper_String::censorString($text);
			$text = discordWebhooksAnnounce_Conversors::bbcode2discord($text);
			if($this->get('message_type')=='me'){
				$text='*'.$text.'*';
			}
			discordWebhooksAnnounce_Announcer::lesserAnnounce(
				$destWh,
				($poster['user_id'])?$poster['username']:discordWebhooksAnnounce_Conversors::html2plain($xenOpt->siropu_chat_bot_name),
				$text,
				($poster['user_id'])?$img:$xenOpt->discordWebhookUrlLogo
			);
		}
		return parent::_postSaveAfterTransaction();
	}
}
