<?php

class discordWebhooksAnnounce_Extend_XfaBlogsDwEntry extends XFCP_discordWebhooksAnnounce_Extend_XfaBlogsDwEntry {
	protected function _postSaveAfterTransaction(){
		if($this->isInsert() && in_array($this->get('allow_view_entry'),[null,'','everyone','members']) && $this->get('entry_id')){
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$bbcode2plain = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('discordWebhooksAnnounce_PlainFormatter',false));
			$preview = strval($bbcode2plain->render($this->get('message')));
			if(strlen($preview)>250){
				$preview = substr($preview,0,250).'...';
			}
			if(XenForo_Application::getOptions()->discordWebhook_newblogentry)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				XenForo_Application::getOptions()->discordWebhookUrlMember,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Blog entry**',
	'attachments' => [[
		'title'=>$this->get('title'),
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('xfa-blog-entry',$this->_getExistingData($this->get('entry_id'))['xfa_blog_entry']),true),
		'text'=>$preview,
		'color'=>'#FF3399',
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
