<?php

class discordWebhooksAnnounce_Extend_XenGalleryCpMedia extends XFCP_discordWebhooksAnnounce_Extend_XenGalleryCpMedia {
	public function actionThumbMini(){
		$mediaId = $this->_input->filterSingle('media_id', XenForo_Input::UINT);
		$media = $this->_getMediaHelper()->assertMediaValidAndViewable($mediaId);

		$this->canonicalizeRequestUrl(
			XenForo_Link::buildPublicLink('xengallery/thumb-mini', $media)
		);
		
		$thumbnailPath = $this->_getMediaModel()->getMediaThumbnailUrl($media);

		if ($media['media_type'] == 'video_embed' || $media['media_type'] == 'video_upload')
		{
			return $this->responseRedirect(
				XenForo_ControllerResponse_Redirect::RESOURCE_CANONICAL_PERMANENT,
				XenForo_Link::buildPublicLink('xengallery', $media)
			);
		}

		$this->getRouteMatch()->setResponseType('raw');

		$viewParams = array(
			'thumbnailPath' => $thumbnailPath
		);
		
		return $this->responseView(
			'XenGallery_ViewPublic_Media_Thumbnail',
			'',
			$viewParams
		);
	}
}
