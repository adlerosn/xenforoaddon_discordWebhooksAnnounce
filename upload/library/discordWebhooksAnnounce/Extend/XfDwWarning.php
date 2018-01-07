<?php

class discordWebhooksAnnounce_Extend_XfDwWarning extends XFCP_discordWebhooksAnnounce_Extend_XfDwWarning {
	protected function _postSaveAfterTransaction(){
		if($this->isInsert()){
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$preview = $this->get('notes');
			if(strlen($preview)>600){
				$preview = substr($preview,0,600).'...';
			}
			if(XenForo_Application::getOptions()->discordWebhook_newwarning)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				XenForo_Application::getOptions()->discordWebhookUrlModerator,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**Member Warned**',
	'attachments' => [[
		'title'=>$this->get('title'),
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('warnings',$this->_getExistingData($this->get('warning_id'))['xf_warning']),true),
		'text'=>$preview,
		'fields'=>[[
			'title'=>'Points',
			'value'=>$this->get('points'),
		],[
			'title'=>'Expiration',
			'value'=>(($this->get('is_expired'))?'Already expired':(($this->get('expiry_date'))?date('r',$this->get('expiry_date')):'Never')),
		],[
			'title'=>'Added to usergroup IDs',
			'value'=>json_encode($this->get('extra_user_group_ids'),JSON_PRETTY_PRINT),
		]],
		'color'=>'#FFCC33',
		'thumb_url'=>$img,
		'footer'=>$poster['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'footer_icon'=>$img,
		'ts'=>time(),
	]],
				]
				
				/*
				'Member Warned',
				$this->get('title').': '.$this->get('notes'),
				XenForo_Application::getOptions()->discordWebhookUrlLogo,
				$poster['username'],
				$img,
				'#FFCC33',
				$this->get('content_title'),
				''.$this->get('points').' points'
				//*/
			);
		}
		return parent::_postSaveAfterTransaction();
	}
}
