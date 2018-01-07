<?php

class discordWebhooksAnnounce_Extend_XfDwPost extends XFCP_discordWebhooksAnnounce_Extend_XfDwPost {
	protected function _postSaveAfterTransaction(){
		$forum = $this->_getForumInfo();
		$thread = $this->_getThreadModel()->getThreadById($this->get('thread_id'));
		$whDest = [];
		if(in_array($thread['node_id'],XenForo_Application::getOptions()->discordWebhookVisibleForums)){
			$whDest[]=XenForo_Application::getOptions()->discordWebhookUrlMember;
		}
		if(array_key_exists($thread['node_id'],XenForo_Application::getOptions()->discordWebhookForumToWebhook)){
			$whDest[]=XenForo_Application::getOptions()->discordWebhookForumToWebhook[$thread['node_id']];
		}
		if($this->isInsert() && !$this->isDiscussionFirstMessage() &&
		$this->get('message_state')=='visible' &&
		count($whDest)>0
		){
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$bbcode2plain = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('discordWebhooksAnnounce_PlainFormatter',false));
			$threadPreview = strval($bbcode2plain->render(
				$this->_getPostModel()->getPostById(
					$this->_getThreadModel()->getThreadById(
						$this->get('thread_id')
						)
					['first_post_id']
					)
				['message']
			));
			$thread = $this->_getThreadModel()->getThreadById($this->get('thread_id'));
			$prefixId = $thread['prefix_id'];
			$prefixPhrase = XenForo_Model::create('XenForo_Model_ThreadPrefix')->getPrefixTitlePhraseName($prefixId);
			$prefix = strval(new XenForo_Phrase($prefixPhrase));
			if($prefixPhrase!=$prefix) $prefix='['.$prefix.'] - ';
			else $prefix = '';
			$replyPreview = strval($bbcode2plain->render($this->get('message')));
			$originalPoster = $xmu->getUserById(
				$this->_getPostModel()->getPostById(
					$this->_getThreadModel()->getThreadById(
						$this->get('thread_id')
						)
					['first_post_id']
					)
				['user_id']
			);
			$oimg = XenForo_Template_Helper_Core::helperAvatarUrl($originalPoster,'m');
			$oimg = XenForo_Link::convertUriToAbsoluteUri($oimg,true);
			$threadPreview = XenForo_Helper_String::censorString($threadPreview);
			$replyPreview = XenForo_Helper_String::censorString($replyPreview);
			if(strlen($threadPreview)>125){
				$threadPreview = substr($threadPreview,0,125).'...';
			}
			if(strlen($replyPreview)>325){
				$replyPreview = substr($replyPreview,0,325).'...';
			}
			$threadPreviewE = explode("\n",$threadPreview);
			if(count($threadPreviewE)>2){
				$threadPreviewE = array_slice($threadPreviewE,0,2);
				$threadPreview = implode("\n",$threadPreviewE).'...';
			}
			$replyPreviewE = explode("\n",$replyPreview);
			if(count($replyPreviewE)>5){
				$replyPreviewE = array_slice($replyPreviewE,0,5);
				$replyPreview = implode("\n",$replyPreviewE).'...';
			}
			if(XenForo_Application::getOptions()->discordWebhook_newthreadreply)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				$whDest,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Thread Reply** by __'.$poster['username'].'__ in __'.XenForo_Model::create('XenForo_Model_Node')->getNodeById($forum['node_id'])['title'].'__',
	'attachments' => [[
		'title'=>$prefix.$thread['title'],
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('threads/unread',$thread),true),
		'text'=>$threadPreview,
		'fields'=>[[
			'title'=>'Replied',
			'value'=>$replyPreview,
		]],
		'color'=>'#33FF00',
		'thumb_url'=>$oimg,
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
