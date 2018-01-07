<?php

class discordWebhooksAnnounce_Extend_XenGalleryDwMedia extends XFCP_discordWebhooksAnnounce_Extend_XenGalleryDwMedia {
	protected function _postSaveAfterTransaction(){
		if($this->isInsert() && $this->get('media_id') && $this->get('category_id')){
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$thumb = XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('xengallery/thumb-mini',$this->_getMediaModel()->getMediaById($this->get('media_id'))),true);
			if(XenForo_Application::getOptions()->discordWebhook_newmedia)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				XenForo_Application::getOptions()->discordWebhookUrlMember,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Media**',
	'attachments' => [[
		'title'=>$this->get('media_title'),
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('xengallery',$this->_getMediaModel()->getMediaById($this->get('media_id'))),true),
		'text'=>$this->_getCategoryModel()->getCategoryById($this->get('category_id'))['category_title'],
		'image_url'=>$thumb,
		'color'=>'#9933CC',
		'thumb_url'=>$img,
		'footer'=>$poster['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'footer_icon'=>$img,
		'ts'=>time(),
	]],
				]
			);
		}
		return parent::_postSaveAfterTransaction();
	}
}
