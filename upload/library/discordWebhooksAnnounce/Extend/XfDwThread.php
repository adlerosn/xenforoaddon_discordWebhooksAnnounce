<?php

class discordWebhooksAnnounce_Extend_XfDwThread extends XFCP_discordWebhooksAnnounce_Extend_XfDwThread {
	protected function _postSaveAfterTransaction(){
		$whDest = [];
		if(in_array($this->get('node_id'),XenForo_Application::getOptions()->discordWebhookVisibleForums)){
			$whDest[]=XenForo_Application::getOptions()->discordWebhookUrlMember;
		}
		if(array_key_exists($this->get('node_id'),XenForo_Application::getOptions()->discordWebhookForumToWebhook)){
			$whDest[]=XenForo_Application::getOptions()->discordWebhookForumToWebhook[$this->get('node_id')];
		}
		if($this->isInsert() && $this->get('discussion_state')=='visible' && $this->get('discussion_type')!='redirect' &&
		count($whDest)>0){
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$prefixId = $this->get('prefix_id');
			$prefixPhrase = XenForo_Model::create('XenForo_Model_ThreadPrefix')->getPrefixTitlePhraseName($prefixId);
			$prefix = strval(new XenForo_Phrase($prefixPhrase));
			if($prefixPhrase!=$prefix) $prefix='['.$prefix.'] - ';
			else $prefix = '';
			$bbcode2plain = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('discordWebhooksAnnounce_PlainFormatter',false));
			$threadPreview = strval($bbcode2plain->render($this->_getPostModel()->getPostById($this->get('first_post_id'))['message']));
			$threadPreview = XenForo_Helper_String::censorString($threadPreview);
			if(strlen($threadPreview)>325){
				$threadPreview = substr($threadPreview,0,325).'...';
			}
			$threadPreviewE = explode("\n",$threadPreview);
			if(count($threadPreviewE)>5){
				$threadPreviewE = array_slice($threadPreviewE,0,5);
				$threadPreview = implode("\n",$threadPreviewE).'...';
			}
			if(XenForo_Application::getOptions()->discordWebhook_newthreads)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				$whDest,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Thread** by __'.$poster['username'].'__ in __'.XenForo_Model::create('XenForo_Model_Node')->getNodeById($this->get('node_id'))['title'].'__',
	'attachments' => [[
		'title'=>$prefix.$this->get('title'),
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('threads',$this->getNewData()['xf_thread']),true),
		'text'=>$threadPreview,
		'color'=>'#00FFCC',
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
