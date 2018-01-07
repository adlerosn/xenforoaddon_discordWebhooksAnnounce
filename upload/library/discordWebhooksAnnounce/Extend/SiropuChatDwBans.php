<?php

class discordWebhooksAnnounce_Extend_SiropuChatDwBans extends XFCP_discordWebhooksAnnounce_Extend_SiropuChatDwBans {
	protected function _postSaveAfterTransaction(){
		if($this->isInsert() && $this->get('ban_user_id')){
			$rsn = $this->get('ban_reason');
			$uid = $this->get('ban_user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$moderator = $xmu->getUserById($this->get('ban_author'));
			$banned = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($banned,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$mimg = XenForo_Template_Helper_Core::helperAvatarUrl($moderator,'m');
			$mimg = XenForo_Link::convertUriToAbsoluteUri($mimg,true);
			if(XenForo_Application::getOptions()->discordWebhook_newchatban)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				XenForo_Application::getOptions()->discordWebhookUrlModerator,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Chat Ban**',
	'attachments' => [[
		'title'=>$banned['username'],
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('chat/banned'),true),
		'text'=>$this->get('ban_reason'),
		'fields'=>[[
			'title'=>'Expiration',
			'value'=>(($this->get('ban_end'))?date('r',$this->get('ban_end')):'Never'),
			'short'=>false,
		],[
			'title'=>'Ban type',
			'value'=>$this->get('ban_type'),
			'short'=>true,
		],[
			'title'=>'Banned room',
			'value'=>(($this->get('ban_room_id')==-1)?'All rooms':'Room "'.($this->_getModel()->getRoomById($this->get('ban_room_id'))['room_name']).'"'),
			'short'=>true,
		]],
		'color'=>'#FF0000',
		'thumb_url'=>$img,
		'footer'=>$moderator['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$moderator),true),
		'footer_icon'=>$mimg,
		'ts'=>time(),
	]],
				]
			/*
				'New Chat Ban',
				'Reason: '.$rsn,
				XenForo_Application::getOptions()->discordWebhookUrlLogo,
				$banned['username'],
				$img,
				'#FF0000',
				(($this->get('ban_end'))?'Temporary':'Forever'),
				'by '.$moderator['username']
				//*/
			);
		}
		return parent::_postSaveAfterTransaction();
	}
}
