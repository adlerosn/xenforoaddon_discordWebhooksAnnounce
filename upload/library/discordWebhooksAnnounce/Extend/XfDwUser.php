<?php

class discordWebhooksAnnounce_Extend_XfDwUser extends XFCP_discordWebhooksAnnounce_Extend_XfDwUser {
	protected function _postSaveAfterTransaction(){
		if($this->isInsert()){
			if($this->get('user_state')=='moderated'){
				$uid = $this->get('user_id');
				$xmu = XenForo_Model::create('XenForo_Model_User');
				$poster = $xmu->getUserById($uid);
				$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
				$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
				$filteredData = $this->_discordWebhook_getUserDetailedData();
				if(XenForo_Application::getOptions()->discordWebhook_newuserawaitingapproval)
				discordWebhooksAnnounce_Announcer::preparedAnnounce(
				XenForo_Application::getOptions()->discordWebhookUrlAdmin,
				[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Member Awaiting Approval**',
	'attachments' => [[
		'title'=>$poster['username'],
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'text'=>'Waiting in moderation queue',
		'color'=>'#FF6600',
		'ts'=>time(),
		'footer'=>$poster['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'footer_icon'=>$img,
		'fields'=>$filteredData,
	]],
				]
				);
			}
		}
		if(
			($this->isInsert() && $this->get('user_state')=='valid')
			||
			($this->isUpdate() && $this->isChanged('user_state') &&
				(
					(
						$this->getExisting('user_state') == 'moderated'
						&&
						$this->get('user_state') == 'valid'
					)
					||
					(
						$this->getExisting('user_state') == 'email_confirm'
						&&
						$this->get('user_state') == 'valid'
					)
				)
			)
		){
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$filteredData = $this->_discordWebhook_getUserDetailedData();
		if(XenForo_Application::getOptions()->discordWebhook_newregistrations)
		discordWebhooksAnnounce_Announcer::preparedAnnounce(
			XenForo_Application::getOptions()->discordWebhookUrlMember,
			[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**New Member**',
	'attachments' => [[
		'title'=>$poster['username'],
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'color'=>'#FF6600',
		'ts'=>time(),
		'footer'=>$poster['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'footer_icon'=>$img,
		'fields'=>$filteredData,
	]],
			]
			);
			//die(print_r($filteredData,true));
		}
		if(
			($this->isUpdate() && $this->isChanged('is_banned') &&
				(
					(
						$this->getExisting('is_banned') == false
						&&
						$this->get('is_banned') == true
					)
					||
					(
						$this->getExisting('is_banned') == true
						&&
						$this->get('is_banned') == false
					)
				)
			)
		){
			$isBanned = $this->get('is_banned');
			$uid = $this->get('user_id');
			$xmu = XenForo_Model::create('XenForo_Model_User');
			$poster = $xmu->getUserById($uid);
			$img = XenForo_Template_Helper_Core::helperAvatarUrl($poster,'m');
			$img = XenForo_Link::convertUriToAbsoluteUri($img,true);
			$filteredData = $this->_discordWebhook_getUserDetailedData();
		if(XenForo_Application::getOptions()->discordWebhookAnnounceMemberBan)
		discordWebhooksAnnounce_Announcer::preparedAnnounce(
			XenForo_Application::getOptions()->discordWebhookUrlModerator,
			[
	'username' => XenForo_Application::getOptions()->boardTitle,
	'icon_url' => XenForo_Application::getOptions()->discordWebhookUrlLogo,
	'text' => '**Member '.($isBanned?'B':'Unb').'anned**',
	'attachments' => [[
		'title'=>$poster['username'],
		'title_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'color'=>'#FF0000',
		'ts'=>time(),
		'footer'=>$poster['username'],
		'footer_link'=>XenForo_Link::convertUriToAbsoluteUri(XenForo_Link::buildPublicLink('members',$poster),true),
		'footer_icon'=>$img,
		'fields'=>$filteredData,
	]],
			]
			);
			//die(print_r($filteredData,true));
		}
		return parent::_postSaveAfterTransaction();
	}
	protected function _discordWebhook_getUserDetailedData(){
		
		$newData = $this->_getUserModel()->getUserById($this->get('user_id'),['join'=>XenForo_Model_User::FETCH_USER_PROFILE])+$this->getMergedNewData();
		$labels = discordWebhooksAnnounce_userDataExtractor::getCustomUserFieldsArray();
		$data = discordWebhooksAnnounce_userDataExtractor::getFields($newData);
		$filteredData=[];
		$dob=[0,0,0];
		foreach($data as $k=>$v){
			if($v===0 || $v===false || $v==='' || $v===[] || $v===null){
				continue;
			}
			if(gettype($v)==='string'){
				if(array_key_exists($k.'_choice_'.$v,$labels)){
					$v = $labels[$k.'_choice_'.$v];
				}
			}
			if(gettype($v)==='array'){
				foreach($v as &$itm){
					if(array_key_exists($k.'_choice_'.$itm,$labels)){
						$itm = $labels[$k.'_choice_'.$itm];
					}
				}
				$v = implode(', ',$v);
			}
			if($k==='dob_day'){
				$dob[0]=$v;
				continue;
			}
			if($k==='dob_month'){
				$dob[1]=$v;
				continue;
			}
			if($k==='dob_year'){
				$dob[2]=$v;
				continue;
			}
			if($k==='username' || $k==='email'){
				continue;
			}
			if(array_key_exists($k,$labels)){
				$k=$labels[$k];
			}
			$filteredData[]=[
				'title'=>$k,
				'value'=>$v,
				'short'=>count($v)<35,
			];
		};
		if($dob[0]!==0&&$dob[1]!==0&&$dob[2]!==0){
			$dobd = mktime(date("H"), date("i"), date("s"), $dob[1], $dob[0], $dob[2]);
			$dobo = new DateTime(date('r',$dobd));
			$nowo = new DateTime();
			$interval = $nowo->diff($dobo);
			$age = $interval->y;
			if(XenForo_Application::getOptions()->discordWebhook_newregistrations_dob){
				$filteredData = array_merge([[
					'title'=>'Birthday',
					'value'=>date('M j, Y',$dobd).' (Age: '.$age.')',
				]],$filteredData);
			}
			//die(print_r([$dob,$dobd,$age],true));
		}
		//die(print_r($filteredData,true));
		return $filteredData;
	}
}
