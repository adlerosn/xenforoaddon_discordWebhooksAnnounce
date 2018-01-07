<?php

class discordWebhooksAnnounce_ExtendClassListener {
	public static function getExtensions(){
		return [
			['NFLJ_AMS_DataWriter_Article'              , 'discordWebhooksAnnounce_Extend_NfljAmsDwArticle'],
			['XfAddOns_Blogs_DataWriter_Entry'          , 'discordWebhooksAnnounce_Extend_XfaBlogsDwEntry'],
			['XenGallery_DataWriter_Media'              , 'discordWebhooksAnnounce_Extend_XenGalleryDwMedia'],
			['XenGallery_ControllerPublic_Media'        , 'discordWebhooksAnnounce_Extend_XenGalleryCpMedia'],
			['Siropu_Chat_DataWriter_Bans'              , 'discordWebhooksAnnounce_Extend_SiropuChatDwBans'],
			['Siropu_Chat_DataWriter_Messages'          , 'discordWebhooksAnnounce_Extend_SiropuChatDwMessages'],
			['XenForo_DataWriter_ReportComment'         , 'discordWebhooksAnnounce_Extend_XfDwReport'],
			['XenForo_DataWriter_Warning'               , 'discordWebhooksAnnounce_Extend_XfDwWarning'],
			['XenForo_DataWriter_User'                  , 'discordWebhooksAnnounce_Extend_XfDwUser'],
			['XenForo_DataWriter_Discussion_Thread'     , 'discordWebhooksAnnounce_Extend_XfDwThread'],
			['XenForo_DataWriter_DiscussionMessage_Post', 'discordWebhooksAnnounce_Extend_XfDwPost'],
		];
	}
	public static function callback($class, array &$extend){
		$xtds = static::getExtensions();
		foreach($xtds as $xtd){
			$baseClass = $xtd[0];
			$toExtend = $xtd[1];
			if($class==$baseClass && !in_array($toExtend, $extend)){
				$extend[]=$toExtend;
			}
		}
	}
}
