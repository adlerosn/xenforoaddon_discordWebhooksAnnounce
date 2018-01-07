<?php

class discordWebhooksAnnounce_Extend_XfDwReport extends XFCP_discordWebhooksAnnounce_Extend_XfDwReport {
	protected function _postSaveAfterTransaction(){
		if($this->isInsert()){
			$report = $this->_getReportModel()->getReportById($this->get('report_id'));
			$report+= $this->_getReportModel()->prepareReport($report);
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			if(XenForo_Application::getOptions()->discordWebhook_newreport)
			discordWebhooksAnnounce_Announcer::preparedAnnounce(
				XenForo_Application::getOptions()->discordWebhookUrlModerator,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New'.(($this->get('is_report'))?' ':' comment on ').'report**',
	'attachments' => [[
		'title'=>strval($report['contentTitle']),
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('reports',$report),true),
		'text'=>$this->get('message'),
		'fields'=>[[
			'title'=>'State',
			'value'=>strval($report['reportState']),
			'short'=>true,
		],[
			'title'=>'State change',
			'value'=>$this->get('state_change')?'To '.$this->get('state_change'):'None',
			'short'=>true,
		],[
			'title'=>'Content reported',
			'value'=>XenForo_Link::convertUriToAbsoluteUri($report['contentLink'],true),
			'mrkdwn'=>true,
		],[
			'title'=>'Report ID',
			'value'=>'#'.$this->get('report_id'),
			'short'=>true,
		],[
			'title'=>'Comment ID',
			'value'=>'#'.$this->get('report_comment_id'),
			'short'=>true,
		]],
		'color'=>'#3333FF',
		'thumb_url'=>$img,
		'footer'=>$poster['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'footer_icon'=>$img,
		'ts'=>time(),
	]],
				]
				
				/*
				(($this->get('is_report'))?'New':'Comment on').' Report #'.$this->get('report_id'),
				'>> '.$this->get('message'),
				XenForo_Application::getOptions()->discordWebhookUrlLogo,
				$poster['username'],
				$img,
				'#3333FF',
				$this->_getReportModel()->getReportById($this->get('report_id'))['report_state'],
				'Status change: '.(($this->get('state_change')=='')?'none':$this->get('state_change'))
				//*/
			);
		}
		return parent::_postSaveAfterTransaction();
	}
}
