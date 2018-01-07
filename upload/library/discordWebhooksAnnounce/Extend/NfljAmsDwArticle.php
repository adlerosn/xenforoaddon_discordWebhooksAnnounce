<?php

class discordWebhooksAnnounce_Extend_NfljAmsDwArticle extends XFCP_discordWebhooksAnnounce_Extend_NfljAmsDwArticle{
	protected function _postSaveAfterTransaction(){
		if($this->isInsert() && $this->get('article_id') && $this->get('article_state')=='visible'){
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
			if(XenForo_Application::getOptions()->discordWebhook_newarticle)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				XenForo_Application::getOptions()->discordWebhookUrlMember,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Article** in category __'.$this->_getAMSCategoryModel()->getCategoryById($this->get('category_id'))['category_name'].'__',
	'attachments' => [[
		'title'=>$this->get('title'),
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('ams',$this->_getAMSArticleModel()->getArticleById($this->get('article_id'))),true),
		'text'=>$preview,
		'color'=>'#0088FF',
		'footer'=>$poster['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'footer_icon'=>$img,
		'thumb_url'=>$img,
		'ts'=>time(),
	]],
				]
			);
		}
		return parent::_postSaveAfterTransaction();
	}
}
